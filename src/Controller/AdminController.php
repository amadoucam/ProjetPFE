<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\Recruteur;
use App\Repository\RecruteurRepository;
use App\Form\RegistrationFormType;
use App\Form\RecruteurType;

use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\HttpFoundation\File\File;
//use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface as UserPasswordEncoderInterface;

class AdminController extends EasyAdminController
{
    // ...
    
    protected function persistUserEntity($user)
    {
        $encodedPassword = $this->encodePassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);

        parent::persistEntity($user);
    }

    protected function updateUserEntity($user)
    {
        $encodedPassword = $this->encodePassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);

        

        parent::updateEntity($user);
    }

    private function encodePassword($user, $password)
    {
        $passwordEncoderFactory = new EncoderFactory([
            User::class => new MessageDigestPasswordEncoder('sha512', true, 5000)
        ]);

        $encoder = $passwordEncoderFactory->getEncoder($user);

        return $encoder->encodePassword($password, $user->getSalt());
    }  

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * RecruteurController constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function persistEntity($entity)
    {
        $this->encoderPassword($entity);
        parent::persistEntity($entity);
    }

    public function updateEntity($entity)
    {
        $this->encoderPassword($entity);
        parent::updateEntity($entity);
    }

    public function encoderPassword($recruteur)
    {
        if (!$recruteur instanceof Recruteur) {
            return;
        }

        $recruteur->setPassword(
            $this->passwordEncoder->encodePassword($recruteur, $recruteur->getPassword())
        );
    }

    
    
}
