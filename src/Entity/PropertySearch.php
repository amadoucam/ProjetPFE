<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class PropertySearch
{

/**
* @ORM\Column(type="string", nullable=true)
*/
private $title;

/**
* @ORM\Column(type="string", length=50, nullable=true)
*/
private $ville;

/**
 * @ORM\ManyToOne(targetEntity="App\Entity\Categorie", inversedBy="offres")
 */
private $categorie;  

 public function getTitle(): ?string
 {
    return $this->title;
 }

 public function setTitle(string $title): self
 {
    $this->title = $title;
    return $this;
 }

 public function getVille(): ?string
 {
    return $this->ville;
 }

 public function setVille(string $ville): self
 {
    $this->ville = $ville;
    return $this;
 }

 public function getCategorie(): ?string
 {
    return $this->categorie;
 }

 public function setCategorie(string $categorie): self
 {
    $this->categorie = $categorie;
    return $this;
 }

}