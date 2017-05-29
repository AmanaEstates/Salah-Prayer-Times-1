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

define('DS', DIRECTORY_SEPARATOR);

require_once dirname(__FILE__) . DS . 'AstroConstants.php';
require_once dirname(__FILE__) . DS . 'SharedClasses.php';
require_once dirname(__FILE__) . DS . 'AstroFunctions.php';
require_once dirname(__FILE__) . DS . 'Settings.php';
require_once dirname(__FILE__) . DS . 'SettingsConstants.php';

Class Prayer_Times extends Settings_Constants
{
    /**
     * Constants
     */
    const KAABA_LAT = 21.423333;
    const KAABA_LONG = 39.823333;
    const DEF_NEAREST_LATITUDE = 48.5;
    const DEF_IMSAAK_ANGLE = 1.5;
    const DEF_IMSAAK_INTERVAL = 10;
    const DEFAULT_ROUND_SEC = 30;
    const AGGRESSIVE_ROUND_SEC = 1;

    const NONE_EX = 0;
    const LAT_ALL = 1;
    const LAT_ALWAYS = 2;
    const LAT_INVALID = 3;
    const GOOD_ALL = 4;
    const GOOD_INVALID = 5;
    const SEVEN_NIGHT_ALWAYS = 6;
    const SEVEN_NIGHT_INVALID = 7;
    const SEVEN_DAY_ALWAYS = 8;
    const SEVEN_DAY_INVALID = 9;
    const HALF_ALWAYS = 10;
    const HALF_INVALID = 11;
    const MIN_ALWAYS = 12;
    const MIN_INVALID = 13;
    const GOOD_DIF = 14;

    const NONE = 0;
    const EGYPT_SURVEY = 1;
    const KARACHI_SHAF = 2;
    const KARACHI_HANAF = 3;
    const NORTH_AMERICA = 4;
    const MUSLIM_LEAGUE = 5;
    const UMM_ALQURRA = 6;
    const FIXED_ISHAA = 7;

    const FAJR = 0;
    const SHUROOQ = 1;
    const THUHR = 2;
    const ASSR = 3;
    const MAGHRIB = 4;
    const ISHAA = 5;
    const IMSAAK = 6;
    const NEXTFAJR = 7;

    /**
     * Astro Cache
     *
     * @var Astro
     */
    public $astroCache;

    /**
     * Settings
     *
     * @var Settings
     */
    public $settings;

    /**
     * Config
     *
     * @var Method
     */
    public $config;

    /**
     * Constructor
     *
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        // Load defaults
        $settings->fajir_rule   = Settings_Constants::$methods[$settings->method]['fajir_rule'];
        $settings->maghrib_rule = Settings_Constants::$methods[$settings->method]['maghrib_rule'];
        $settings->isha_rule    = Settings_Constants::$methods[$settings->method]['isha_rule'];

        $this->settings = $settings;
        $this->astroCache = new Astro();

    }

    /**
     * Reset configs
     */
    public function resetConfigs()
    {
        // Config
        $this->config = new Method();

        $this->config->fajrAng = ($this->settings->fajir_rule[0] == 0) ? $this->settings->fajir_rule[1] : 0;
        $this->config->fajrInv = ($this->settings->fajir_rule[0] == 1) ? $this->settings->fajir_rule[1] : 0;

        $this->config->ishaaAng = ($this->settings->isha_rule[0] == 0) ? $this->settings->isha_rule[1] : 0;
        $this->config->ishaaInv = ($this->settings->isha_rule[0] == 1) ? $this->settings->isha_rule[1] : 0;

        $this->config->imsaakAng = self::DEF_IMSAAK_ANGLE;
        $this->config->imsaakInv = 0;

        $this->config->mathhab = $this->settings->juristic;
        $this->config->round = 1;
        $this->config->nearestLat = $this->settings->latitude;
        $this->config->extreme = 7;

        $this->config->offset = 0;
        for ($i = 0; $i < 6; $i++) {
            $this->config->offList[$i] = 0;
        }

        $this->config->round = 0;
    }

    /**
     * Get prayer times
     *
     * @param int $day
     * @param int $month
     * @param int $year
     *
     * @return array
     */
    public function getPrayerTimes($day, $month, $year)
    {
        $this->resetConfigs();

        $loc = new Location();
        $date = new Date();
        $ptList[0] = new Prayer();
        $ptList[1] = new Prayer();
        $ptList[2] = new Prayer();
        $ptList[3] = new Prayer();
        $ptList[4] = new Prayer();
        $ptList[5] = new Prayer();
        $imsaak = new Prayer();
        $nextImsaak = new Prayer();
        $nextFajr = new Prayer();

        $date->day = $day;
        $date->month = $month;
        $date->year = $year;

        $loc->degreeLat = $this->settings->latitude;
        $loc->degreeLong = $this->settings->longitude;
        $loc->gmtDiff = $this->getOffset($this->settings->timezone, $year . '-' . $month . '-' . $day);
        $loc->dst = 0;
        $loc->seaLevel = 0;
        $loc->pressure = 1010;
        $loc->temperature = 10;

        $this->getPrayerTimesLocation($loc, $this->config, $date, $ptList);
        $this->getImsaak($loc, $this->config, $date, $imsaak);
        $this->getNextDayFajr($loc, $this->config, $date, $nextFajr);
        $this->getNextDayImsaak($loc, $this->config, $date, $nextImsaak);
        $qibla = $this->getNorthQibla($loc);

        for ($i = 0; $i < 6; $i++) {
           $today[$i] = $this->translate_h($ptList[$i]->hour) . ':'
                   . $this->translate_m($ptList[$i]->minute);
        }

        return $today;
    }

    /**
     * Get offset from a timezone
     *
     * @param string $userTimeZone
     * @param dateTime $dateTime
     *
     * @return type
     */
    public function getOffset($userTimeZone, $dateTime = 'now')
    {
        $userDateTimeZone = new DateTimeZone($userTimeZone);
        $userDateTime     = new DateTime($dateTime, $userDateTimeZone);
        return ($userDateTimeZone->getOffset($userDateTime) / 3600);
    }

    /**
     * Make hours look nicer
     *
     * @param double $hour
     *
     * @return double
     */
    public function translate_h($hour)
    {
        if($hour <= 12){ return "$hour";} else { return $hour-12;}
    }

    /**
     * Make minutes look nicer
     *
     * @param double $minute
     *
     * @return double
     */
    public function translate_m($minute)
    {
        return sprintf("%02d", $minute);
    }

    /**
     * Get prayer times
     *
     * @param Location $loc
     * @param Method $conf
     * @param Date $date
     * @param Prayer $pt
     *
     * @return void
     */
    public function getPrayerTimesLocation(&$loc, &$conf, &$date, &$pt)
    {
        $lastDay = 0;
        $julianDay = 0;

        $this->getDayInfo($date, $loc->gmtDiff, $lastDay, $julianDay);
        $this->getPrayerTimesByDay($loc, $conf, $lastDay, $julianDay, $pt, 0);
    }

    /**
     * Get prayer times by day
     *
     * @param Location $loc
     * @param Method $conf
     * @param int $lastDay
     * @param double $julianDay
     * @param Prayer $pt
     * @param int $type
     *
     * @return void
     */
    public function getPrayerTimesByDay(&$loc, &$conf, $lastDay, $julianDay, &$pt, $type)
    {
        $i = 0; $invalid = 0;
        $th = 0; $sh = 0; $mg = 0; $fj = 0; $is = 0; $ar = 0;
        $lat = 0; $lon = 0; $dec = 0;
        $tempPrayer[0] = 0;
        $tempPrayer[1] = 0;
        $tempPrayer[2] = 0;
        $tempPrayer[3] = 0;
        $tempPrayer[4] = 0;
        $tempPrayer[5] = 0;
        $tAstro = new Astro();

        $lat = $loc->degreeLat;
        $lon = $loc->degreeLong;
        $invalid = 0;

        AstroFunctions::getAstroValuesByDay($julianDay, $loc, $this->astroCache, $tAstro);
        $dec = AstroFunctions::DEG_TO_RAD($tAstro->dec[1]);

        $fj = $this->getFajIsh($lat, $dec, $conf->fajrAng);
        $sh = $this->getShoMag($loc, $tAstro, self::SHUROOQ);
        $th = $this->getThuhr($lon, $tAstro);
        $ar = $this->getAssr($lat, $dec, $conf->mathhab);
        $mg = $this->getShoMag($loc, $tAstro, self::MAGHRIB);
        $is = $this->getFajIsh($lat, $dec, $conf->ishaaAng);

        if ($fj == 99) {
            $tempPrayer[0] = 99;
            $invalid = 1;
        } else {
            $tempPrayer[0] = $th - $fj;
        }

        if ($sh == 99) {
            $invalid = 1;
        }

        $tempPrayer[1] = $sh;
        $tempPrayer[2] = $th;
        $tempPrayer[3] = $th + $ar;
        $tempPrayer[4] = $mg;

        if ($mg == 99) {
            $invalid = 1;
        }

        if ($is == 99) {
            $tempPrayer[5] = 99;
            $invalid = 1;
        }
        else {
            $tempPrayer[5] = $th + $is;
        }

        for ($i = 0; $i < 6; $i++) {
            $pt[$i]->isExtreme = 0;
        }

        if (($conf->extreme != self::NONE_EX) && !(($conf->extreme == self::GOOD_INVALID ||
                                              $conf->extreme == self::LAT_INVALID ||
                                              $conf->extreme == self::SEVEN_NIGHT_INVALID ||
                                              $conf->extreme == self::SEVEN_DAY_INVALID ||
                                              $conf->extreme == self::HALF_INVALID) &&
                                             ($invalid == 0))) {
            $exdecPrev = 0; $exdecNext = 0;
            $exTh = 99; $exFj = 99; $exIs = 99; $exAr = 99; $exIm = 99; $exSh = 99; $exMg = 99;
            $portion = 0;
            $nGoodDay = 0;
            $exinterval = 0;
            $exLoc = $loc;
            $exAstroPrev = new Astro();
            $exAstroNext = new Astro();

            switch ($conf->extreme) {
            case self::LAT_ALL:
            case self::LAT_ALWAYS:
            case self::LAT_INVALID:
                $exLoc->degreeLat = $conf->nearestLat;
                $exFj = $this->getFajIsh($conf->nearestLat, $dec, $conf->fajrAng);
                $exIm = $this->getFajIsh($conf->nearestLat, $dec, $conf->imsaakAng);
                $exIs = $this->getFajIsh($conf->nearestLat, $dec, $conf->ishaaAng);
                $exAr = $this->getAssr($conf->nearestLat, $dec, $conf->mathhab);
                $exSh = $this->getShoMag ($exLoc, $tAstro, self::SHUROOQ);
                $exMg = $this->getShoMag ($exLoc, $tAstro, self::MAGHRIB);

                switch ($conf->extreme) {
                case self::LAT_ALL:
                    $tempPrayer[0] = $th - $exFj;
                    $tempPrayer[1] = $exSh;
                    $tempPrayer[3] = $th + $exAr;
                    $tempPrayer[4] = $exMg;
                    $tempPrayer[5] = $th + $exIs;
                    $pt[0]->isExtreme = 1;
                    $pt[1]->isExtreme = 1;
                    $pt[2]->isExtreme = 1;
                    $pt[3]->isExtreme = 1;
                    $pt[4]->isExtreme = 1;
                    $pt[5]->isExtreme = 1;
                    break;

                case self::LAT_ALWAYS:
                    $tempPrayer[0] = $th - $exFj;
                    $tempPrayer[5] = $th + $exIs;
                    $pt[0]->isExtreme = 1;
                    $pt[5]->isExtreme = 1;
                    break;

                case self::LAT_INVALID:
                    if ($tempPrayer[0] == 99) {
                        $tempPrayer[0] = $th - $exFj;
                        $pt[0]->isExtreme = 1;
                    }
                    if ($tempPrayer[5] == 99) {
                        $tempPrayer[5] = $th + $exIs;
                        $pt[5]->isExtreme = 1;
                    }
                    break;
                }
                break;

            case self::GOOD_ALL:
            case self::GOOD_INVALID:
            case self::GOOD_DIF:
                $exAstroPrev = $this->astroCache;
                $exAstroNext = $this->astroCache;

                for ($i = 0; $i <= $lastDay; $i++) {
                    $nGoodDay = $julianDay - $i;
                    AstroFunctions::getAstroValuesByDay($nGoodDay, $loc, $exAstroPrev, $tAstro);
                    $exdecPrev = AstroFunctions::DEG_TO_RAD($tAstro->dec[1]);
                    $exFj = $this->getFajIsh($lat, $exdecPrev, $conf->fajrAng);

                    if ($exFj != 99) {
                        $exIs = $this->getFajIsh($lat, $exdecPrev, $conf->ishaaAng);
                        if ($exIs != 99) {
                            $exTh = $this->getThuhr($lon, $tAstro);
                            $exSh = $this->getShoMag($loc, $tAstro, self::SHUROOQ);
                            $exMg = $this->getShoMag($loc, $tAstro, self::MAGHRIB);
                            $exAr = $this->getAssr($lat, $exdecPrev, $conf->mathhab);
                            break;
                        }
                    }

                    $nGoodDay = $julianDay + $i;
                    AstroFunctions::getAstroValuesByDay($nGoodDay, $loc, $exAstroNext, $tAstro);
                    $exdecNext = AstroFunctions::DEG_TO_RAD($tAstro->dec[1]);
                    $exFj = $this->getFajIsh($lat, $exdecNext, $conf->fajrAng);
                    if ($exFj != 99) {
                        $exIs = $this->getFajIsh($lat, $exdecNext, $conf->ishaaAng);
                        if ($exIs != 99) {
                            $exTh = $this->getThuhr($lon, $tAstro);
                            $exSh = $this->getShoMag($loc, $tAstro, self::SHUROOQ);
                            $exMg = $this->getShoMag($loc, $tAstro, self::MAGHRIB);
                            $exAr = $this->getAssr($lat, $exdecNext, $conf->mathhab);
                            break;
                        }
                    }
                }

                switch ($conf->extreme)
                {
                case self::GOOD_ALL:
                    $tempPrayer[0] = $exTh - $exFj;
                    $tempPrayer[1] = $exSh;
                    $tempPrayer[2] = $exTh;
                    $tempPrayer[3] = $exTh + $exAr;
                    $tempPrayer[4] = $exMg;
                    $tempPrayer[5] = $exTh + $exIs;
                    for ($i = 0; $i < 6; $i++) {
                        $pt[$i]->isExtreme = 1;
                    }
                    break;
                case self::GOOD_INVALID:
                    if ($tempPrayer[0] == 99) {
                        $tempPrayer[0] = $exTh - $exFj;
                        $pt[0]->isExtreme = 1;
                    }
                    if ($tempPrayer[5] == 99) {
                        $tempPrayer[5] = $exTh + $exIs;
                        $pt[5]->isExtreme = 1;
                    }
                    break;
                case self::GOOD_DIF:
                    break;
                }
                break;

            case self::SEVEN_NIGHT_ALWAYS:
            case self::SEVEN_NIGHT_INVALID:
            case self::SEVEN_DAY_ALWAYS:
            case self::SEVEN_DAY_INVALID:
            case self::HALF_ALWAYS:
            case self::HALF_INVALID:
                switch ($conf->extreme) {
                case self::SEVEN_NIGHT_ALWAYS:
                case self::SEVEN_NIGHT_INVALID:
                    $portion = (24 - ($tempPrayer[4] - $tempPrayer[1])) * (1 / 7.0);
                    break;
                case self::SEVEN_DAY_ALWAYS:
                case self::SEVEN_DAY_INVALID:
                    $portion = ($tempPrayer[4] - $tempPrayer[1]) * (1 / 7.0);
                    break;
                case self::HALF_ALWAYS:
                case self::HALF_INVALID:
                    $portion = (24 - $tempPrayer[4] - $tempPrayer[1]) * (1 / 2.0);
                    break;
                }

                if ($conf->extreme == self::SEVEN_NIGHT_INVALID ||
                    $conf->extreme == self::SEVEN_DAY_INVALID ||
                    $conf->extreme == self::HALF_INVALID) {
                    if ($tempPrayer[0] == 99) {
                        if ($conf->extreme == self::HALF_INVALID) {
                            $tempPrayer[0] = $portion - ($conf->fajrInv / 60.0);
                        } else {
                            $tempPrayer[0] = $tempPrayer[1] - $portion;
                        }
                        $pt[0]->isExtreme = 1;
                    }
                    if ($tempPrayer[5] == 99) {
                        if ($conf->extreme == self::HALF_INVALID) {
                            $tempPrayer[5] = $portion + ($conf->ishaaInv / 60.0) ;
                        } else {
                            $tempPrayer[5] = $tempPrayer[4] + $portion;
                        }
                        $pt[5]->isExtreme = 1;
                    }
                } else {
                    if ($conf->extreme == self::HALF_ALWAYS) {
                        $tempPrayer[0] = $portion - ($conf->fajrInv / 60.0);
                        $tempPrayer[5] = $portion + ($conf->ishaaInv / 60.0);
                    } else {
                        $tempPrayer[0] = $tempPrayer[1] - $portion;
                        $tempPrayer[5] = $tempPrayer[4] + $portion;
                    }
                    $pt[0]->isExtreme = 1;
                    $pt[5]->isExtreme = 1;
                }
                break;

            case self::MIN_ALWAYS:
                $tempPrayer[0] = $tempPrayer[1];
                $tempPrayer[5] = $tempPrayer[4];
                $pt[0]->isExtreme = 1;
                $pt[5]->isExtreme = 1;
                break;

            case self::MIN_INVALID:
                if ($tempPrayer[0] == 99) {
                    $exinterval = $conf->fajrInv / 60.0;
                    $tempPrayer[0] = $tempPrayer[1] - $exinterval;
                    $pt[0]->isExtreme = 1;
                }
                if ($tempPrayer[5] == 99) {
                    $exinterval = $conf->ishaaInv / 60.0;
                    $tempPrayer[5] = $tempPrayer[4] + $exinterval;
                    $pt[5]->isExtreme = 1;
                }
                break;
            }
        }

        if ($conf->extreme != self::MIN_INVALID &&
            $conf->extreme != self::HALF_INVALID &&
            $conf->extreme != self::HALF_ALWAYS) {
            if ($conf->fajrInv != 0) {
                $tempPrayer[0] = $tempPrayer[1] - ($conf->fajrInv / 60.0);
            }
            if ($conf->ishaaInv != 0) {
                $tempPrayer[5] = $tempPrayer[4] + ($conf->ishaaInv / 60.0);
            }
        }

        if ($type == self::IMSAAK || $type == self::NEXTFAJR) {
            $this->base6hm($tempPrayer[0], $loc, $conf, $pt[0], $type);
        }
        else {
            for ($i = 0; $i < 6; $i++) {
                $this->base6hm($tempPrayer[$i], $loc, $conf, $pt[$i], $i);
            }
        }
    }

    /**
     * Get base6
     *
     * @param double $bs
     * @param Location $loc
     * @param Method $conf
     * @param Prayer $pt
     * @param int $type
     *
     * @return void
     */
    public function base6hm($bs, &$loc, &$conf, &$pt, $type)
    {
        $min = 0; $sec = 0;

        if ($bs == 99) {
            $pt->hour = 99;
            $pt->minute = 99;
            $pt->second = 0;
            return;
        }

        if ($conf->offset == 1) {
            if ($type == self::IMSAAK || $type == self::NEXTFAJR) {
                $bs += ($conf->offList[0] / 60.0);
            } else {
                $bs += ($conf->offList[$type] / 60.0);
            }
        }

        if ($bs < 0) {
            while ($bs < 0) {
                $bs = 24 + $bs;
            }
        }

        $min = ($bs - floor($bs)) * 60;
        $sec = ($min - floor($min)) * 60;

        if ($conf->round == 1)
        {
            if ($sec >= self::DEFAULT_ROUND_SEC)
                $bs += 1/60.0;
            $min = ($bs - floor($bs)) * 60;
            $sec = 0;

        } else if ($conf->round == 2 || $conf->round == 3) {
            switch ($type) {
            case self::FAJR:
            case self::THUHR:
            case self::ASSR:
            case self::MAGHRIB:
            case self::ISHAA:
            case self::NEXTFAJR:
                if ($conf->round == 2) {
                    if ($sec >= self::DEFAULT_ROUND_SEC) {
                        $bs += 1 / 60.0;
                        $min = ($bs - floor($bs)) * 60;
                    }
                } else if ($conf->round == 3) {
                    if ($sec >= self::AGGRESSIVE_ROUND_SEC) {
                        $bs += 1 / 60.0;
                        $min = ($bs - floor($bs)) * 60;
                    }
                }
                $sec = 0;
                break;
            case self::SHUROOQ:
            case self::IMSAAK:
                $sec = 0;
                break;
            }
        }

        $bs += $loc->dst;
        if ($bs >= 24)
            $bs = fmod($bs, 24);

        $pt->hour = (int)$bs;
        $pt->minute = (int)$min;
        $pt->second = (int)$sec;
    }

    /**
     * Get imask
     *
     * @param Location $loc
     * @param Method $conf
     * @param Date $date
     * @param Prayer $pt
     *
     * @return void
     */
    public function getImsaak(&$loc, &$conf, &$date, &$pt)
    {
        $tmpConf = new Method();
        $lastDay = 0;
        $julianDay = 0;
        /* Prayer[6] */
        $temp[0] = new Prayer();
        $temp[1] = new Prayer();
        $temp[2] = new Prayer();
        $temp[3] = new Prayer();
        $temp[4] = new Prayer();
        $temp[5] = new Prayer();
        $tmpConf = $conf;

        if ($conf->fajrInv != 0) {
            if ($conf->imsaakInv == 0) {
                $tmpConf->fajrInv += self::DEF_IMSAAK_INTERVAL;
            } else {
                $tmpConf->fajrInv += $conf->imsaakInv;
            }
        } else if ($conf->imsaakInv != 0) {
            $tmpConf->offList[0] += ($conf->imsaakInv * -1);
            $tmpConf->offset = 1;
        } else {
            $tmpConf->fajrAng += $conf->imsaakAng;
        }

        $this->getDayInfo($date, $loc->gmtDiff, $lastDay, $julianDay);
        $this->getPrayerTimesByDay($loc, $tmpConf, $lastDay, $julianDay, $temp, self::IMSAAK);

        if ($temp[0]->isExtreme != 0)
        {
            $tmpConf = $conf;
            if ($conf->imsaakInv == 0)
            {
                $tmpConf->offList[0] -= self::DEF_IMSAAK_INTERVAL;
                $tmpConf->offset = 1;
            } else
            {
                $tmpConf->offList[0] -= $conf->imsaakInv;
                $tmpConf->offset = 1;
            }
            $this->getPrayerTimesByDay($loc, $tmpConf, $lastDay, $julianDay, $temp, self::IMSAAK);
        }

        $pt = $temp[0];
    }

    /**
     * Get day next imask
     *
     * @param Location $loc
     * @param Method $conf
     * @param Date $date
     * @param Prayer $pt
     *
     * @return void
     */
    public function getNextDayImsaak(&$loc, &$conf, &$date, &$pt)
    {
        $temppt = new Prayer();
        $tempd = $date;
        $tempd->day++;
        $this->getImsaak($loc, $conf, $tempd, $temppt);
        $pt = $temppt;
    }

    /**
     * Get next day fajir
     *
     * @param Location $loc
     * @param Method $conf
     * @param Date $date
     * @param Prayer $pt
     *
     * @return void
     */
    public function getNextDayFajr(&$loc, &$conf, &$date, &$pt)
    {
        /* Prayer[6] */
        $temp[0] = new Prayer();
        $temp[1] = new Prayer();
        $temp[2] = new Prayer();
        $temp[3] = new Prayer();
        $temp[4] = new Prayer();
        $temp[5] = new Prayer();
        $lastDay = 0;
        $julianDay = 0;

        $this->getDayInfo($date, $loc->gmtDiff, $lastDay, $julianDay);
        $this->getPrayerTimesByDay($loc, $conf, $lastDay, $julianDay + 1, $temp, self::NEXTFAJR);

        $pt = $temp[0];
    }

    /**
     * Get fajir/isha
     *
     * @param double $Lat
     * @param double $dec
     * @param double $Ang
     *
     * @return double
     */
    public function getFajIsh($Lat, $dec, $Ang)
    {
        $part1 = cos(AstroFunctions::DEG_TO_RAD($Lat)) * cos($dec);
        $part2 = -sin(AstroFunctions::DEG_TO_RAD($Ang)) - sin(AstroFunctions::DEG_TO_RAD($Lat)) * sin($dec);
        $part3 = $part2 / $part1;
        if ($part3 <= AstroFunctions::INVALID_TRIGGER) {
            return 99;
        }
        return AstroFunctions::DEG_TO_10_BASE * AstroFunctions::RAD_TO_DEG(acos($part3));
    }

    /**
     * Get shorooq
     *
     * @param Location $loc
     * @param Astro $astro
     * @param int $type
     *
     * @return double
     */
    public function getShoMag(&$loc, &$astro, $type)
    {
        $lhour = 0; $M = 0; $sidG = 0; $ra0 = $astro->ra[0]; $ra2 = $astro->ra[2];
        $A = 0; $B = 0; $H = 0; $sunAlt = 0; $R = 0; $tH = 0;
        $part1 = sin(AstroFunctions::DEG_TO_RAD($loc->degreeLat)) * sin(AstroFunctions::DEG_TO_RAD($astro->dec[1]));
        $part2a = AstroFunctions::CENTER_OF_SUN_ANGLE;
        $part2 = sin(AstroFunctions::DEG_TO_RAD($part2a)) - $part1;
        $part3 = cos(AstroFunctions::DEG_TO_RAD($loc->degreeLat)) * cos(AstroFunctions::DEG_TO_RAD($astro->dec[1]));
        $part4 = $part2 / $part3;

        if ($part4 <= -1 || $part4 >= 1) {
            return 99;
        }

        $lhour =  AstroFunctions::limitAngle180((AstroFunctions::RAD_TO_DEG(acos($part4))));
        $M = (($astro->ra[1] - $loc->degreeLong - $astro->sid[1]) / 360.0);

        if ($type == self::SHUROOQ) {
            $M = $M - ($lhour / 360.0);
        }
        if ($type == self::MAGHRIB) {
            $M = $M + ($lhour / 360.0);
        }

        $M = AstroFunctions::limitAngle111($M);

        $sidG = AstroFunctions::limitAngle($astro->sid[1] + 360.985647 * $M);

        $ra0 = $astro->ra[0];
        $ra2 = $astro->ra[2];

        if ($astro->ra[1] > 350 && $astro->ra[2] < 10) {
            $ra2 += 360;
        }
        if ($astro->ra[0] > 350 && $astro->ra[1] < 10) {
            $ra0 = 0;
        }

        $A = $astro->ra[1] + ($M * (($astro->ra[1] - $ra0) +
                                    ($ra2 - $astro->ra[1] ) +
                                    (($ra2 - $astro->ra[1] ) -
                                     ($astro->ra[1] - $ra0)) * $M) / 2.0);

        $B = $astro->dec[1] + ($M * (($astro->dec[1] - $astro->dec[0]) +
                                     ($astro->dec[2] - $astro->dec[1]) +
                                     (($astro->dec[2] - $astro->dec[1]) -
                                      ($astro->dec[1] - $astro->dec[0])) * $M) / 2.0);

        $H = AstroFunctions::limitAngle180between($sidG + $loc->degreeLong - $A);

        $tH = $H - AstroFunctions::RAD_TO_DEG($astro->dra[1]);

        $sunAlt = AstroFunctions::RAD_TO_DEG(asin(sin(AstroFunctions::DEG_TO_RAD($loc->degreeLat)) * sin(AstroFunctions::DEG_TO_RAD($B))
                                  + cos(AstroFunctions::DEG_TO_RAD($loc->degreeLat)) * cos(AstroFunctions::DEG_TO_RAD($B))
                                  * cos(AstroFunctions::DEG_TO_RAD($tH))));

        $sunAlt += AstroFunctions::getRefraction($loc, $sunAlt);

        $R = ($M + (($sunAlt - AstroFunctions::CENTER_OF_SUN_ANGLE + (AstroFunctions::ALTITUDE_REFRACTION * pow($loc->seaLevel, 0.5)))
                    / (360.0 * cos(AstroFunctions::DEG_TO_RAD($B)) * cos(AstroFunctions::DEG_TO_RAD($loc->degreeLat)) * sin(AstroFunctions::DEG_TO_RAD($tH)))));

        return $R * 24.0;
    }

    /**
     * Get thur
     *
     * @param double $lon
     * @param Astro $astro
     *
     * @return double
     */
    public function getThuhr($lon, &$astro)
    {
        $M = 0; $sidG = 0;
        $ra0 = $astro->ra[0]; $ra2 = $astro->ra[2];
        $A = 0; $H = 0;

        $M = (($astro->ra[1] - $lon - $astro->sid[1]) / 360.0);
        $M = AstroFunctions::limitAngle111($M);
        $sidG = $astro->sid[1] + 360.985647 * $M;

        if ($astro->ra[1] > 350 && $astro->ra[2] < 10) {
            $ra2 += 360;
        }
        if ($astro->ra[0] > 350 && $astro->ra[1] < 10) {
            $ra0 = 0;
        }

        $A = $astro->ra[1] + ($M * (($astro->ra[1] - $ra0)
                                    + ($ra2 - $astro->ra[1]) +
                                    (($ra2 - $astro->ra[1]) -
                                     ($astro->ra[1] - $ra0)) * $M) / 2.0);

        $H = AstroFunctions::limitAngle180between($sidG + $lon - $A);

        return 24.0 * ($M - $H / 360.0);
    }

    /**
     * Get asr
     *
     * @param double $Lat
     * @param double $dec
     * @param int $mathhab
     *
     * @return double
     */
    public function getAssr($Lat, $dec, $mathhab)
    {
        $part1 = 0; $part2 = 0; $part3 = 0; $part4 = 0;

        $part1 = $mathhab + tan(AstroFunctions::DEG_TO_RAD($Lat) - $dec);
        if ($part1 < 1 || $Lat < 0) {
            $part1 = $mathhab - tan(AstroFunctions::DEG_TO_RAD($Lat) - $dec);
        }

        $part2 = (AstroFunctions::PI / 2.0) - atan($part1);
        $part3 = sin($part2) - sin(AstroFunctions::DEG_TO_RAD($Lat)) * sin($dec);
        $part4 = ($part3 / (cos(AstroFunctions::DEG_TO_RAD($Lat)) * cos($dec)));

        return AstroFunctions::DEG_TO_10_BASE * AstroFunctions::RAD_TO_DEG(acos($part4));
    }

    /**
     * Get day of year
     *
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return int
     */
    public function getDayofYear($year, $month, $day)
    {
        $i = 0;
        $isLeap = (($year & 3) == 0) && (($year % 100) != 0 || ($year % 400) == 0);
        $dayList = array (
                          array(0,31,28,31,30,31,30,31,31,30,31,30,31),
                          array(0,31,29,31,30,31,30,31,31,30,31,30,31)
        );

        for ($i = 1; $i < $month; $i++) {
            $day += $dayList[$isLeap][$i];
        }

        return $day;
    }

    /**
     * dms 2 decimal
     *
     * @param int $deg
     * @param int $min
     * @param double $sec
     * @param char $dir
     *
     * @return double
     */
    public function dms2Decimal($deg, $min, $sec, /* char */ $dir)
    {
        $sum = $deg + (($min / 60.0) + ($sec / 3600.0));
        if ($dir == 'S' || $dir == 'W' || $dir == 's' || $dir == 'w') {
            return $sum * (-1.0);
        }
        return $sum;
    }

    /**
     * decimal 2 Dms
     *
     * @param double $decimal
     * @param int $deg
     * @param int $min
     * @param double $sec
     *
     * @return void
     */
    public function decimal2Dms( $decimal, &$deg, &$min, &$sec)
    {
        $tempmin = 0; $tempsec = 0; $n1 = 0; $n2 = 0;

        $tempmin = $this->modf($decimal, $n1) * 60.0;
        $tempsec = $this->modf($tempmin, $n2) * 60.0;

        $deg = (int)$n1;
        $min = (int)$n2;
        $sec = $tempsec;
    }

    /**
     * Get day info
     *
     * @param Date $date
     * @param double $gmt
     * @param int $lastDay
     * @param double $julianDay
     *
     * @return void
     */
    public function getDayInfo(&$date, $gmt, &$lastDay, &$julianDay)
    {
        $ld = 0;
        $jd = 0;
        $ld = $this->getDayOfYear($date->year, 12, 31);
        $jd = AstroFunctions::getJulianDay($date, $gmt);
        $lastDay = $ld;
        $julianDay = $jd;
    }

    /**
     * Get qibla
     *
     * @param Location $loc
     *
     * @return double
     */
    public function getNorthQibla(&$loc)
    {
        $num = 0; $denom = 0;
        $num = sin(AstroFunctions::DEG_TO_RAD($loc->degreeLong) - AstroFunctions::DEG_TO_RAD(self::KAABA_LONG));
        $denom = (cos(AstroFunctions::DEG_TO_RAD($loc->degreeLat)) * tan(AstroFunctions::DEG_TO_RAD(self::KAABA_LAT))) -
            (sin(AstroFunctions::DEG_TO_RAD($loc->degreeLat)) * ((cos((AstroFunctions::DEG_TO_RAD ($loc->degreeLong) -
                                                       AstroFunctions::DEG_TO_RAD(self::KAABA_LONG))))));
        return AstroFunctions::RAD_TO_DEG(atan2($num, $denom));
    }

    /**
     * modf implementation
     *
     * @param double $x
     * @param double $i
     *
     * @return double
     */
    public function modf($x, &$i)
    {
        $s = $x > 0 ? 1 : -1;
        $x = abs($x);
        $i = floor($x);
        $f = $x - $i;
        $i *= $s;
        $f *= $s;
        return $f;
    }
}