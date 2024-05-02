<?php

namespace App\Service;

use App\Message\SendEmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class MessageHandler
{
    private $mailer;

    public function __construct(
        MailerInterface $mailer
        )
    {
        $this->mailer = $mailer;
    }

    public function __invoke(SendEmailMessage $message)
    {
        $email = (new Email())
            ->from('your@example.com')
            ->to($message->getRecipient())
            ->subject($message->getSubject())
            ->text($message->getBody());

        $this->mailer->send($email);
    }
}