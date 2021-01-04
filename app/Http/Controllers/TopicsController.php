<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\TopicRequest;
use App\Models\Topic;
use App\Models\Category;
use App\Models\User;
use App\Models\Link;
use Auth;
use App\Handlers\ImageUploadHandler;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    public function index(Request $request, Topic $topic, User $user, Link $link)
    {
        $topics = $topic->withOrder($request->order)
                        ->with('user', 'category') //預加載訪止 N+1 問題
                        ->paginate(20);

        $active_users = $user->getActiveUsers();
        $links = $link->getAllCached();

        return view('topics.index', compact('topics', 'active_users', 'links'));
    }

    public function show(Request $request, Topic $topic)
    {
        //URL矯正
        if( ! empty($topic->slug) && $topic->slug != $request->slug){
            return redirect($topic->link(), 301);
        }

        return view('topics.show', compact('topic'));
    }

    //新建話題:view
    public function create(Topic $topic)
    {
        $categories = Category::all();
        return view('topics.create_and_edit', compact('topic', 'categories'));
    }

    //新建話題:儲存資料
    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
        $topic->user_id = Auth::id();
        $topic->save();

        return redirect()->to($topic->link())->with('success', '建立話題成功！');
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

        return redirect()->to($topic->link())->with('success', '編輯成功！');
    }

    //文章刪除
    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);
        $topic->delete();

        return redirect()->route('topics.index')->with('success', '刪除成功！');
    }

    //新建話題:上傳圖片
    public function uploadImage(Request $request, ImageUploadHandler $uploader)
    {
        //初始化返回資料，默認是失敗的
        $data = [
            'success' => false,
            'msg' => '上傳失敗！',
            'file_path' => '',
        ];

        //判斷是否有上傳檔案，並賦值給 $file
        if($file = $request->upload_file){
            //保存圖片到主機(本地端)
            $result = $uploader->save($request->upload_file, 'topics', \Auth::id(), 1024);

            //圖片保存成功
            if($result){
                $data['file_path'] = $result['path'];
                $data['msg'] = '上傳成功！';
                $data['success'] = true;
            }
        }

        return $data;
    }
}
