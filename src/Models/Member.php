<?php

namespace MystNov\Core\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class Member extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $connection = 'mysql_main';

    protected $table = 'members';

    protected $guard = 'master';

    protected $perPage = 50;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * ------------------------------------------
     * Scopes
     * ------------------------------------------
     */

    public function scopeAsc($query)
    {
        return $query->orderBy('created_at', 'asc')->orderBy('id', 'asc');
    }

    public function scopeDesc($query)
    {
        return $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
    }

    public function scopeOfMasterPage($query)
    {
        return $query->where('page_id', Auth::user()->hasMasterPage->id ?? null);
    }

    public function scopeActive()
    {
        return $this->whereNull('disabled_at');
    }

    public function scopeDisable()
    {
        return $this->whereNotNull('disabled_at');
    }

    /**
     * ------------------------------------------
     * Attributes
     * ------------------------------------------
     */

    /**
     * Return name field from first_name & last_name
     */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getGenderTextAttribute()
    {
        return ucfirst($this->gender);
    }

    public function getIsActiveAttribute()
    {
        return is_null($this->disabled_at);
    }

    public function getIsEmailVerifiedAttribute()
    {
        return ! is_null($this->email_verified_at);
    }

    public function getIsRemovedAttribute()
    {
        return ! is_null($this->deleted_at);
    }

    /**
     * ------------------------------------------
     * Relationships
     * ------------------------------------------
     */

    /**
     * This member belong to a Master Page
     *
     * @return void
     */
    public function masterPage()
    {
        return $this->hasOne(MasterPage::class, 'id', 'page_id')->withTrashed();
    }

    /**
     * This member owner a Master Page
     *
     * @return boolean
     */
    public function hasMasterPage()
    {
        return $this->hasOne(MasterPage::class, 'member_id', 'id')->withTrashed();
    }

    public function shareholding()
    {
        return $this->hasOne(ShareHolding::class, 'member_id', 'id')->withTrashed();
    }

    public function shareholdingTransaction($year, $month)
    {
        return $this->hasOne(ShareHoldingTransaction::class, 'member_id', 'id')->where('year', $year)->where('month', $month)->first();
    }

    public function shareholdingTransactions()
    {
        return $this->hasMany(ShareHoldingTransaction::class, 'member_id', 'id');
    }

    public function latestProfitSharingPeriod()
    {
        return $this->hasOne(ShareHoldingTransaction::class, 'member_id', 'id')->desc();
    }

    // Quan hệ: những người được mình giới thiệu (F1)
    public function referrals()
    {
        return $this->hasMany(Network::class, 'member_id');
    }

    // Quan hệ: người giới thiệu mình
    public function referredBy()
    {
        return $this->hasOne(Network::class, 'relate_member_id');
    }

    /**
     * ------------------------------------------
     * Funcs
     * ------------------------------------------
     */

    public function filter()
    {
        $query = $this;
        $request = request();

        if ($request->filled('search')) {
            $query = $query->where(function ($cond) use ($request) {
                return $cond->where('first_name', 'LIKE', _search_text($request->search))
                    ->orWhere('last_name', 'LIKE', _search_text($request->search))
                    ->orWhere('email', 'LIKE', _search_text($request->search))
                    ->orWhere('phone', 'LIKE', _search_text($request->search));
            });
        }

        if ($request->filled('name')) {
            $query = $query->where(function ($cond) use ($request) {
                return $cond->where('first_name', 'LIKE', _search_text($request->name))->orWhere('last_name', 'LIKE', _search_text($request->name));
            });
        }

        if ($request->filled('email')) {
            $query = $query->where('email', 'LIKE', _search_text($request->email));
        }

        if ($request->filled('phone')) {
            $query = $query->where('phone', 'LIKE', _search_text($request->phone));
        }

        if ($request->filled('status') && count($request->status) === 1) {
            if (in_array('active', $request->status)) {
                $query = $query->active();
            }

            if (in_array('disable', $request->status)) {
                $query = $query->disable();
            }
        }

        if ($request->filled('master_page')) {
            $query = $query->where('page_id', $request->master_page);
        }

        return $query;
    }

    public static function updateBalance(float $amount, int $memberId = null)
    {
        $member = static::query()->find($memberId ?? Auth::user()->id);
        $member->point += $amount;

        return $member->save();
    }
}
