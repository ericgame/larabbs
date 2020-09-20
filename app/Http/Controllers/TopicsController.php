<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Topic;


class TopicsController extends Controller
{
    public function index()
    {
        return view('topics.index');
    }

    public function show(Request $request, Topic $topic)
    {
        //URL矯正
        if( ! empty($topic->slug) && $topic->slug != $request->slug){
            return redirect($topic->link(), 301);
        }

        return view('topics.show', compact('topic'));
    }
}
