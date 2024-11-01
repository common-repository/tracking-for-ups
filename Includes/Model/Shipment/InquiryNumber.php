<?php

declare(strict_types=1);

namespace UpsTracking\Includes\Model\Shipment;

use UpsTracking\Includes\Model\Shipment\Activity\ActivityStatus;

final class InquiryNumber
{
    private ?string $code;
    private ?string $value;
    private ?string $description;

    private function __construct(?string $code, ?string $value, ?string $description)
    {
        $this->code = $code;
        $this->value = $value;
        $this->description = $description;
    }

    public static function fromNullableArray(?array $inquiryNumber): ?InquiryNumber
    {
        if (!is_array($inquiryNumber)) {
            return null;
        }

        return InquiryNumber::fromArray($inquiryNumber);
    }

    private static function fromArray(array $inquiryNumber): ?InquiryNumber {
        $code = isset($inquiryNumber['Code']) ? $inquiryNumber['Code'] : null;
        $value = isset($inquiryNumber['Value']) ? $inquiryNumber['Value'] : null;
        $description = isset($inquiryNumber['Description']) ? $inquiryNumber['Description'] : null;

        if (!$code && !$value && !$description) {
            return null;
        }

        return new InquiryNumber($code, $value, $description);
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

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

}