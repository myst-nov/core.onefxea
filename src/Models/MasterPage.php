<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;
use MystNov\Core\Enums\NetworkSurplusPointsRecipient;

class MasterPage extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql_main';

    protected $table = 'master_pages';

    /**
     * ----------------------------------------------
     * Scopes
     * ----------------------------------------------
     */

    public function scopeIsAvailable($query)
    {
        return $query->whereNull('disabled_at')->whereNull('locked_at');
    }

    public function scopeIsActive($query)
    {
        return $query->whereNull('disabled_at');
    }

    public function scopeIsDisabled($query)
    {
        return $query->whereNotNull('disabled_at');
    }

    public function scopeIsUnlocked($query)
    {
        return $query->whereNull('locked_at');
    }

    public function scopeIsLocked($query)
    {
        return $query->whereNotNull('locked_at');
    }

    public function scopeIsRemoved($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    /**
     * ----------------------------------------------
     * Attributes
     * ----------------------------------------------
     */

    public function getIsActiveAttribute()
    {
        return is_null($this->disabled_at) ? true : false;
    }

    public function getPageUrlAttribute()
    {
        return Request::getScheme() . '://' . $this->page_id . '.' . _url_non_protocol(config('app.main_url') ?? config('app.url'));
    }

    public function getIsRemovedAttribute()
    {
        return is_null($this->deleted_at) ? false : true;
    }

    public function getIsLockedAttribute()
    {
        return is_null($this->locked_at) ? false : true;
    }

    public function getnNetworkSurplusPointsRecipientLabelAttribute()
    {
        return NetworkSurplusPointsRecipient::options()[$this->network_surplus_points_recipient];
    }

    /**
     * ----------------------------------------------
     * Relationships
     * ----------------------------------------------
     */

    public function owner()
    {
        return $this->hasOne(Member::class, 'id', 'member_id')->withTrashed();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'master_page_product', 'page_id', 'product_id');
    }

    /**
     * Get the member that owns the Master Page.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'page_id');
    }
}
