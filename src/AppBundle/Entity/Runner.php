<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Runner
 *
 * @ORM\Table(name="runner")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RunnerRepository")
 */
class Runner
{
    const GENDER_MALE = false;
    const GENDER_FEMALE = true;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $surname;

    /**
     * @var bool
     *
     * @ORM\Column(name="gender", type="boolean")
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthDate", type="date", nullable=true)
     * @Assert\NotBlank()
     */
    private $birthDate;

    /**
     * @var string
     *
     * @ORM\Column(name="club", type="string", length=255, nullable=true)
     */
    private $club;

    /**
     * @var string
     *
     * @ORM\Column(name="nationality", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $nationality;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=10, nullable=true)
     */
    private $phone;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", inversedBy="runner", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var RaceRunner[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RaceRunner", mappedBy="runner")
     */
    private $raceRunners;

    /**
     * Runner constructor.
     */
    public function __construct()
    {
        $this->raceRunners = new ArrayCollection();
        $this->gender = self::GENDER_MALE;
    }

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
     * Set name
     *
     * @param string $name
     *
     * @return Runner
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return Runner
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set gender
     *
     * @param boolean $gender
     *
     * @return Runner
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return bool
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     *
     * @return Runner
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set club
     *
     * @param string $club
     *
     * @return Runner
     */
    public function setClub($club)
    {
        $this->club = $club;

        return $this;
    }

    /**
     * Get club
     *
     * @return string
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * Set nationality
     *
     * @param string $nationality
     *
     * @return Runner
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * Get nationality
     *
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return RaceRunner[]|ArrayCollection
     */
    public function getRaceRunners()
    {
        return $this->raceRunners;
    }

    /**
     * @param RaceRunner[]|ArrayCollection $raceRunners
     */
    public function setRaceRunners($raceRunners)
    {
        $this->raceRunners = $raceRunners;
    }

    /**
     * @param RaceRunner $raceRunner
     * @return RaceRunner[]|ArrayCollection
     */
    public function addRaceRunners(RaceRunner $raceRunner){
        $this->raceRunners->add($raceRunner);
        return $this->raceRunners;
    }
}



