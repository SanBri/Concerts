<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Concert;
use App\Entity\Reservation;
use App\Entity\ChangePassword;
use App\Form\ConcertSearchType;
use App\Form\CreateConcertType;
use App\Form\ChangePasswordType;
use App\Form\CreateReservationType;
use App\Controller\EmailsController;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert; 
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ConcertsController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $user = $this->getUser();
        if ($user) {
        $userId = $user->getId();
        } else {
            $userId = NULL;
        }

        return $this->render('concerts/index.html.twig', [
            'controller_name' => 'ConcertsController',
            'title' => 'index',
            'userId' => $userId
        ]);
    }

    /**
     * @Route("/create", name="createConcert")
     * @Route("/edit/{id}/{organizerToken}", name="editConcert")
     */
    public function concertForm($organizerToken = null, Concert $concert = null, Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();

        if ($user) {
            $userRole = $user->getRole();
            $userId = $user->getId();
            $title = "Modifier les informations";

            if ( $userRole == 'Organizer' ) {
                if (!$concert) {
                    $concert = new Concert();
                    $title = "Ajouter un concert";
                    $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                    if (strpos($url, 'edit')) {
                        return $this->redirectToRoute('unknownConcert');
                    }
                } else {
                    $organizer = $concert->getOrganizer();
                    $organizerId = $organizer->getId(); 
                    $concertStatus = $concert->getStatus();
                    if ( $organizerId != $userId || !$this->isCsrfTokenValid('concert-organizer', $organizerToken)  ) {
                        return $this->redirectToRoute('error403');
                    } else if ( $concertStatus == "Annulé" ) {
                        return $this->redirectToRoute('canceledConcert');
                    }
                }
                $form = $this->createForm(CreateConcertType::class, $concert);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $concert->setStatus('À venir');
                    $concert->setOrganizer($user);
                    $manager->persist($concert);
                    $manager->flush();
                    return $this->redirectToRoute("showConcert", ["id" => $concert->getId()]);
                }
                return $this->render('concerts/concertForm.html.twig', [
                    'title' => $title,
                    'createForm' => $form->createView(),
                    'editMode' => $concert->getId() !== null,
                    'concertId' => $concert->getId()
                ]);
            } else {
                return $this->redirectToRoute('error403');
            }
        } else {
            return $this->redirectToRoute('login');
        }
    }

    public function isUserTheOrganizer($concertId, $organizerToken) {
        $user = $this->getUser();
        if ($user) {
            if ($concertId === null) {
                $resp = 'unknownConcert';
            } else {
                $concertRepo = $this->getDoctrine()->getRepository(Concert::class);
                $concert = $concertRepo->find($concertId); 
                $concertStatus = $concert->getStatus();
                if ( $concertStatus == "À venir" ) { 
                    $userId = $user->getId();
                    $organizer = $concert->getOrganizer();
                    $organizerId = $organizer->getId();
                    if ($userId == $organizerId && $this->isCsrfTokenValid('concert-organizer', $organizerToken)) {
                        $resp = true;
                    } else {
                        $resp = 'error403';
                    }
                } else {
                    $resp = 'canceledConcert';
                }
            }
        } else {
            $resp = 'login';
        }
        return $resp;
    }

    /**
     * @Route("/mes_concerts/{userId}", name="myConcertsList")
     */
    public function myConcertsList($userId) {
        $actualUser = $this->getUser();
        if ($actualUser) {
            $actualUserId = $actualUser->getId();
            $actualUserRole = $actualUser->getRole();
            if ( $actualUserRole == "Organizer" ) {
                if ( $actualUserId == $userId ) {
                    $concertRepo = $this->getDoctrine()->getRepository(Concert::class);
                    $comingConcerts = $concertRepo->findMyConcerts($userId, 'À venir');
                    $canceledConcerts = $concertRepo->findMyConcerts($userId, 'Annulé');
                    $pastConcerts = $concertRepo->findMyConcerts($userId, 'Passé');
                    return $this->render('concerts/myConcertsList.html.twig', [
                        'title' => 'Mes concerts',
                        'concerts' => $comingConcerts,
                        'canceledConcerts' => $canceledConcerts,
                        'pastConcerts' => $pastConcerts
                    ]);
                } else {
                    return $this->redirectToRoute('myConcertsList', ['userId' => $actualUserId] );
                }
            } else {
                return $this->redirectToRoute('error403');
            }
        } else {
            return $this->redirectToRoute('login');
        }
    }

    /**
     * @Route("/changement_date/{id}/{organizerToken}", name="changeConcertDate")
     */
    public function changeConcertDate(Concert $concert = null, $organizerToken, Request $request)
    { 
        if ($concert) {
            $concertId = $concert->getId();
        } else {
            $concertId = null;
        }
        $resp = $this->isUserTheOrganizer($concertId, $organizerToken);
        if ($resp === true) {
            $currentYear = date('Y');
            $form = $this->createFormBuilder($concert)
                        ->add('date', DateTimeType::class, [
                            'years' => range($currentYear, $currentYear+3),
                            'minutes' => array(0, 15, 30, 45),
                            'html5' => false,
                            'format' => 'yyyyMMMdd hh:ii',
                            'model_timezone' => "Europe/Paris"
                        ])
                        ->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid() ) {
                $formDataNewDate = $form->get('date')->getData();
                return $this->render('concerts/confirmChangeDate.html.twig', [
                    'title' => "Confirmation du report de l'événement",
                    'newDate' => $formDataNewDate,
                    'concert' => $concert,
                    'organizerToken' => $organizerToken
                ]);      
            }
            return $this->render('concerts/changeConcertDate.html.twig', [
                'title' => "Reporter cet événement",
                'concert' => $concert,
                'newDateForm' => $form->createView()
            ]);
        } else {
           return $this->redirectToRoute($resp);
        } 
    }

    /**
     * @Route("/changeDate/{id}/{organizerToken}/{newDate}/{confirmToken}", name="changeDate")
     */
    public function changeDate($newDate, $organizerToken, $confirmToken, Concert $concert = null, Request $request, ObjectManager $manager)
    { 
        if ($concert) {
            $concertId = $concert->getId();
        } else {
            $concertId = null;
        }
        $resp = $this->isUserTheOrganizer($concertId, $organizerToken);
        if ($resp === true) {
            if ( $this->isCsrfTokenValid($concertId, $confirmToken) ) {
                $newDate_dt = DateTime::createFromFormat('d-m-Y-h-i', $newDate);
                $newDate_stamp = $newDate_dt->getTimestamp();
                $newDateFormat = $newDate_dt->setTimestamp($newDate_stamp);
                $concert->setDate($newDateFormat);
                $manager->persist($concert);
                $manager->flush();
                return $this->redirectToRoute("showConcert", ['id' => $concert->getId()]);
            } else {
                return $this->redirectToRoute("changeConcertDate", ["id" => $concertId, "organizerToken" => $organizerToken]);
            }
        } else {
           return $this->redirectToRoute($resp);
        } 
    }

    /**
     * @Route("/concerts", name="concertsList")
     */
    public function concertsList(Request $request, ObjectManager $manager, PaginatorInterface $paginator)
    {
        $query = false;
        $title = "Liste des concerts";
        $concertRepo = $this->getDoctrine()->getRepository(Concert::class);
        $datas = $concertRepo->findComingConcerts('À venir');
        $concerts = $paginator->paginate($datas, $request->query->getInt('page', 1), 8);
        //* Actualise le statut des événements déjà passés :
        date_default_timezone_set("Europe/Paris");
        $date = time();
        foreach ($datas as $concert) {
            $concertDate = $concert->getDate();
            $concertDateTimestamp = $concertDate->getTimestamp();
            $concertId = $concert->getId();
            if ($concertDateTimestamp < $date) {
                $concert->setStatus('Passé');
                $manager->persist($concert);
                $reservationRepo = $this->getDoctrine()->getRepository(Reservation::class);
                $reservations = $reservationRepo->findAllReservationsForThisConcert($concertId); 
                foreach ($reservations as $reservation) {
                    $reservation->setStatus('Passé');
                    $manager->persist($reservation);
                }
                $manager->flush();
            }
        }
        $form = $this->createForm(ConcertSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $queryConcerts = $form->get('concertsQuery')->getData();
            $concerts = $concertRepo->findConcertSearch($queryConcerts);
            $title = ' Recherche de concerts : ' . $queryConcerts . '';
            $query = true;
        }
        
        return $this->render('concerts/concertsList.html.twig', [
            'title' => $title,
            'concertsSearchForm' => $form->createView(),
            'concerts' => $concerts,
            'query' => $query
        ]);
    }

    /**
     * @Route("/concert/{id}", name="showConcert")
     */
    public function show($id, Request $request, ObjectManager $manager, EmailsController $emailsCtlr)
    {
        
        $concertRepo = $this->getDoctrine()->getRepository(Concert::class);
        $concert = $concertRepo->find($id);
        if ($concert) {
            $reservation = new Reservation();
            $form = $this->createForm(CreateReservationType::class, $reservation);
            $form->handleRequest($request);
            $concertReservations = $concert->getReservation();
            $concertMaxPlaces = $concert->getMaxPlaces();
            $remainingPlaces = $concertMaxPlaces - $concertReservations;
            $concertName = $concert->getName();
            $notEnoughPlaces = NULL;

            if ($form->isSubmitted() && $form->isValid() ) {
                $formDatasPlaces = $form->get('reservedPlaces')->getData();
                if ($formDatasPlaces <= $remainingPlaces) {
                    $reservation->setUser($this->getUser())
                                ->setConcert($concert)
                                ->setMailSent(false)
                                ->setStatus('En cours');
                    $manager->persist($reservation);
                    $manager->flush();
                    $reservedPlaces = $reservation->getReservedPlaces();
                    $concert->setReservation($concertReservations + $reservedPlaces);
                    $manager->persist($concert);
                    $manager->flush();
                    return $this->redirectToRoute("sendReservationConfirmationEmail", ['reservationId' => $reservation->getId()]);
                } else {
                    $notEnoughPlaces = "Il n'y a pas assez de place disponible";
                }
            }
    
            return $this->render('concerts/showConcert.html.twig', [
                'title' => $concertName,
                'concert' => $concert,
                'reservationForm' => $form->createView(),
                'remainingPlaces' => $remainingPlaces,
                'currentDate' => date('d/m/Y'),
                'currentDay' => date('d'),
                'currentMonth' => date('m'),
                'currentYear' => date('Y'),
                'notEnoughPlaces' => $notEnoughPlaces
            ]);

        } else {
            return $this->redirectToRoute("unknownConcert");
        }

    }

    /**
     * @Route("/confirmation_annulation_concert/{id}/{organizerToken}", name="confirmCancelConcert")
     */
    public function confirmCancelConcert(Concert $concert = null, $organizerToken)
    {
        if ($concert) {
            $concertId = $concert->getId();
        } else {
            $concertId = null;
        }
        $resp = $this->isUserTheOrganizer($concertId, $organizerToken);
        if ($resp === true) {
            return $this->render('concerts/confirmCancelConcert.html.twig', [
                'title' => "Confirmation d'annulation d'événement",
                'concert' => $concert,
                'organizerToken' => $organizerToken
            ]);
        } else {
            return $this->redirectToRoute($resp);
        }
    }

    /**
     * @Route("/annulation_concert/{id}/{organizerToken}/{confirmToken}", name="cancelConcert")
     */
    public function cancelConcert(Concert $concert = null, $organizerToken, $confirmToken, ObjectManager $manager)
    {
        if ($concert) {
            $concertId = $concert->getId();
        } else {
            $concertId = null;
        }
        $resp = $this->isUserTheOrganizer($concertId, $organizerToken);
        if ($resp === true) {
            if ( $this->isCsrfTokenValid($concertId, $confirmToken) ) {
                $concert->setStatus('Annulé');
                $reservationRepo = $this->getDoctrine()->getRepository(Reservation::class);
                $reservations = $reservationRepo->findAllReservationsForThisConcert($concertId); 
                foreach ($reservations as $reservation) {
                    $reservation->setStatus('Annulé');
                    $manager->persist($reservation);
                }
                $manager->persist($concert);
                $manager->flush();
                return $this->redirectToRoute("showConcert", ["id" => $concert->getId()]);
            } else {
                return $this->redirectToRoute("confirmCancelConcert", ["id" => $concertId, "organizerToken" => $organizerToken]);
        }
        } else {
            return $this->redirectToRoute($resp);
        }
    }

}