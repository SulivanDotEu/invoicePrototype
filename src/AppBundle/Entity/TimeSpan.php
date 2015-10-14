<?php
/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 13/10/2015
 * Time: 15:23
 */

namespace AppBundle\Entity;

use AppBundle\Object\DateSlot;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class TimeSpan
 * @package AppBundle\Entity
 * @ORM\Table(name="timespan")
 * @ORM\Entity()

 */
class TimeSpan
{

    // /!\ Warning, Danger Will Robinson ! /!\
    // Changing thes constant may hurt my feelings but overall the TimeTableView
    // Don't be silly and let this like this!
    ///!\ You have been warned ! /!\
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

//
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TimeTable", inversedBy="timeSpans", fetch="EAGER")
     * @ORM\JoinColumn(nullable=true, referencedColumnName="timetable_id")
     */
    protected $timeTable;

    /**
     * @var string
     *
     * @ORM\Column(name="day_from", type="integer", columnDefinition="ENUM('1', '2', '3', '4', '5', '6', '7')")
     */
    protected $dayFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="day_to", type="integer", columnDefinition="ENUM('1', '2', '3', '4', '5', '6', '7')")
     */
    protected $dayTo;

    /**
     * @var string
     *
     * @ORM\Column(name="hour_from", type="time")
     */
    protected $hourFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hour_to", type="time")
     */
    protected $hourTo;

    public static function nextDayIndex($index)
    {
        return ($index % 7) + 1;
    }

    public function __toString()
    {
        return 'TimeSpan #' . $this->getId() . ' : ' . $this->getDayFrom() . ' -> ' . $this->getDayTo() .
        ' (' . $this->getHourFrom()->format('H:i:s') . ' -> ' . $this->getHourTo()->format('H:i:s') . ')';
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

    public function contains(\DateTime $datetime)
    {
        $dateSlots = $this->createOpeningHoursFromDateTime($datetime);
        foreach ($dateSlots as $dateSlot) {
            if ($dateSlot->contains($datetime)) return true;
        }
        return false;
//		return $openingHours->contains($datetime);
    }

    /**
     * This method create real open hours for the context of the datetime
     * by considering month, year and day.
     * To avoid bug, 3 hours (or slot) are created:
     *      the open hours of the last week
     *      the open hours of the current week
     *      the open hours of the next week
     * @param \DateTime $datetime
     *
     * @return array[DateSlot]
     * todo refactor this method
     */
    public function createOpeningHoursFromDateTime(\DateTime $datetime)
    {
        $date = clone $datetime;
        $dayNumber = $datetime->format("N");
        $diff = $this->getDayFrom() - $dayNumber;
//		echo("diff between (conso-interval)" . $dayNumber . " and (timespan)" . $this->getDayFrom() . " = " . $diff . ' <br>');

        $from = clone $datetime;
        self::addDays($from, $diff);

//		$from->add(new \DateInterval("P".$diff.'D'));
        $from->setTime(
            $this->getHourFrom()->format('H'),
            $this->getHourFrom()->format('i'),
            $this->getHourFrom()->format('s'));

        $to = clone $from;
        $diff = ($this->getDayFrom() - ($this->getDayTo() + 7)) % 7;
        self::addDays($to, $diff, true);
//		$to->add(new \DateInterval("P".$diff.'D'));
        $to->setTime(
            $this->getHourTo()->format('H'),
            $this->getHourTo()->format('i'),
            $this->getHourTo()->format('s'));

        $currentWeekSlot = new DateSlot($from, $to);
        $weekInterval = new \DateInterval("P7D");

        // next week
        $nextWeekFrom = clone $from;
        $nextWeekTo = clone $to;
        $nextWeekFrom->add($weekInterval);
        $nextWeekTo->add($weekInterval);
        $nextWeekSlot = new DateSlot($nextWeekFrom, $nextWeekTo);

        // previous
        $previousWeekFrom = clone $from;
        $previousWeekTo = clone $to;
        $previousWeekFrom->sub($weekInterval);
        $previousWeekTo->sub($weekInterval);
        $previousWeekSlot = new DateSlot($previousWeekFrom, $previousWeekTo);
        return [$previousWeekSlot, $currentWeekSlot, $nextWeekSlot];
    }

    /**
     * Get dayFrom
     *
     * @return string
     */
    public function getDayFrom()
    {
        return $this->dayFrom;
    }

    /**
     * Set dayFrom
     *
     * @param string $dayFrom
     *
     * @return TimeSpan
     */
    public function setDayFrom($dayFrom)
    {
        $this->dayFrom = $dayFrom;

        return $this;
    }

    public static function addDays(\DateTime $dateTime, $amountOfDay, $alwaysAbsolute = false)
    {
        $absoluteAmountOfDay = abs($amountOfDay);
        if ($amountOfDay == 0) {
            return $dateTime;
        }
        if ($amountOfDay > 0 OR $alwaysAbsolute) {
            $dateTime->add(new \DateInterval("P" . $absoluteAmountOfDay . 'D'));
        } else {
            $dateTime->sub(new \DateInterval("P" . $absoluteAmountOfDay . 'D'));
        }

        return $dateTime;
    }

    /**
     * Get hourFrom
     *
     * @return \DateTime
     */
    public function getHourFrom()
    {
        return $this->hourFrom;
    }

    /**
     * Set hourFrom
     *
     * @param \DateTime $hourTo
     *
     * @return TimeSpan
     */
    public function setHourFrom($hourFrom)
    {
        $this->hourFrom = $hourFrom;

        return $this;
    }

    /**
     * Get dayTo
     *
     * @return \DateTime
     */
    public function getDayTo()
    {
        return $this->dayTo;
    }

    /**
     * Set dayTo
     *
     * @param string $dayTo
     *
     * @return TimeSpan
     */
    public function setDayTo($dayTo)
    {
        $this->dayTo = $dayTo;

        return $this;
    }

    /**
     * Get hourTo
     *
     * @return \DateTime
     */
    public function getHourTo()
    {
        return $this->hourTo;
    }

    /**
     * Set hourTo
     *
     * @param \DateTime $hourTo
     *
     * @return TimeSpan
     */
    public function setHourTo($hourTo)
    {
        $this->hourTo = $hourTo;

        return $this;
    }

    /**
     * Get timeTable
     *
     * @return TimeTable
     */
    public function getTimeTable()
    {
        return $this->timeTable;
    }

    /**
     * Set timeTable
     *
     * @param TimeTable $timeTable
     *
     * @return TimeSpan
     */
    public function setTimeTable(TimeTable $timeTable = null)
    {
        if ($timeTable == null)
        {
            return;
        }
        if ($timeTable == $this->getTimeTable())
        {
            return;
        }
        $this->timeTable = $timeTable;
        $timeTable->addTimeSpan($this);

        return $this;
    }
}