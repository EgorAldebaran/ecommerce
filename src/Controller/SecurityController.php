<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/room/mail', name: 'mail_room')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        /*
        $email = (new Email())
               ->from('hello@example.com')
               ->to('OccultDebugger@yandex.ru')
               ->subject('from Symfony')
               ->text('duck the system from Symfony')
               ->html('<p>duck the system from syfmony</p>');
        $mailer->send($email);
        */

        $email = (new TemplatedEmail())
               ->from('duck@example.com')
               ->to(new Address('OccultDebugger@yandex.ru'))
               ->subject('Thanks for signing up!')
               ->htmlTemplate('emails/signup.html.twig')
               ->context([
                   'expiration_date' => new \DateTime('+7 days'),
                   'username' => 'root',
               ]);
        
        return new Response('success');
    }
}
