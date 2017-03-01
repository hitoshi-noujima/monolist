<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Item;

class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Form::text('keyword')から送信される検索ワードを取得
        $keyword = request()->keyword;
        
        // カラの配列
        $items = [];
        
        if ($keyword) {
            // 楽天API
            // インスタンス
            $client = new \RakutenRws_Client();
            
            // アプリID (デベロッパーID) をセット
            // .envの'RAKUTEN_APPLICATION_ID'の値を設定
            $client->setApplicationId(env('RAKUTEN_APPLICATION_ID'));
            
            // 楽天商品検索API (IchibaItem/Search/)から検索
            // $client->execute 検索オプション
            // 検索ワードを設定し、画像があるもののみに絞り込み、20件検索
            $rws_response = $client->execute('IchibaItemSearch', [
                'keyword' => $keyword,
                'imageFlag' => 1,
                'hits' => 20,
            ]);

            // 扱い易いように Item としてインスタンスを作成する（保存(save)はしない）
            // $rws_response->getData() → 結果(配列)を取得
            // 'Items'をforeachする一つ一つは$rws_itemに格納される
            foreach ($rws_response->getData()['Items'] as $rws_item) {
                $item = new Item();
                $item->code = $rws_item['Item']['itemCode'];
                $item->name = $rws_item['Item']['itemName'];
                $item->url = $rws_item['Item']['itemUrl'];
                // str_replace() → 第三引数から第一引数を見つけ出して、第二引数に置換する関数
                // この場合は第ニ引数に '' とカラ文字を入れているので、見つけたら削除
                $item->image_url = str_replace('?_ex=128x128', '', $rws_item['Item']['mediumImageUrls'][0]['imageUrl']);
                // $items配列に順次追加
                $items[] = $item;
            }
        }

        return view('items.create', [
            'keyword' => $keyword,
            'items' => $items,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Item::find($id);
        $want_users = $item->want_users;
        $have_users = $item->have_users;
        
        return view('items.show', [
          'item' => $item,
          'want_users' => $want_users,
          'have_users' => $have_users,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
