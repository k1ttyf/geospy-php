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
     * @param string $image The location of your photo.
     * @param int|null $top_k The number of top predictions to return. Default is 5 but you can request up to 50 GPS location predictions.
     * @throws Exception
     */
    public function predict(string $image, ?int $top_k = null) : array {
        $params = ["image" => base64_encode($image)];
        if(!empty($top_k))
            $params["top_k"] = $top_k;
        return $this->sendRequest("POST", "/predict", $params);
    }

    /**
     * Creating direct link to location in Google/Yandex Maps.
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     */
    public function getLocationLinks(float $latitude, float $longitude) : array {
        return [
            "google" => sprintf("https://www.google.com/maps/place/%s,%s", $latitude, $longitude),
            "yandex" => sprintf("https://yandex.ru/maps/?text=%s,%s", $latitude, $longitude)
        ];
    }

    /**
     * Sending requests to API server.
     * @param string $requestMethod HTTP Request Method.
     * @param string $method GeoSpy API Method.
     * @param array|null $params Request Body.
     * @throws Exception
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
     * @throws Exception
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
