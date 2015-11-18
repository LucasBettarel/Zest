<?php

namespace SE\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AttendanceData
 *
 * @ORM\Table(name="attendancedata")
 * @ORM\Entity(repositoryClass="SE\ReportBundle\Entity\AttendanceDataRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class AttendanceData
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
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", length=255, nullable=true)
     */
    private $updatedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="refresher", type="boolean", nullable=true)
     */
    private $refresher = 0;

    /**
     * @var \Integer
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @var \Integer
     *
     * @ORM\Column(name="month", type="integer")
     */
    private $month;

    /**
     * @var array
     *
     * @ORM\Column(name="jsonAttendance", type="json_array", nullable=true)
     */
    private $jsonAttendance;

    /**
     * @var array
     *
     * @ORM\Column(name="jsonData", type="json_array", nullable=true)
     */
    private $jsonData;

    /**
     * @var array
     *
     * @ORM\Column(name="jsonCharts", type="json_array", nullable=true)
     */
    private $jsonCharts;

    /**
     * @var array
     *
     * @ORM\Column(name="tableTemplate", type="json_array", nullable=true)
     */
    private $tableTemplate;

    public function __construct()
    {
     $this->createdAt = new \Datetime();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AttendanceData
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return AttendanceData
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateDate()
    {
        $this->setUpdatedAt(new \Datetime());
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set jsonAttendance
     *
     * @param array $jsonAttendance
     * @return AttendanceData
     */
    public function setJsonAttendance($jsonAttendance)
    {
        $this->jsonAttendance = $jsonAttendance;

        return $this;
    }

    /**
     * Get jsonAttendance
     *
     * @return array 
     */
    public function getJsonAttendance()
    {
        return $this->jsonAttendance;
    }

    /**
     * Set jsonData
     *
     * @param array $jsonData
     * @return AttendanceData
     */
    public function setJsonData($jsonData)
    {
        $this->jsonData = $jsonData;

        return $this;
    }

    /**
     * Get jsonData
     *
     * @return array 
     */
    public function getJsonData()
    {
        return $this->jsonData;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return AttendanceData
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set month
     *
     * @param integer $month
     * @return AttendanceData
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return integer 
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set jsonCharts
     *
     * @param array $jsonCharts
     * @return AttendanceData
     */
    public function setJsonCharts($jsonCharts)
    {
        $this->jsonCharts = $jsonCharts;

        return $this;
    }

    /**
     * Get jsonCharts
     *
     * @return array 
     */
    public function getJsonCharts()
    {
        return $this->jsonCharts;
    }

    /**
     * Set tableTemplate
     *
     * @param array $tableTemplate
     * @return AttendanceData
     */
    public function setTableTemplate($tableTemplate)
    {
        $this->tableTemplate = $tableTemplate;

        return $this;
    }

    /**
     * Get tableTemplate
     *
     * @return array 
     */
    public function getTableTemplate()
    {
        return $this->tableTemplate;
    }

    /**
     * Set refresher
     *
     * @param boolean $refresher
     * @return AttendanceData
     */
    public function setRefresher($refresher)
    {
        $this->refresher = $refresher;

        return $this;
    }

    /**
     * Get refresher
     *
     * @return boolean 
     */
    public function getRefresher()
    {
        return $this->refresher;
    }
}
