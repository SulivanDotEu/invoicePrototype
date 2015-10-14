<?php
/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 13/10/2015
 * Time: 15:55
 */

namespace AppBundle\Object;


class DateSlot
{

    /**
     * @var \DateTime
     */
    public $start;

    /**
     * @var \DateTime
     */
    public $end;

    function __construct(\DateTime $start, \DateTime $end)
    {
        $this->setEnd($end);
        $this->setStart($start);
    }

    function __toString()
    {
        return 'DateSlot [' . $this->getStart()->format('l d/m/Y H:i:s') .
        ' => ' . $this->getEnd()->format('l d/m/Y H:i:s') . ']';
    }

    /**
     * PLEASE DONT TOUCH THE INTERVAL, EVEN THE DATETIME OBJECTS IN IT
     *
     * PRE: interval is in the same state as before
     * return a new DateSlot that represents the intersection between the current
     * object and the interval.
     *
     * ----[A.START]----[B.START]----------[A.END]----[B.END]----->
     *
     * 0) return NULL if no intersection ---[A.S]--[A.E]-------[B.S]--[B.E]-->
     * Or return a DateSlot (3 possibilities)
     * 1) (A contains B)
     *      ----[A.S]----[B.S]----------[B.E]----[A.E]----->
     *      --------------|----------------|--------------->
     *
     * 2)       ----[A.START]----[B.START]----------[A.END]----[B.END]----->
     * 3)       ----[B.START]----[A.START]----------[B.END]----[A.END]----->
     * RESULT=  -------------------|------------------|-------------------->
     *
     * @param DateIntervalInterface $interval
     *
     * @return null|DateSlot
     */
    public function intersect(DateSlot $interval = null)
    {
        // si il n'y a pas d'intersection, on retourne null
        if ($this->isIntervalBefore($interval) OR $this->isIntervalAfter($interval))
        {
            return null;
        }

        $start = null;
        $end = null;

        // si la session à commencer avant l'horaire, la consommation début au début de l'horaire
        // PICK UP THE START OF INTERSECTION ---START---[END]--->
        if (self::before($interval->getStart(), $this->getStart()))
        {
            // ------INTERVAL----------THIS------
            // ------------------------|---------
            $start = clone $this->getStart();
        }
        else
        {
            // ----------THIS------INTERVAL------
            // ------------------------|---------
            $start = clone $interval->getStart();
        }

        // PICK UP THE END OF INTERSECTION ---[START]---END--->
        if (self::after($interval->getEnd(), $this->getEnd()))
        {
            // ----------THIS------INTERVAL------>
            // ------------|--------------------->
            $end = clone $this->getEnd();
        }
        else
        {
            // ------INTERVAL------THIS---------->
            // ----------|----------------------->
            $end = clone $interval->getEnd();
        }

        $dateSlot = new DateSlot($start, $end);
        if ($dateSlot->getStart()->getTimestamp() == $dateSlot->getEnd()->getTimestamp())
        {
            return;
        }

        return $dateSlot;
    }

    /**
     * return true is the interval is strictly before the dateslot
     * return false if not.
     *
     * eg:
     *      interval [01/01/2014 => 30/01/2014] and dateslot [01/01/2020 => 30/01/2020]
     *      return true
     *
     *      interval [01/01/2020 => 30/01/2020] and dateslot [01/01/2014 => 30/01/2014]
     *      return false
     *
     * @param DateSlot $interval
     *
     * @return bool
     */
    public function isIntervalBefore(DateSlot $interval)
    {
        return $this->isDateTimeBefore($interval->getEnd());
    }

    /**
     * return true is the interval is strictly AFTER the dateslot
     * return false if not.
     *
     * eg:
     *      interval [01/01/2014 => 30/01/2014] and dateslot [01/01/2020 => 30/01/2020]
     *      return false
     *
     *      interval [01/01/2020 => 30/01/2020] and dateslot [01/01/2014 => 30/01/2014]
     *      return true
     *
     * @param DateSlot $interval
     *
     * @return bool
     */
    public function isIntervalAfter(DateSlot $interval = null)
    {
        return $this->isDateTimeAfter($interval->getStart());
    }

    public function isDateTimeBefore(\DateTime $datetime = null)
    {
        return self::before($datetime, $this->getStart());
//        return $datetime->getTimestamp() < $this->getStart()->getTimestamp();
    }

    public function isDateTimeEqualOrBefore(\DateTime $datetime = null)
    {
        return self::before($datetime, $this->getStart(), true);
//		return $datetime->getTimestamp() <= $this->getStart()->getTimestamp();
    }

    public function isDateTimeAfter(\DateTime $datetime = null)
    {
        return self::after($datetime, $this->getEnd());
//        return $datetime->getTimestamp() > $this->getEnd()->getTimestamp();
    }

    // todo bel use static::after
    public function isDateTimeEqualOrAfter(\DateTime $datetime = null)
    {
        return self::after($datetime, $this->getEnd(), true);
//		return $datetime->getTimestamp() >= $this->getEnd()->getTimestamp();
    }

    public function isTsBefore($ts)
    {
        return $this->getStart()->getTimestamp() < $ts;
    }

    public function isTsAfter($ts)
    {
        return $ts < $this->getEnd()->getTimestamp();
    }

    /**
     * @param \DateTime $datetime
     *
     * @return bool
     * @deprecated
     */
    public function isDateTimeDuring(\DateTime $datetime)
    {
        return ($this->isDateTimeAfter($datetime) AND $this->isDateTimeAfter($datetime));
    }

    public function contains(\DateTime $datetime)
    {
        return !($this->isDateTimeAfter($datetime) OR $this->isDateTimeBefore($datetime));
    }

    /**
     * return true if first < second
     * ------first------second------>
     *
     * @param \DateTime $first
     * @param \DateTime $second
     *
     * @return bool
     */
    public static function before(\DateTime $first = null, \DateTime $second = null, $orEquals = false)
    {
        if ($orEquals AND $first == null AND $second == null)
        {
            return true;
        }
        // if first and second or both LIMITLESS, none of them are before or after each other
        if ($first == null AND $second == null)
        {
            return false;
        }
        // if the first date is null aka LIMITLESS or INFINITE
        // then first is after second=> so true
        // ---second---------------> first
        if ($first == null)
        {
            return false;
        }
        // if the second date is null aka LIMITLESS or INFINITE
        // the second is first => so false
        // ---first---------------> second
        if ($second == null)
        {
            return true;
        }

        return $first->getTimestamp() < $second->getTimestamp();
    }

    /**
     * return true if first > second
     * ------second------first------>
     *
     * @param \DateTime $first
     * @param \DateTime $second
     *
     * @return bool
     */
    public static function after(\DateTime $first = null, \DateTime $second = null, $orEquals = false)
    {
        if ($orEquals AND $first == null AND $second == null)
        {
            return true;
        }
        // if first and second or both LIMITLESS, none of them are before or after each other
        if ($first == null AND $second == null)
        {
            return false;
        }
        // if the second date is null aka LIMITLESS or INFINITE
        // then second is after => so false
        // ------------first------> second
        if ($second == null)
        {
            return false;
        }
        // if the first date is null, aka LIMITLESS
        // then first is after => so true
        // ------second------------> first
        if ($first == null)
        {
            return true;
        }

        return $first->getTimestamp() > $second->getTimestamp();
    }

    /**
     * return true if first < (before) before
     * ------first------second------>
     *
     * @param $first integer
     * @param $second integer
     *
     * @return bool
     */
    public static function tsBefore($first, $second)
    {
        return $first < $second;
    }

    public static function tsAfter($first, $second)
    {
        return $first > $second;
    }

    // @todo interface a rajouter ici
    public function equalsInterval($interval)
    {
        return ($this->getStart()->getTimestamp() == $interval->getStart()->getTimestamp() AND
            $this->getStart()->getTimestamp() == $interval->getEnd()->getTimestamp());
    }

    public function makeSubTimeSlot(DateSlot $interval)
    {
        $format = 'l d/m H:i:s';
        // si hors du slot
        //if($this->isTsAfter($interval->getEnd()->getTimestamp()-1)){
        if ($this->getEnd()->getTimestamp() < $interval->getStart()->getTimestamp() + 1)
        {
            return null;
        }
        //else if($this->isTsBefore($interval->getStart()->getTimestamp()+1)){
        else if ($this->getStart()->getTimestamp() > $interval->getEnd()->getTimestamp() - 1)
        {
            return null;
        }
        else
        {
        }

        // si exactement le meme ou strictement plus grand ; is the same or strictly bigger
        if ($this->equalsInterval($interval))
        {
            return $this;
        }

        // if has started before ; si a commencé avant
        if ($this->isDateTimeBefore($interval->getStart()))
        {
            // est ce que la date de fin est dans mon interval?
            if (self::before($interval->getEnd(), $this->getEnd()))
            {
                return new DateSlot($this->getStart(), $interval->getEnd());
            }
            else
            {
                return new DateSlot($this->getStart(), $this->getEnd());
            }
        }
        else if ($this->isDateTimeAfter($interval->getEnd()))
        {
            if (self::after($interval->getStart(), $this->getStart()))
            {
                return new DateSlot($interval->getStart(), $this->getEnd());
            }
            else
            {
                return new DateSlot($this->getStart(), $this->getEnd());
            }
        }

        return new DateSlot($interval->getStart(), $interval->getEnd());
    }

    /**
     * @param \DateTime $end
     */
    public function setEnd($end)
    {
        $this->end = clone $end;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart($start)
    {
        $this->start = clone $start;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }
}