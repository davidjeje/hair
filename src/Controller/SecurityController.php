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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class SecurityController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, ImageRepository $imageRepository, Request $request, /*LoginLinkHandlerInterface $loginLinkHandler,*/ UserRepository $userRepository): Response
    {
        // check if login form is submitted
        /*if ($request->isMethod('POST')) {
            // load the user in some way (e.g. using the form input)
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            // create a login link for $user this returns an instance
            // of LoginLinkDetails
            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
            $loginLink = $loginLinkDetails->getUrl();

            // ... send the link and return a response (see next section)
        }

        // if it's not submitted, render the "login" form
        //return $this->render('security/login.html.twig');*/
    
        /*$error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        $user = new User(); 
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        return $this->render('service/loginOrRegistration.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'picture' => $imageRepository->findOneBySomeField(1), 'user' => $user,
            'form' => $form->createView()]);*/

        if ($this->getUser() ) {
            //if ($this->getUser()->getIsActive() === true){
                //$this->addFlash('success', ' Bonjour ' );
                return $this->redirectToRoute('service_index');
            }
            //else{
                /*$this->addFlash('error', ' Pour accèder à votre compte, rendez-vous sur votre compte pour valider et finaliser votre inscription. Ensuite, vous pourrez vours rendre dans votre espace client. A très vite !!!');*/
                //return $this->redirectToRoute('app_logout');
            
        /*}
        else{
                
            //return $this->redirectToRoute('app_logout', ['route' => 'app_login']);
            //return $this->redirectToRoute('app_lucky_number', ['max' => 10]);
        }*/    
    }

    /**
     * @Route("/login2", name="secu_login")
     */
    public function requestLoginLink(NotifierInterface $notifier, LoginLinkHandlerInterface $loginLinkHandler, UserRepository $userRepository, Request $request, MailerInterface $mailer)
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            //dd($user->getRoles());
            $roles = ['ROLE_USER'];
            if($user->getRoles() === $roles){
                $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

                $email = (new TemplatedEmail())
                ->from('dada.pepe.alal@gmail.com')
                ->to(new Address($user->getEmail()))
                ->subject('Mot de passe oublié')

                // path of the Twig template to render
                ->htmlTemplate('service/link.html.twig')

                // pass variables (name => value) to the template
                ->context([
                'loginLinkDetails' => $loginLinkDetails,
                ]);
                try {
                    $mailer->send($email);
                } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                //'error message or try to resend the message'
                };

                $this->addFlash('success', ' Rendez-vous sur votre boîte mail et clicker sur le lien pour se connecter. À bientôt !!');
            }
            

            return $this->redirectToRoute('service_index');

            // create a notification based on the login link details
            /*$notification = new LoginLinkNotification(
                $loginLinkDetails,
                'Welcome to MY WEBSITE!' // email subject
            );
            // create a recipient for this user
            $recipient = new Recipient($user->getEmail());

            // send the notification to the user
            $notifier->send($notification, $recipient);*/

            // render a "Login link is sent!" page
            //return $this->render('security/login_link_sent.html.twig');
        }
    }


    /**
     * @Route("/login_check", name="login_check")
     */
    public function check()
    {
        throw new \LogicException('This code should never be reached');
    }

    /**
     * @Route("/logout", name="app_logout", methods="GET|POST")
     */
    public function logout(): Response
    {
        
    }

    
}
