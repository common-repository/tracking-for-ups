<?php

declare(strict_types=1);

namespace UpsTracking\Includes\Model\Shipment\Activity;

final class ActivityStatus
{
    private ?string $type;
    private ?string $code;
    private ?string $description;

    private function __construct(?string $type, ?string $code, ?string $description)
    {
        $this->type = $type;
        $this->code = $code;
        $this->description = $description;
    }

    public static function fromNullableArray(?array $currentStatus): ?ActivityStatus
    {
        if (!is_array($currentStatus)) {
            return null;
        }

        return ActivityStatus::fromArray($currentStatus);
    }

    private static function fromArray(array $currentStatus): ?ActivityStatus {
        $type = isset($currentStatus['Type']) ? $currentStatus['Type'] : null;
        $code = isset($currentStatus['Code']) ? $currentStatus['Code'] : null;
        $description = isset($currentStatus['Description']) ? $currentStatus['Description'] : null;

        if (!$type && !$code && !$description) {
            return null;
        }

        return new ActivityStatus($type, $code, $description);
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
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
    public function getDescription(): ?string
    {
        return $this->description;
    }

}