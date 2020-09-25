<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Concert;
use App\Entity\Reservation;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationsController extends AbstractController
{

    /**
     * @Route("/reservations/{userId}", name="reservationsList")
     */
    public function reservationsList($userId, Request $request, ObjectManager $manager)
    {
        $reservationRepo = $this->getDoctrine()->getRepository(Reservation::class);
        $reservations = $reservationRepo->findReservationsByUser($userId);

        return $this->render('reservations/reservationsList.html.twig', [
            'title' => 'Mes réservations',
            'reservations' => $reservations
        ]);
    }
    
}
