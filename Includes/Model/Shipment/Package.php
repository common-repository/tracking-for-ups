<?php

declare(strict_types=1);

namespace UpsTracking\Includes\Model\Shipment;

use UpsTracking\Includes\Model\Shipment\Activity\Activity;

final class Package
{
    private string $trackingNumber;
    private ?array $activities;

    private function __construct(string $trackingNumber, array $activities = [])
    {
        $this->trackingNumber = $trackingNumber;
        $this->activities = $activities;
    }

    public static function fromNullableArray(?array $packages): ?array
    {
        if (!is_array($packages)) {
            return null;
        }

        $array = [];

        if (isset($packages[0])) {
            foreach ($packages as $package) {
                $array[] = Package::fromArray($package);
            }
        } else {
            $array[] = Package::fromArray($packages);
        }

        return empty($array) ? null : $array;
    }

    private static function fromArray(array $package): Package {
        $trackingNumber = $package['TrackingNumber'];
        $activities = Activity::fromNullableArray($package['Activity']);

        return new Package($trackingNumber, $activities);
    }

    /**
     * @return string
     */
    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    /**
     * @return Activity[]|null
     */
    public function getActivities(): ?array
    {
        return $this->activities;
    }


}