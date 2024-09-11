# k1ttyf/geospy-php
SDK for using GeoSpy API in PHP. Based on [GeoSpy API](https://dev.geospy.ai/docs/routes).

[![License](https://img.shields.io/github/license/k1ttyf/geospy-php)](https://github.com/k1ttyf/geospy-php/blob/main/LICENSE)
![Packagist Downloads](https://img.shields.io/packagist/dt/k1ttyf/geospy-php)
![GitHub release (latest by date including pre-releases)](https://img.shields.io/github/v/release/k1ttyf/geospy-php?include_prereleases)
![GitHub last commit](https://img.shields.io/github/last-commit/k1ttyf/geospy-php)

## 🛠 Install
Run this command at the command prompt:
```shell
composer require k1ttyf/geospy-php
```

## 🔌 Usage

### [Predict](https://dev.geospy.ai/docs/routes#predict)

```php
<?php

$image = __DIR__ . "/image.jpeg";
require_once __DIR__ . "/vendor/autoload.php";
$GeoSpy = new k1ttyf\GeoSpy\API("YOUR_API_KEY");

try {
    $result = $GeoSpy->predict($image);
    foreach($result["geo_predictions"] as $value){
        echo "Latitude: " . $value["coordinates"][0] . PHP_EOL;
        echo "Longitude: " . $value["coordinates"][1] . PHP_EOL;
        foreach($GeoSpy->getLocationLinks($value["coordinates"][0], $value["coordinates"][1]) as $service => $link){
            echo ucfirst($service) . ": " . $link . PHP_EOL;
        }
        echo "Score: " . $value["score"] . PHP_EOL;
        echo "Similarity Score (1km): " . $value["similarity_score_1km"] . PHP_EOL;
        echo "Address: " . $value["address"] . PHP_EOL;
        echo PHP_EOL;
    }
} catch(Exception $e){
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
```

### getLocationLinks
```php
<?php

$latitude = 48.858112335205;
$longitude = 2.2949459552765;

require_once __DIR__ . "/vendor/autoload.php";
$GeoSpy = new k1ttyf\GeoSpy\API("YOUR_API_KEY");

foreach($GeoSpy->getLocationLinks($latitude, $longitude) as $service => $link){
    echo ucfirst($service) . ": " . $link . PHP_EOL;
}
```

## 💣 Troubleshooting

Please if you find any errors or inaccuracies - [report it](https://github.com/k1ttyf/geospy-php/issues)
