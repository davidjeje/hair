<?php

namespace App\Form;

use App\Entity\Paginator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaginatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page')
<<<<<<< HEAD
            ->add('nbPage')
            ->add('nameRoute')
=======
            ->add('nbPages')
            ->add('nomRoute')
>>>>>>> paginator
            ->add('paramsRoute')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Paginator::class,
        ]);
    }
}
