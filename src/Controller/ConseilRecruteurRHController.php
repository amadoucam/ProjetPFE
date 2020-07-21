<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConseilRecruteurRHController extends AbstractController
{
    /**
     * @Route("/conseil-recruteur", name="conseil_recruteur")
     */
    public function accueil()
    {
        return $this->render('conseil_recruteur_rh/index.html.twig');
    }

    /**
     * @Route("/conseils-recrutement/trouver-candidats/6-choses-a-faire-apres-la-publication-de-l-offre-d-emploi", name="conseil_rh")
     */
    public function conseilRH()
    {
        return $this->render('conseil_recruteur_rh/conseil_rh.html.twig');
    }

    /**
     * @Route("/conseils-recrutement/entretien-6-idees-pour-ameliorer-lexperience-candidat", name="conseil_idees")
     */
    public function conseilRecruteur()
    {
        return $this->render('conseil_recruteur_rh/conseil_idees.html.twig');
    }

    /**
     * @Route("/conseils-recrutement/5-bonnes-raisons-de-recruter-des-collaborateurs-en-situation-de-handicap", name="conseil_5_raison")
     */
    public function conseilRaison()
    {
        return $this->render('conseil_recruteur_rh/conseil_5_raison.html.twig');
    }

    /**
     * @Route("/conseils-recrutement/respect-de-la-vie-privee-des-candidats-obligation-legale-et-confort-de-travail", name="conseil_vie_privee")
     */
    public function conseilVie()
    {
        return $this->render('conseil_recruteur_rh/conseil_vie_privee.html.twig');
    }

}
