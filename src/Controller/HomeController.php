<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Repository\RecruteurRepository;
use App\Entity\Postuler;
use App\Form\Postuler1Type;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Entity\CategorySearch;
use App\Form\CategorySearchType;
use App\Entity\Categorie;

use App\Form\PostulerType;
use App\Repository\PostulerRepository;
use App\Entity\Recruteur;
use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function accueil(Request $request)
    {
        //page d'exception NotFoundException
        //throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à cette page');

        return $this->render('home.html.twig');
    } 

    /**
     * @Route("/recherche", name="offres_recherche", methods={"GET", "POST"})
     */
    public function search(Request $request, OffreRepository $repo)
    {
        $propertySearch = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $propertySearch);
        $form->handleRequest($request);

        $offres = [];
        if($form->isSubmitted() && $form->isValid()) {
           
            $title = $propertySearch->getTitle();
            $ville = $propertySearch->getVille();
            $categorie = $propertySearch->getCategorie();
      /*      if($title != ""){  
                $offres = $this->getDoctrine()->getRepository(Offre::class)->findBy(['title' => $title]);  
            }else
            {      
                $offres = $this->getDoctrine()->getRepository(Offre::class)->findAll();
            }  */

            if($ville != ""){  
                $offres = $this->getDoctrine()->getRepository(Offre::class)->findBy(['ville' => $ville]);  
                
            }else
            {      
                $offres = $this->getDoctrine()->getRepository(Offre::class)->findAll();
            } 

        /*    if($categorie != ""){    
                $offres = $this->getDoctrine()->getRepository(Offre::class)->findAll();
            }else 
            {      
                $offres = $this->getDoctrine()->getRepository(Offre::class)->findBy(['categorie' => $categorie]);
            }  */

        }

        return $this->render('home/recherche.html.twig', [
            'form' => $form->createView(),
            'offres' => $offres
        ]);
    }

    /**
     * @Route("/offres", name="offres_annonce")
     */
    public function index(Request $request, PaginatorInterface $paginator, OffreRepository $repo)
    {
        //$repo = $this->getDoctrine()->getRepository(Offre::class);
        //$offres = $repo->findAll();
        $donnees = $this->getDoctrine()->getRepository(Offre::class)->findBy([],['createdAt' => 'desc']);

        $offres = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            5 // Nombre de résultats par page
        );

        return $this->render('home/index.html.twig', [
            'paginator' => $donnees,
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

            $this->addFlash('success', 'Offre Créer avec succes!');
            return $this->redirectToRoute('offres_annonce'); 

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

            $this->addFlash('success', 'Offre modifier avec succes!');
            return $this->redirectToRoute('offres_annonce_show', ['id' => $offres->getId()]);
            }

              //return $this->redirectToRoute('offres_annonce', ['id' => $offres->getId()]);
            

        return $this->render('home/create.html.twig', [
              'offres' => $offres,
              'formOffre' => $form->createView(),
              'editMode' => $offres->getId() !== null
            ]);
    }

    /**
     * @Route("/mes_offres/{offres}/{id}", name="liste_offre")
     */
    public function postulation($id, Request $request, Recruteur $recruteur, Offre $offres)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $repository = $this->getDoctrine()->getManager()->getRepository(recruteur::class);
        $recruteur = $repository->find($recruteur); 

        $repository = $this->getDoctrine()->getManager()->getRepository(offre::class);
        $offre = $repository->find($offres);

        $em = $this->getDoctrine()->getManager();
        
        return $this->render('profile/liste_offre.html.twig', [
            'recruteur' => $recruteur,
            'offre' => $offre,
        ]);
    }

    /**
     * @Route("/offres/{id}", name="offres_annonce_show") 
     */
    public function show(Offre $offres, $id, Request $request, EntityManagerInterface $manager, \Swift_Mailer $mailer) 
    {
        return $this->render('home/show.html.twig', [
            'offres' => $offres,
        ]);
    }
    
    /**
     * @Route("/offres/postuler/{user}/offre/{offres}", name="offres_annonce_postulation")
     */
    public function postuler(Offre $offres, User $user, Request $request, EntityManagerInterface $manager) 
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
            $repository =$this->getDoctrine()->getManager()->getRepository(user::class);
            $user=$repository->find($user);
            $repo = $this->getDoctrine()->getManager()->getRepository(offre::class);
            $offre = $repo->find($offres);

        $postuler = new Postuler();
        $form = $this->createForm(PostulerType::class, $postuler);
        $form->handleRequest($request);
        $postuler->setUser($user);
        $postuler->setOffre($offre);     

        if ($form->isSubmitted() && $form->isValid()) {
            $FormData = $form->getData();
            $manager = $this->getDoctrine()->getManager();
            
            $manager->persist($postuler);
            $manager->flush();

            $this->addFlash('success', 'Vous avez postuler avec succes!');
            return $this->redirectToRoute('profile');
        
        }

        return $this->render('home/postuler.html.twig', [
            'user' => $user,
            'offre' => $offre,
            'formPostuler' => $form->createView(),
        ]);
    }
   
}



/*


        return $this->render('home/recherche.html.twig', [
            'form' => $form->createView(),
            'offres' => $offres
        ]);

*/

/*

public function search(Request $request, OffreRepository $repo,PaginatorInterface $paginator, EntityManagerInterface $em)
    {
        
        $propertySearch = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $propertySearch);
        $form->handleRequest($request);

        //$offres = [];

        $offres = $repo->findAll();
 
        if ($form->isSubmitted() && $form->isValid()) {
 
            $title = $form->getData()->getTitle();

            $offres = $repo->search($title);


            if ($offres == null) {
                $this->addFlash('erreur', 'Aucune Offre contenant ce mot clé dans le titre n\'a été trouvé, essayez en un autre.');
           
            }

    }

     // Paginate the results of the query
     $offres = $paginator->paginate(
        // Doctrine Query, not results
        $offres,
        // Define the page parameter
        $request->query->getInt('page', 1),
        // Items per page
        4
    );
        return $this->render('home/recherche.html.twig', [
            'form' => $form->createView(),
            'offres' => $offres
        ]);
    }

*/