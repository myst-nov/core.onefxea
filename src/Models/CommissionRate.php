<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionRate extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main';

    /**
     * Tên bảng liên kết với Model.
     * @var string
     */
    protected $table = 'commission_rates';

    /**
     * Các thuộc tính có thể gán hàng loạt (Mass Assignable).
     * @var array<int, string>
     */
    protected $fillable = [
        'referrer_id',
        'level',
        'rate_percentage',
        'page_id'
    ];

    /**
     * Các thuộc tính nên được chuyển đổi sang các kiểu dữ liệu cụ thể.
     * @var array<string, string>
     */
    protected $casts = [
        'rate_percentage' => 'decimal:2', // Đảm bảo tỷ lệ là số thập phân với 2 chữ số
        'level' => 'integer',
        'referrer_id' => 'integer',
        'page_id' => 'integer',
    ];

    // --------------------------------------------------------------------------
    // RELATIONSHIPS
    // --------------------------------------------------------------------------

    /**
     * Định nghĩa quan hệ BelongsTo với người giới thiệu (referrer).
     * Mối quan hệ này sẽ là NULL cho các cài đặt Mặc Định Toàn Hệ Thống.
     *
     * @return BelongsTo
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'referrer_id');
    }

    // --------------------------------------------------------------------------
    // ATTRIBUTES
    // --------------------------------------------------------------------------


    public function getNameAttribute()
    {
        return 'F' . $this->level;
    }
}
