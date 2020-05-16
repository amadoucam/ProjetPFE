<?php

namespace App\Controller;

use App\Entity\Recruteur;
use App\Form\RegistrationFormType;
use App\Repository\RecruteurRepository;
use App\Form\ResetPassType2;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\FormError;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/inscription/recruteur", name="app_register")
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer)
    {
        $recruteur = new Recruteur();
        $form = $this->createForm(RegistrationFormType::class, $recruteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $recruteur->setPassword(
                $passwordEncoder->encodePassword(
                    $recruteur,
                    $form->get('password')->getData()
                )
            );

            $manager = $this->getDoctrine()->getManager();
            $recruteur->setActivationToken(md5(uniqid()));
            $manager->persist($recruteur);
            $manager->flush();

             //token
            $message = (new \Swift_Message('Nouveau compte'))
                // On attribue l'expéditeur
                ->setFrom('camaraamadou775@gmail.com')
                // On attribue le destinataire
                ->setTo($recruteur->getEmail())
                // On crée le texte avec la vue
                ->setBody(
                    $this->renderView(
                        'emails/activation.html.twig', ['token' => $recruteur->getActivationToken()]
                    ),
                    'text/html'
                )
            ;
            //envoie email
            $mailer->send($message);

            // do anything else you need here, like send an email
            $this->addFlash('success', 'Votre compte à bien été enregistré.');

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activer/{token}", name="activer")
     */
    public function activer($token, RecruteurRepository $recruteur)
    {
        // On recherche si un utilisateur avec ce token existe dans la base de données
        $recruteur = $recruteur->findOneBy(['activation_token' => $token]);

        // Si aucun utilisateur n'est associé à ce token
        if(!$recruteur){
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // On supprime le token
        $recruteur->setActivationToken(null);
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($recruteur);
        $manager->flush();

        // On génère un message
        $this->addFlash('message', 'Utilisateur activé vous pouvez vous connecter!');

        // On retourne à l'accueil /home
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/pass-oublier", name="app_reset_password")
     */
    public function changerPassword (Request $request, RecruteurRepository $recruRepo, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $manager)
    {
        $form = $this->createForm(ResetPassType2::class, $recruteur);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //recuperation des données
            $donnees = $form->getData();
            //si un utilisateur à cette email
            $recruteur = $recruRepo->findOneByEmail($donnees['email']);
            //si l'utilisateur n'existe pas
            if(!$recruteur) {
                $this->addFlash('danger', 'Cette adresse mail n\'existe pas');

                return $this->redirectToRoute('security_login');
            }
            $token = $tokenGenerator->generateToken();

            try {
                $recruteur->setResetToken($token);
                $manager->persist($recruteur);
                $manager->flush();
            }
            catch (\Exception $e) {
                $this->addFlash('warning', 'Une erreur est survenue : ' . $e->getMessage());
                return $this->redirectToRoute('security_login');
            }

            //url de réinitialisation de mot de passe
            $url = $this->generateUrl('app_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new \Swift_Message('Nouveau compte'))
                // On attribue l'expéditeur
                ->setFrom('camaraamadou@gmail.com')
                // On attribue le destinataire
                ->setTo($recruteur->getEmail())
                // On crée le texte avec la vue
                ->setBody(
                    "<p>Salut, </p><p>Une demande de réinitialisation de mot de passe a été effectuée pour le
                            site Emplois. Veuillez cliquer sur le lien suivant : " .'<a>' . $url . '</a>' . "</p>",
                    'text/html'
                )
            ;
            //envoie email
            $mailer->send($message);

            $this->addFlash('message', 'Un e-mail de réinitialisation de mot de passe vous a été envoyé');
            return $this->redirectToRoute('security_login');
        }
        //on envoie vers la page de demande de l'é-mail
        return $this->render('emails/reset_password.html.twig', ['emailForm' => $form->createView()]);
    }

    /**
     * @Route("/pass-oublier/{token}", name="app_password")
     */
    public function newPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {
        // On cherche un utilisateur avec le token donné
        $recruteur = $this->getDoctrine()->getRepository(Recruteur::class)->findOneBy(['reset_token' => $token]);

        // Si l'utilisateur n'existe pas
        if (!$recruteur) {
            // On affiche une erreur
            $this->addFlash('danger', 'Token Inconnu');
            return $this->redirectToRoute('security_login');
        }

        // Si le formulaire est envoyé en méthode post
        if ($request->isMethod('POST')) {
            // On supprime le token
            $recruteur->setResetToken(null);

            // On chiffre le mot de passe
            $recruteur->setPassword($passwordEncoder->encodePassword($recruteur, $request->request->get('password')));

            // On stocke
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recruteur);
            $entityManager->flush();

            // On crée le message flash
            $this->addFlash('message', 'Mot de passe modifier avec succès');

            // On redirige vers la page de connexion
            return $this->redirectToRoute('security_login');
        }else {
            // Si on n'a pas reçu les données, on affiche le formulaire
            return $this->render('emails/new_pass.html.twig', ['token' => $token]);
        }

    }

    
}
