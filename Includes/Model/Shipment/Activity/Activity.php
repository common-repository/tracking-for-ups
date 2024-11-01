<?php

declare(strict_types=1);

namespace UpsTracking\Includes\Model\Shipment\Activity;

final class Activity
{
    private ?array $activityLocations;
    private ?ActivityStatus $activityStatus;
    private ?string $description;
    private ?string $date;
    private ?string $time;
    private ?string $trailer;

    private function __construct(
        ?array $activityLocations,
        ?ActivityStatus $activityStatus,
        ?string $description,
        ?string $date,
        ?string $time,
        ?string $trailer)
    {
        $this->activityLocations = $activityLocations;
        $this->activityStatus = $activityStatus;
        $this->description = $description;
        $this->date = $date;
        $this->time = $time;
        $this->trailer = $trailer;
    }

    public static function fromNullableArray(?array $activities): ?array
    {
        if (!is_array($activities)) {
            return null;
        }

        $array = [];

        if (isset($activities[0])) {
            foreach ($activities as $activity) {
                $array[] = Activity::fromArray($activity);
            }
        } else {
            $array[] = Activity::fromArray($activities);
        }

        return empty($array) ? null : $array;
    }

    private static function fromArray(array $activity): ?Activity {
        $activityLocations = ActivityLocation::fromNullableArray(Activity::arrayOrNull($activity, 'ActivityLocation'));
        $activityStatus = ActivityStatus::fromNullableArray(Activity::arrayOrNull($activity, 'Status'));
        $description = isset($activity['Description']) ? $activity['Description'] : null;
        $date = isset($activity['Date']) ? $activity['Date'] : null;
        $time = isset($activity['Time']) ? $activity['Time'] : null;
        $trailer = isset($activity['Trailer']) ? $activity['Trailer'] : null;

        if (!$activityLocations && !$activityStatus && !$description && !$date && !$time && !$trailer) {
            return null;
        }

        return new Activity($activityLocations, $activityStatus, $description, $date, $time, $trailer);
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
     * @return ActivityLocation[]|null
     */
    public function getActivityLocations(): ?array
    {
        return $this->activityLocations;
    }

    /**
     * @return ActivityStatus|null
     */
    public function getActivityStatus(): ?ActivityStatus
    {
        return $this->activityStatus;
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
    public function getDate(): ?string
    {
        return $this->date;
    }

    /**
     * @return string|null
     */
    public function getTime(): ?string
    {
        return $this->time;
    }

    /**
     * @return string|null
     */
    public function getTrailer(): ?string
    {
        return $this->trailer;
    }
}