<?php

namespace App\Model\Services\Emails;

use App\Model\Email;
use App\Model\Orm;
use Contributte\Monolog\LoggerManager;
use Nette\Mail\Message;
use Latte\Engine;
use Nette\Mail\SendmailMailer;
use Nextras\Dbal\Utils\DateTimeImmutable;
use Psr\Log\LoggerInterface;

class EmailSenderService
{
    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function sendEmailsInQueue(): void
    {
        $emails = $this->orm->emails->findBy([
            "sentSuccess" => null,
            "sentAt" => null
        ]);

        foreach ($emails as $email) {
            $this->sendEmail($email);
        }
    }

    public function sendEmail(Email $email): void
    {
        $latte = new Engine();

        $params = [];
        $html = $latte->renderToString(__DIR__ . "/templates/" . $email->emailTemplate->file, $params);

        $mail = new Message();

        $mail
            ->setFrom($email->senderEmail, $email->senderName)
            ->addTo($email->receiverEmail)
            ->setSubject($email->subject)
            ->setHtmlBody($html, WWW_DIR);

        $mailer = new SendmailMailer();

        try {
            $mailer->send($mail);
            $email->sentAt = new DateTimeImmutable();
            $email->sentSuccess = true;
            $this->orm->emails->persistAndFlush($email);
        } catch (\Exception $exception) {
            $email->error = $exception->getMessage();
            $email->sentAt = new DateTimeImmutable();
            $email->sentSuccess = false;
            $this->orm->emails->persistAndFlush($email);
        }
    }
}
