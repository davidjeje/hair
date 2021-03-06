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
     * @Route("/load", name="event_load", methods={"GET"})
     */
    public function load(EventRepository $eventRepository, ImageRepository $imageRepository): Response
    {

        //dd($eventRepository->findAll());
        return $eventRepository->findAll();
        /*return $this->render('event/load.html.twig'/*, [
            'events' => $eventRepository->findAll(),
        ]);*/
    }

    /**
     * @Route("/new", name="event_new", methods={"GET"})
     */
    public function new(Request $request, ImageRepository $imageRepository)
    {
        $event = new Event();
        
        $startDateEvent = new \dateTime($_GET['start']);
        
        $EndDateEvent =  new \dateTime($_GET['end']);

        //$user = $this->getUser();
        
        $event->setTitle($_GET['title']);
        $event->setStart($startDateEvent);
        $event->setEndd($EndDateEvent);
        //$event->setMember($user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($event);
        $entityManager->flush();    
    }
}

