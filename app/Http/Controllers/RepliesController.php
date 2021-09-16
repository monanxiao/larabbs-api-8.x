<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReplyRequest;
use Auth;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    // 接收回复内容数据
	public function store(ReplyRequest $request, Reply $reply)
	{

        // XSS 攻击处理
        $content = clean($request->content, 'user_topic_body');

        // 检测内容 XSS 攻击代码 和 空数据
        if ( empty($content) ) {

            // 跳转到当前话题页面
            return redirect()->back()->with('danger', '回复失败~');
        }


        $reply->content = $request->content;// 回复内容
        $reply->user_id = Auth::id();// 回复用户
        $reply->topic_id = $request->topic_id;// 回复话题
        $reply->save();// 保存内容

        // 跳转到当前话题页面
        return redirect()->to($reply->topic->link())->with('success', '回复成功~');

	}

    // 删除回复
	public function destroy(Reply $reply)
	{
		$this->authorize('destroy', $reply);
		$reply->delete();

		return redirect()->to($reply->topic->link())->with('sucees', '删除成功！');
	}
}
