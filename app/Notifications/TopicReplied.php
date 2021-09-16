<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Reply;

class TopicReplied extends Notification implements ShouldQueue
{
    use Queueable;

    public $reply;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Reply $reply)
    {
        // 注入回复实体，方便 toDatabase 方法中的使用
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // 开启通知的频道
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    // 邮箱通知方法
    public function toMail($notifiable)
    {
        // 回复话题链接
        $url = $this->reply->topic->link(['#reply' . $this->reply->id]);

        // 发送通知
        return (new MailMessage)
                    ->line('你的话题有新回复！')
                    ->action('查看回复', $url);
    }

    // 数据库通知方法
    public function toDatabase($notifiable)
    {
        // 回复内容所属话题
        $topic = $this->reply->topic;
        // 回复话题内容的链接
        $link =  $topic->link(['#reply' . $this->reply->id]);

        // 存入数据库里的数据
        return [
            'reply_id' => $this->reply->id, // 回复ID
            'reply_content' => $this->reply->content, // 回复内容
            'user_id' => $this->reply->user->id, // 用户ID
            'user_name' => $this->reply->user->name, // 回复名称
            'user_avatar' => $this->reply->user->avatar, // 回复头像
            'topic_link' => $link, // 回复地址
            'topic_id' => $topic->id, // 话题ID
            'topic_title' => $topic->title, // 话题标题
        ];
    }



    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
