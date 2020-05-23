<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\FormationType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Formation;
use App\Entity\Experience;
use App\Entity\Langue;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Dompdf\Dompdf;
use Dompdf\Options;

class CandidatController extends AbstractController
{
    /**
     * @Route("/candidat/{id}", name="candidat")
     */
    public function index($id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $repository = $this->getDoctrine()->getManager()->getRepository(user::class);
        $user = $repository->find($id);

        $em = $this->getDoctrine()->getManager();
        
        return $this->render('candidat/index.html.twig');
    }

    /**
     * @Route("/profile", name ="profile")
     */
    public function showProfile(){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('candidat/profile.html.twig');
    }

    /**
     * @Route("/profile/edit/{id}", name="editProfile")
     */
    public function editProfile(Request $request,User $user)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(RegistrationType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profile');
        }
        return $this->render('candidat/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/allCv" , name="allCv")
     */
    public function  allCv(){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $repository = $this->getDoctrine()->getManager()->getRepository(user::class);
        $candidat = $repository->findAll();
        return $this->render('admin/allCv.html.twig', array('candidat'=>$candidat));
    }

}

