<?php

namespace App\Form;

use App\Entity\History;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HistoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('articleId')
            ->add('articleVille')
            ->add('description')
            ->add('contact')
            ->add('image')
            ->add('donationId')
            ->add('montant')
            ->add('email')
            ->add('nom')
            ->add('prenom')
            ->add('donationVille')
            ->add('carteBancaire')
            ->add('article')
            ->add('donationform')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => History::class,
        ]);
    }
}
