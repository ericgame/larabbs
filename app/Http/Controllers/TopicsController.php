<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\TopicRequest;
use App\Models\Topic;
use App\Models\Category;
use App\Models\User;
use Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

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

    //文章編輯
    public function edit(Topic $topic)
    {
        $this->authorize('update', $topic);
        $categories = Category::all();

        return view('topics.create_and_edit', compact('topic', 'categories'));
    }

    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);
        $topic->update($request->all());

        return redirect()->to($topic->link())->with('success', '更新成功！');
    }

    //文章刪除
    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);
        $topic->delete();

        return redirect()->route('topics.index')->with('success', '刪除成功！');
    }

}
