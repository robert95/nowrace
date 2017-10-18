<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Race
 *
 * @ORM\Table(name="race")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceRepository")
 */
class Race
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
     * @var \DateTime
     *
     * @ORM\Column(name="startTime", type="datetime")
     */
    private $startTime;

    /**
     * @var float
     *
     * @ORM\Column(name="distance", type="float")
     */
    private $distance;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_runners", type="integer")
     */
    private $maxRunners;

    /**
     * @var Contest
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contest", inversedBy="races")
     * @ORM\JoinColumn(name="contest_id", referencedColumnName="id")
     */
    private $contest;

    /**
     * @var RaceCategory[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RaceCategory", mappedBy="race")
     */
    private $categories;

    /**
     * @var Track
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Track", inversedBy="races")
     * @ORM\JoinColumn(name="track_id", referencedColumnName="id")
     *
     */
    private $track;

    /**
     * @var RaceRunner[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RaceRunner", mappedBy="race")
     */
    private $raceRunners;

    /**
     * Race constructor.
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->raceRunners = new ArrayCollection();
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
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return Race
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set distance
     *
     * @param float $distance
     *
     * @return Race
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @return Contest
     */
    public function getContest()
    {
        return $this->contest;
    }

    /**
     * @param Contest $contest
     */
    public function setContest($contest)
    {
        $this->contest = $contest;
    }

    /**
     * @return RaceCategory[]|ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param RaceCategory[]|ArrayCollection $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @param RaceCategory $raceCategory
     */
    public function addCategories(RaceCategory $raceCategory)
    {
        $this->categories->add($raceCategory);
    }

    /**
     * @return integer
     */
    public function getMaxRunners()
    {
        return $this->maxRunners;
    }

    /**
     * @param integer $maxRunners
     */
    public function setMaxRunners($maxRunners)
    {
        $this->maxRunners = $maxRunners;
    }

    /**
     * @return Track
     */
    public function getTrack()
    {
        return $this->track;
    }

    /**
     * @param Track $track
     */
    public function setTrack($track)
    {
        $this->track = $track;
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

