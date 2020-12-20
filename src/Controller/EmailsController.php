<?php

namespace App\Controller;

use App\Entity\Reservation;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmailsController extends AbstractController
{

    /**
     * @Route("/email_confirmation_reservation/{reservationId}/", name="sendReservationConfirmationEmail")
     */
    public function sendConfirmationReservationEmail($reservationId, \Swift_Mailer $mailer, ObjectManager $manager)
    {
        $reservationRepo = $this->getDoctrine()->getRepository(Reservation::class);
        $reservation = $reservationRepo->find($reservationId);
        if ($reservation) {
            $mailAlreadySent = $reservation->getStatus();
            if ($mailAlreadySent != 'Confirmé') {
                $user = $this->getUser();
                $userEmail = $user->getUsername();
                $email = (new \Swift_Message('Confirmation réservation'))
                        ->setFrom('edelwevents@gmail.com')
                        ->setTo($userEmail)
                        ->setBody($this->renderView('emails/reservationConfirmation.html.twig'));
                $mailer->send($email); 
                $reservation->setStatus('Confirmé');
                $manager->persist($reservation);
                $manager->flush();
                return $this->redirectToRoute("reservationsList", ['userId' => $user->getId()]);
            } else {
                return $this->redirectToRoute("error403");
            }
        } else {
            return $this->redirectToRoute("error404");
        }
    }
}
