<?php
/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 13/10/2015
 * Time: 15:21
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ComplexTimeTable
 *
 * @ORM\Table(name="timeTable")
 * @ORM\Entity()
 */
class TimeTable {

    /**
     * @var integer
     *
     * @ORM\Column(name="timetable_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="datetime", nullable=true)
     */
    protected $name;

    /**
     * @var TimeSpan
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TimeSpan", orphanRemoval=true, cascade={"all"}, fetch="EAGER", mappedBy="timeTable")
     * @ORM\OrderBy({"dayFrom" = "ASC"})
     */
    protected $timeSpans;

    /**
     * Constructor
     */
    public function __toString()
    {
        return "Complex Time Table #" . $this->getId() . ' : ' . $this->getInternalName();
    }

    public function defineTheHours(Consumption $conso, DateIntervalInterface $interval)
    {
        $timespans = $this->getTimeSpans();
        foreach ($timespans as $timespan)
        {
            /** @var $timespan TimeSpan */
            $slots = $timespan->createOpeningHoursFromDateTime($interval->getStart());
            foreach ($slots as $slot)
            {
                $intersection = $slot->intersect($interval);
                if ($intersection != null)
                {
                    /** @var $intersection DateSlot */
                    $conso->setDateStart($intersection->getStart());
                    $conso->setDateEnd($intersection->getEnd());

                    return $conso;
                }
            }
        }
    }

    public function contains(\DateTime $datetime)
    {
        $timespans = $this->getTimeSpans();
        foreach ($timespans as $timespan)
        {
            /** @var $timespan TimeSpan */
            if($timespan->contains($datetime)) return true;
        }
        return false;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->timeSpans = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @param TimeSpan $timeSpans
     * @return $this|void
     */
    public function addTimeSpan(TimeSpan $timeSpans)
    {
        if ($timeSpans == null)
        {
            return;
        }
        if ($this->timeSpans->contains($timeSpans))
        {
            return;
        }
        $this->timeSpans[ ] = $timeSpans;
        $timeSpans->setTimeTable($this);

        return $this;
    }

    /**
     * @param TimeSpan $timeSpans
     */
    public function removeTimeSpan(TimeSpan $timeSpans)
    {
        $this->timeSpans->removeElement($timeSpans);
        $timeSpans->setTimeTable(null);
    }

    /**
     * Get timeSpans
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTimeSpans()
    {
        return $this->timeSpans;
    }
}