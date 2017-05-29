<?php
/**
 * ********************************************************************
 * This script is based on a C++ prayer times calculation program
 * provided by {ITL Project} at http://www.ArabEyes.org / Thamer Mahmoud
 * https://github.com/arabeyes-org/ITL/tree/master/prayertime
 * provided under the GNU Lesser General Public License
 * ********************************************************************
 *                  Converted to PHP by SalahHour.com
 *             All rights reserved for Amana Estates, Inc.
 * ********************************************************************
 * For Support and Other information please contact SalahHour.com
 * Distrubuted By: SalahHour.com
 */

class Date
{
    /**
     * @var int
     */
    public $day;

    /**
     * @var int
     */
    public $month;

    /**
     * @var int
     */
    public $year;
}

class Location
{
    /**
     * @var double
     */
    public $degreeLong;

    /**
     * @var double
     */
    public $degreeLat;

    /**
     * @var double
     */
    public $gmtDiff;

    /**
     * @var int
     */
    public $dst;

    /**
     * @var double
     */
    public $seaLevel;

    /**
     * @var double
     */
    public $pressure;

    /**
     * @var double
     */
    public $temperature;
}

class Method
{
    /**
     * @var double
     */
    public $fajrAng;

    /**
     * @var double
     */
    public $ishaaAng;

    /**
     * @var double
     */
    public $imsaakAng;

    /**
     * @var int
     */
    public $fajrInv;

    /**
     * @var int
     */
    public $ishaaInv;

    /**
     * @var int
     */
    public $imsaakInv;

    /**
     * @var int
     */
    public $round;

    /**
     * @var int
     */
    public $mathhab;

    /**
     * @var double
     */
    public $nearestLat;

    /**
     * @var int
     */
    public $extreme;

    /**
     * @var int
     */
    public $offset;

    /**
     * @var array double
     */
    public $offList;

    /**
     * Constructor
     */
    public function __construct()
    {
        for ($i = 0; $i < 6; $i++) {
            $this->offList[$i] = 0;
        }
    }
}

class Prayer {
    public $hour;
    public $minute;
    public $second;
    public $isExtreme;
}

class Astro
{
    /**
     * @var double
     */
    public $jd;

    /**
     * @var array double[3]
     */
    public $dec;

    /**
     * @var array double
     */
    public $ra;

    /**
     * @var array double
     */
    public $sid;

    /**
     * @var array double
     */
    public $dra;

    /**
     * @var array double
     */
    public $rsum;

    /**
     * Constructor
     */
    public function __construct()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->dec[$i] = 0;
            $this->ra[$i] = 0;
            $this->sid[$i] = 0;
            $this->dra[$i] = 0;
            $this->rsum[$i] = 0;
        }
    }
}

class AstroDay
{
    /**
     * @var double
     */
    public $dec;

    /**
     * @var double
     */
    public $ra;

    /**
     * @var double
     */
    public $sidtime;

    /**
     * @var double
     */
    public $dra;

    /**
     * @var double
     */
    public $rsum;
}