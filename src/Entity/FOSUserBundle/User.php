<?php
// src/Entity/User.php

namespace App\Entity\FOSUserBundle;

use App\Entity\Profile;
use App\Entity\Sortie;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 *  @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap( {"usersimple" = "UserSimple", "user" = "User"} )
 * 
 */
 class User extends BaseUser
{

    /**
     * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    protected $id;
    /**
     *  @ORM\Column(name="sexe",type="string", nullable=false)
    *  @Assert\NotBlank()
    */
    protected $sexe;

    /**
     *  @ORM\Column(name="prenom",type="string", nullable=false)
    *  @Assert\NotBlank()
    */
    protected $prenom;

    
    /**
     *  @ORM\Column(name="date_birth",type="datetime", nullable=false)
    *  @Assert\NotBlank()
    *  @Assert\DateTime()
    */
    protected $dateBirth;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profile", cascade={"persist"})
    * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
    */
    protected $profile;

    /**
     *  @ORM\Column(name="date_inscription",type="datetime", nullable=false)
    *  @Assert\NotBlank()
    *  @Assert\DateTime()
    */
    protected $dateInscription;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="organisateur")
     */
    private $sorties;

    /**
    *  @ORM\Column(name="last_activity",type="datetime", nullable=true)
    *  @Assert\DateTime()
    */
    protected $lastActivity;

    public function __construct()
    {
        parent::__construct();
        $this->dateInscription= new \DateTime('now',new \DateTimeZone('Europe/Paris'));
        $this->sorties = new ArrayCollection();
    }
    
    /**
     * @return \DateTime
    */
    public function getDateInscription()
    {
        return $this->dateInscription;
    }

    public function getSexe()
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe)
    {
        $this->sexe = $sexe;

        return $this;
    }
    /**
     * @return boolean
    */
    public function isHomme(){
        return $this->sexe === "H";
    }

    /**
     * @return string
    */
    public function getPrenom()
    {
        return $this->prenom;
    }
    /**
     * @param string
    */
    public function setPrenom(string $prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }


    /**
     * Get the value of dateBirth
    */ 
    public function getDateBirth()
    {
        return $this->dateBirth;
    }

    /**
     * Set the value of dateBirth
    *
    * @return  self
    */ 
    public function setDateBirth($dateBirth)
    {
        $this->dateBirth = $dateBirth;
        return $this;
    }

    public function getAge(): ?int
    {
        $date_naissance=$this->getDateBirth();
        $today=new \DateTime();
        $diff= $date_naissance->diff($today);
        $annees= intval($diff->format("%Y"));
        return $annees;
    }
    public function isMajeur() : bool
    {
        return $this->getAge()>=18;
    }

    /**
     * @Assert\Callback
    */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (!$this->isMajeur() ) {
            $context->buildViolation('Vous devez etre majeur pour vous incrire.')
                ->atPath('dateBirth')
                ->addViolation();
        }
        if(!preg_match("((?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,})",$this->getPlainPassword())){
            $context->buildViolation('Votre mot de passe doit contenir au moins 8 caractères alpha numeriques (chiffre + minuscule + majuscule)')
            ->atPath('plainPassword')
            ->addViolation();
        }
        //plainPassword  ((?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,})
    }


    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }


    /**
 * @return Collection|Sortie[]
 */
public function getSorties(): ArrayCollection
{
    return $this->sorties;
}

public function addSorty(Sortie $sorty): self
{
    if (!$this->sorties->contains($sorty)) {
        $this->sorties[] = $sorty;
       // $sorty->setTypeSortie($this);
    }

    return $this;
}

public function removeSorty(Sortie $sorty): self
{
    if ($this->sorties->contains($sorty)) {
        $this->sorties->removeElement($sorty);
        // set the owning side to null (unless already changed)
        if ($sorty->getTypeSortie() === $this) {
            $sorty->setTypeSortie(null);
        }
    }

    return $this;
}

    /**
     * Get the value of lastActivity
     */ 
    public function getLastActivity() :?\DateTime
    {
        return $this->lastActivity;
    }

    /**
     * Set the value of lastActivity
     *
     * @return  self
     */ 
    public function setLastActivity(\DateTime $lastActivity) :self
    {
        $this->lastActivity = $lastActivity;

        return $this;
    }

    public function isActiveNow()
    {
        $this->setLastActivity(new \DateTime());
    }
}