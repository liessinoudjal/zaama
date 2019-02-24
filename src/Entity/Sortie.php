<?php

namespace App\Entity;

use App\Entity\FOSUserBundle\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SortieRepository")
 */
class Sortie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FOSUserBundle\User", inversedBy="sorties")
     *
     */
    private $organisateur;

     /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $dateSortie;
    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $portable;

     /**
     * @ORM\Column(type="integer", nullable=false, length=2)
     * @Assert\Range(
     *      min = 0,
     *      max = 23,
     *      minMessage = "Heure minimumm {{ limit }}h",
     *      maxMessage = "Heure maximum {{ limit }}h")
     */
    private $heure;

    /**
     * @ORM\Column(type="integer", nullable=false, length=2)
     * @Assert\Range(
     *      min = 0,
     *      max = 59,
     *      minMessage = "Minute minimumm {{ limit }}h",
     *      maxMessage = "Minute maximum {{ limit }}h")
     */
    private $minute;


      /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $heureSortie;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;


    /**
     * @ORM\Column(type="string", length=80)
     */
    private $intitule;

     /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $presentation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeSortie", inversedBy="sorties")
     *
     */
    private $typeSortie;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $entreFille;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(
     *      min = 2,
     *      max = 20,
     *      minMessage = "Nombre de participants minimum {{ limit }}",
     *      maxMessage = "Nombre de participants maximum {{ limit }}")
     */
    private $nbPersonneMax;

    /**
     * @ORM\Column(type="boolean", nullable= true)
     */
    private $statut;

    public function __construct()
    {
        $this->statut= false;
        $this->createdAt= new \DateTime();
        $this->setNbPersonneMax(2);
        $this->setHeure(0);
        $this->setMinute(0);
    }

    public function __toString()
    {
        return empty($this->getIntitule())? "Sortie": "Sortie : ". $this->getIntitule();
    }
    public function getId(): ?int
    {
        return $this->id;
    }


    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(User $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }


    public function getPortable(): ?string
    {
        return $this->portable;
    }

    public function setPortable(?string $portable): self
    {
        $this->portable = $portable;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }


    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getTypeSortie(): ?TypeSortie
    {
        return $this->typeSortie;
    }

    public function setTypeSortie(?TypeSortie $typeSortie): self
    {
        $this->typeSortie = $typeSortie;

        return $this;
    }

    public function getEntreFille(): ?bool
    {
        return $this->entreFille;
    }

    public function setEntreFille(?bool $entreFille): self
    {
        $this->entreFille = $entreFille;

        return $this;
    }

    public function getNbPersonneMax(): ?int
    {
        return $this->nbPersonneMax;
    }

    public function setNbPersonneMax(?int $nbPersonneMax): self
    {
        $this->nbPersonneMax = $nbPersonneMax;

        return $this;
    }

    public function getStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get the value of createdAt
     */ 
    public function getCreatedAt()
    {
        return $this->createdAt;
    }


    /**
     * Get the value of dateSortie
     */ 
    public function getDateSortie()
    {
        return $this->dateSortie;
    }

    /**
     * Set the value of dateSortie
     *
     * @return  self
     */ 
    public function setDateSortie($dateSortie)
    {
        $this->dateSortie = $dateSortie;

        return $this;
    }

    /**
     * @return string
     * Get the value of presentation
     */ 
    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    /**
     * Set the value of presentation
     *
     * @return  self
     */ 
    public function setPresentation($presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }


    /**
     * Get min = 0,
     */ 
    public function getMinute() :?string
    {
        return $this->formatTimeTwoChars($this->minute);
    }

    /**
     * Set min = 0,
     *
     * @return  self
     */ 
    public function setMinute(?int $minute)
    {
        $this->minute = $minute;

        return $this;
    }

    /**
     * Get min = 0,
     */ 
    public function getHeure() : ?string
    {
        return $this->formatTimeTwoChars($this->heure);
    }

    /**
     * Set min = 0,
     *
     * @return  self
     */ 
    public function setHeure(?int $heure)
    {
        $this->heure = $heure;

        return $this;
    }

    public function getHeureSortie() :string
    {
            $this->setHeureSortie();
            return $this->heureSortie;
    }

    public function setHeureSortie($heure = null){
        $this->heureSortie = $this->getHeure().":".$this->getMinute();
    }

    private function formatTimeTwoChars(?int $number) : string
    {
        if(intval($number) < 10 )
            $number = "0".$number;
            return (string) $number;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */ 
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
