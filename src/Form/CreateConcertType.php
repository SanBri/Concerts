<?php

namespace App\Form;

use App\Entity\Concert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class CreateConcertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $currentYear = date('Y');
        $builder
            ->add('name')
            ->add('description')
            ->add('city')
            ->add('adress')
            ->add('price', IntegerType::class)
            ->add('maxPlaces', IntegerType::class)
        ;
        if ( strpos($url,'edit') === false ) { 
            $builder
                ->add('date', DateTimeType::class, [
                'html5' => false,
                'date_format' => 'dd MMM yyyy',
                'model_timezone' => 'Europe/Paris',
                'years' => range($currentYear, $currentYear+3),
                'minutes' => array(0, 15, 30, 45)
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Concert::class,
        ]);
    }
}
