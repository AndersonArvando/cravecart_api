<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

function sendMail($view_file_name, $data, $receiver_email, $subject, $cc_email_list = [], $attachments = [])
{
    $cc_email_list = $cc_email_list ? $cc_email_list : [];
    $receiver_email = $receiver_email ? $receiver_email : [];

    try {
        $mail = Mail::send($view_file_name, $data, function ($message) use ($subject, $cc_email_list, $receiver_email, $attachments) {
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $message->subject($subject);
            if ($receiver_email) {
                $message->to($receiver_email);
            }
            if (is_array($cc_email_list) && count($cc_email_list) > 0) {
                $message->cc($cc_email_list);
            }
            if (!empty($attachments)) {
                foreach ($attachments as $attachment) {
                    $file_name = pathinfo($attachment, PATHINFO_BASENAME);
                    $message->attach($attachment, [
                        'as' => $file_name
                    ]);
                }
            }
        });

        return true;
    } catch (\Exception $e) {
        Log::error(json_encode($e->getMessage()));
        Log::error(json_encode(compact('receiver_email', 'subject', 'data')));
        // echo $e->getMessage();
        return false;
    }
}

function generateVerificationCode()
{
    return random_int(100000, 999999);
}
