<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Item;

class RankingController extends Controller
{
    public function want()
    {
        $items = [];
        
        if (Item::exists()) {
            
            // \DB::table(‘item_user’)
            // Want の中間テーブルである item_user を指定しています。 SQL でいうと FROM item_user に相当します。
            
            // join(‘items’, ‘item_user.item_id’, ‘=’, ‘items.id’)
            // items テーブルを item_user テーブルと結合しています。
            
            // select(‘items.’, \DB::raw(‘COUNT() AS count’))
            // 取得したいテーブルのカラムを選択
            // COUNT(*) AS count で集計しています。
            // また、 AS を使って COUNT 計算によって一時的に出現するカラムに、 count という名前をつけています 
            
            $items = \DB::table('item_user')->join('items', 'item_user.item_id', '=', 'items.id')->select('items.*', \DB::raw('COUNT(*) as count'))->where('type', 'want')->groupBy('items.id')->orderBy('count', 'DESC')->take(10)->get();
        }
        
        return view('ranking.want', [
            'items' => $items,
            'type' => 'Wants'
        ]);
    }
    
    public function have()
    {
        $items = [];
        
        if (Item::exists()) {
            $items = \DB::table('item_user')->join('items', 'item_user.item_id', '=', 'items.id')->select('items.*', \DB::raw('COUNT(*) as count'))->where('type', 'have')->groupBy('items.id')->orderBy('count', 'DESC')->take(10)->get();
        }
        
        return view('ranking.have', [
            'items' => $items,
            'type' => 'Has'
        ]);
    }
}
