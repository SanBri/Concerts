<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Concert;
use App\Entity\Reservation;
use App\Form\CreateConcertType;
use App\Form\CreateReservationType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     */
    public function createConcert(Request $request, ObjectManager $manager)
    {
        $concert = new Concert();
        $form = $this->createForm(CreateConcertType::class, $concert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $concert->setStatus('À venir');
            $manager->persist($concert);
            $manager->flush();
            return $this->redirectToRoute('concertsList');
        }

        return $this->render('concerts/createConcert.html.twig', [
            'title' => 'Ajouter un concert',
            'createForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/concerts", name="concertsList")
     */
    public function concertsList()
    {
        
        $status = 'À venir';
        $concertRepo = $this->getDoctrine()->getRepository(Concert::class);
        $concerts = $concertRepo->findComingConcerts($status);
        
        return $this->render('concerts/concertsList.html.twig', [
            'title' => 'Liste des concerts',
            'concerts' => $concerts
        ]);
    }

    /**
     * @Route("/concert/{id}", name="showConcert")
     */
    public function show($id, Request $request, ObjectManager $manager)
    {
        
        $concertRepo = $this->getDoctrine()->getRepository(Concert::class);
        $concert = $concertRepo->find($id);
        $reservation = new Reservation();
        $form = $this->createForm(CreateReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {
            $reservation->setUser($this->getUser())
                        ->setConcert($concert);
            $manager->persist($reservation);
            $manager->flush();
            $concert->setReservation($concert->reservation + $reservation->reservedPlaces);
            $manager->persist($concert);
            $manager->flush();
            return $this->redirectToRoute("reservationsList");
        }

        if ($concert) {
            return $this->render('concerts/showConcert.html.twig', [
                'concert' => $concert,
                'reservationForm' => $form->createView(),
                'title' => $concert->name,
                'currentDate' => date('d/m/Y'),
                'currentDay' => date('d'),
                'currentMonth' => date('m'),
                'currentYear' => date('Y')
            ]);
        } else {
            return $this->render('errors/unknownConcert.html.twig', [
                'title' => 'Concert introuvable'
                ]);
        }
    }


}


