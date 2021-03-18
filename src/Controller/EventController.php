<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Entity\Date;
use App\Repository\DateRepository;
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
     * @Route("/event", name="event_index", methods={"GET"})
     */
    public function index(EventRepository $eventRepository, ImageRepository $imageRepository)
    {
        return $this->redirectToRoute('service_index');
        /*return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);*/
    }

    /**
     * @Route("/event/give/data", name="event_give_data", methods={"GET|POST"})
     */
    public function dataGive(EventRepository $eventRepository, ImageRepository $imageRepository)
    { 
        $startDateEvent = new \dateTime($_GET['start']);
        
        $EndDateEvent =  new \dateTime($_GET['end']);

        $dates = new Date();

        $date = $dates->setStart($startDateEvent);
        $date = $dates->setEnd($EndDateEvent);

        $orm = $this->getDoctrine()->getManager();
        $orm->persist($date);
        $orm->flush();

        return $this->redirectToRoute('service_index');
    }

    /**
     * @Route("/event/data/to/delete", name="event_data_to_delete", methods={"GET|POST"})
     */
    public function eventDataToDelete(EventRepository $eventRepository, ImageRepository $imageRepository,Request $request)
    { 
        $startDateEvent = new \dateTime($_GET['start']);
        
        $endDateEvent =  new \dateTime($_GET['end']);

        $event = $eventRepository->findOneBy([
            'member' => $this->getUser()->getId(),
            'start' => $startDateEvent,
            'end' => $endDateEvent
        ]);

        //dd($event);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($event);
        $entityManager->flush();
        
        return $this->redirectToRoute('service_index');
    }

    /**
     * @Route("/event/success/delete", name="event_success_delete", methods={"GET|POST"})
     */
    public function eventSuccessDelete(EventRepository $eventRepository, ImageRepository $imageRepository)
    {
        $this->addFlash('success', ' La suppression de votre RDV est réussit !!! ');

        return $this->redirectToRoute('service_index');
    }

    /**
     * @Route("/event/change", name="event_change", methods={"GET|POST"})
     */
    public function change(EventRepository $eventRepository, DateRepository $dateRepository,ImageRepository $imageRepository)
    {
        $dates = $dateRepository->findAll();

        foreach($dates AS $date) 
        {
            $date = $date;
        }
        
        $startDateEvent = $date->getStart();
        
        $EndDateEvent =  $date->getEnd();

        $event = $eventRepository->findOneBy(['start' => $startDateEvent, 'end' => $EndDateEvent, 'member' => $this->getUser()->getId()]);
        
        $startDate = $event->getStart();
        $startDate = $startDate->format("d-m-Y H:i:s");
        //dd($startDate);
        
        return $this->render('event/change.html.twig', [
            'event' => $event, 'startDate' => $startDate, 'picture' => $imageRepository->findOneBySomeField(1)
        ]);
    }

    /**
     * @Route("/event/update", name="event_update", methods={"GET|POST"})
     */
    public function update(EventRepository $eventRepository, ImageRepository $imageRepository)
    {
        $idBookingOld = $_POST['id'];
        //dd($idBookingOld);

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

        return $this->render('event/updatePlanning.html.twig', ['picture' => $imageRepository->findOneBySomeField(1), 'idBookingOld' => $idBookingOld, 'data' => $data]);
    }

    /**
     * @Route("/event/modification/validate/{idBookingOld}", name="event_modification_validate", methods={"GET|POST"})
     */
    public function eventModificationValidate(EventRepository $eventRepository, ImageRepository $imageRepository, $idBookingOld)
    {
        $eventOld = $eventRepository->find($idBookingOld);
        //dd($_GET['start']);
        $startDateEvent = new \dateTime($_GET['start']);
        
        $EndDateEvent =  new \dateTime($_GET['end']);

        $eventNew = $eventOld->setStart($startDateEvent);
        $eventNew = $eventOld->setEnd($EndDateEvent);

        $orm = $this->getDoctrine()->getManager();
        $orm->persist($eventNew);
        $orm->flush();

        return $this->redirectToRoute('service_index');
    }

    /**
     * @Route("/event/success/update", name="event_success_update", methods={"GET|POST"})
     */
    public function eventSuccessUpdate(EventRepository $eventRepository, ImageRepository $imageRepository)
    {
        $this->addFlash('success', ' La modification de votre RDV est réussit !!! ');

        return $this->redirectToRoute('service_index');
    }



    /**
     * @Route("/event/planning/update", name="event_planning_update", methods={"GET"})
     */
    public function planningUpdate(EventRepository $eventRepository, ImageRepository $imageRepository)
    {
        $user = $this->getUser()->getId();
        
        //$product = $repository->findOneBy(['name' => 'Keyboard']);
        $rdvMembers = $eventRepository->findBy(['member' => $user]);
        //dd($rdvMembers);
        $rdvs = [];

        foreach ($rdvMembers as $rdvMember ) {
            $rdvs[] = [
                
                'title' => $rdvMember->getTitle(),
                'start' => $rdvMember->getStart()->format("Y-m-d H:i:s"),
                'end' => $rdvMember->getEnd()->format("Y-m-d H:i:s")
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('event/planningUpdate.html.twig', [
            'datas' => $data,'picture' => $imageRepository->findOneBySomeField(1) 
        ]);
    } 

    /**
     * @Route("/event/planning/delete", name="event_planning_delete", methods={"GET"})
     */
    public function planningDelete(EventRepository $eventRepository, ImageRepository $imageRepository)
    {
        $user = $this->getUser()->getId();
        
        //$product = $repository->findOneBy(['name' => 'Keyboard']);
        $rdvMembers = $eventRepository->findBy(['member' => $user]);
        //dd($rdvMembers);
        $rdvs = [];

        foreach ($rdvMembers as $rdvMember ) {
            $rdvs[] = [
                
                'title' => $rdvMember->getTitle(),
                'start' => $rdvMember->getStart()->format("Y-m-d H:i:s"),
                'end' => $rdvMember->getEnd()->format("Y-m-d H:i:s")
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('event/planningDelete.html.twig', [
            'datas' => $data,'picture' => $imageRepository->findOneBySomeField(1) 
        ]);
    } 

    /**
     * @Route("/event/Order", name="event_order_summary", methods={"GET"})
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
     * @Route("/event/new/{id}", name="event_new", methods={"GET"}) 
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
    }

    /**
     * @Route("/event/payment", name="event_payment", methods={"POST"}) 
     */
    public function payment(Request $request, ImageRepository $imageRepository, ServiceRepository $serviceRepository)
    {
        if(isset($_POST['price']) && !empty($_POST['price'])){
        
            require_once('../vendor/autoload.php'); 

            \Stripe\Stripe::setApiKey('sk_test_51IUKvkAez1VhFKwvtERCpj2IulQqmxVUCMx9sps8QNf0AhUoYZuoErBOClKD0hLhMJaDC6Quu67B6j8JGUEdf2tk00nVCIAkAY');

            /*var_dump($price);
            die()*/
            /*$price = (float)$price;
            $price = $price * 100;
            var_dump($price);
            die();*/
            //$price = floatval($price);
            $intent = \Stripe\PaymentIntent::create([
            'amount' => $_POST['price']*100,
            'currency' => 'eur'
            ]);
            /*var_dump($intent);
            die();*/

            //return $this->redirectToRoute('event_order_summary');

            return $this->render('event/payment.html.twig', [
            'services' => $serviceRepository->findAll(), 'picture' => $imageRepository->findOneBySomeField(1), 'intent' => $intent]);   
        }
        /*$price = (float)$price;
        var_dump($price);
        die();*/
        
    }
}

