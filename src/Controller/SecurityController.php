<?php
 
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\ImageRepository;
use App\Entity\User;
use App\Form\UserType;
use App\Form\ProfilType;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, ImageRepository $imageRepository, Request $request): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('service_index');
         }

        $user = new User(); 
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('service/loginOrRegistration.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'picture' => $imageRepository->findOneBySomeField(1), 'user' => $user,
            'form' => $form->createView()]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        /*throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');*/
    }
}
