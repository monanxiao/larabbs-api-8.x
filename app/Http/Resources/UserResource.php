<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $showSensitiveFields = false;

    // 将资源转换为数组
    public function toArray($request)
    {
        // 假如是false
        if (!$this->showSensitiveFields) {
            // 隐藏字段
            $this->resource->makeHidden(['phone', 'email']);
        }

        $data = parent::toArray($request);

        $data['bound_phone'] = $this->resource->phone ? true : false;
        $data['bound_wechat'] = ($this->resource->weixin_unionid || $this->resource->weixin_openid) ? true : false;

        return $data;
    }

    // 假如调用了此方法，说明已经登陆，无需隐藏字段
    public function showSensitiveFields()
    {
        $this->showSensitiveFields = true;

        return $this;
    }

}
