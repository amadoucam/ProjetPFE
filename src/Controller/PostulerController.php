<?php

namespace App\Controller;

use App\Entity\Postuler;
use App\Form\Postuler1Type;
use App\Repository\PostulerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/postuler")
 */
class PostulerController extends AbstractController
{
    /**
     * @Route("/", name="postuler_index", methods={"GET"})
     */
    public function index(PostulerRepository $postulerRepository): Response
    {
        return $this->render('postuler/index.html.twig', [
            'postulers' => $postulerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="postuler_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $postuler = new Postuler();
        $form = $this->createForm(Postuler1Type::class, $postuler);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($postuler);
            $entityManager->flush();

            return $this->redirectToRoute('postuler_index');
        }

        return $this->render('postuler/new.html.twig', [
            'postuler' => $postuler,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="postuler_show", methods={"GET"})
     */
    public function show(Postuler $postuler): Response
    {
        return $this->render('postuler/show.html.twig', [
            'postuler' => $postuler,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="postuler_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Postuler $postuler): Response
    {
        $form = $this->createForm(Postuler1Type::class, $postuler);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('postuler_index');
        }

        return $this->render('postuler/edit.html.twig', [
            'postuler' => $postuler,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="postuler_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Postuler $postuler): Response
    {
        if ($this->isCsrfTokenValid('delete'.$postuler->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($postuler);
            $entityManager->flush();
        }

        return $this->redirectToRoute('postuler_index');
    }
}
