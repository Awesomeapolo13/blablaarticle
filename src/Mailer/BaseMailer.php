<?php

namespace App\Mailer;

use Closure;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class BaseMailer
{
    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @var ContainerBagInterface
     */
    protected $params;

    /**
     * @param MailerInterface $mailer
     * @param ContainerBagInterface $params
     */
    public function __construct(MailerInterface $mailer, ContainerBagInterface $params)
    {
        $this->mailer = $mailer;
        $this->params = $params;
    }

    /**
     * @param string $template - путь до шаблона
     * @param string $subject - заголовок письма
     * @param UserInterface $user - пользователь, которому нужно отправить письмо
     * @param Closure|null $callback - дополнительные параметры для расширения объекта TemplatedEmail
     * @throws TransportExceptionInterface - исключение при неудачной отправке письма
     */
    protected function send(string $template, string $subject, UserInterface $user, Closure $callback = null): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->params->get('sender.email'), $this->params->get('sender.name'))) // отправитель
            ->to(new Address($user->getEmail(), $user->getFirstName())) // получатель
            ->subject($subject) // заголовок
            ->htmlTemplate($template) // текст или путь до шаблона
        ;

        // чтобы расширить имеющийся функционал
        if ($callback) {
            $callback($email);
        }

        $this->mailer->send($email);
    }
}
