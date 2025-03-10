<?php

namespace MystNov\Core\Http\Traits;

use App\Support\Facades\MasterPage as FacadesMasterPage;
use Carbon\Carbon;
use MystNov\Core\Enums\NetworkSurplusPointsRecipient;
use MystNov\Core\Enums\OptionName;
use MystNov\Core\Enums\OrderStatus;
use MystNov\Core\Enums\PointOrderPaymentMethod;
use MystNov\Core\Enums\PointOrderSource;
use MystNov\Core\Enums\WalletOwnerType;
use MystNov\Core\Enums\WalletSource;
use MystNov\Core\Enums\WalletTransactionType;
use MystNov\Core\Models\Member;
use MystNov\Core\Models\MemberProduct;
use MystNov\Core\Models\Option;
use MystNov\Core\Models\PointOrder;

trait OrderTrait
{
    public function shareCommission($order, $transaction, $discountedPoint)
    {
        $presenterIds = $this->getPresenterNetwork($order->member_id);
        $maxNetworkLevel = Option::where('name', OptionName::MAX_COMMISSION_LEVEL)->where('page_id', $order->page_id)->first()->value ?? config('define.max_network_level');
        if (count($presenterIds) > 0) {
            foreach ($presenterIds as $key => $presenterId) {
                if ($key >= $maxNetworkLevel) {
                    break;
                }

                $commissionPercent = Option::where('name', 'commission_percent_f' . $key + 1)->where('page_id', $order->page_id ?? null)->first()->value ?? null;

                $commissionPoints = $discountedPoint * ((float)$commissionPercent / 100);

                // Save commission point history
                // Bỏ qua phần này
                // Lịch sử chia sẻ Point chuyển sang Member Wallet
                $commissionPointOrder = new PointOrder;
                $commissionPointOrder->member_id = $presenterId;
                $commissionPointOrder->source = PointOrderSource::COMMISSION;
                $commissionPointOrder->point = $commissionPoints;
                $commissionPointOrder->payment_method = PointOrderPaymentMethod::OTHER;
                $commissionPointOrder->network_member_id = $order->member_id;
                $commissionPointOrder->order_id = $order->id;
                $commissionPointOrder->save();

                // Lưu lịch sử thay đổi Point trong Wallet khi chia hoa hồng
                // Trừ Point trong Master Wallet
                $this->mWallet->newInstance()->insert((object)[
                    'parent_id'       => $transaction->id,
                    'owner_id'        => $order->page_id,
                    'owner_type'      => WalletOwnerType::MASTER,
                    'point'           => -$commissionPoints,
                    'source'          => WalletSource::SEND_COMMISSION,
                    'morph_member_id' => $presenterId,
                    'morph_type'      => WalletTransactionType::ORDER,
                    'morph_id'        => $order->id,
                ]);

                // Cộng Point hoa hồng vào Member Wallet
                $this->mWallet->newInstance()->insert((object)[
                    'owner_id'        => $presenterId,
                    'owner_type'      => WalletOwnerType::MEMBER,
                    'page_id'         => $order->page_id,
                    'point'           => $commissionPoints,
                    'source'          => WalletSource::GET_COMMISSION,
                    'morph_member_id' => $presenterId, // ID của member nhận hoa hồng
                    'morph_type'      => WalletTransactionType::NETWORK_MEMBER,
                    'morph_id'        => $order->member_id, // ID của member gửi hoa hồng
                ]);

                // Add commission point to Presenter's Wallet
                $presenter = Member::find($presenterId);
                if ($presenter) {
                    $presenter->point += $commissionPoints;
                    $presenter->save();
                }
            }
        }
    }

    public function handleMemberProduct($order)
    {
        // Get carbon time
        $dt = Carbon::now();

        // Trường hợp Gia hạn sản phẩm
        // - Cập nhật thời gian hết hạn +1 năm
        // - Cập nhật các thông tin sản phẩm mới nhất từ System
        if ($this->mode === 'EXTEND_PRODUCT') {
            $mp = MemberProduct::find($order->member_product_id);

            if ($mp) {
                $expiresAt = new Carbon($mp->expires_at);

                // Update Expires time
                // If the product has expired -> New Expires_At from now + 1yr
                if ($dt->gt($expiresAt)) {
                    $newExpiresAt = $dt->addYear();
                }
                // If the product has not expired -> New Expires_At from Old Expires_at + 1yr
                else {
                    $newExpiresAt = $expiresAt->addYear();
                }

                $mp->expires_at = $newExpiresAt;

                // Update thông tin mới nếu gói đã thay đổi
                $mp->product_type = $order->product_type;
                $mp->product_name = $order->product_name;
                $mp->product_price = $order->product_price;
                $mp->save();

                // Lưu lịch sử mua hàng
                $mp->orders()->syncWithoutDetaching([$order->id]);
            }
        }

        // Trường hợp mua sản phẩm từ System
        // Tạo thông tin sản phẩm cho Member
        if ($this->mode === 'BUY_SYSTEM_PRODUCT') {
            /**
             * Store Member products when Member buy new products
             */
            $memberProducts = [];
            $productInsertedCodes = [];

            for ($i = 1; $i <= $order->qty; $i++) {
                $productInsertedCodes[] = $productCode = $this->generateProductCode($order, $i);
                $memberProducts[] = [
                    'expires_at'    => null,
                    'member_id'     => $order->member_id,
                    'product_id'    => $order->product_id,
                    'product_type'  => $order->product_type,
                    'product_code'  => $productCode,
                    'product_name'  => $order->product_name,
                    'product_price' => $order->product_price,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ];
            }
            MemberProduct::insert($memberProducts);

            // Get inserted Ids
            $memberProductInsertedIds = MemberProduct::whereIn('product_code', $productInsertedCodes)->select('id')->get()->pluck('id');

            // Một product sẽ thuộc nhiều order (trường hợp member mua mới và gia hạn)
            // Một order sẽ có nhiều product (mua một product với số lượng nhiều)
            $order->memberProducts()->syncWithoutDetaching($memberProductInsertedIds);
        }

        // Trường hợp mua sản phẩm thuộc Master Page (do Master IB sở hữu)
        // Chuyển sản phẩm từ User Master IB sang cho User mua hàng
        if ($this->mode === 'BUY_MASTER_PAGE_PRODUCT') {
            // Lấy ra số lượng sản phẩm ($qty) của Master IB
            // Update owner thành User mua hàng
            $updatedRecords = MemberProduct::whereNull('expires_at')
                ->where('member_id', FacadesMasterPage::page()->member_id)
                ->where('product_id', $order->product_id)
                ->asc()
                ->limit($order->qty)
                ->pluck('id');


            MemberProduct::whereIn('id', $updatedRecords)
                ->update([
                    'member_id'  => $order->member_id,
                    'created_at' => now()
                ]);

            // Tạo order detail cho các sản phẩm vừa mua
            $order->memberProducts()->attach($updatedRecords);
        }
    }

    protected function generateProductCode($order, $loopIndex)
    {
        // <product_code>-<order_id>-<index>
        return $order->product_code . '-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . '-' . str_pad($loopIndex, 4, '0', STR_PAD_LEFT);
    }

    public function completeOrder($order)
    {
        $revenue = $this->distributeRevenue($order->total, $order->page_id);

        // Lưu lịch sử thay đổi Point vào Wallet
        // Cộng 45% Point vào System Wallet
        $this->mWallet->newInstance()->insert((object)[
            'owner_type'      => WalletOwnerType::SYSTEM,
            'point'           => $revenue['for_system'],
            'source'          => WalletSource::GET_FROM_ORDER,
            'morph_member_id' => $order->member_id,
            'morph_type'      => WalletTransactionType::ORDER,
            'morph_id'        => $order->id,
        ]);

        // Cộng 55% Point vào Master Wallet
        $transaction = $this->mWallet->newInstance()->insert((object)[
            'owner_id'        => $order->page_id,
            'owner_type'      => WalletOwnerType::MASTER,
            'point'           => $revenue['for_master'],
            'source'          => WalletSource::GET_FROM_ORDER,
            'morph_member_id' => $order->member_id,
            'morph_type'      => WalletTransactionType::ORDER,
            'morph_id'        => $order->id,
        ]);

        // Share commissions point for member
        $this->shareCommission($order, $transaction, $revenue['for_network']);

        // Lưu thông tin product thuộc về member
        // Với trạng thái "Get New" hoặc "Extend"
        $this->handleMemberProduct($order);

        // Kiểm tra nếu Master Page này đã cài đặt phần Point dư còn lại sẽ chuyển về cho System
        $this->transferSurplusPointToSystem($order, $revenue['for_network']);
    }

    /**
     * Kiểm tra nếu Master Page này đã cài đặt phần Point dư còn lại sẽ chuyển về cho System
     * Point dư: là số point Master Page chia cho Network còn thừa do số cấp của Network không đạt đến tối đa theo cài đặt
     * Ví dụ: Master Page cài đặt tối đa 10 cấp, nhưng Network hiện tại chỉ có 6 cấp thì số tiền thừa cho 4 cấp còn lại sẽ trả về System
     * thay vì thuộc về Master Page như mặc định
     */
    public function transferSurplusPointToSystem($order, $revenueForNetwork)
    {
        if (FacadesMasterPage::page()->network_surplus_points_recipient === NetworkSurplusPointsRecipient::SYSTEM->value) {
            // Tổng số Point đã chia thành công cho Network
            $revenueSharedToNetwork = $this->mWallet
                ->where('owner_id', $order->page_id)
                ->where('owner_type', WalletOwnerType::MASTER)
                ->where('source', WalletSource::SEND_COMMISSION)
                ->where('morph_type', WalletTransactionType::ORDER)
                ->where('morph_id', $order->id)
                ->sum('point');

            // Số tiền thừa hoàn về cho System
            $surplusPoint = $revenueForNetwork - abs($revenueSharedToNetwork ?? 0);

            // Chuyển tiền về System
            // 1. Trừ tiền trong Master Wallet
            $this->mWallet->newInstance()->insert((object)[
                'owner_id'        => $order->page_id,
                'owner_type'      => WalletOwnerType::MASTER,
                'point'           => -$surplusPoint,
                'source'          => WalletSource::SEND_SURPLUS,
                'morph_member_id' => $order->member_id,
                'morph_type'      => WalletTransactionType::ORDER,
                'morph_id'        => $order->id,
            ]);

            // 2. Cộng tiền vào System Wallet
            $this->mWallet->newInstance()->insert((object)[
                'owner_type'      => WalletOwnerType::SYSTEM,
                'point'           => $surplusPoint,
                'source'          => WalletSource::GET_SURPLUS,
                'morph_member_id' => $order->member_id,
                'morph_type'      => WalletTransactionType::ORDER,
                'morph_id'        => $order->id,
            ]);
        }
    }

    /**
     * Khi Master Page bán sản phẩm của Master IB sở hữu
     * Toàn bộ doanh thu sẽ thuộc về Master Page
     * không chia lại cho System
     */
    public function completeMasterPageProductOrder($order)
    {
        // Lấy tùy chọn % hoa hồng Master chia cho Network
        $networkDiscountRatio = Option::where('name', OptionName::DISCOUNT_RATIO)->where('page_id', $order->page_id)->first()->value ?? 0;

        // 100% doanh thu sẽ về Master Wallet
        $totalForMaster = $order->total;

        // Từ doanh thu nhận về, trích ra một phần theo cài đặt để chia hoa hồng cho member
        $discountedForNetworkMember = $totalForMaster * (float)$networkDiscountRatio / 100;

        // Cộng 100% Point vào Master Wallet
        $transaction = $this->mWallet->newInstance()->insert((object)[
            'owner_id'        => $order->page_id,
            'owner_type'      => WalletOwnerType::MASTER,
            'point'           => $totalForMaster,
            'source'          => WalletSource::GET_FROM_ORDER,
            'morph_member_id' => $order->member_id,
            'morph_type'      => WalletTransactionType::ORDER,
            'morph_id'        => $order->id,
        ]);

        // Share commissions point for member
        $this->shareCommission($order, $transaction, $discountedForNetworkMember);

        // Lưu thông tin product thuộc về member
        // Với trạng thái "Get New" hoặc "Extend"
        $this->handleMemberProduct($order);
    }

    public function cancelOrder($order)
    {
        // Nếu member đã thanh toán cho order này
        // Hoàn số point tương ứng lại cho member
        if (in_array($order->previous_status, [OrderStatus::PAID->value, OrderStatus::PROCESSING->value, OrderStatus::COMPLETED->value])) {
            $total = $order->total;

            // Refund points to member's wallet
            $member = Member::find($order->member_id);
            $member->point += $total;
            $member->save();

            // Lưu lịch sử hoàn point vào Point Order
            // Bỏ qua phần này
            // Point Order chỉ lưu thông tin các giao dịch Nạp point vào ví
            // Các giao dịch thay đổi số point khác sẽ chuyển sang lưu trên Wallet
            $pointOrder = new PointOrder;
            $pointOrder->member_id = $order->member_id;
            $pointOrder->source = PointOrderSource::REFUND_ORDER;
            $pointOrder->point = $total;
            $pointOrder->payment_method = PointOrderPaymentMethod::OTHER;
            $pointOrder->order_id = $order->id;
            $pointOrder->order_type = $order->product_type;
            $pointOrder->save();

            // Hoàn point vào Member Wallet
            $this->mWallet->newInstance()->insert((object)[
                'owner_id'        => $order->member_id,
                'owner_type'      => WalletOwnerType::MEMBER,
                'page_id'         => $order->page_id,
                'point'           => $order->total,
                'source'          => WalletSource::GET_REFUND,
                'morph_member_id' => $order->member_id,
                'morph_type'      => WalletTransactionType::ORDER,
                'morph_id'        => $order->id,
            ]);
        }
    }
}
