<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnswerRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $messageId;
    public $question;

    public function __construct($messageId, $question)
    {
        $this->messageId = $messageId;
        $this->question = $question;
    }

    public function build()
    {
        $url = url('/create?message_id=' . $this->messageId . '&question=' . urlencode($this->question));

        return $this->subject('Answer Request')
                    ->view('emails.answerRequest', ['url' => $url]);
    }
}
