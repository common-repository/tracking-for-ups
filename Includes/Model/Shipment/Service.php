<?php

declare(strict_types=1);

namespace UpsTracking\Includes\Model\Shipment;

final class Service
{
    private string $code;
    private string $description;

    private function __construct(string $code, string $description)
    {
        $this->code = $code;
        $this->description = $description;
    }

    public static function fromNullableArray(?array $service): ?Service
    {
        if (!is_array($service)) {
            return null;
        }

        return Service::fromArray($service);
    }

    private static function fromArray(array $service): ?Service {
        $code = $service['Code'];
        $description = $service['Description'];

        return new Service($code, $description);
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