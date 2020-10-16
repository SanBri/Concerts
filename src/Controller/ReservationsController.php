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
    public function reservationsList($userId)
    {
        $resp = $this->isTheSameUser('reservationsList', $userId);
        if ($resp === true) {
            $reservationRepo = $this->getDoctrine()->getRepository(Reservation::class);
            $reservations = $reservationRepo->findReservationsByUser($userId, 'À venir');
            return $this->render('reservations/reservationsList.html.twig', [
                'title' => 'Mes réservations',
                'reservations' => $reservations
            ]);
        } else {
            return $resp;
        }
    }

    public function isUserAuthorisedToCancelReservation($reservationId, $submittedToken) {
        $actualUser = $this->getUser();
        if ($actualUser != NULL) {
                $actualUserId = $actualUser->getId();
                $reservationRepo = $this->getDoctrine()->getRepository(Reservation::class);
                $reservation = $reservationRepo->find($reservationId);
                $reservationStatus = $reservation->getStatus();
            if ($reservation && $reservationStatus == 'Confirmé') {
                $reservationUser = $reservation->getUser();
                $reservationUserId = $reservationUser->getId();
                if ($reservationUserId == $actualUserId && $this->isCsrfTokenValid('cancel-reservation', $submittedToken) ) {
                    $resp = true;
                } else {
                    $resp = "error403";
                }
            } else {
                $resp = "error404";
            }
        } else {
            $resp = "login";
        }
        return $resp;
    }

    /**
     * @Route("/confirmation_annulation_reservation/{reservationId}/{submittedToken}", name="confirmReservationCancel")
     */
    public function confirmReservationCancel($reservationId, $submittedToken)
    {
        $resp = $this->isUserAuthorisedToCancelReservation($reservationId, $submittedToken);
        if ($resp === true) {
            return $this->render('reservations/confirmReservationCancel.html.twig', [
                'title' => 'Confirmation',
                'reservationId' => $reservationId,
                'submittedToken' => $submittedToken
            ]);
        } else {
            return $this->redirectToRoute($resp);
        }
    }

    /**
     * @Route("/annulation_reservation/{reservationId}/{submittedToken}", name="cancelReservation")
     */
    public function cancelReservation($reservationId, $submittedToken, ObjectManager $manager)
    {
        $resp = $this->isUserAuthorisedToCancelReservation($reservationId, $submittedToken);
        if ($resp === true) {
            $reservationRepo = $this->getDoctrine()->getRepository(Reservation::class);
            $reservation = $reservationRepo->find($reservationId);
            $concert = $reservation->getConcert();
            $concertReservations = $concert->getReservation();
            $reservedPlaces = $reservation->getReservedPlaces();
            $concert->setReservation($concertReservations - $reservedPlaces);
            $reservation->setStatus('Annulé');
            $manager->persist($reservation);
            $manager->flush();
            return $this->render('errors/success.html.twig', [
                'title' => 'Confirmation',
                'message' => 'Votre réservation a bien été annulée'
            ]);
        } else {
            return $this->redirectToRoute($resp);
        }
    }
}