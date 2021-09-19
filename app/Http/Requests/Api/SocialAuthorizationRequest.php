<?php

namespace App\Http\Requests\Api;

class SocialAuthorizationRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'code' => 'required_without:access_token|string',
            'access_token' => 'required_without:code|string',
        ];

        // 微信登录 且 code 不为空的时候，增加 openid 验证
        if ($this->social_type == 'wechat' && !$this->code) {
            $rules['openid']  = 'required|string';
        }

        return $rules;
    }
}
