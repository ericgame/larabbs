<?php

namespace App\Models\Traits;

use App\Models\Topic;
use App\Models\Reply;
use Carbon\Carbon;
use Cache;
use DB;
use Arr;

trait ActiveUserHelper
{
    //存放臨時用戶資料
    protected $users = [];

    //配置信息
    protected $topic_weight = 4; //話題權重
    protected $reply_weight = 1; //回覆權重
    protected $pass_days = 7; //多少天內發表過內容
    protected $user_number = 6; //取出來多少用戶

    //緩存相關配置
    protected $cache_key = 'larabbs_active_users';
    protected $cache_expire_in_seconds = 65 * 60;

    public function getActiveUsers()
    {
        /*
            嘗試從緩存中取出 cache_key 對應的數據。如果能取到，便直接返回數據。
            否則執行匿名函數中的代碼來取出活躍用戶數據，返回的同時做了緩存。
        */
        return Cache::remember($this->cache_key, $this->cache_expire_in_seconds, function(){
            return $this->calculateActiveUsers();
        });
    }

    public function calculateAndCacheActiveUsers()
    {
        //取得活躍用戶列表
        $active_users = $this->calculateActiveUsers();
        //並加以緩存
        $this->cacheActiveUsers($active_users);
    }

    public function calculateActiveUsers()
    {
        $this->calculateTopicScore();
        $this->calculateReplyScore();

        //陣列依照得分排序
        $users = Arr::sort($this->users, function($user){
            return $user['score'];
        });

        //我們需要的是倒序，高分靠前，第2個參數為保持陣列的 key 不變
        $users = array_reverse($users, true);

        //只取出我們要的數量
        $users = array_slice($users, 0, $this->user_number, true);

        //建立一個空集合
        $active_users = collect();

        foreach($users as $user_id => $user){
            //找尋一下，是否可以找到用戶
            $user = $this->find($user_id);

            //如果資料庫有該用戶
            if($user){
                //把此用戶實體放入集合的最後面
                $active_users->push($user);
            }
        }

        return $active_users;
    }

    public function calculateTopicScore()
    {
        /*
            從話題資料表裡取出限定時間範圍($pass_days)內有發表過話題的用戶，
            並且同時取出用戶此段時間內發布話題的數量
        */
        $topic_users = Topic::query()->select(DB::raw('user_id, count(*) as topic_count'))
                                    ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
                                    ->groupBy('user_id')
                                    ->get();
        
        //根據話題數量計算得分
        foreach($topic_users as $value){
            $this->users[$value->user_id]['score'] = $value->topic_count * $this->topic_weight;
        }
    }

    public function calculateReplyScore()
    {
        /*
            從回覆資料表裡取出限定時間範圍($pass_days)內有發表過回覆的用戶，
            並且同時取出用戶此段時間內發布回覆的數量
        */
        $reply_users = Reply::query()->select(DB::raw('user_id, count(*) as reply_count'))
                                    ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
                                    ->groupBy('user_id')
                                    ->get();

        //根據回覆數量計算得分
        foreach($reply_users as $value){
            $reply_score = $value->reply_count * $this->reply_weight;

            if(isset($this->users[$value->user_id])){
                $this->users[$value->user_id]['score'] += $reply_score;
            }else{
                $this->users[$value->user_id]['score'] = $reply_score;
            }
        }
    }

    public function cacheActiveUsers($active_users)
    {
        //將資料放入緩存中
        Cache::put($this->cache_key, $active_users, $this->cache_expire_in_seconds);
    }
}
