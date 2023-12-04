<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\Base64ToUploadedFileTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('ville', ChoiceType::class, [
            'choices' => ['Tunis' => 'tunis',
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
                    ->add('description')
            ->add('contact')
            ->add('creationDate')
            ->add('image', FileType::class, [
                'label' => 'Image (JPEG, PNG, or GIF file)',
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'mapped' => false, // Do not map the field with an entity property
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Valider',
            ]);

        $builder->get('image')->addModelTransformer(new class() implements DataTransformerInterface {
            public function transform($value)
            {
                // Transform the value from your database to a format usable by the form field
                return null; // Modify this according to your data
            }

            public function reverseTransform($value)
            {
                // Transform the value from the form to the value saved in your database
                return $value; // Modify this according to your data
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
