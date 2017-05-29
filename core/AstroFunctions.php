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

class AstroFunctions
{
    /**
     * Constants
     */
    const INVALID_TRIGGER = -.999;
    const PI = 3.1415926535898;
    const DEG_TO_10_BASE = 1/15.0;
    const CENTER_OF_SUN_ANGLE = -0.833370;
    const ALTITUDE_REFRACTION = 0.0347;
    const REF_LIMIT = 9999999;

    /**
     * Degree to rad
     *
     * @param double $A
     *
     * @return double
     */
    public static function DEG_TO_RAD($A)
    {
        return $A * (AstroFunctions::PI / 180.0);
    }

    /**
     * Rad to degrees
     *
     * @param double $A
     *
     * @return double
     */
    public static function RAD_TO_DEG($A)
    {
        return $A / (AstroFunctions::PI / 180.0);
    }

    /**
     * Get refration
     *
     * @param Location $loc
     * @param double $sunAlt
     *
     * @return double
     */
    public static function getRefraction(&$loc, $sunAlt)
    {
        $part1 = ($loc->pressure / 1010.0) * (283.0 / (273.0 + $loc->temperature));
        $part2 = 1.0 / tan(AstroFunctions::DEG_TO_RAD($sunAlt + (7.31 / ($sunAlt + 4.4)))) + 0.0013515;
        return ($part1 * $part2) / 60.0;
    }

    /**
     * Get julian day
     *
     * @param Date $date
     * @param douuble $gmt
     *
     * @return double
     */
    public static function getJulianDay(&$date, $gmt)
    {
        $jdB = 0;
        $jdY = $date->year;
        $jdM = $date->month;
        $JD = 0;

        if ($date->month <= 2) {
            $jdY -= 1;
            $jdM += 12;
        }

        if ($date->year < 1) {
            $jdY += 1;
        }

        if (($date->year > 1582) || (($date->year == 1582) && (($date->month > 10) || (($date->month == 10) && ($date->day >= 4))))) {
            $jdB = 2 - floor($jdY / 100.0) + floor(($jdY / 100.0) / 4.0);
        }

        $JD = floor(365.25 * ($jdY + 4716.0)) + floor(30.6001 * ($jdM + 1)) + ($date->day + (-$gmt) / 24.0) + $jdB - 1524.5;
        return $JD;
    }

    /**
     * Get astro value by day
     *
     * @param double $julianDay
     * @param Location $loc
     * @param Astro $astro
     * @param Astro $topAstro
     *
     * @return void
     */
    public static function getAstroValuesByDay($julianDay, &$loc, &$astro, &$topAstro)
    {
        $ad = new AstroDay();

        if ($astro->jd == $julianDay - 1) {
            $astro->ra[0] = $astro->ra[1];
            $astro->ra[1] = $astro->ra[2];
            $astro->dec[0] = $astro->dec[1];
            $astro->dec[1] = $astro->dec[2];
            $astro->sid[0] = $astro->sid[1];
            $astro->sid[1] = $astro->sid[2];
            $astro->dra[0] = $astro->dra[1];
            $astro->dra[1] = $astro->dra[2];
            $astro->rsum[0] = $astro->rsum[1];
            $astro->rsum[1] = $astro->rsum[2];
            AstroFunctions::computeAstroDay($julianDay + 1, $ad);
            $astro->ra[2] = $ad->ra;
            $astro->dec[2] = $ad->dec;
            $astro->sid[2] = $ad->sidtime;
            $astro->dra[2] = $ad->dra;
            $astro->rsum[2] = $ad->rsum;
        } else if ($astro->jd == $julianDay + 1) {
            $astro->ra[2] = $astro->ra[1];
            $astro->ra[1] = $astro->ra[0];
            $astro->dec[2] = $astro->dec[1];
            $astro->dec[1] = $astro->dec[0];
            $astro->sid[2] = $astro->sid[1];
            $astro->sid[1] = $astro->sid[0];
            $astro->dra[2] = $astro->dra[1];
            $astro->dra[1] = $astro->dra[0];
            $astro->rsum[2] = $astro->rsum[1];
            $astro->rsum[1] = $astro->rsum[0];
            AstroFunctions::computeAstroDay($julianDay - 1, $ad);
            $astro->ra[0] = $ad->ra;
            $astro->dec[0] = $ad->dec;
            $astro->sid[0] = $ad->sidtime;
            $astro->dra[0] = $ad->dra;
            $astro->rsum[0] = $ad->rsum;
        } else if ($astro->jd != $julianDay) {
            AstroFunctions::computeAstroDay($julianDay - 1, $ad);
            $astro->ra[0] = $ad->ra;
            $astro->dec[0] = $ad->dec;
            $astro->sid[0] = $ad->sidtime;
            $astro->dra[0] = $ad->dra;
            $astro->rsum[0] = $ad->rsum;
            AstroFunctions::computeAstroDay($julianDay, $ad);
            $astro->ra[1] = $ad->ra;
            $astro->dec[1] = $ad->dec;
            $astro->sid[1] = $ad->sidtime;
            $astro->dra[1] = $ad->dra;
            $astro->rsum[1] = $ad->rsum;
            AstroFunctions::computeAstroDay($julianDay + 1, $ad);
            $astro->ra[2] = $ad->ra;
            $astro->dec[2] = $ad->dec;
            $astro->sid[2] = $ad->sidtime;
            $astro->dra[2] = $ad->dra;
            $astro->rsum[2] = $ad->rsum;
        }

        $astro->jd = $julianDay;
        AstroFunctions::computeTopAstro($loc, $astro, $topAstro);
    }

    /**
     * Compute Astro Day
     *
     * @param double $JD
     * @param AstroDay $astroday
     *
     * @return void
     */
    public static function computeAstroDay($JD, &$astroday)
    {
        $i = 0;
        $R = 0; $Gg = 0; $G = 0;
        $tL = 0; $L = 0;
        $tB = 0; $B = 0;
        $X0 = 0; $X1 = 0; $X2 = 0; $X3 = 0; $X4 = 0;
        $U = 0; $E0 = 0; $E = 0; $lamda = 0; $V0 = 0; $V = 0;
        $RAn = 0; $RAd = 0; $RA = 0; $DEC = 0;
        $B0sum = 0; $B1sum = 0;
        $R0sum=0; $R1sum=0; $R2sum=0; $R3sum=0; $R4sum=0;
        $L0sum=0; $L1sum=0; $L2sum=0; $L3sum=0; $L4sum=0; $L5sum=0;
        $xsum = 0; $psi = 0; $epsilon = 0;
        $deltaPsi = 0; $deltaEps = 0;
        $JC = ($JD - 2451545.0) / 36525.0;
        $JM = $JC / 10.0 ;
        $JM2 = pow($JM, 2);
        $JM3 = pow($JM, 3);
        $JM4 = pow($JM, 4);
        $JM5 = pow($JM, 5);

        for ($i = 0; $i < 64; $i++) {
            $L0sum += AstroConstants::$L0[$i][0] * cos(AstroConstants::$L0[$i][1] + AstroConstants::$L0[$i][2] * $JM);
        }
        for ($i = 0; $i < 34; $i++) {
            $L1sum += AstroConstants::$L1[$i][0] * cos(AstroConstants::$L1[$i][1] + AstroConstants::$L1[$i][2] * $JM);
        }
        for ($i=0; $i < 20; $i++) {
            $L2sum += AstroConstants::$L2[$i][0] * cos(AstroConstants::$L2[$i][1] + AstroConstants::$L2[$i][2] * $JM);
        }
        for ($i=0; $i < 7; $i++) {
            $L3sum += AstroConstants::$L3[$i][0] * cos(AstroConstants::$L3[$i][1] + AstroConstants::$L3[$i][2] * $JM);
        }
        for ($i=0; $i < 3; $i++) {
            $L4sum += AstroConstants::$L4[$i][0] * cos(AstroConstants::$L4[$i][1] + AstroConstants::$L4[$i][2] * $JM);
        }
        $L5sum = AstroConstants::$L5[0][0] * cos(AstroConstants::$L5[0][1] + AstroConstants::$L5[0][2] * $JM);

        $tL = ($L0sum + ($L1sum * $JM) + ($L2sum * $JM2)
              + ($L3sum * $JM3) + ($L4sum * $JM4)
              + ($L5sum * $JM5)) / pow (10, 8);

        $L = AstroFunctions::limitAngle(AstroFunctions::RAD_TO_DEG($tL));

        for ($i=0; $i < 5; $i++) {
            $B0sum += AstroConstants::$B0[$i][0] * cos(AstroConstants::$B0[$i][1] + AstroConstants::$B0[$i][2] * $JM);
        }
        for ($i=0; $i < 2; $i++) {
            $B1sum += AstroConstants::$B1[$i][0] * cos(AstroConstants::$B1[$i][1] + AstroConstants::$B1[$i][2] * $JM);
        }

        $tB= ($B0sum + ($B1sum * $JM)) / pow (10, 8);
        $B = AstroFunctions::RAD_TO_DEG($tB);

        for ($i = 0; $i < 40; $i++) {
            $R0sum += AstroConstants::$R0[$i][0] * cos(AstroConstants::$R0[$i][1] + AstroConstants::$R0[$i][2] * $JM);
        }
        for ($i = 0; $i < 10; $i++) {
            $R1sum += AstroConstants::$R1[$i][0] * cos(AstroConstants::$R1[$i][1] + AstroConstants::$R1[$i][2] * $JM);
        }
        for ($i = 0; $i < 6; $i++) {
            $R2sum += AstroConstants::$R2[$i][0] * cos(AstroConstants::$R2[$i][1] + AstroConstants::$R2[$i][2] * $JM);
        }
        for ($i = 0; $i < 2; $i++) {
            $R3sum += AstroConstants::$R3[$i][0] * cos(AstroConstants::$R3[$i][1] + AstroConstants::$R3[$i][2] * $JM);
        }
        $R4sum = AstroConstants::$R4[0][0] * cos(AstroConstants::$R4[0][1] + AstroConstants::$R4[0][2] * $JM);

        $R = ($R0sum + ($R1sum * $JM) + ($R2sum * $JM2)
             + ($R3sum * $JM3) + ($R4sum * $JM4)) / pow (10, 8);

        $G = AstroFunctions::limitAngle(($L + 180));
        $Gg = -$B;

        $X0 = 297.85036 + (445267.111480 * $JC) - (0.0019142 * pow ($JC, 2)) +
            pow ($JC, 3) / 189474.0;
        $X1 = 357.52772 + (35999.050340 * $JC) - (0.0001603 * pow ($JC, 2)) -
            pow ($JC, 3) / 300000.0;
        $X2 = 134.96298 + (477198.867398 * $JC) + (0.0086972 * pow ($JC, 2)) +
            pow ($JC, 3) / 56250.0;
        $X3 = 93.27191 + (483202.017538 * $JC) - (0.0036825 * pow ($JC, 2)) +
            pow ($JC, 3) / 327270.0;
        $X4 = 125.04452 - (1934.136261 * $JC) + (0.0020708 * pow ($JC, 2)) +
            pow ($JC, 3) / 450000.0;

        for ($i = 0; $i < 63; $i++) {
            $xsum += $X0 * AstroConstants::$SINCOEFF[$i][0];
            $xsum += $X1 * AstroConstants::$SINCOEFF[$i][1];
            $xsum += $X2 * AstroConstants::$SINCOEFF[$i][2];
            $xsum += $X3 * AstroConstants::$SINCOEFF[$i][3];
            $xsum += $X4 * AstroConstants::$SINCOEFF[$i][4];
            $psi += (AstroConstants::$PE[$i][0] + $JC * AstroConstants::$PE[$i][1]) * sin(AstroFunctions::DEG_TO_RAD($xsum));
            $epsilon += (AstroConstants::$PE[$i][2] + $JC * AstroConstants::$PE[$i][3]) * cos(AstroFunctions::DEG_TO_RAD($xsum));
            $xsum = 0;
        }

        $deltaPsi = $psi / 36000000.0;
        $deltaEps = $epsilon / 36000000.0;

        $U = $JM / 10.0;
        $E0 = 84381.448 - 4680.93 * $U - 1.55 * pow($U, 2) + 1999.25 * pow($U, 3)
            - 51.38 * pow($U, 4) - 249.67 * pow($U, 5) - 39.05 * pow($U, 6) + 7.12
            * pow($U, 7) + 27.87 * pow($U, 8) + 5.79 * pow($U, 9) + 2.45 * pow($U, 10);
        $E = $E0 / 3600.0 + $deltaEps;
        $lamda = $G + $deltaPsi + (-20.4898 / (3600.0 * $R));

        $V0 = 280.46061837 + 360.98564736629 * ($JD - 2451545) +
            0.000387933 * pow($JC, 2) - pow($JC, 3) / 38710000.0;
        $V = AstroFunctions::limitAngle($V0) + $deltaPsi * cos(AstroFunctions::DEG_TO_RAD($E));

        $RAn = sin(AstroFunctions::DEG_TO_RAD($lamda)) * cos(AstroFunctions::DEG_TO_RAD($E)) -
            tan(AstroFunctions::DEG_TO_RAD($Gg)) * sin(AstroFunctions::DEG_TO_RAD($E));
        $RAd = cos(AstroFunctions::DEG_TO_RAD($lamda));
        $RA = AstroFunctions::limitAngle(AstroFunctions::RAD_TO_DEG(atan2($RAn, $RAd)));

        $DEC = asin(sin(AstroFunctions::DEG_TO_RAD($Gg)) * cos(AstroFunctions::DEG_TO_RAD($E)) +
                    cos(AstroFunctions::DEG_TO_RAD($Gg)) * sin(AstroFunctions::DEG_TO_RAD($E)) *
                    sin(AstroFunctions::DEG_TO_RAD($lamda)));

        $astroday->ra = $RA;
        $astroday->dec = $DEC;
        $astroday->sidtime = $V;
        $astroday->dra = 0;
        $astroday->rsum = $R;
    }

    /**
     * Compute Top Astro
     *
     * @param Location $loc
     * @param Astro $astro
     * @param Astro $topAstro
     *
     * @return void
     */
    public static function computeTopAstro(&$loc, &$astro, &$topAstro)
    {
        $i = 0;
        $lHour = 0; $SP = 0;
        $tU = 0; $tCos = 0; $tSin = 0; $tRA0 = 0; $tRA = 0; $tDEC = 0;

        for ($i = 0; $i < 3; $i++) {
            $lHour = AstroFunctions::limitAngle($astro->sid[$i] + $loc->degreeLong - $astro->ra[$i]);

            $SP = 8.794 / (3600 * $astro->rsum[$i]);

            $tU = atan(0.99664719 * tan(AstroFunctions::DEG_TO_RAD($loc->degreeLat)));

            $tCos = cos($tU) + (($loc->seaLevel) / 6378140.0) *
                cos(AstroFunctions::DEG_TO_RAD($loc->degreeLat));

            $tSin = 0.99664719 * sin($tU) + ($loc->seaLevel/6378140.0) *
                sin(AstroFunctions::DEG_TO_RAD($loc->degreeLat));

            $tRA0 = (((-$tCos) * sin(AstroFunctions::DEG_TO_RAD($SP)) * sin(AstroFunctions::DEG_TO_RAD($lHour)))
                    / (cos($astro->dec[$i]) - $tCos * sin(AstroFunctions::DEG_TO_RAD($SP)) * cos(AstroFunctions::DEG_TO_RAD($lHour))));

            $tRA = $astro->ra[$i] +  AstroFunctions::RAD_TO_DEG($tRA0);

            $tDEC = AstroFunctions::RAD_TO_DEG(atan2((sin($astro->dec[$i]) - $tSin * sin(AstroFunctions::DEG_TO_RAD($SP))) * cos($tRA0),
                                    cos($astro->dec[$i]) - $tCos * sin(AstroFunctions::DEG_TO_RAD($SP)) *
                                    cos(AstroFunctions::DEG_TO_RAD($lHour))));

            $topAstro->ra[$i] = $tRA;
            $topAstro->dec[$i] = $tDEC;
            $topAstro->sid[$i] = $astro->sid[$i];
            $topAstro->dra[$i] = $tRA0;
            $topAstro->rsum[$i] = $astro->rsum[$i];
        }
    }

    /**
     * Limit Angle
     *
     * @param double $L
     *
     * @return double
     */
    public static function limitAngle($L)
    {
        $F = 0;
        $L /= 360.0;
        $F = $L - floor($L);
        if ($F > 0) {
            return 360 * $F;
        } else if ($F < 0) {
            return 360 - 360 * $F;
        } else {
            return $L;
        }
    }

    /**
     * Angle Limiter 180
     *
     * @param double $L
     *
     * @return double
     */
    public static function limitAngle180($L)
    {
        $F = 0;
        $L /= 180.0;
        $F = $L - floor($L);
        if ($F > 0)
            return 180 * $F;
        else if ($F < 0)
            return 180 - 180 * $F;
        else return $L;
    }

    /**
     * Angle Limiter 111
     *
     * @param double $L
     *
     * @return double
     */
    public static function limitAngle111($L)
    {
        $F = 0;
        $F = $L - floor($L);
        if ($F < 0)
            return $F += 1;
        return $F;
    }

    /**
     * Angle Limiter Between 180
     *
     * @param double $L
     *
     * @return double
     */
    public static function limitAngle180between($L)
    {
        $F;
        $L /= 360.0;
        $F = ($L - floor($L)) * 360.0;
        if  ($F < -180)
            $F += 360;
        else if ($F > 180)
            $F -= 360;
        return $F;
    }
}