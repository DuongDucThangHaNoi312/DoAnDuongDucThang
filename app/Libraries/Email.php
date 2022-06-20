<?php

namespace App\Library;

class Email
{
    public static function send($templatePath, $data = [], $receiverAddress, $receiverName, $title)
    {
        try {
            \Mail::send($templatePath, $data, function($message) use($receiverAddress, $receiverName, $title) {
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                $message->to($receiverAddress, $receiverName);
                $message->subject($title);
                //\Log::info(__FUNCTION__ . ': ' . env('MAIL_ADDRESS', 'noreply@ksshop.vn') . ' - ' . env('MAIL_NAME', 'KSSHOP.VN'));
            });
        } catch (Exception $e) {
            \Log::info('#Email: ' . $title . '<br/>Description: ' . $e->getMessage());
        }
    }

    public static function sendRaw($receiverAddress, $title, $content, $receiverName = 'Khách hàng')
    {
        try {
            \Mail::send([], [], function($message) use($content, $receiverAddress, $receiverName, $title) {
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                $message->to($receiverAddress, $receiverName);
                // $message->replyTo(env('MAIL_REPLY_ADDRESS'), env('MAIL_REPLY_NAME'));
                $message->subject($title);
                $message->setBody($content, 'text/html');
            });
        } catch (Exception $e) {
            \Log::info('#Email: ' . $title . '<br/>Description: ' . $e->getMessage());
        }
    }
}
