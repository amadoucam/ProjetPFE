<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('username')
            ->add('password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)
            ->add('nom')
            ->add('prenom')
            ->add('civility', ChoiceType::class,[
                'choices' => [
                    'monsieur' => 'monsieur',
                    'madame' => 'madame',
                    'autre' => 'autre',
                ],
            ])
            ->add('datenaissance', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('nationalite', CountryType::class)
            ->add('postal_code', NumberType::class)
            ->add('adresse')
            ->add('tel')
            ->add('avatar', FileType::class)
            ->add('cv', FileType::class, [
                'label' => 'Document (PDF file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '500000k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez charger un fichier PDF valide',
                    ])
                ],
            ])
            ->add('level', ChoiceType::class, [
                'choices' => [
                    '-- exple: bac +1' => '',
                    'Ingenieur' => 'ingenieur',
                    'Secondaire' => 'secondaire',
                    'Formations professionnelles' => 'formation professionnelle',
                    'Bac' => 'bac',
                    'Bac +1' => 'bac +1',
                    'Bac +2' => 'bac +2',
                    'Bac +3' => 'bac +3',
                    'Bac +4' => 'bac +4',
                    'Bac +5' => 'bac +5',
                    'Doctorat' => 'doctorat'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
