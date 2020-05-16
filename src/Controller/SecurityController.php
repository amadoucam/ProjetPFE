<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Recruteur;
use App\Repository\UserRepository;
use App\Form\ResetPassType;
//use App\Entity\Specialite;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
//use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\FormError;
//use App\Form\ResetPasswordType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;


class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, SluggerInterface $slugger, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(user::class);
        $spe = $repository->findAll();
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

        $file = $user->getAvatar();
        $fileName = md5(uniqid()). '.' .$file->guessExtension();
        try {
                $file->move(
                        $this->getParameter('upload_directory'),
                        $fileName
                );
            } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
            }

            $brochureFile = $form->get('cv')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setCv($newFilename); 
                }

        $manager = $this->getDoctrine()->getManager();
        $user->setAvatar($fileName); 
        //$user->setAvatar('default.jpg');
        $hash = $encoder->encodePassword($user, $user->getPassword());

        $user->setPassword($hash);
        
        $user->setActivationToken(md5(uniqid()));
        $manager->persist($user);
        $manager->flush();
        
         //token
            $message = (new \Swift_Message('Nouveau compte'))
                // On attribue l'expéditeur
                ->setFrom('camaraamadou775@gmail.com')
                // On attribue le destinataire
                ->setTo($user->getEmail())
                // On crée le texte avec la vue
                ->setBody(
                    $this->renderView(
                        'emails/activation.html.twig', ['token' => $user->getActivationToken()]
                    ),
                    'text/html'
                )
            ;
            //envoie email
            $mailer->send($message);

        $this->addFlash('success', 'Votre compte à bien été enregistré.');
        return $this->redirectToRoute('security_login');

        }
        
        return $this->render('security/registration.html.twig', 
        [
            'user' => $user,
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

    /**
     * @Route("/profile/motdepasse", name="nouveau_password")
     */
    public function editPassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
    	$em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
    	$form = $this->createForm(ResetPasswordType::class, $user);

    	$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Si l'ancien mot de passe est bon
            if ($encoder->getPassword($user, $password)) {

                $hash = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($hash);

                $em->persist($user);
                $em->flush();

                $this->addFlash('notice', 'Votre mot de passe à bien été changé !');

                return $this->redirectToRoute('profile');

            } else {
                $form->addError(new FormError('Ancien mot de passe incorrect'));
            }

        }

    	return $this->render('emails/edit_password_user.html.twig', array(
    		'form' => $form->createView(),
    	));
    }

    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UserRepository $user)
    {
        // On recherche si un utilisateur avec ce token existe dans la base de données $user =
        $user = $user->findOneBy(['activation_token' => $token]);

        // Si aucun utilisateur n'est associé à ce token
        if(!$user){
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // On supprime le token
        $user->setActivationToken(null);
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();

        // On génère un message
        $this->addFlash('message', 'Utilisateur activé vous pouvez vous connecter!');

        // On retourne à l'accueil /home
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/pass-oubli", name="app_reset_password")
     */
    public function resetPassword (Request $request, UserRepository $userRepo, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $manager)
    {
        $form = $this->createForm(ResetPassType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //recuperation des données
            $donnees = $form->getData();
            //si un utilisateur à cette email
            $user = $userRepo->findOneByEmail($donnees['email']);
            //si l'utilisateur n'existe pas
            if(!$user) {
                $this->addFlash('danger', 'Cette adresse mail n\'existe pas');

                return $this->redirectToRoute('security_login');
            }
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $manager->persist($user);
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
                ->setTo($user->getEmail())
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
     * @Route("/pass-oubli/{token}", name="app_password")
     */
    public function newPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {
        // On cherche un utilisateur avec le token donné
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);

        // Si l'utilisateur n'existe pas
        if (!$user) {
            // On affiche une erreur
            $this->addFlash('danger', 'Token Inconnu');
            return $this->redirectToRoute('security_login');
        }

        // Si le formulaire est envoyé en méthode post
        if ($request->isMethod('POST')) {
            // On supprime le token
            $user->setResetToken(null);

            // On chiffre le mot de passe
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));

            // On stocke
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
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



