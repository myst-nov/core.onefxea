<?php

namespace MystNov\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Facades\Auth;

class Model extends BaseModel
{
    use HasFactory;

    protected $perPage = 50;

    public function scopeAsc($query, $column = 'id')
    {
        return $query->orderBy('created_at', 'asc')->orderBy($column, 'asc');
    }

    public function scopeDesc($query, $column = 'id')
    {
        return $query->orderBy('created_at', 'desc')->orderBy($column, 'desc');
    }

    public function scopeOfMasterPage($query)
    {
        if (is_admin()) {
            return $query;
        }

        // Is Master in Management Page
        if (is_master()) {
            return $query->where('page_id', Auth::guard('master')->user()->hasMasterPage->id);
        }

        // In Sale Page
        return $query->where('page_id', _master_page_id() ?? null);
    }

    public function scopeOfMember($query)
    {
        return $query->where('member_id', Auth::user()->id);
    }

    public static function findByParam(array $params = [])
    {
        $query = static::query();

        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }

    public static function findByName(string $name)
    {
        $query = static::findByParam(['name' => $name]);

        return $query;
    }

    public static function updateByParam(array $values, array $params = [])
    {
        $query = static::query();

        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        return $query->update($values);
    }

    public static function getByParam(array $params = [])
    {
        $query = static::query();

        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        return $query->get();
    }

    public static function checkExist(array $params = [])
    {
        $query = static::query();

        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        return $query->exists();
    }
}
