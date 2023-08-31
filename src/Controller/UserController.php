<?php  

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;


class UserController extends AbstractController
{
    #[Route('/room/register', name: 'register_room')]
    public function registerRoom(Request $request, UserPasswordHasherInterface $userPasswordHasher,UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = new User;
        $form = $this->createform(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $email = (new TemplatedEmail())
                   ->from('duck@example.com')
                   ->to($user->getEmail())
                   ->subject('Thanks for signing up!')
                   ->htmlTemplate('emails/signup.html.twig')
                   ->context([
                       'expiration_date' => new \DateTime('+7 days'),
                       'username' => 'root',
                   ]);
            $mailer->send($email);

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('preregistration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('room/guest', name: 'guest_room')]
    public function guestRoom(): Response
    {
        return $this->render('preregistration/guest.html.twig');
    }

}
