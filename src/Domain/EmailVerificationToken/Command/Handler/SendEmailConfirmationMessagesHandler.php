<?php

declare(strict_types=1);

namespace App\Domain\EmailVerificationToken\Command\Handler;

use App\Domain\EmailVerificationToken\Command\SendEmailConfirmationMessagesCommand;
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

        $email = (new Email())
            ->from($this->robotEmail)
            ->to($email)
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html(sprintf('<p>See Twig integration for better HTML integration!</p> Token: <a href="%s">click to please</a>', $url));

        $this->mailer->send($email);
    }
}
