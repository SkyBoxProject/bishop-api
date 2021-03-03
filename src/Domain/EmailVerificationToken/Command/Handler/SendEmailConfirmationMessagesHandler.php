<?php

declare(strict_types=1);

namespace App\Domain\EmailVerificationToken\Command\Handler;

use App\Domain\EmailVerificationToken\Command\SendEmailConfirmationMessagesCommand;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SendEmailConfirmationMessagesHandler implements MessageHandlerInterface
{
    private MailerInterface $mailer;
    private UrlGeneratorInterface $urlGenerator;
    private string $robotEmail;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $urlGenerator, string $robotEmail)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->robotEmail = $robotEmail;
    }

    public function __invoke(SendEmailConfirmationMessagesCommand $command): void
    {
        $email = $command->getEmail();
        $token = $command->getToken();

        $url = $this->urlGenerator->generate('app_rest_v1_auth_emailverification', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
        $loginUrl = $this->urlGenerator->generate('app_rest_v1_auth_login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new TemplatedEmail())
            ->from($this->robotEmail)
            ->to($email)
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Подтверждение Email!')
            ->htmlTemplate('emails/confirmation_email.html.twig')
            ->context([
                'username' => $email,
                'url' => $url,
                'login' => $loginUrl,
            ]);

        $this->mailer->send($email);
    }
}
