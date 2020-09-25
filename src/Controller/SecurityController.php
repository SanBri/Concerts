<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegisterType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="register")
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRole('User');
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('concertsList');
        }

        return $this->render('security/register.html.twig', [
            'title' => 'Inscription',
            'registerForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig', [
            'title' => 'Connexion',
            'controller_name' => 'SecurityController'
        ]);
    }

    /**
     * @Route("/deconnexion", name="logout")
     */
    public function logout(){}
}
