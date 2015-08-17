<?php

namespace SE\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Calendar
 *
 * @ORM\Table(name="calendar")
 * @ORM\Entity(repositoryClass="SE\ReportBundle\Entity\CalendarRepository")
 */
class Calendar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt", type="date", nullable=false)
     */
    private $dt;

    /**
     * @var integer
     *
     * @ORM\Column(name="y", type="smallint")
     */
    private $y;

    /**
     * @var integer
     *
     * @ORM\Column(name="q", type="smallint")
     */
    private $q;

    /**
     * @var integer
     *
     * @ORM\Column(name="m", type="smallint")
     */
    private $m;

    /**
     * @var integer
     *
     * @ORM\Column(name="d", type="smallint")
     */
    private $d;

    /**
     * @var integer
     *
     * @ORM\Column(name="dw", type="smallint")
     */
    private $dw;

    /**
     * @var string
     *
     * @ORM\Column(name="monthName", type="text")
     */
    private $monthName;

    /**
     * @var string
     *
     * @ORM\Column(name="dayName", type="text")
     */
    private $dayName;

    /**
     * @var integer
     *
     * @ORM\Column(name="w", type="smallint")
     */
    private $w;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isWeekday", type="boolean")
     */
    private $isWeekday;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isHoliday", type="boolean")
     */
    private $isHoliday;

    /**
     * @var string
     *
     * @ORM\Column(name="holidayDescr", type="text")
     */
    private $holidayDescr;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPayday", type="boolean")
     */
    private $isPayday;


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
     * Set dt
     *
     * @param \DateTime $dt
     * @return Calendar
     */
    public function setDt($dt)
    {
        $this->dt = $dt;

        return $this;
    }

    /**
     * Get dt
     *
     * @return \DateTime 
     */
    public function getDt()
    {
        return $this->dt;
    }

    /**
     * Set y
     *
     * @param integer $y
     * @return Calendar
     */
    public function setY($y)
    {
        $this->y = $y;

        return $this;
    }

    /**
     * Get y
     *
     * @return integer 
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Set q
     *
     * @param integer $q
     * @return Calendar
     */
    public function setQ($q)
    {
        $this->q = $q;

        return $this;
    }

    /**
     * Get q
     *
     * @return integer 
     */
    public function getQ()
    {
        return $this->q;
    }

    /**
     * Set m
     *
     * @param integer $m
     * @return Calendar
     */
    public function setM($m)
    {
        $this->m = $m;

        return $this;
    }

    /**
     * Get m
     *
     * @return integer 
     */
    public function getM()
    {
        return $this->m;
    }

    /**
     * Set d
     *
     * @param integer $d
     * @return Calendar
     */
    public function setD($d)
    {
        $this->d = $d;

        return $this;
    }

    /**
     * Get d
     *
     * @return integer 
     */
    public function getD()
    {
        return $this->d;
    }

    /**
     * Set dw
     *
     * @param integer $dw
     * @return Calendar
     */
    public function setDw($dw)
    {
        $this->dw = $dw;

        return $this;
    }

    /**
     * Get dw
     *
     * @return integer 
     */
    public function getDw()
    {
        return $this->dw;
    }

    /**
     * Set monthName
     *
     * @param string $monthName
     * @return Calendar
     */
    public function setMonthName($monthName)
    {
        $this->monthName = $monthName;

        return $this;
    }

    /**
     * Get monthName
     *
     * @return string 
     */
    public function getMonthName()
    {
        return $this->monthName;
    }

    /**
     * Set dayName
     *
     * @param string $dayName
     * @return Calendar
     */
    public function setDayName($dayName)
    {
        $this->dayName = $dayName;

        return $this;
    }

    /**
     * Get dayName
     *
     * @return string 
     */
    public function getDayName()
    {
        return $this->dayName;
    }

    /**
     * Set w
     *
     * @param integer $w
     * @return Calendar
     */
    public function setW($w)
    {
        $this->w = $w;

        return $this;
    }

    /**
     * Get w
     *
     * @return integer 
     */
    public function getW()
    {
        return $this->w;
    }

    /**
     * Set isWeekday
     *
     * @param boolean $isWeekday
     * @return Calendar
     */
    public function setIsWeekday($isWeekday)
    {
        $this->isWeekday = $isWeekday;

        return $this;
    }

    /**
     * Get isWeekday
     *
     * @return boolean 
     */
    public function getIsWeekday()
    {
        return $this->isWeekday;
    }

    /**
     * Set isHoliday
     *
     * @param boolean $isHoliday
     * @return Calendar
     */
    public function setIsHoliday($isHoliday)
    {
        $this->isHoliday = $isHoliday;

        return $this;
    }

    /**
     * Get isHoliday
     *
     * @return boolean 
     */
    public function getIsHoliday()
    {
        return $this->isHoliday;
    }

    /**
     * Set holidayDescr
     *
     * @param string $holidayDescr
     * @return Calendar
     */
    public function setHolidayDescr($holidayDescr)
    {
        $this->holidayDescr = $holidayDescr;

        return $this;
    }

    /**
     * Get holidayDescr
     *
     * @return string 
     */
    public function getHolidayDescr()
    {
        return $this->holidayDescr;
    }

    /**
     * Set isPayday
     *
     * @param boolean $isPayday
     * @return Calendar
     */
    public function setIsPayday($isPayday)
    {
        $this->isPayday = $isPayday;

        return $this;
    }

    /**
     * Get isPayday
     *
     * @return boolean 
     */
    public function getIsPayday()
    {
        return $this->isPayday;
    }
}
