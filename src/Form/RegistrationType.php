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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TelType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
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
            ->add('adresse', TextType::class)
            ->add('tel',TelType::class)
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
            ->add('level', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
