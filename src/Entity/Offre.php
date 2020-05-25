<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OffreRepository")
 */
class Offre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="10", max="255", minMessage="Minimum 10 caractères")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min="10", minMessage="Minimum 10 caractères Votre contenu est court")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url()
     */
    private $image;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categorie", inversedBy="offres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Recruteur", inversedBy="offres")
     */
    private $recruteur;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Postuler", mappedBy="offre")
     */
    private $postulers;

    public function __construct()
    {
        $this->postulers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getRecruteur(): ?Recruteur
    {
        return $this->recruteur;
    }

    public function setRecruteur(?Recruteur $recruteur): self
    {
        $this->recruteur = $recruteur;

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }

    /**
     * @return Collection|Postuler[]
     */
    public function getPostulers(): Collection
    {
        return $this->postulers;
    }

    public function addPostuler(Postuler $postuler): self
    {
        if (!$this->postulers->contains($postuler)) {
            $this->postulers[] = $postuler;
            $postuler->setOffre($this);
        }

        return $this;
    }

    public function removePostuler(Postuler $postuler): self
    {
        if ($this->postulers->contains($postuler)) {
            $this->postulers->removeElement($postuler);
            // set the owning side to null (unless already changed)
            if ($postuler->getOffre() === $this) {
                $postuler->setOffre(null);
            }
        }

        return $this;
    }

}
