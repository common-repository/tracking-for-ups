<?php

declare(strict_types=1);

namespace UpsTracking\Includes\Model\Shipment;

final class ShipmentWeight
{
    private string $unitOfMeasurement;
    private string $weight;

    private function __construct(string $unitOfMeasurement, string $weight)
    {
        $this->unitOfMeasurement = $unitOfMeasurement;
        $this->weight = $weight;
    }

    public static function fromNullableArray(?array $shipmentWeight): ?ShipmentWeight
    {
        if (!is_array($shipmentWeight)) {
            return null;
        }

        return ShipmentWeight::fromArray($shipmentWeight);
    }

    private static function fromArray(array $shipmentWeight): ShipmentWeight {
        $code = $shipmentWeight['UnitOfMeasurement']['Code'];
        $weight = $shipmentWeight['Weight'];

        return new ShipmentWeight($code, $weight);
    }

    /**
     * @return string
     */
    public function getUnitOfMeasurement(): string
    {
        return $this->unitOfMeasurement;
    }

    /**
     * @return string
     */
    public function getWeight(): string
    {
        return $this->weight;
    }

}