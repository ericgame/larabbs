<?php

namespace App\Observers;

use Illuminate\Support\Str;
use App\Models\Topic;
use Stichoza\GoogleTranslate\GoogleTranslate;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function saving(Topic $topic)
    {
        //XSS過濾
        $topic->body = clean($topic->body, 'user_topic_body');

        //產生話題摘錄
        $topic->excerpt = make_excerpt($topic->body);

        //產生網址slug (例如: 把 "測試翻譯slug" 翻譯成 test-translation-slug)
        if(!$topic->slug){
            //GoogleTranslate::trans($topic->title:要翻譯的資料, 'en':將資料翻譯成英語, null:自動偵測要翻譯的資料的語言)
            //Str::slug 將英文句子的空格替換為"-"(例如: 把 "test translation slug" 轉成 test-translation-slug)
            $topic->slug = Str::slug(GoogleTranslate::trans($topic->title, 'en', null));
        }
    }
}