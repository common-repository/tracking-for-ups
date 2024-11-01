<?php

declare(strict_types=1);

namespace UpsTracking\Includes\Model\Shipment;

final class CurrentStatus
{
    private string $code;
    private string $description;

    private function __construct(string $code, string $description)
    {
        $this->code = $code;
        $this->description = $description;
    }

    public static function fromNullableArray(?array $currentStatus): ?CurrentStatus
    {
        if (!is_array($currentStatus)) {
            return null;
        }

        return CurrentStatus::fromArray($currentStatus);
    }

    private static function fromArray(array $currentStatus): CurrentStatus {
        $code = $currentStatus['Code'];
        $description = $currentStatus['Description'];

        return new CurrentStatus($code, $description);
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