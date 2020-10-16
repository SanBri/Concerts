<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ErrorsController extends AbstractController
{
    /**
     * @Route("/unknown", name="error404")
     */
    public function error404()
    {
        return $this->render('errors/error404.html.twig', [
            'title' => 'Erreur 404',
        ]);
    }

    /**
     * @Route("/forbidden", name="error403")
     */
    public function error403()
    {
        return $this->render('errors/error403.html.twig', [
            'title' => 'Erreur 403',
        ]);
    }

    /**
     * @Route("/unknown_concert", name="unknownConcert")
     */
    public function unknownConcert()
    {
        return $this->render('errors/unknownConcert.html.twig', [
            'title' => 'Concert introuvable',
        ]);
    }

    /**
     * @Route("/concert_annulé", name="canceledConcert")
     */
    public function canceledConcert()
    {
        return $this->render('errors/canceledConcert.html.twig', [
            'title' => 'Concert annulé',
        ]);
    }

    
}
