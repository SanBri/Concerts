<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Concert;
use App\Entity\Reservation;
use Swift_Mailer;
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
            $reservations = $reservationRepo->findReservationsByUser($userId, 'Confirmé');
            $canceledReservations = $reservationRepo->findReservationsByUser($userId, 'Annulé');
            $pastReservations = $reservationRepo->findReservationsByUser($userId, 'Passé');
            return $this->render('reservations/reservationsList.html.twig', [
                'title' => 'Mes réservations',
                'reservations' => $reservations,
                'canceledReservations' => $canceledReservations,
                'pastReservations' => $pastReservations
            ]);
        } else {
            return $resp;
        }
    }

    public function isTheSameUser($route, $userId) {
        $actualUser = $this->getUser();
        if ($actualUser) {
            $actualUserId = $actualUser->getId();
            if ($userId == $actualUserId) {
                $resp = true;
            } else {
                $resp = $this->redirectToRoute($route, ['userId' => $actualUserId]);
            }
        } else {
            $resp = $this->redirectToRoute("login");
        }
        return $resp;
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
    public function cancelReservation($reservationId, $submittedToken, ObjectManager $manager, Swift_Mailer $mailer)
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
            $message = (new \Swift_Message('Votre réservation a été annulée'))
                ->setFrom('edelwevents@gmail.com')
                ->setTo('sandro.brignoli@outlook.fr')
                ->setBody('Votre réservation a bien été annulée, merci. ', 'text/html');
            $mailer->send($message);
            return $this->render('errors/success.html.twig', [
                'title' => 'Confirmation',
                'message' => 'Votre réservation a bien été annulée'
            ]);
        } else {
            return $this->redirectToRoute($resp);
        }
    }
}