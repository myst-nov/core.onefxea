<?php

namespace MystNov\Core\Http\Traits;

use MystNov\Core\Enums\OptionName;
use MystNov\Core\Models\MasterPage;
use MystNov\Core\Models\Member;
use MystNov\Core\Models\Option;

trait NetworkTrait
{
    protected $maxNetworkLevel = 0;

    protected $pageId = null;

    /**
     * Get members info in My Network
     */
    public function getNetworkMembers($memberId)
    {
        $member = Member::withTrashed()->find($memberId);

        $this->pageId = $member->page_id;
        $networkMembers = $this->mNetwork->where('member_id', $memberId)->get();

        $this->maxNetworkLevel = Option::where('name', OptionName::MAX_COMMISSION_LEVEL)->where('page_id', $this->pageId)->first()->value ?? config('define.max_network_level');

        return $this->loopNetworkMember($networkMembers);
    }

    /**
     * Loop for get Members info from level 1 to max
     */
    private function loopNetworkMember($members, $level = 1)
    {
        if (! empty($members)) {
            foreach ($members as $key => $networkMember) {
                // Nếu số level vượt quá max level thì ngưng lặp related members của member này
                // Lặp danh sách related members của member kế tiếp
                if ($level > $this->maxNetworkLevel) {
                    unset($members[$key]);
                    continue;
                }

                $members[$key] = $this->mMember->withTrashed()->find($networkMember->relate_member_id);

                $members[$key]['members'] = $this->mNetwork->where('member_id', $networkMember->relate_member_id)->get();

                $this->loopNetworkMember($members[$key]['members'], $level + 1);
            }
        }

        return $members;
    }

    /**
     * Get the my presenter ID and their parent IDs
     */
    public function getPresenterNetwork($memberId, $presenters = [], $level = 1)
    {
        $member = Member::find($memberId);

        if (! $member) {
            return $presenters;
        }

        $this->maxNetworkLevel = Option::where('name', OptionName::MAX_COMMISSION_LEVEL)->where('page_id', $member->page_id)->first()->value ?? config('define.max_network_level');

        if ($level > $this->maxNetworkLevel) {
            return $presenters;
        }

        $relationshipNetwork = $this->mNetwork->where('relate_member_id', $memberId)->first();

        if ($relationshipNetwork) {
            $presenters[] = $relationshipNetwork->member_id;
            return $this->getPresenterNetwork(end($presenters), $presenters, $level + 1);
        }

        return $presenters;
    }

    public function getNetworkPresentersOfOrder($order)
    {
        // Get Network Presenter commission list
        $networkPresenters = [];

        $presenterIds = $this->getPresenterNetwork($order->member_id);
        $maxNetworkLevel = Option::where('name', OptionName::MAX_COMMISSION_LEVEL)->where('page_id', $order->page_id)->first()->value ?? config('define.max_network_level');
        if (count($presenterIds) > 0) {
            foreach ($presenterIds as $key => $presenterId) {
                if ($key >= $maxNetworkLevel) {
                    break;
                }

                $memberInfo = Member::find($presenterId);
                if (! $memberInfo) {
                    break;
                }

                $commissionPercent = Option::where('name', 'commission_percent_f' . $key + 1)->where('page_id', $order->page_id)->first()->value ?? null;
                $commissionPoints = $order->total * ((float)$commissionPercent / 100);

                $networkPresenters[] = (object)[
                    'member_info'        => $memberInfo,
                    'commission_percent' => $commissionPercent,
                    'commission_earned'  => $commissionPoints,
                    'level'              => $key + 1
                ];
            }
        }

        return $networkPresenters;
    }

    public function distributeRevenue($total, $pageId)
    {
        $masterPage = MasterPage::find($pageId);

        // Chiết khấu mặc định do System cài đặt chung cho tất cả Master Page (default: 45%)
        $defaultDiscountRatio = Option::where('name', OptionName::DISCOUNT_RATIO->value)->where('page_id', null)->first()->value ?? 0;

        // Chiết khấu được cài đặt riêng cho Master Page (default: 45%)
        $masterDiscountRatio = $masterPage->discount_ratio;

        // Chiết khấu được Master Page cài đặt cho Network của họ (default: 30% trong tổng 45% của Master Page)
        $networkDiscountRatio = Option::where('name', OptionName::DISCOUNT_RATIO->value)->where('page_id', $pageId)->first()->value ?? 0;

        // Chiết khấu của Master Page ưu tiên lấy theo cài đặt riêng của Master Page
        // Nếu không có cài đặt riêng thì áp dụng cài đặt chung
        $masterDiscountRatio = $masterDiscountRatio ?? $defaultDiscountRatio;

        $revenue = [
            // A. Mức point (%) mà System sẽ nhận được
            'system_discount_ratio'  => 100 - $masterDiscountRatio,

            // B. Mức point (%) mà Master Page sẽ nhận được
            'master_discount_ratio'  => $masterDiscountRatio,

            // C. Mức point (%) mà Master Page dành ra từ B để chia hoa hồng cho Network của họ
            'network_discount_ratio' => $networkDiscountRatio,
        ];

        // 55% doanh thu sẽ về System Wallet
        $revenue['for_system'] = $total * (100 - (float)$masterDiscountRatio) / 100;

        // 45% doanh thu sẽ về Master Wallet
        $revenue['for_master'] = $total * (float)$masterDiscountRatio / 100;

        // Từ 45% doanh thu nhận về, trích ra một phần theo cài đặt để chia hoa hồng cho member
        $revenue['for_network'] = $revenue['for_master'] * (float)$networkDiscountRatio / 100;

        return $revenue;
    }
}
