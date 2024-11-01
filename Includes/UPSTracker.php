<?php

declare( strict_types=1 );

namespace UpsTracking\Includes;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define the UPS Tracking functionality.
 *
 * @link       https://github.com/aarsla/tracking-for-ups
 * @since      1.0.0
 * @package    UpsTracking
 * @subpackage UpsTracking/Includes
 * @author     Aid Arslanagic <aarsla@gmail.com>
 */
final class UPSTracker {
	public static function getUpsTracking( string $inquiryNumber, bool $asJson = true ) {
		$params            = UPSTracker::queryOptions( $inquiryNumber );
		$jsonEncodedParams = wp_json_encode( $params );

		$response = UPSTracker::sendRequest( $jsonEncodedParams );

		if ( ! $asJson ) {
			return $response;
		}

		return json_decode( $response, true, 512, JSON_OBJECT_AS_ARRAY );
	}

	public static function testFixture( string $fixtureName ): string {
		return file_get_contents( dirname( __FILE__ ) . '/../Fixtures/' . $fixtureName . '.json', true );
	}

	private static function queryOptions( string $inquiryNumber, bool $jsonEncode = false ): array {
		$upsTrackingOptions = get_option( UPS_TRACKING_SLUG.'-general' );

		return [
			'UPSSecurity'  => [
				'UsernameToken'      => [
					'Username' => $upsTrackingOptions['user-id-tx'],
					'Password' => $upsTrackingOptions['password-tx'],
				],
				'ServiceAccessToken' => [
					'AccessLicenseNumber' => $upsTrackingOptions['license-key-tx'],
				]
			],
			'TrackRequest' => [
				'Request'       => [
					'RequestOption'        => 1,
					'TransactionReference' => [
						'CustomerContext' => 'Test 001'
					]
				],
				'InquiryNumber' => $inquiryNumber
			]
		];
	}

	private static function sendRequest( string $jsonEncodedParams ): string {
		$upsTrackingOptions = get_option( UPS_TRACKING_SLUG.'-general' );
		$url                = $upsTrackingOptions['endpoint-url-tx'];

		$response = wp_remote_post( $url, [
			'timeout' => 45,
			'headers' => [
				'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept',
				'Access-Control-Allow-Methods: POST',
				'Access-Control-Allow-Origin: *',
				'Content-Type: application/json',
				'Content-Length: ' . strlen( $jsonEncodedParams )
			],
			'body'    => $jsonEncodedParams
		] );

		if ( is_wp_error( $response ) || ! isset( $response['response'] ) || ! is_array( $response['response'] ) ) {
			return '';
		}

		if ( is_wp_error( $response ) ) {
			return 'ERROR: ' . $response->get_error_message();
		}

		return $response['body'];
	}
}
