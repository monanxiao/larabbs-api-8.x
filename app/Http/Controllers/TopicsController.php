<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use Auth;
use App\Handlers\ImageUploadHandler;
use App\Models\User;
use App\Models\Link;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);// 未登录用户只可以查看首页和详情页
    }

	public function index(Request $request, Topic $topic, User $user, Link $link)
	{
        $topics = $topic->withOrder($request->order) // 调用模型中排序方法
                        ->with('user', 'category') // 预加载防止 N+1 问题
                        ->paginate(30);// 分页 30条

        // 取出活跃用户
        $active_users = $user->getActiveUsers();

        // 取出推荐资源
        $links = $link->getAllCached();

		return view('topics.index', compact('topics', 'active_users', 'links'));
	}

    public function show(Request $request, Topic $topic)
    {
        // URL 矫正
        if ( ! empty($topic->slug) && $topic->slug != $request->slug) {

            // 假如 URL 存在slug 但是url地址不正确的时候，自动校正 跳转到带 slug url 上
            return redirect($topic->link(), 301);
        }

        return view('topics.show', compact('topic'));
    }

    // 创建话题
	public function create(Topic $topic)
	{
        $categories = Category::all();// 取出所有分类

		return view('topics.create_and_edit', compact('topic','categories'));
	}

    // 接收话题数据
	public function store(TopicRequest $request, Topic $topic)
	{

        $topic->fill($request->all()); // 创建一个实例赋值
        $topic->user_id = Auth::id(); // 赋值当前登录用户
        $topic->save(); // 保存数据

		// return redirect()->route('topics.show', $topic->id)->with('success', '帖子创建成功！');
		return redirect()->to($topic->link())->with('success', '帖子创建成功！');
	}

    // 编辑话题
	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);

        // 取出所有分类
        $categories = Category::all();

		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

    // 接收更新数据
	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		// return redirect()->route('topics.show', $topic->id)->with('success', '更新成功！');
		return redirect()->to($topic->link())->with('success', '更新成功！');
	}

    // 帖子删除功能
	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('success', '话题删除成功!');
	}

    // 图片上传
    public function uploadImage(Request $request, ImageUploadHandler $uploader)
    {
        // 初始化返回数据，默认是失败的
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];

        //判断是否有文件上传，并赋值给 $file
        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = $uploader->save($file, 'topics', \Auth::id(), 1024);

            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
        }

        return $data;

    }
}
