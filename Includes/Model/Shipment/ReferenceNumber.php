<?php

declare(strict_types=1);

namespace UpsTracking\Includes\Model\Shipment;

use UpsTracking\Includes\Model\Shipment\Activity\ActivityStatus;

final class ReferenceNumber
{
    private ?string $code;
    private ?string $value;

    private function __construct(?string $code, ?string $value)
    {
        $this->code = $code;
        $this->value = $value;
    }

    public static function fromNullableArray(?array $referenceNumber): ?ReferenceNumber
    {
        if (!is_array($referenceNumber)) {
            return null;
        }

        return ReferenceNumber::fromArray($referenceNumber);
    }

    private static function fromArray(array $referenceNumber): ?ReferenceNumber {
        $code = isset($referenceNumber['Code']) ? $referenceNumber['Code'] : null;
        $value = isset($referenceNumber['Value']) ? $referenceNumber['Value'] : null;

        if (!$code && !$value) {
            return null;
        }

        return new ReferenceNumber($code, $value);
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

}