<?php

namespace App\Controller;

use App\Entity\Recruteur;
//use App\Form\RegistrationFormType;
use App\Form\RecruteurType;
use App\Repository\RecruteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profil", name="profil_index", methods={"GET"})
     */
    public function index(RecruteurRepository $recruteurRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('profile/profile.html.twig', [
            'recruteurs' => $recruteurRepository->findAll(),
        ]);
    }

}

/*
public function profile(Request $request,Recruteur $recruteur, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(RegistrationFormType::class,$recruteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recruteur->setPassword(
                $passwordEncoder->encodePassword(
                    $recruteur,
                    $form->get('password')->getData()
                )
            );
           // $this->getDoctrine()->getManager()->flush();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recruteur);
            $entityManager->flush();

            return $this->redirectToRoute('profil_index');
        }
        return $this->render('profile/profile_edit.html.twig', [
            'recruteur' => $recruteur,
            'form' => $form->createView(),
        ]);
*/
