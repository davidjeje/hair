<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use App\Repository\ImageRepository;
use App\Entity\Paginator;
use App\Form\PaginatorType;
use App\Repository\PaginatorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use App\Form\UseType;
use App\Form\UsType;
use App\Form\UsersType;
use App\Form\ProfilType;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;


class ServiceController extends AbstractController
{ 
    /**
     * @Route("/", name="service_index", methods={"GET"})
     */
    public function index(ServiceRepository $serviceRepository, ImageRepository $imageRepository): Response
    {
        
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(), 'picture' => $imageRepository->findOneBySomeField(1)
        ]);
    }

    /**
     * @Route("/about", name="service_about", methods={"GET"})
     */
    public function about(ImageRepository $imageRepository): Response
    {
        return $this->render('service/about.html.twig', ['picture' => $imageRepository->findOneBySomeField(1)]);
    }

    /**
     * @Route("/calendar/{id}", name="service_calendar", methods={"GET"})
     */
    public function calendar(ImageRepository $imageRepository, EventRepository $eventRepository, $id): Response
    {
        $events = $eventRepository->findAll();

        $rdvs = [];

        foreach ($events as $event ) {
            $rdvs[] = [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'start' => $event->getStart()->format("Y-m-d H:i:s"),
                'end' => $event->getEnd()->format("Y-m-d H:i:s")
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('service/calendar.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'id' => $id, 'data' => $data]);
    }


    /**
     * @Route("/updatePassword", name="update_password", methods={"GET|POST"}) 
     */
    public function updatePassword(ImageRepository $imageRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        //dd($user);
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())  {
            
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);  
            
            $orm = $this->getDoctrine()->getManager();
            $orm->persist($user);
            $orm->flush();

            $this->addFlash('success', ' La modification de votre mot de passe est réussite !!! Dorénavant connectez-vous avec ce mot de passe.');

            return $this->redirectToRoute('service_index');
        }

        return $this->render('service/updatePassword.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'user' => $user,
            'form' => $form->createView()]); 
    }

    /**
     * @Route("/updateEmailUser", name="update_email_user", methods={"GET|POST"}) 
     */
    public function updateEmailUser(ImageRepository $imageRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        //dd($user);
        $form = $this->createForm(UseType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())  {
            
            $email = $user->getEmail();
            //dd($email);
            $user->setEmail($email);  
            
            $orm = $this->getDoctrine()->getManager();
            $orm->persist($user);
            $orm->flush();

            $this->addFlash('success', ' La modification de votre Email est réussite !!! ');

            return $this->redirectToRoute('service_index');
        }

        return $this->render('service/updateEmailUser.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'user' => $user,
            'form' => $form->createView()]); 
    }

    /**
     * @Route("/updateNumberUser", name="update_number_user", methods={"GET|POST"}) 
     */
    public function updateNumberUser(ImageRepository $imageRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        //dd($user);
        $form = $this->createForm(UsType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())  {
            
            $number = $user->getNumber();
            //dd($email);
            $user->setEmail($number);  
            
            $orm = $this->getDoctrine()->getManager();
            $orm->persist($user);
            $orm->flush();

            $this->addFlash('success', ' La modification de votre numéro de téléphone est réussite !!! ');

            return $this->redirectToRoute('service_index');
        }

        return $this->render('service/updateNumberUser.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'user' => $user,
            'form' => $form->createView()]); 
    }


    /**
     * @Route("/login1", name="login_or_registration", methods={"GET|POST"}) 
     */
    public function loginOrRegistration(ImageRepository $imageRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils, MailerInterface $mailer): Response
    {
        $user = new User(); 
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();
        //dd($user);

        if ($form->isSubmitted() && $form->isValid())  {
            /*var_dump($user->getEmail());
            die();*/
            $submittedToken = $request->request->get('token');
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            //$roles = ['ROLE_USER'];
            $user->setPassword($password);  
            $user->setIsActive(false);
            //$user->setRoles($roles);
            $user->setToken($submittedToken);
            
            $orm = $this->getDoctrine()->getManager();
            $orm->persist($user);
            $orm->flush();

            $emailUser = $user->getEmail();

            /*$message = (new \Swift_Message('Nous vous souhaitons la bienvenu. Clic sur le lien pour valider ton inscription ! A bientôt !!!'))
                ->setFrom('dada.pepe.alal@gmail.com')
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        'service/mail.html.twig',
                        ['submittedToken' => $submittedToken]
                    ),
                    'text/html'
                ); 

            $mailer->send($message);*/
            /*$email = (new Email())
            ->from('dada.pepe.alal@gmail.com')
            ->to($emailUser)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');*/
            $email = (new TemplatedEmail())
            ->from('dada.pepe.alal@gmail.com')
            ->to(new Address($emailUser))
            ->subject('Valider votre inscription')

            // path of the Twig template to render
            ->htmlTemplate('service/mail.html.twig')

            // pass variables (name => value) to the template
            ->context([
            'submittedToken' => $submittedToken,
            ]);
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                //'error message or try to resend the message'
            };
            
            $this->addFlash('success', ' Félicitations ! Bienvenu parmi nous ! Votre compte a bien été enregistré. Pour finaliser l\'inscription, rendez-vous sur votre boîte mail et clicker sur le lien. À bientôt !!');

            return $this->redirectToRoute('service_index');
        }
        
        return $this->render('service/loginOrRegistration.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'user' => $user,
            'form' => $form->createView(), 'last_username' => $lastUsername, 'error' => $error]); 
    }
 
    /**
     * @Route("/validate/{submittedToken}", name="validate", methods="GET|POST")
     */
    public function validateAccount(UserRepository $userRepository, Request $request, $submittedToken)
    {
        
        $user = $userRepository->findOneBySomeField($submittedToken);
        //Son compte utilisateur est actif.
        $user->setIsActive(true);
        $roles = ['ROLE_USER'];
        $user->setRoles($roles);
        $orm = $this->getDoctrine()->getManager();
        $orm->persist($user);
        $orm->flush();
        
        return $this->redirectToRoute('service_index');
    }

    /**
     * @Route("/connectionLink", name="connectionLink", methods="GET|POST")
     */
    public function connectionLink(ImageRepository $imageRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils, MailerInterface $mailer)
    {

        return $this->render('service/connectionLink.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), /*'user' => $user, 'last_username' => $lastUsername, 'error' => $error*/]);
    }

    /**
     * @Route("/singIn", name="service_singIn", methods="GET|POST")
     */
    public function signIn(Request $request, ImageRepository $imageRepository, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils):Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();    

        return $this->render('service/signIn.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'last_username' => $lastUsername]);
    }

    /**
     * @Route("/service/{page}", name="service_service", methods={"GET"})
     */
    public function service(ServiceRepository $serviceRepository, $page, ImageRepository $imageRepository): Response
    {
        $nombreMaxParPage = 6;
        $nombreMax = 6;
        $firstResult = ($page-1) * $nombreMaxParPage;
        //Cette variable stock la fonction située dans le repository commentaire et qui elle même stock des valeurs pour lui permettre d'afficher un nombre de commentaire par figure et par page.

        $serviceNumber = $serviceRepository->serviceNumber($firstResult, $nombreMax);

        //Cette variable stock la fonction située dans le repository paginator et qui elle même stock des valeurs pour lui permettre d'afficher un nombre de commentaire par figure et par page.
        $findAllPage = $serviceRepository->findAllPage($page, $nombreMaxParPage);
        
        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($findAllPage) / $nombreMaxParPage),
            'nomRoute' => 'service_service',
            'paramsRoute' => array()
        );
        return $this->render('service/service.html.twig', [
            /*'services' => $serviceRepository->findAll(),*/'serviceNumber' => $serviceNumber, 'pagination' => $pagination, 'picture' => $imageRepository->findOneBySomeField(1)]);
    }

    /**
     * @Route("/logout/user", name="logout", methods="GET|POST")
     */
    public function logout(): Response
    {
        
        return $this->render(
            'service/index.html.twig',
            [
                    'mainNavLogin' => false,
                    'title' => 'Deconnexion',
                    'error' => null,
            ]
        );
    }

    #[Route('/new', name: 'service_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($service);
            $entityManager->flush();

            return $this->redirectToRoute('service_index');
        }

        return $this->render('service/new.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    } 

    /**
     * @Route("/details/{id}", name="service_show", methods={"GET"})
     */
    public function show(Service $service, ImageRepository $imageRepository): Response
    {
        return $this->render('service/show.html.twig', [
            'service' => $service, 'picture' => $imageRepository->findOneBySomeField(1)
        ]);
    }


    #[Route('/{id}/edit', name: 'service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('service_index');
        }

        return $this->render('service/edit.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'service_delete', methods: ['DELETE'])]
    public function delete(Request $request, Service $service): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($service);
            $entityManager->flush();
        }

        return $this->redirectToRoute('service_index');
    }
}
