<?php

namespace App\Controller;

use App\Entity\Paginator;
use App\Form\PaginatorType;
use App\Repository\PaginatorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/paginator')]
class PaginatorController extends AbstractController
{
    #[Route('/', name: 'paginator_index', methods: ['GET'])]
    public function index(PaginatorRepository $paginatorRepository): Response
    {
        return $this->render('paginator/index.html.twig', [
            'paginators' => $paginatorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'paginator_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $paginator = new Paginator();
        $form = $this->createForm(PaginatorType::class, $paginator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($paginator);
            $entityManager->flush();

            return $this->redirectToRoute('paginator_index');
        }

        return $this->render('paginator/new.html.twig', [
            'paginator' => $paginator,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'paginator_show', methods: ['GET'])]
    public function show(Paginator $paginator): Response
    {
        return $this->render('paginator/show.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    #[Route('/{id}/edit', name: 'paginator_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Paginator $paginator): Response
    {
        $form = $this->createForm(PaginatorType::class, $paginator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('paginator_index');
        }

        return $this->render('paginator/edit.html.twig', [
            'paginator' => $paginator,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'paginator_delete', methods: ['DELETE'])]
    public function delete(Request $request, Paginator $paginator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paginator->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($paginator);
            $entityManager->flush();
        }

        return $this->redirectToRoute('paginator_index');
    }
}
