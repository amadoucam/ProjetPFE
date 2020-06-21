<?php

namespace App\Entity;

use App\Repository\AdministrateurRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\AdministrateurRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="L'email indiqué est déjà utilisé"
 * )
 */
class Administrateur implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8", minMessage="Votre mot de passe doit faire minimum 8 caractere")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password",message="Vous n'avez pas taper le meme mot de passe")
     */
    public $confirm_password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     *
     */
    public function eraseCredentials(){}

    /**
     * @return string|void|null
     */
    public function getSalt(){}

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface     
     */
    public function getUsername(): string 
    {
        return $this->email;
    }

    /**
     * @return array|string[]
     */
    public function getRoles(){
        return ['ROLE_SUPER_ADMIN'];
    }

    public function __toString()
    {
        return $this->email;
    }

}
