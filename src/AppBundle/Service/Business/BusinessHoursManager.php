<?php
/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 13/10/2015
 * Time: 15:16
 */

namespace AppBundle\Service\Business;

use AppBundle\Entity\TimeSpan;
use AppBundle\Entity\TimeTable;
use JMS\DiExtraBundle\Annotation\Service;

/**
 * Class BusinessHoursManager
 * @package AppBundle\Service\Business
 * @Service("business_hours_manager")
 */
class BusinessHoursManager
{
    /** @var \DateTime */
    public $h9;
    /** @var \DateTime */
    public $h12;
    /** @var \DateTime */
    public $h13;
    /** @var \DateTime */
    public $h1330;
    /** @var \DateTime */
    public $h17;

    function __construct()
    {
        $this->h9 = new \DateTime();
        $this->h9
            ->setTime(9, 00, 00);
        $this->h12 = new \DateTime();
        $this->h12
            ->setTime(12, 00, 00);
        $this->h13 = new \DateTime();
        $this->h13
            ->setTime(13, 00, 00);
        $this->h1330 = new \DateTime();
        $this->h1330
            ->setTime(13, 30, 00);
        $this->h17 = new \DateTime();
        $this->h17
            ->setTime(17, 00, 00);
    }

    public function isDateTimeInBusinessHours(\DateTime $date)
    {
        $timetable = $this->getBusinessHours();
        return $timetable->contains($date);
    }

    /**
     * @return TimeTable
     */
    public function getBusinessHours()
    {
        $timeTable = new TimeTable();
        $timeTable->addTimeSpan($this->_generateTimeSpan(TimeSpan::MONDAY, TimeSpan::MONDAY, $this->h1330, $this->h17));

        $timeTable->addTimeSpan($this->_generateTimeSpan(TimeSpan::TUESDAY, TimeSpan::TUESDAY, $this->h9, $this->h13));
        $timeTable->addTimeSpan($this->_generateTimeSpan(TimeSpan::TUESDAY, TimeSpan::TUESDAY, $this->h1330, $this->h17));

        $timeTable->addTimeSpan($this->_generateTimeSpan(TimeSpan::WEDNESDAY, TimeSpan::WEDNESDAY, $this->h9, $this->h13));
        $timeTable->addTimeSpan($this->_generateTimeSpan(TimeSpan::WEDNESDAY, TimeSpan::WEDNESDAY, $this->h1330, $this->h17));

        $timeTable->addTimeSpan($this->_generateTimeSpan(TimeSpan::THURSDAY, TimeSpan::THURSDAY, $this->h9, $this->h13));
        $timeTable->addTimeSpan($this->_generateTimeSpan(TimeSpan::THURSDAY, TimeSpan::THURSDAY, $this->h1330, $this->h17));

        $timeTable->addTimeSpan($this->_generateTimeSpan(TimeSpan::FRIDAY, TimeSpan::FRIDAY, $this->h9, $this->h13));

        return $timeTable;
    }

    /**
     * @param $fromDay
     * @param $toDay
     * @param $fromHour
     * @param $toHour
     * @return TimeSpan
     */
    private function _generateTimeSpan($fromDay, $toDay, $fromHour, $toHour)
    {
        $timeSpan = new TimeSpan();
        $timeSpan
            ->setDayFrom($fromDay)
            ->setDayTo($toDay)
            ->setHourFrom($fromHour)
            ->setHourTo($toHour);
        return $timeSpan;
    }

}