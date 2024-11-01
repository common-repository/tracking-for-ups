<?php

declare(strict_types=1);

namespace UpsTracking\Includes\Model\Shipment;

final class ShipmentType
{
    private string $code;
    private string $description;

    private function __construct(string $code, string $description)
    {
        $this->code = $code;
        $this->description = $description;
    }

    public static function fromNullableArray(?array $shipmentType): ?ShipmentType
    {
        if (!is_array($shipmentType)) {
            return null;
        }

        return ShipmentType::fromArray($shipmentType);
    }

    private static function fromArray(array $shipmentType): ?ShipmentType {
        $code = $shipmentType['Code'];
        $description = $shipmentType['Description'];

        return new ShipmentType($code, $description);
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

}