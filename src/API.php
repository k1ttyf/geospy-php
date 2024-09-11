<?php

namespace k1ttyf\GeoSpy;

use GuzzleHttp\Client as Client;
use k1ttyf\GeoSpy\Exception as Exception;

class API {

    /** @var object */
    private object $client;

    /** @var array */
    private array $config;

    /**
     * Instantiates a new GeoSpy client object. 
     * @param string $api_key The GeoSpy API key
     * @param Client|null $client The GuzzleHttp\Client
     */
    public function __construct(string $api_key, ?Client $client = null) {
        $this->client = !empty($client) ? $client : new Client();
        $this->config["http_errors"] = false;
        $this->config["timeout"] = 5;
        $this->config["connect_timeout"] = 5;
        $this->config["base_uri"] = "https://dev.geospy.ai";
        $this->config["headers"] = [
            "Authorization" => "Bearer " . $api_key,
            "Content-Type" => "application/json"
        ];
    }

    /**
     * This is the base GeoSpy geolocation prediction endpoint.
     * @link https://dev.geospy.ai/docs/routes#predict
     * @param string $image Photo (file_get_contents or file path)
     * @param int|null $top_k The number of top predictions to return. Default is 5 but you can request up to 50 GPS location predictions.
     * @throws \k1ttyf\GeoSpy\Exception
     * @return array
     */
    public function predict(string $image, ?int $top_k = null) : array {
        $params = ["image" => $this->validateFile($image)];
        if(!empty($top_k))
            $params["top_k"] = $top_k;
        return $this->sendRequest("POST", "/predict", $params);
    }

    /**
     * This is the base GeoSpy geolocation prediction endpoint.
     * @link https://dev.geospy.ai/docs/routes#geospy-basic-api-providing-a-simple-prediction-explanation-as-well-as-a-a-single-a-general-location-prediction
     * @param string $image Photo (file_get_contents or file path)
     * @throws \k1ttyf\GeoSpy\Exception
     * @return array
     */
    public function predictV1(string $image) : array {
        return $this->sendRequest("POST", "/predict_v1", ["image" => $this->validateFile($image)]);
    }

    /**
     * Creating direct link to location in Google/Apple/Yandex/2GIS Maps.
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @return array
     */
    public function getLocationLinks(float $latitude, float $longitude) : array {
        $coords = sprintf("%s,%s", $latitude, $longitude);
        $invertedCoords = sprintf("%s,%s", $longitude, $latitude);
        return [
            "google" => sprintf("https://www.google.com/maps/place/%s", $coords),
            "apple" => sprintf("https://maps.apple.com/?sll=%s", $coords),
            "yandex" => sprintf("https://yandex.ru/maps/?text=%s", $coords),
            "2gis" => sprintf("https://2gis.ru/geo/%s", $invertedCoords)
        ];
    }

    /**
     * This method getting image in Base64
     * @param string $image Image or path to image
     * @throws \k1ttyf\GeoSpy\Exception
     * @return string
     */
    private function validateFile(string $image) : string {
        if(file_exists($image)){
            $image = @file_get_contents($image);
            if(!$image)
                Throw new Exception("File not exists");
        }
        return base64_encode($image);
    }

    /**
     * Sending requests to API server.
     * @param string $requestMethod HTTP Request Method.
     * @param string $method GeoSpy API Method.
     * @param array|null $params Request Body.
     * @throws \k1ttyf\GeoSpy\Exception
     * @return array
     */
    private function sendRequest(string $requestMethod, string $method, ?array $params = null) : array {
        try {
            $this->config["json"] = $params;
            $request = $this->client->request($requestMethod, $method, $this->config);
            $response = $request->getBody()->getContents();
            return $this->checkAnswer($response);
        } catch(Exception $e){
            Throw new Exception($e->getMessage());
        }
    }

    /**
     * Checking for a response from the server.
     * @param string $response The API server response.
     * @throws \k1ttyf\GeoSpy\Exception
     * @return array
     */
    private function checkAnswer(string $response) : array {
        $decoded = json_decode($response, true);
        if(!is_array($decoded))
            Throw new Exception("The server returned a non-JSON response");
        elseif($decoded["status"] !== 200)
            Throw new Exception($decoded["title"]);
        else
            return $decoded;
    }

}
