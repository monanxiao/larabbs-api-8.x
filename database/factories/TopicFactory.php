<?php

namespace Database\Factories;

use App\Models\Topic;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class TopicFactory extends Factory
{
    protected $model = Topic::class;

    public function definition()
    {
        $sentence = $this->faker->sentence();// 话题标题

        // 所有用户 ID 数组，如：[1,2,3,4]
        $user_ids = User::all()->pluck('id')->toArray();

        // 所有分类 ID 数组，如：[1,2,3,4]
        $category_ids = Category::all()->pluck('id')->toArray();

        // 随机取一个月以内的时间
        $updated_at = $this->faker->dateTimeThisMonth();

        // 为创建时间传参，意为最大不超过 $updated_at，因为创建时间需永远比更改时间要早
        $created_at = $this->faker->dateTimeThisMonth($updated_at);

        return [
            'title' => $sentence,
            'body' => $this->faker->text(), // 小段文本
            'excerpt' => $sentence, // 摘录
            'user_id' => $this->faker->randomElement($user_ids), // 用户ID
            'category_id' => $this->faker->randomElement($category_ids), //　分类ID
            'created_at' => $created_at,
            'updated_at' => $updated_at
        ];
    }
}
