<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Entity\Postuler;
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
     * @Route("/recherche", name="offres_recherche")
     */
    public function search(Request $request)
    {
        
        $propertySearch = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $propertySearch);
        $form->handleRequest($request);

        $offres = [];
        if($form->isSubmitted() && $form->isValid()) {
           
            $title = $propertySearch->getTitle();
            if($title != ""){    //&& $content != ""
                $offres = $this->getDoctrine()->getRepository(Offre::class)->findBy(['title' => $title]);
            }else 
            { 
                $offres = $this->getDoctrine()->getRepository(Offre::class)->findAll();
            } 
        }
        return $this->render('home/recherche.html.twig', [
            'form' => $form->createView(),
            'offres' => $offres
        ]);
    }

    /**
     * @Route("/cherches", name="recherches")
     */
    public function searchs(OffreRepository $offreRepository,Request $request): Response
    {

        $categorySearch = new CategorySearch();
        $form = $this->createForm(CategorySearchType::class, $categorySearch);
        $form->handleRequest($request);

        $offreRepository = [];
        if($form->isSubmitted() && $form->isValid()) {
            $categorie_id = $categorySearch->getTitle();
            if($categorie_id != ""){
                
                $offreRepository = $this->getDoctrine()->getRepository(Offre::class)->findBy(['categorie_id' => $categorie_id]);
            }else 
            { 
                $offreRepository = $this->getDoctrine()->getRepository(Offre::class)->findAll();
            } 
        }
        return $this->render('home/search.html.twig', [
            'form' => $form->createView(),
            'offres' => $offreRepository->findAll()
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
    public function show(Offre $offres, $id, Request $request, EntityManagerInterface $manager, \Swift_Mailer $mailer) 
    {

        return $this->render('home/show.html.twig', [
            
            'offres' => $offres,
        ]);
    }

    
    /**
     * @Route("/offres/postuler/{user}/offre/{offres}", name="offres_annonce_postulation")
     */
    public function postuler(Offre $offres, User $user, Request $request, EntityManagerInterface $manager, \Swift_Mailer $mailer) 
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
            $contactFormData = $form->getData();
            $manager = $this->getDoctrine()->getManager();
            
            $manager->persist($postuler);
            $manager->flush();
            

            $message = (new \Swift_Message('Nouveau compte'))
                // On attribue l'expéditeur
                ->setFrom('camaraamadou775@gmail.com')
                // On attribue le destinataire
                ->setTo($contactFormData->getEmail())
                // On crée le texte avec la vue
                ->setBody(
                    $contactFormData->getUser(),
                    'text/html'
                )
            ;

            $mailer->send($message);

            $this->addFlash('success', 'Votre message à été Envoyez avec succes!');
            //return $this->redirectToRoute('home');
            return $this->redirectToRoute('profile');
        
        }

        return $this->render('home/postuler.html.twig', [
            'user' => $user,
            'offre' => $offre,
        
            'formPostuler' => $form->createView(),
        ]);
    }
   

}
