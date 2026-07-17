<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class NotificationMail extends Mailable
{

    use Queueable, SerializesModels;


    public $notification;


    public function __construct($notification)
    {
        $this->notification = $notification;
    }



    public function build()
    {

        return $this
            ->subject('New Notification Received')
            ->view('emails.notification');

    }

}