<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    
    
    // User が Want も Have も両方しているアイテム一覧を取得
    public function items()
    {
        return $this->belongsToMany(Item::class)->withPivot('type')->withTimestamps();
    }
    
    //  User が Want しているアイテム一覧を取得
    public function want_items()
    {
        return $this->items()->where('type', 'want');
    }
    
    
    
    // Want 中間テーブルにレコードを保存
    public function want($itemId)
    {
        // 既に Want しているかの確認
        $exist = $this->is_wanting($itemId);

        if ($exist) {
            // 既に Want していれば何もしない
            return false;
        } else {
            // 未 Want であれば Want する
            $this->items()->attach($itemId, ['type' => 'want']);
            return true;
        }
    }
    
    
    
    // Want 中間テーブルにレコードを削除
    public function dont_want($itemId)
    {
        // 既に Want しているかの確認
        $exist = $this->is_wanting($itemId);

        if ($exist) {
            // 既に Want していれば Want を外す
            // detachはtype で絞り込んで削除することができないのでSQL文
            \DB::delete("DELETE FROM item_user WHERE user_id = ? AND item_id = ? AND type = 'want'", [\Auth::user()->id, $itemId]);
        } else {
            // 未 Want であれば何もしない
            return false;
        }
    }
    

    // 既に Want しているかどうかを判定
    public function is_wanting($itemIdOrCode)
    {
        // $item.id と 出力パラメータの itemCode のどちらでも判定しなければいけない
        
        // is_numeric() → 整数かどうかで$item.idかitemCodeを判断
        if (is_numeric($itemIdOrCode)) {
            // 整数であれば$item.id
            // exists() → データがあればTrue、データが無ければFalseを返す
            $item_id_exists = $this->want_items()->where('item_id', $itemIdOrCode)->exists();
            return $item_id_exists;
        } else {
            // 整数でなければitemCode
            $item_code_exists = $this->want_items()->where('code', $itemIdOrCode)->exists();
            return $item_code_exists;
        }
    }
    
    
    //  User が Have しているアイテム一覧を取得
    public function have_items()
    {
        return $this->items()->where('type', 'have');
    }
    
    // Have 中間テーブルにレコードを保存
    public function have($itemId)
    {
        // 既に Have しているかの確認
        $exist = $this->is_having($itemId);

        if ($exist) {
            // 既に Have していれば何もしない
            return false;
        } else {
            // 未 Have であれば Have する
            $this->items()->attach($itemId, ['type' => 'have']);
            return true;
        }
    }
    
    // Have 中間テーブルにレコードを削除
    public function dont_have($itemId)
    {
        // 既に Have しているかの確認
        $exist = $this->is_having($itemId);

        if ($exist) {
            // 既に Have していれば Have を外す
            // detachはtype で絞り込んで削除することができないのでSQL文
            \DB::delete("DELETE FROM item_user WHERE user_id = ? AND item_id = ? AND type = 'have'", [\Auth::user()->id, $itemId]);
        } else {
            // 未 Have であれば何もしない
            return false;
        }
    }
    
    // 既に Have しているかどうかを判定
    public function is_having($itemIdOrCode)
    {
        // $item.id と 出力パラメータの itemCode のどちらでも判定しなければいけない
        
        // is_numeric() → 整数かどうかで$item.idかitemCodeを判断
        if (is_numeric($itemIdOrCode)) {
            // 整数であれば$item.id
            // exists() → データがあればTrue、データが無ければFalseを返す
            $item_id_exists = $this->have_items()->where('item_id', $itemIdOrCode)->exists();
            return $item_id_exists;
        } else {
            // 整数でなければitemCode
            $item_code_exists = $this->have_items()->where('code', $itemIdOrCode)->exists();
            return $item_code_exists;
        }
    }
}
