<?php

$image = __DIR__ . "/image.jpeg";
require_once __DIR__ . "/vendor/autoload.php";
$GeoSpy = new k1ttyf\GeoSpy\API("YOUR_API_KEY");

try {
    $result = $GeoSpy->predict(file_get_contents($image));
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
