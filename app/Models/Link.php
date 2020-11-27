<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Cache;

class Link extends Model
{
    protected $fillable = ['title', 'link'];

    public $cache_key = 'larabbs_links';
    protected $cache_expire_in_seconds = 1440 * 60;

    public function getAllCached()
    {
        /*
            嘗試從緩存中取出 cache_key 對應的資料。如果能取到，就直接返回資料。
            否則執行匿名函數中的代碼來取出 links 表中所有的資料，返回的同時做了緩存。
        */
        return Cache::remember($this->cache_key, $this->cache_expire_in_seconds, function(){
            return $this->all();
        });
    }
}
