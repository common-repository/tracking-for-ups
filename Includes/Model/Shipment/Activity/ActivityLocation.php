<?php

declare(strict_types=1);

namespace UpsTracking\Includes\Model\Shipment\Activity;

use UpsTracking\Includes\Model\Shipment\Package;

final class ActivityLocation
{
    private ?Address $address;
    private ?string $code;
    private ?string $city;
    private ?string $stateProvinceCode;
    private ?string $description;
    private ?string $signedForByName;

    public function __toString(): string
    {
        if ($this->address) {
            return $this->code . ' ' . $this->description . ' ' . $this->address;
        }

        if ($this->city || $this->stateProvinceCode) {
            return $this->city . ' ' . $this->stateProvinceCode;
        }

        return 'Unknown location';
    }

    private function __construct(
        ?Address $address,
        ?string $code,
        ?string $city,
        ?string $description,
        ?string $signedForByName,
        ?string $stateProvinceCode
    )
    {
        $this->address = $address;
        $this->code = $code;
        $this->city = $city;
        $this->description = $description;
        $this->signedForByName = $signedForByName;
        $this->stateProvinceCode = $stateProvinceCode;
    }

    public static function fromNullableArray(?array $activityLocations): ?array
    {
        if (!is_array($activityLocations)) {
            return null;
        }

        $array = [];

        if (isset($activityLocations[0])) {
            foreach ($activityLocations as $activityLocation) {
                $array[] = ActivityLocation::fromArray($activityLocation);
            }
        } else {
            $array[] = ActivityLocation::fromArray($activityLocations);
        }

        return empty($array) ? null : $array;
    }

    private static function fromArray(array $activityLocation): ?ActivityLocation {
        $address = Address::fromNullableArray(ActivityLocation::arrayOrNull($activityLocation, 'Address'));
        $code = isset($activityLocation['Code']) ? $activityLocation['Code'] : null;
        $city = isset($activityLocation['City']) ? $activityLocation['City'] : null;
        $description = isset($activityLocation['Description']) ? $activityLocation['Description'] : null;
        $signedForByName = isset($activityLocation['SignedForByName']) ? $activityLocation['SignedForByName'] : null;
        $stateProvinceCode = isset($activityLocation['StateProvinceCode']) ? $activityLocation['StateProvinceCode'] : null;

        if (!$address && !$code && !$city && !$description && !$description && !$signedForByName && !$stateProvinceCode) {
            return null;
        }

        return new ActivityLocation($address, $code, $city, $description, $signedForByName, $stateProvinceCode);
    }

    private static function arrayOrNull(array $array, string $index): ?array {
        if (!is_array($array)) {
            return null;
        }

        if (!array_key_exists($index, $array)) {
            return null;
        }

        return $array[$index];
    }

    /**
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        return $this->address;
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
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function getStateProvinceCode(): ?string
    {
        return $this->stateProvinceCode;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getSignedForByName(): ?string
    {
        return $this->signedForByName;
    }

}