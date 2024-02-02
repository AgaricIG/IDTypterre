<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Mailer
{
  private $mailer;
  private $sender;

  public function __construct(MailerInterface $mailer, ParameterBagInterface $params)
  {
    $this->mailer = $mailer;
    $this->sender = $params->has('mailer.email.sender') ? $params->get('mailer.email.sender') : 'no-reply@nebulaweb.fr';
    $this->contact = $params->has('mailer.email.contact') ? $params->get('mailer.email.contact') : 'no-reply@nebulaweb.fr';
  }

  public function sendTestEmail($address)
  {
    $email = (new TemplatedEmail())
            ->from(new Address($this->sender, 'Email Test Command'))
            ->to($address)
            ->subject('This email is a test')
            ->htmlTemplate('email/test.html.twig');
        ;

    $this->mailer->send($email);
  }

  public function sendPasswordResetEmail($user, $resetToken, $lifetime)
  {
    $email = (new TemplatedEmail())
                ->from(new Address($this->sender, 'Email Recovery Bot'))
                ->to($user->getEmail())
                ->subject('Your password reset request')
                ->htmlTemplate('email/password_reset.html.twig')
                ->context([
                    'user' => $user,
                    'resetToken' => $resetToken,
                    'tokenLifetime' => $lifetime,
                ])
            ;

    $this->mailer->send($email);
  }

  public function sendContactMessage($email, $content)
  {
    $email = (new TemplatedEmail())
                ->from(new Address($email, 'Someone on the contact form'))
                ->to($this->contact)
                ->subject('Message from contact form')
                ->htmlTemplate('email/contact.html.twig')
                ->context([
                  'askerEmail' => $email,
                  'content' => $content
                ])
            ;

    $this->mailer->send($email);
  }

}