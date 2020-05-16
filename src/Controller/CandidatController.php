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
        $listformation = $em->getRepository(formation::class)->findBy(array('user' => $user));
        $langue = $em->getRepository(langue::class)->findBy(array('user'=>$user));
        $experience = $em->getRepository(experience::class)->findBy(array('user' => $user));
        
        return $this->render('candidat/index.html.twig',array('listformation'=>$listformation,
            'langue'=>$langue,
            'experience'=>$experience
            ));
    }

     /**
     * @Route("/cvcandidat/{id}", name="cv_candidat")
     */
    public function cvCandidat ($id)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $repository = $this->getDoctrine()->getManager()->getRepository(user::class);
        $user = $repository->find($id);

        $em = $this->getDoctrine()->getManager();
        $listformation = $em->getRepository(formation::class)->findBy(array('user' => $user));
        $langue = $em->getRepository(langue::class)->findBy(array('user'=>$user));
        $experience = $em->getRepository(experience::class)->findBy(array('user' => $user));

        $html = $this->renderView('candidat/pdf.html.twig',array('listformation'=>$listformation,
            'langue'=>$langue,
            'experience'=>$experience
            ));
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);  
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
     * @Route("/supprimeformation/{id}/{id_user}", name ="supprimerFormation")
     */

    public function  supprimerinformation($id,$id_user){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $repository = $this->getDoctrine()->getRepository(formation::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em ->remove($repository);
        $em->flush();
        return $this->redirectToRoute('candidat', ['id' => $id_user]);

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

    /**
     * @Route("/supprimelangue/{id}/{id_user}", name ="supprimerlangue")
     */

    public function  supprimerlangue($id, $id_user){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $repository = $this->getDoctrine()->getRepository(langue::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em ->remove($repository);
        $em->flush();
        return $this->redirectToRoute('candidat', ['id' => $id_user]);

    }

    /**
     * @Route("/supprimerexperionce/{id}/{id_user}", name ="supprimerexperionce")
     */
    public function  supprimerexperionce($id,$id_user){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $repository = $this->getDoctrine()->getRepository(experience::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($repository);
        $em->flush();
        return $this->redirectToRoute('candidat', ['id' => $id_user]);

    }

    /**
     * @Route("/cv/{id}", name="cv")
     */
    public function cv($id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $repository = $this->getDoctrine()->getManager()->getRepository(user::class);
        $user = $repository->find($id);

        $em = $this->getDoctrine()->getManager();
        $listformation = $em->getRepository(formation::class)->findBy(array('user' => $user));
        $langue = $em->getRepository(langue::class)->findBy(array('user'=>$user));
        $experience = $em->getRepository(experience::class)->findBy(array('user' => $user));
        return $this->render('admin/showCv.html.twig',array('listformation'=>$listformation,
            'langue'=> $langue,
            'experience'=> $experience,
            'user'=> $user
        ));
    }

    /**
     * @Route("/", name="home")
     */
    public function home(){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('home.html.twig');
    }

}

/*telecharger pdf
$pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $repository = $this->getDoctrine()->getManager()->getRepository(user::class);
        $user = $repository->find($id);

        $em = $this->getDoctrine()->getManager();
        $listformation = $em->getRepository(formation::class)->findBy(array('user' => $user));
        $langue = $em->getRepository(langue::class)->findBy(array('user'=>$user));
        $experience = $em->getRepository(experience::class)->findBy(array('user' => $user));

        $html = $this->renderView('candidat/pdf.html.twig',array('listformation'=>$listformation,
            'langue'=>$langue,
            'experience'=>$experience
            ));
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);  
*/
