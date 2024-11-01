<?php

declare(strict_types=1);

namespace UpsTracking\Includes\Model\Shipment;

use UpsTracking\Includes\Model\Shipment\Activity\Activity;

final class Shipment
{
    private ?InquiryNumber $inquiryNumber;
    private ?ShipmentType $shipmentType;
    private ?CurrentStatus $currentStatus;
    private ?ShipmentWeight $shipmentWeight;
    private ?string $pickupDate;
    private ?Service $service;
    private ?ReferenceNumber $referenceNumber;
    private ?string $shipperNumber;
    private ?array $activities;
    private ?array $packages;

    private function __construct(
        ?InquiryNumber $inquiryNumber,
        ?ShipmentType $shipmentType,
        ?CurrentStatus $currentStatus,
        ?ShipmentWeight $shipmentWeight,
        ?string $pickupDate,
        ?Service $service,
        ?ReferenceNumber $referenceNumber,
        ?string $shipperNumber,
        ?array $activities,
        ?array $packages
    )
    {
        $this->inquiryNumber = $inquiryNumber;
        $this->shipmentType = $shipmentType;
        $this->currentStatus = $currentStatus;
        $this->shipmentWeight = $shipmentWeight;
        $this->pickupDate = $pickupDate;
        $this->service = $service;
        $this->referenceNumber = $referenceNumber;
        $this->shipperNumber = $shipperNumber;
        $this->activities = $activities;
        $this->packages = $packages;
    }

    public static function fromResponse(array $shipping): Shipment {
        $inquiryNumber = InquiryNumber::fromNullableArray(Shipment::arrayOrNull($shipping, 'InquiryNumber'));
        $shipmentType = ShipmentType::fromNullableArray(Shipment::arrayOrNull($shipping, 'ShipmentType'));
        $currentStatus = CurrentStatus::fromNullableArray(Shipment::arrayOrNull($shipping, 'CurrentStatus'));
        $shipmentWeight = ShipmentWeight::fromNullableArray(Shipment::arrayOrNull($shipping, 'ShipmentWeight'));
        $pickupDate = Shipment::valueOrNull($shipping, 'PickupDate');
        $service = Service::fromNullableArray(Shipment::arrayOrNull($shipping, 'Service'));
        $referenceNumber = ReferenceNumber::fromNullableArray(Shipment::arrayOrNull($shipping, 'ReferenceNumber'));
        $shipperNumber = Shipment::valueOrNull($shipping, 'ShipperNumber');
        $activities = Activity::fromNullableArray(Shipment::arrayOrNull($shipping, 'Activity'));
        $packages = Package::fromNullableArray(Shipment::arrayOrNull($shipping, 'Package'));

        return new Shipment(
            $inquiryNumber,
            $shipmentType,
            $currentStatus,
            $shipmentWeight,
            $pickupDate,
            $service,
            $referenceNumber,
            $shipperNumber,
            $activities,
            $packages
        );
    }

    private static function arrayOrNull(array $array, string $index): ?array {
        if (!is_array($array)) {
            return null;
        }

        if (!array_key_exists($index, $array)) {
            return null;
        }

        if (!is_array($array[$index])) {
            return null;
        }

        return $array[$index];
    }

    private static function valueOrNull(array $array, string $index): ?string
    {
        if (!is_array($array)) {
            return null;
        }

        if (!array_key_exists($index, $array)) {
            return null;
        }

        return $array ? (string)$array[$index] : null;
    }

        /**
     * @return InquiryNumber|null
     */
    public function getInquiryNumber(): ?InquiryNumber
    {
        return $this->inquiryNumber;
    }

    /**
     * @return ShipmentType|null
     */
    public function getShipmentType(): ?ShipmentType
    {
        return $this->shipmentType;
    }

    /**
     * @return CurrentStatus|null
     */
    public function getCurrentStatus(): ?CurrentStatus
    {
        return $this->currentStatus;
    }

    /**
     * @return ShipmentWeight|null
     */
    public function getShipmentWeight(): ?ShipmentWeight
    {
        return $this->shipmentWeight;
    }

    /**
     * @return string|null
     */
    public function getPickupDate(): ?string
    {
        return $this->pickupDate;
    }

    /**
     * @return Service|null
     */
    public function getService(): ?Service
    {
        return $this->service;
    }

    /**
     * @return ReferenceNumber|null
     */
    public function getReferenceNumber(): ?ReferenceNumber
    {
        return $this->referenceNumber;
    }

    /**
     * @return string|null
     */
    public function getShipperNumber(): ?string
    {
        return $this->shipperNumber;
    }

    /**
     * @return Activity[]|null
     */
    public function getActivities(): ?array
    {
        return $this->activities;
    }

    /**
     * @return Package[]|null
     */
    public function getPackages(): ?array
    {
        return $this->packages;
    }

}