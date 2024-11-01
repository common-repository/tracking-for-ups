<?php

declare(strict_types=1);

namespace UpsTracking\Includes\Model;

use UpsTracking\Includes\Model\Shipment\Shipment;

final class UPS
{
    private ResponseStatus $responseStatus;
    private Shipment $shipment;
    private ?string $disclaimer;
    private ?ResponseStatus $fault = null;

    /**
     * UPSResponse constructor.
     * @param array $decodedResponse
     */
    private function __construct(array $decodedResponse)
    {
        if (isset($decodedResponse['Fault'])) {
            $this->fault = ResponseStatus::fromFault($decodedResponse['Fault']);
            return;
        }

        $trackResponse = $decodedResponse['TrackResponse'];

        $this->responseStatus = ResponseStatus::fromResponse($trackResponse['Response']['ResponseStatus']);
        $this->shipment = Shipment::fromResponse($trackResponse['Shipment']);
        $this->disclaimer = $trackResponse['Disclaimer'] ?: null;
    }

    public static function fromJsonString(string $response): UPS {
        $decodedResponse = json_decode($response, true, 512,  JSON_OBJECT_AS_ARRAY);
        return new UPS($decodedResponse);
    }

    public static function fromArray(array $response): UPS {
        return new UPS($response);
    }

    /**
     * @return array|mixed|null
     */
    public function getFault()
    {
        return $this->fault;
    }

    /**
     * @return string
     */
    public function getResponseStatus()
    {
        return $this->responseStatus;
    }

    /**
     * @return Shipment
     */
    public function getShipment(): Shipment
    {
        return $this->shipment;
    }

    /**
     * @return array|mixed
     */
    public function getDisclaimer()
    {
        return $this->disclaimer;
    }
}