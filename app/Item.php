<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['code', 'name', 'url', 'image_url'];
    
    // アイテムをWant も Have も両方のしている User 一覧を取得
    public function users()
    {
        // Laravel では中間テーブルに対応する存在を Pivot と呼びます
        // type は通常の中間テーブルには無いカラムなので、 withPivot('type') として、 
        // type を考慮する必要があることを伝えています
        return $this->belongsToMany(User::class)->withPivot('type')->withTimestamps();
    }
    
    //  Want のみの User 一覧を取得
    public function want_users()
    {
        return $this->users()->where('type', 'want');
    }
    
    //  Have のみの User 一覧を取得
    public function have_users()
    {
        return $this->users()->where('type', 'have');
    }
}
