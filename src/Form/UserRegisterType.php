<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        if ( strpos($url,'inscription') ) { 
            $builder
                ->add('email', EmailType::class)
                ->add('password', PasswordType::class)
                ->add('confirmPassword', PasswordType::class)
            ;
        } else if ( strpos($url,'modifier_mot_de_passe') ) {
            $builder 
                ->add('confirmPassword', PasswordType::class)
                ->add('newPassword', PasswordType::class)
                ->add('confirmNewPassword', PasswordType::class)
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
