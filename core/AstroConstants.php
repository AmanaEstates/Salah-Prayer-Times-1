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
class AstroConstants
{
    /**
     * Static Variable
     *
     * @var array
     */
    public static $L0 = array(
        array(175347046, 0, 0),
        array(3341656, 4.6692568, 6283.07585),
        array(34894, 4.6261, 12566.1517),
        array(3497, 2.7441, 5753.3849),
        array(3418, 2.8289, 3.5231),
        array(3136, 3.6277, 77713.7715),
        array(2676, 4.4181, 7860.4194),
        array(2343, 6.1352, 3930.2097),
        array(1324, 0.7425, 11506.7698),
        array(1273, 2.0371, 529.691),
        array(1199, 1.1096, 1577.3435),
        array(990, 5.233, 5884.927),
        array(902, 2.045, 26.298),
        array(857, 3.508, 398.149),
        array(780, 1.179, 5223.694),
        array(753, 2.533, 5507.553),
        array(505, 4.583, 18849.228),
        array(492, 4.205, 775.523),
        array(357, 2.92, 0.067),
        array(317, 5.849, 11790.629),
        array(284, 1.899, 796.298),
        array(271, 0.315, 10977.079),
        array(243, 0.345, 5486.778),
        array(206, 4.806, 2544.314),
        array(205, 1.869, 5573.143),
        array(202, 2.4458, 6069.777),
        array(156, 0.833, 213.299),
        array(132, 3.411, 2942.463),
        array(126, 1.083, 20.775),
        array(115, 0.645, 0.98),
        array(103, 0.636, 4694.003),
        array(102, 0.976, 15720.839),
        array(102, 4.267, 7.114),
        array(99, 6.21, 2146.17),
        array(98, 0.68, 155.42),
        array(86, 5.98, 161000.69),
        array(85, 1.3, 6275.96),
        array(85, 3.67, 71430.7),
        array(80, 1.81, 17260.15),
        array(79, 3.04, 12036.46),
        array(71, 1.76, 5088.63),
        array(74, 3.5, 3154.69),
        array(74, 4.68, 801.82),
        array(70, 0.83, 9437.76),
        array(62, 3.98, 8827.39),
        array(61, 1.82, 7084.9),
        array(57, 2.78, 6286.6),
        array(56, 4.39, 14143.5),
        array(56, 3.47, 6279.55),
        array(52, 0.19, 12139.55),
        array(52, 1.33, 1748.02),
        array(51, 0.28, 5856.48),
        array(49, 0.49, 1194.45),
        array(41, 5.37, 8429.24),
        array(41, 2.4, 19651.05),
        array(39, 6.17, 10447.39),
        array(37, 6.04, 10213.29),
        array(37, 2.57, 1059.38),
        array(36, 1.71, 2352.87),
        array(36, 1.78, 6812.77),
        array(33, 0.59, 17789.85),
        array(30, 0.44, 83996.85),
        array(30, 2.74, 1349.87),
        array(25, 3.16, 4690.48)
    );

    /**
     * Static Variable
     *
     * @var array
     */
    public static $L1 = array(
        array(628331966747.0, 0, 0),
        array(206059, 2.678235, 6283.07585),
        array(4303, 2.6351, 12566.1517),
        array(425, 1.59, 3.523),
        array(119, 5.796, 26.298),
        array(109, 2.966, 1577.344),
        array(93, 2.59, 18849.23),
        array(72, 1.14, 529.69),
        array(68, 1.87, 398.15),
        array(67, 4.41, 5507.55),
        array(59, 2.89, 5223.69),
        array(56, 2.17, 155.42),
        array(45, 0.4, 796.3),
        array(36, 0.47, 775.52),
        array(29, 2.65, 7.11),
        array(21, 5.34, 0.98),
        array(19, 1.85, 5486.78),
        array(19, 4.97, 213.3),
        array(17, 2.99, 6275.96),
        array(16, 0.03, 2544.31),
        array(16, 1.43, 2146.17),
        array(15, 1.21, 10977.08),
        array(12, 2.83, 1748.02),
        array(12, 3.26, 5088.63),
        array(12, 5.27, 1194.45),
        array(12, 2.08, 4694),
        array(11, 0.77, 553.57),
        array(10, 1.3, 3286.6),
        array(10, 4.24, 1349.87),
        array(9, 2.7, 242.73),
        array(9, 5.64, 951.72),
        array(8, 5.3, 2352.87),
        array(6, 2.65, 9437.76),
        array(6, 4.67, 4690.48)
    );

    /**
     * Static Variable
     *
     * @var array
     */
    public static $L2 = array(
        array(52919, 0, 0),
        array(8720, 1.0721, 6283.0758),
        array(309, 0.867, 12566.152),
        array(27, 0.05, 3.52),
        array(16, 5.19, 26.3),
        array(16, 3.68, 155.42),
        array(10, 0.76, 18849.23),
        array(9, 2.06, 77713.77),
        array(7, 0.83, 775.52),
        array(5, 4.66, 1577.34),
        array(4, 1.03, 7.11),
        array(4, 3.44, 5573.14),
        array(3, 5.14, 796.3),
        array(3, 6.05, 5507.55),
        array(3, 1.19, 242.73),
        array(3, 6.12, 529.69),
        array(3, 0.31, 398.15),
        array(3, 2.28, 553.57),
        array(2, 4.38, 5223.69),
        array(2, 3.75, 0.98)
    );

    /**
     * Static Variable
     *
     * @var array
     */
    public static $L3 = array(
        array(289, 5.844, 6283.076),
        array(35, 0, 0),
        array(17, 5.49, 12566.15),
        array(3, 5.2, 155.42),
        array(1, 4.72, 3.52),
        array(1, 5.3, 18849.23),
        array(1, 5.97, 242.73)
    );

    /**
     * Static Variable
     *
     * @var array
     */
    public static $L4 = array(
        array(114.0, 3.142, 0.0),
        array(8.0, 4.13, 6283.08),
        array(1.0, 3.84, 12566.15)
    );

    /**
     * Static Variable
     *
     * @var array
     */
    public static $L5 = array(
        array(1, 3.14, 0)
    );

    /**
     * Static Variable
     *
     * @var array
     */
    public static $B0 = array(
        array(280, 3.199, 84334.662),
        array(102, 5.422, 5507.553),
        array(80, 3.88, 5223.69),
        array(44, 3.7, 2352.87),
        array(32, 4, 1577.34)
    );

    /**
     * Static Variable
     *
     * @var array
     */
    public static $B1 = array(
        array(9, 3.9, 5507.55),
        array(6, 1.73, 5223.69)
    );

    /**
     * Static Variable
     *
     * @var array
     */
    public static $R0 = array(
        array(100013989, 0, 0),
        array(1670700, 3.0984635, 6283.07585),
        array(13956, 3.05525, 12566.1517),
        array(3084, 5.1985, 77713.7715),
        array(1628, 1.1739, 5753.3849),
        array(1576, 2.8469, 7860.4194),
        array(925, 5.453, 11506.77),
        array(542, 4.564, 3930.21),
        array(472, 3.661, 5884.927),
        array(346, 0.964, 5507.553),
        array(329, 5.9, 5223.694),
        array(307, 0.299, 5573.143),
        array(243, 4.273, 11790.629),
        array(212, 5.847, 1577.344),
        array(186, 5.022, 10977.079),
        array(175, 3.012, 18849.228),
        array(110, 5.055, 5486.778),
        array(98, 0.89, 6069.78),
        array(86, 5.69, 15720.84),
        array(86, 1.27, 161000.69),
        array(85, 0.27, 17260.15),
        array(63, 0.92, 529.69),
        array(57, 2.01, 83996.85),
        array(56, 5.24, 71430.7),
        array(49, 3.25, 2544.31),
        array(47, 2.58, 775.52),
        array(45, 5.54, 9437.76),
        array(43, 6.01, 6275.96),
        array(39, 5.36, 4694),
        array(38, 2.39, 8827.39),
        array(37, 0.83, 19651.05),
        array(37, 4.9, 12139.55),
        array(36, 1.67, 12036.46),
        array(35, 1.84, 2942.46),
        array(33, 0.24, 7084.9),
        array(32, 0.18, 5088.63),
        array(32, 1.78, 398.15),
        array(28, 1.21, 6286.6),
        array(28, 1.9, 6279.55),
        array(26, 4.59, 10447.39)
    );

    /**
     * Static Variable
     *
     * @var array
     */
    public static $R1 = array(
        array(103019, 1.10749, 6283.07585),
        array(1721, 1.0644, 12566.1517),
        array(702, 3.142, 0),
        array(32, 1.02, 18849.23),
        array(31, 2.84, 5507.55),
        array(25, 1.32, 5223.69),
        array(18, 1.42, 1577.34),
        array(10, 5.91, 10977.08),
        array(9, 1.42, 6275.96),
        array(9, 0.27, 5486.78)
    );

    /**
     * Static Variable
     *
     * @var array
     */
    public static $R2 = array(
        array(4359, 5.7846, 6283.0758),
        array(124, 5.579, 12566.152),
        array(12, 3.14, 0),
        array(9, 3.63, 77713.77),
        array(6, 1.87, 5573.14),
        array(3, 5.47, 18849)
    );

    /**
     * Static Variable
     *
     * @var array
     */
    public static $R3 = array(
        array(145, 4.273, 6283.076),
        array(7, 3.92, 12566.15)
    );

    /**
     * Static Variable
     *
     * @var array
     */
    public static $R4 = array(
        array(4, 2.56, 6283.08)
    );

    /**
     * Static Variable
     *
     * @var array
     */
    public static $PE = array(
        array(-171996, -174.2, 92025, 8.9),
        array(-13187, -1.6, 5736, -3.1),
        array(-2274, -0.2, 977, -0.5),
        array(2062, 0.2, -895, 0.5),
        array(1426, -3.4, 54, -0.1),
        array(712, 0.1, -7, 0),
        array(-517, 1.2, 224, -0.6),
        array(-386, -0.4, 200, 0),
        array(-301, 0, 129, -0.1),
        array(217, -0.5, -95, 0.3),
        array(-158, 0, 0, 0),
        array(129, 0.1, -70, 0),
        array(123, 0, -53, 0),
        array(63, 0, 0, 0),
        array(63, 0.1, -33, 0),
        array(-59, 0, 26, 0),
        array(-58, -0.1, 32, 0),
        array(-51, 0, 27, 0),
        array(48, 0, 0, 0),
        array(46, 0, -24, 0),
        array(-38, 0, 16, 0),
        array(-31, 0, 13, 0),
        array(29, 0, 0, 0),
        array(29, 0, -12, 0),
        array(26, 0, 0, 0),
        array(-22, 0, 0, 0),
        array(21, 0, -10, 0),
        array(17, -0.1, 0, 0),
        array(16, 0, -8, 0),
        array(-16, 0.1, 7, 0),
        array(-15, 0, 9, 0),
        array(-13, 0, 7, 0),
        array(-12, 0, 6, 0),
        array(11, 0, 0, 0),
        array(-10, 0, 5, 0),
        array(-8, 0, 3, 0),
        array(7, 0, -3, 0),
        array(-7, 0, 0, 0),
        array(-7, 0, 3, 0),
        array(-7, 0, 3, 0),
        array(6, 0, 0, 0),
        array(6, 0, -3, 0),
        array(6, 0, -3, 0),
        array(-6, 0, 3, 0),
        array(-6, 0, 3, 0),
        array(5, 0, 0, 0),
        array(-5, 0, 3, 0),
        array(-5, 0, 3, 0),
        array(-5, 0, 3, 0),
        array(4, 0, 0, 0),
        array(4, 0, 0, 0),
        array(4, 0, 0, 0),
        array(-4, 0, 0, 0),
        array(-4, 0, 0, 0),
        array(-4, 0, 0, 0),
        array(3, 0, 0, 0),
        array(-3, 0, 0, 0),
        array(-3, 0, 0, 0),
        array(-3, 0, 0, 0),
        array(-3, 0, 0, 0),
        array(-3, 0, 0, 0),
        array(-3, 0, 0, 0),
        array(-3, 0, 0, 0)
    );

    /**
     * Static Variable
     *
     * @var array
     */
    public static $SINCOEFF = array(
        array(0, 0, 0, 0, 1),
        array(-2, 0, 0, 2, 2),
        array(0, 0, 0, 2, 2),
        array(0, 0, 0, 0, 2),
        array(0, 1, 0, 0, 0),
        array(0, 0, 1, 0, 0),
        array(-2, 1, 0, 2, 2),
        array(0, 0, 0, 2, 1),
        array(0, 0, 1, 2, 2),
        array(-2, -1, 0, 2, 2),
        array(-2, 0, 1, 0, 0),
        array(-2, 0, 0, 2, 1),
        array(0, 0, -1, 2, 2),
        array(2, 0, 0, 0, 0),
        array(0, 0, 1, 0, 1),
        array(2, 0, -1, 2, 2),
        array(0, 0, -1, 0, 1),
        array(0, 0, 1, 2, 1),
        array(-2, 0, 2, 0, 0),
        array(0, 0, -2, 2, 1),
        array(2, 0, 0, 2, 2),
        array(0, 0, 2, 2, 2),
        array(0, 0, 2, 0, 0),
        array(-2, 0, 1, 2, 2),
        array(0, 0, 0, 2, 0),
        array(-2, 0, 0, 2, 0),
        array(0, 0, -1, 2, 1),
        array(0, 2, 0, 0, 0),
        array(2, 0, -1, 0, 1),
        array(-2, 2, 0, 2, 2),
        array(0, 1, 0, 0, 1),
        array(-2, 0, 1, 0, 1),
        array(0, -1, 0, 0, 1),
        array(0, 0, 2, -2, 0),
        array(2, 0, -1, 2, 1),
        array(2, 0, 1, 2, 2),
        array(0, 1, 0, 2, 2),
        array(-2, 1, 1, 0, 0),
        array(0, -1, 0, 2, 2),
        array(2, 0, 0, 2, 1),
        array(2, 0, 1, 0, 0),
        array(-2, 0, 2, 2, 2),
        array(-2, 0, 1, 2, 1),
        array(2, 0, -2, 0, 1),
        array(2, 0, 0, 0, 1),
        array(0, -1, 1, 0, 0),
        array(-2, -1, 0, 2, 1),
        array(-2, 0, 0, 0, 1),
        array(0, 0, 2, 2, 1),
        array(-2, 0, 2, 0, 1),
        array(-2, 1, 0, 2, 1),
        array(0, 0, 1, -2, 0),
        array(-1, 0, 1, 0, 0),
        array(-2, 1, 0, 0, 0),
        array(1, 0, 0, 0, 0),
        array(0, 0, 1, 2, 0),
        array(0, 0, -2, 2, 2),
        array(-1, -1, 1, 0, 0),
        array(0, 1, 1, 0, 0),
        array(0, -1, 1, 2, 2),
        array(2, -1, -1, 2, 2),
        array(0, 0, 3, 2, 2),
        array(2, -1, 0, 2, 2)
    );
}