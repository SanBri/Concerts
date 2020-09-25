<?php

namespace App\Form;

use App\Entity\Concert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class CreateConcertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentYear = date('Y');
        $currentMonth = date('M');
        $currentDay = date('d');
        $builder
            ->add('name')
            ->add('description')
            ->add('date', DateType::class, [
                'format' => 'ddMMMyyyy',
                'years' => range($currentYear, $currentYear+3),
            ])
            ->add('city')
            ->add('adress')
            ->add('price', IntegerType::class)
            ->add('maxPlaces', IntegerType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Concert::class,
        ]);
    }
}
