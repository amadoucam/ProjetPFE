<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Contact $contact = null, Request $request, EntityManagerInterface $manager, \Swift_Mailer $mailer)
    {
        if (!$contact) {
               $contact = new Contact();
            }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        //$this->addFlash('info', 'Plus dinfo svp');

        if ($form->isSubmitted() && $form->isValid()) {

           $contactFormData = $form->getData();
           //dump($contactFormData);

          /* $message = (new \Swift_Message('You Got Mail!'))
                ->setFrom($contactFormData['email'])
                ->setTo('camaraamadou775@gmail.com')
                ->setBody(
                    $contactFormData['message'],
                    'text/plain'
                )
            ; */
            $manager->persist($contact);
            $manager->flush();

            $message = (new \Swift_Message('Nouveau compte'))
                // On attribue l'expéditeur
                ->setFrom('camaraamadou775@gmail.com')
                // On attribue le destinataire
                ->setTo($contactFormData->getEmail())
                // On crée le texte avec la vue
                ->setBody(
                    $contactFormData->getMessage(),
                    'text/html'
                )
            ;

            $mailer->send($message);

            $this->addFlash('success', 'Votre message à été Envoyez avec succes!');

            return $this->redirectToRoute('contact');
        
        }

        return $this->render('contact/index.html.twig', [
            'our_form' => $form->createView(),
        ]);
    }
}
