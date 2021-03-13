<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ImageRepository;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;


#[Route('/event')]
class EventController extends AbstractController
{
    /**
     * @Route("/", name="event_index", methods={"GET"})
     */
    public function index(EventRepository $eventRepository, ImageRepository $imageRepository): Response
    {
        return $this->render('event/index.html.twig'/*, [
            'events' => $eventRepository->findAll(),
        ]*/);
    } 

    /**
     * @Route("/Order", name="event_order_summary", methods={"GET"})
     */
    public function order(EventRepository $eventRepository, ImageRepository $imageRepository): Response
    {  

        //dd($this->getUser()->getId());
        $lastEvent = $eventRepository->findAll();
        //$lastIdEvent = $eventRepository->findOneBy(['member' => $this->getUser()->getId()]);
        //dd($lastIdEvent);
        
        foreach($lastEvent AS $lastIdEvent)
        {
            $lastIdEvent = $lastIdEvent->getId(); 
        }
        /*var_dump($lastIdEvent);
        die();*/
        $oneEvent = $eventRepository->find($lastIdEvent);
        //dd($oneEvent->getService()->getName());
        $start = $oneEvent->getStart();
        //$start->format("Y-m-d H:i:s");
        $start = $start->format("d-m-Y H:i:s");
        //dd($start);
        //die();

        return $this->render('event/orderSummary.html.twig', [
            'event' => $oneEvent, 'start' =>$start,'picture' => $imageRepository->findOneBySomeField(1) 
        ]);
    }  

    /**
     * @Route("/new/{id}", name="event_new", methods={"GET"}) 
     */
    public function new(Request $request, ImageRepository $imageRepository, ServiceRepository $serviceRepository, $id)
    {
        $id = intval($id);
        
        $service = $serviceRepository->findOneBySomeField($id);
        /*var_dump($service);
        die();*/
        $event = new Event();
        /**/
        $startDateEvent = new \dateTime($_GET['start']);
        
        $EndDateEvent =  new \dateTime($_GET['end']);

        $user = $this->getUser();
        
        $event->setTitle($_GET['title']);
        $event->setStart($startDateEvent);
        $event->setEnd($EndDateEvent);
        $event->setMember($user);
        $event->setService($service);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($event);
        $entityManager->flush(); 


        return $this->redirectToRoute('event_order_summary');

        /*return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(), 'picture' => $imageRepository->findOneBySomeField(1)
        ]);*/   
    }

    /**
     * @Route("/payment/{price}", name="event_payment", methods={"GET"}) 
     */
    public function payment(Request $request, ImageRepository $imageRepository, ServiceRepository $serviceRepository, $price)
    {
        require_once('../vendor/autoload.php'); 

        \Stripe\Stripe::setApiKey('sk_test_51IUKvkAez1VhFKwvtERCpj2IulQqmxVUCMx9sps8QNf0AhUoYZuoErBOClKD0hLhMJaDC6Quu67B6j8JGUEdf2tk00nVCIAkAY');

/*sk_test_51IUKvkAez1VhFKwvtERCpj2IulQqmxVUCMx9sps8QNf0AhUoYZuoErBOClKD0hLhMJaDC6Quu67B6j8JGUEdf2tk00nVCIAkAY*/

        $intent = \Stripe\PaymentIntent::create([
            'amount' => $price*100,
            'currency' => 'eur'
        ]);
        /*var_dump($intent);
        die();*/

        //return $this->redirectToRoute('event_order_summary');

        return $this->render('event/payment.html.twig', [
            'services' => $serviceRepository->findAll(), 'picture' => $imageRepository->findOneBySomeField(1), 'intent' => $intent
        ]);   
    }
}

