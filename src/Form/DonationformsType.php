<?php

namespace App\Form;
use App\Entity\Donationforms;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DonationformsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant')
            ->add('email')
            ->add('nom')
            ->add('prenom')
            ->add('ville', ChoiceType::class, [
                'choices' => [
                    'Tunis' => 'tunis',
                    'Sfax' => 'sfax',
                    'Sousse' => 'sousse',
                    'Kairouan' => 'kairouan',
                    'Bizerte' => 'bizerte',
                    'Gabès' => 'gabes',
                    'Ariana' => 'ariana',
                    'Gafsa' => 'gafsa',
                    'La Marsa' => 'la_marsa',
                    'Ben Arous' => 'ben_arous',
                    'Monastir' => 'monastir',
                    'Médenine' => 'medenine',
                    'Nabeul' => 'nabeul',
                    'Tataouine' => 'tataouine',
                    'Hammamet' => 'hammamet',
                    'Mahdia' => 'mahdia',
                    'Kasserine' => 'kasserine',
                    'Djerba' => 'djerba',
                    'Siliana' => 'siliana',
                    'Tozeur' => 'tozeur',
                    'Kébili' => 'kebili',
                    'Béja' => 'beja',
                    'Le Kef' => 'le_kef',
                    'Jendouba' => 'jendouba',
                ],
                'placeholder' => 'Sélectionner une ville',
            ])
            
           
            ->add('carteBancaire');
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Donationforms::class,
        ]);
    }
}
