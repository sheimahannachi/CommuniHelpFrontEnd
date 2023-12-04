<?php

namespace App\Form;

use App\Entity\Listec;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListecType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            ->add('nomproduit')
            ->add('contact')
            ->add('nomdest')
            ->add('emailc_')
            ->add('adressec_')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Listec::class,
        ]);
    }
}
