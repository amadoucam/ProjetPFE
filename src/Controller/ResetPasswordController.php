<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\ResetPasswordType;
use App\Form\UserType;

/**
 * Controller used to manage current user.
 *
 * @IsGranted("ROLE_USER")
 *
 * @author Romain Monteil <monteil.romain@gmail.com>
 */
class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/change-password", methods="GET|POST", name="user_change_password")
     */
    public function nouveauPassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, $form->get('newPassword')->getData()));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('security_logout');
        }

        return $this->render('emails/edit_password_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
