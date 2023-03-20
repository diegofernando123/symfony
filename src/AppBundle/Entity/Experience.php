<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Constraints as AppAssert;

/**
 * Experience
 *
 * @ORM\Table(name="user_experience")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ExperienceRepository")
 */
class Experience
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="experiences")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $company;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var Industry
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Industry")
     * @ORM\JoinColumn(name="industry_id", referencedColumnName="id", onDelete="RESTRICT", nullable=false)
     */
    private $industry;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $specialisation;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="date")
     */
    private $start;

    /**
     * @AppAssert\ExperienceEnd()
     * @ORM\Column(type="date", nullable=true)
     */
    private $end;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $isCurrent = false;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Message
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getCompany()
    {
    	return $this->company;
    }

    public function setCompany($value)
    {
    	$this->company = $value;
    }

    public function getLogo()
    {
    	return $this->logo;
    }

    public function setLogo($value)
    {
    	$this->logo = $value;
    }

    public function getTitle()
    {
    	return $this->title;
    }

    public function setTitle($value)
    {
    	$this->title = $value;
    }

    public function getIndustry()
    {
    	return $this->industry;
    }

    public function setIndustry($value)
    {
    	$this->industry = $value;
    }

    public function getLocation()
    {
    	return $this->location;
    }

    public function setLocation($value)
    {
    	$this->location = $value;
    }

    public function getSpecialisation()
    {
    	return $this->specialisation;
    }

    public function setSpecialisation($value)
    {
    	$this->specialisation = $value;
    }

    public function getStart()
    {
//      if($this->start == null) {
//    		$this->start = new \DateTime();
//    	}
    	return $this->start;
    }

    public function setStart($value)
    {
    	$this->start = $value;
    }

    public function getStartYear() {
    	return $this->getStart() == null ? null : $this->getStart()->format('Y');
    }

    public function getStartMonth() {
    	return $this->getStart() == null ? null : $this->getStart()->format('n');
    }

    public function setStartYear($value) {
    	if($this->getStart() == null) {
    		$this->start = new \DateTime();
    	}
    	$this->start->setDate($value, $this->getStart()->format('m'), $this->getStart()->format('d'));
    }

    public function setStartMonth($value) {
    	if($this->getStart() == null) {
    		$this->start = new \DateTime();
    	}
    	$this->start->setDate($this->getStart()->format('Y'), $value, $this->getStart()->format('d'));
    }

    public function getEnd()
    {
//      if($this->end == null) {
//    		$this->end = new \DateTime();
//    	}
    	return $this->end;
    }

    public function setEnd($value)
    {
    	$this->end = $value;
    }

    public function getEndYear() {
    	return $this->getEnd() == null ? null : $this->getEnd()->format('Y');
    }

    public function getEndMonth() {
    	return $this->getEnd() == null ? null : $this->getEnd()->format('n');
    }

    public function setEndYear($value) {
    	if($this->getEnd() == null) {
    		$this->end = new \DateTime();
    	}

    	$this->end->setDate($value, $this->getEnd()->format('m'), $this->getEnd()->format('d'));
    }

    public function setEndMonth($value) {
    	if($this->getEnd() == null) {
    		$this->end = new \DateTime();
    	}
    	
    	$this->end->setDate($this->getEnd()->format('Y'), $value, $this->getEnd()->format('d'));
    }
    
    public function getIsCurrent()
    {
    	return $this->isCurrent;
    }

    public function setIsCurrent($value)
    {
    	$this->isCurrent = $value;
    }
}
