<?php

namespace App\Models\Traits;

//use Redis; //如果 "use Redis;" 有問題，就用 "use Illuminate\Support\Facades\Redis;"
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

trait LastActivedAtHelper
{
    //緩存相關
    protected $hash_prefix = 'larabbs_last_actived_at_';
    protected $field_prefix = 'user_';

    //記錄會員在網站活動最後的時間
    public function recordLastActivedAt()
    {
        //獲取今日Redis哈希表名稱，如：larabbs_last_actived_at_2020-09-07
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());

        //欄位名稱，如：user_1
        $field = $this->getHashField();

        //測試Redis:獲取哈希表裡的全部資料
        // dd(Redis::hGetAll($hash));

        //當前時間，如：2020-09-07 08:33:25
        $now = Carbon::now()->toDateTimeString();

        //資料寫入Redis，欄位已存在會被更新
        Redis::hSet($hash, $field, $now);
    }

    //設定排程將Redis資料寫入資料庫
    public function syncUserActivedAt()
    {
        //獲取昨日的哈希表名稱，如：larabbs_last_actived_at_2020-09-11
        $hash = $this->getHashFromDateString(Carbon::yesterday()->toDateString());

        //測試artisan命令 "php artisan larabbs:sync-user-actived-at" (將上一行的yesterday改為now)
        // $hash = $this->getHashFromDateString(Carbon::now()->toDateString());

        //從Redis中獲取所有哈希表裡的資料
        $dates = Redis::hGetAll($hash);

        //遍歷，並同步到資料庫中
        foreach($dates as $user_id => $actived_at){
            //會將user_1轉換為1
            $user_id = str_replace($this->field_prefix, '', $user_id);

            //只有當會員存在時才更新到資料庫中
            if($user = $this->find($user_id)){
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }

        //以資料庫為中心的存儲，既已同步，即可刪除
        Redis::del($hash);
    }

    //取出會員在網站活動的最後時間
    public function getLastActivedAtAttribute($value)
    {
        //獲取今日對應的哈希表名稱
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());

        //欄位名稱，如：user_1
        $field = $this->getHashField();

        //三元運算符，優先選擇Redis的資料，否則使用資料庫的資料
        $datetime = Redis::hGet($hash, $field) ? : $value;

        //如果存在，返回時間對應的Carbon實體
        if($datetime){
            return new Carbon($datetime);
        }else{
            //否則使用會員註冊時間
            return $this->created_at;
        }
    }

    //Redis哈希表的命名
    public function getHashFromDateString($date)
    {
        //Redis哈希表的命名，如：larabbs_last_actived_at_2020-09-07
        return $this->hash_prefix . $date;
    }

    //Redis哈希表的欄位名稱
    public function getHashField()
    {
        //欄位名稱，如：user_1
        return $this->field_prefix . $this->id;
    }
}