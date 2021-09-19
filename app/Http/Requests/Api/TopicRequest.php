<?php

namespace App\Http\Requests\Api;

class TopicRequest extends FormRequest
{
    public function rules()
    {

        // 判断请求类型
        switch($this->method()) {
            case 'POST': // 发布话题
                return [
                    'title' => 'required|string',
                    'body' => 'required|string',
                    'category_id' => 'required|exists:categories,id',
                ];
                break;
            case 'PATCH': // 更新数据
                return [
                    'title' => 'string',
                    'body' => 'string',
                    'category_id' => 'exists:categories,id',
                ];
                break;
        }
    }

    public function attributes()
    {
        return [
            'title' => '标题',
            'body' => '话题内容',
            'category_id' => '分类',
        ];
    }
}
