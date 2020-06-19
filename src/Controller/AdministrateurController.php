<?php

namespace App\Controller;

use App\Entity\Administrateur;
use App\Repository\AdministrateurrRepository;
use App\Form\AdminType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\FormError;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdministrateurController extends AbstractController
{
    /**
     * @Route("/inscription-admin", name="security_admin")
     */
    public function registration_admin(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(administrateur::class);
        $spe = $repository->findAll();
        $administrateur = new Administrateur();
        $form = $this->createForm(AdminType::class, $administrateur);
        $form->handleRequest($request);  
        if($form->isSubmitted() && $form->isValid()){
        
        $manager = $this->getDoctrine()->getManager();
        $hash = $encoder->encodePassword($administrateur, $administrateur->getPassword());
        $administrateur->setPassword($hash);
        
        $manager->persist($administrateur);
        $manager->flush();

        $this->addFlash('success', 'Votre compte à bien été enregistré.');
        return $this->redirectToRoute('security_login');

        }
        
        return $this->render('security/admin.html.twig', 
        [
            'administrateur' => $administrateur,
            'spe'=>$spe,
            'form' => $form->createView(),
        ]
        );
   
    }


    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
 
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout(){}
    
}
