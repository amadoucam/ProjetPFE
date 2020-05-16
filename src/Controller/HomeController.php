<?php

namespace App\Controller;

use App\Entity\Recruteur;
use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function accueil(Request $request)
    {
        return $this->render('home.html.twig');
    }

    /**
     * @Route("/offres", name="offres_annonce")
     */
    public function index(Request $request, PaginatorInterface $paginator, OffreRepository $repo)
    {
        //$repo = $this->getDoctrine()->getRepository(Offre::class);
        //$offres = $repo->findAll();
        $donnees = $this->getDoctrine()->getRepository(Offre::class)->findBy([],['createdAt' => 'asc']);

        $offres = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            5 // Nombre de résultats par page
        );

        return $this->render('home/index.html.twig', [
            'offres' => $offres
        ]);
    }

    /**
     * @Route("/offres/new/{id}", name="offres_create")
     */
    public function formOffre(Request $request, $id)
    {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $repository =$this->getDoctrine()->getManager()->getRepository(recruteur::class);
            $recruteur = $repository->find($id);

            $offres = new Offre();
            
            $form = $this->createForm(OffreType::class, $offres);
            $form->handleRequest($request);

            $offres->setRecruteur($recruteur);
           
            if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($offres);
            $entityManager->flush();

            return $this->redirectToRoute('home'); //, ['id' => $offres->getId()]);

            }

        return $this->render('home/create.html.twig', [
            'recruteur' => $recruteur,
              'offres' => $offres,
              'formOffre' => $form->createView(),
              'editMode' => $offres->getId() !== null
            ]);
    }

    /**
     * @Route("/offres/{id}/edit", name="offres_edit")
     */
    public function editOffre(Offre $offres, Request $request)
    {
            
            $form = $this->createForm(OffreType::class, $offres);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('offres_annonce_show');
            }

              //return $this->redirectToRoute('offres_annonce', ['id' => $offres->getId()]);
            

        return $this->render('home/create.html.twig', [
              'offres' => $offres,
              'formOffre' => $form->createView(),
              'editMode' => $offres->getId() !== null
            ]);
    }


    /**
     * @Route("/offres/{id}", name="offres_annonce_show")
     */
    public function show(Offre $offres) {

        return $this->render('home/show.html.twig', [
            'offres' => $offres
        ]);
    }

    

}
