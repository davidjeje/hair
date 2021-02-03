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
use App\Form\ProfilType;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\Session;

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
     * @Route("/login1", name="login_or_registration", methods={"GET|POST"}) 
     */
    public function loginOrRegistration(ImageRepository $imageRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils): Response
    {
        $user = new User(); 
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();


        if ($form->isSubmitted() && $form->isValid())  {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $roles = ['ROLE_USER'];
            $user->setPassword($password);  
            $user->setIsActive(false);
            $user->setRoles($roles);
            
            $orm = $this->getDoctrine()->getManager();
            $orm->persist($user);
            $orm->flush();
            // Message qui s'affiche sur la boîte mail de l'utilisateur qui souhaite ouvrir un compte utilisateur.
            
            $this->addFlash('success', ' Félicitation !!! Bienvenu parmi nous !!! Votre compte a bien été enregistré. Vous pouvez vous connecter sur votre compte avec les informations que vous venez de renseigner pour l\'inscription.');

            return $this->redirectToRoute('service_index');
        }
        
        return $this->render('service/loginOrRegistration.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'user' => $user,
            'form' => $form->createView(), 'last_username' => $lastUsername, 'error' => $error]); 
    }

    /**
     * @Route("/login2", name="login", methods="GET|POST")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        /*$form2 = $this->createForm(UserType::class, $user);

        $form2->handleRequest($request);*/

        $form2 = $this->get('form.factory')
            ->createNamedBuilder('')
            ->add('username', \Symfony\Component\Form\Extension\Core\Type\TextType::class, ['label' => 'name'])
            ->add('password', \Symfony\Component\Form\Extension\Core\Type\PasswordType::class, ['label' => 'Mot de passe'])
                 
            ->getForm();

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();
        

        if ($form2->isSubmitted() && $form2->isValid()) {
            $session = new Session();
            $session->start();
            return $this->redirectToRoute('service_index');
        } 
        return $this->render(
            'service/loginOrRegistration.html.twig',
            [
                    'mainNavLogin' => true,
                    'title' => 'Connexion',
                    'form' => $form2->createView(),
                    'last_username' => $lastUsername,
                    'error' => $error,
            ]
        ); 
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
