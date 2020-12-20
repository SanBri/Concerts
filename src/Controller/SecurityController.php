<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ChangePassword;
use App\Form\UserRegisterType;
use App\Form\ChangePasswordType;
use App\Form\ResetPasswordType;
use Swift_Mailer;
use Doctrine\Persistence\ObjectManager;
use App\Controller\ReservationsController;
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

    /**
     * @Route("/mon_compte/{userId}", name="myAccount")
    */
    public function myAccount($userId)
    {
        $resp = $this->isTheSameUser('myAccount', $userId); // Fonction dans AbstractController
        if ($resp === true) {
            return $this->render('security/myAccount.html.twig', [
                'title' => 'Mon compte',
            ]);
        } else {
            return $resp;
        }
    }


    /**
     * @Route("/modifier_mot_de_passe/{userId}/", name="passwordChange")
    */
    public function passwordChange($userId, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder) {

        $resp = $this->isTheSameUser('passwordChange', $userId); // Fonction dans AbstractController
        if ($resp === true) {  
            $changePassword = new ChangePassword();
            $user = $this->getUser();
            $form = $this->createForm(ChangePasswordType::class, $changePassword);
            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ) {
                $formActualPasswordData = $form->get('password')->getData();
                $userPassword = $user->getPassword();
                $passwordChecked = password_verify($formActualPasswordData, $userPassword);
                if ($passwordChecked === true) { 
                    $formNewPasswordData = $form->get('newPassword')->getData();
                    $hash = $encoder->encodePassword($user, $formNewPasswordData);
                    $user->setPassword($hash);
                    $manager->persist($user);
                    $manager->flush();   
                    return $this->render('errors/success.html.twig', [
                        'title' => 'Confirmation',
                        'message' => "Votre mot de passe a bien été modifié"
                    ]); 
                } else {
                    $this->addFlash('wrongPassword', 'Le mot de passe est incorrect');
                }
            }

            return $this->render('security/passwordChange.html.twig', [
                'title' => 'Modifier mon mot de passe',
                'passwordChangeForm' => $form->createView(),
            ]);
        } else { 
            return $resp;
        }
    }

    /**
     * @Route("/oubli_mot_de_passe", name="forgetPassword")
    */
    public function forgetPassword(Request $request, Swift_Mailer $mailer, ObjectManager $manager, UserPasswordEncoderInterface $encoder) {

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request); 
        $notUser = null; 
        $isUser = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $formDataUserEmail = $form->get('userEmail')->getData();
            $concertRepo = $this->getDoctrine()->getRepository(User::class);
            $user = $concertRepo->findOneBy(array('email' => $formDataUserEmail)); 
            if ($user) {
                $userEmail = $user->getEmail();
                $newPassword = random_int(10000000, 99999999);
                $hashedPassword = $encoder->encodePassword($user, $newPassword);
                $user->setPassword($hashedPassword);
                $manager->persist($user);
                $manager->flush();   
                $email = (new \Swift_Message('Mot de passe réinitialisé'))
                ->setFrom('edelwevents@gmail.com')
                ->setTo($userEmail)
                ->setBody($this->renderView('emails/resetPasswordEmail.html.twig', [
                    'newPassword' => $newPassword,
                ]));
                $mailer->send($email); 
                $isUser = true;
            } else {
                $notUser = "Ce compte n'existe pas";
            }
        }

        return $this->render('security/passwordForgotten.html.twig', [
            'title' => 'Oubli de mot de passe',
            'userEmailForm' => $form->createView(),
            'isUser' => $isUser,
            'notUser' => $notUser,
        ]);
    }
}


