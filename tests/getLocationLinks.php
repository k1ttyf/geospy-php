<?php

$latitude = 48.858112335205;
$longitude = 2.2949459552765;

require_once __DIR__ . "/vendor/autoload.php";
$GeoSpy = new k1ttyf\GeoSpy\API("YOUR_API_KEY");

foreach($GeoSpy->getLocationLinks($latitude, $longitude) as $service => $link){
    echo ucfirst($service) . ": " . $link . PHP_EOL;
}
