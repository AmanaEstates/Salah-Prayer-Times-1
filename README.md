# Salah-Prayer-Times-1

PHP library for prayer times based on C library from ArabEyes:
https://github.com/arabeyes-org/ITL/tree/master/prayertime

#How to use

Prayer times need the following information to be generated:

 - Latitude
 - Longitude
 - GMT offset
 - Date
 
To use simple include the main core file `see Example.php for usage`:

````
define('DS', DIRECTORY_SEPARATOR);
require dirname(__FILE__) . DS . 'core' . DS . 'Prayer_Times.php';
````

Then intialize a location/setting object:

```
$settings               = new Settings('US');
$settings->location     = array('Detroit', 'Michigan', 'US');
$settings->latitude     = 42.4056;
$settings->longitude    = -83.0531;
$settings->timezone     = 'America/Detroit';
```

This will automatically assign the method/madhab based on the country (US). Not not all countries have defaults so you can do the following additional sets:

```
$settings->method = 4;   // Complete list please check `core/Settings.php`
$settings->juristic = 0; // (0 - Shafi/Hanbli/Maliki, 1 - Hanafi)
```

To access prayer times you call the prayer time library as so:

```
$prayer = new Prayer_Times($settings);
$times = $prayer->getPrayerTimes(25, 12, 2017);
echo '--------------------';
echo 'Salah Times for December 25th, 2017' . PHP_EOL;
echo 'Fajir: '      . format_am_pm($times[0]) . PHP_EOL;
echo 'Duha: '       . format_am_pm($times[1]) . PHP_EOL;
echo 'Dhur: '       . format_am_pm($times[2]) . PHP_EOL;
echo 'Asr: '        . format_am_pm($times[3]) . PHP_EOL;
echo 'Maghrib: '    . format_am_pm($times[4]) . PHP_EOL;
echo 'Isha: '       . format_am_pm($times[5]) . PHP_EOL;
```

# Need help or have any question?
Contact us at amanaestates@gmail.com. You can also visit us at SalahHour.com
