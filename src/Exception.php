<?php

namespace k1ttyf\GeoSpy;

final class Exception extends \Exception {

    /**
     * Construct the exception.
     * @link https://php.net/manual/en/exception.construct.php
     * @param string $message The Exception message to throw.
     * @param int $code The Exception code.
     * @param Throwable|null $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __costruct(string $message, int $code = 422, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}