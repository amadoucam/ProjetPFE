<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConseilCarriereController extends AbstractController
{
    /**
     * @Route("/conseil-carriere", name="conseil_carriere")
     */
    public function index()
    {
        return $this->render('conseil_carriere/index.html.twig');
    }

    /**
     * @Route("/conseil-carriere/article/senior-organiser-son-cv-par-competence", name="conseil_senior")
     */
    public function conseilSenior()
    {
        return $this->render('conseil_carriere/conseil_senior.html.twig');
    }

    /**
     * @Route("/conseil-carriere/article/quel-cv-pour-une-candidature-spontanee", name="conseil_candidature")
     */
    public function conseilCandidature()
    {
        return $this->render('conseil_carriere/conseil_candidature.html.twig');
    }

    /**
     * @Route("/conseil-carriere/article/alternance-apprentissage-cv", name="conseil_exemple_cv")
     */
    public function conseilExempleCv()
    {
        return $this->render('conseil_carriere/conseil_exemple_cv.html.twig');
    }

}
