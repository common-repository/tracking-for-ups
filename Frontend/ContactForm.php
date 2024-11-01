<?php

declare(strict_types=1);

namespace UpsTracking\Frontend;

// If this file is called directly, abort.
use UpsTracking\Includes\Model\Activity\Activity;
use UpsTracking\Includes\Model\UPS;
use UpsTracking\Includes\UPSTracker;

if (!defined('ABSPATH')) exit;

/**
 * Contact form and Shortcode template.
 *
 * @link       https://github.com/aarsla/tracking-for-ups
 * @since      1.0.0
 * @package    UpsTracking
 * @subpackage UpsTracking/Frontend
 * @author     Aid Arslanagic <aarsla@gmail.com>
 */
class ContactForm
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     */
    private string $pluginSlug;

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     * @param   $pluginSlug     The name of the plugin.
     * @param   $version        The version of this plugin.
     */
    public function __construct(string $pluginSlug)
    {
        $this->pluginSlug = $pluginSlug;
    }

    /**
     * Register all the hooks of this class.
     *
     * @since   1.0.0
     * @param   $isAdmin    Whether the current request is for an administrative interface page.
     */
    public function initializeHooks(bool $isAdmin): void
    {
        // 'wp_ajax_' hook needs to be run on frontend and admin area too.
        add_action('wp_ajax_capitalizeText', array($this, 'capitalizeText'), 10);

        // Frontend
        if (!$isAdmin)
        {
            add_shortcode('ups-tracking-form', array($this, 'formShortcode'));
        }
    }

    /**
     * Contact form shortcode.
     *
     * @link https://developer.wordpress.org/reference/functions/add_shortcode/
     * Shortcode attribute names are always converted to lowercase before they are passed into the handler function. Values are untouched.
     *
     * The function called by the shortcode should never produce output of any kind.
     * Shortcode functions should return the text that is to be used to replace the shortcode.
     * Producing the output directly will lead to unexpected results.
     *
     * @since   1.0.0
     * @param   $attributes Attributes.
     * @param   $content    The post content.
     * @param   $tag        The name of the shortcode.
     * @return  The text that is to be used to replace the shortcode.
     */
    public function formShortcode($attributes = null, $content = null, string $tag = ''): string
    {
        // Enqueue scripts
        wp_enqueue_script($this->pluginSlug . 'form');

        // Inline scripts. This is how we pass data to scripts
        $script  = 'ajaxUrl = ' . json_encode(admin_url('admin-ajax.php')) . '; ';
        $script .= 'nonce = ' . json_encode(wp_create_nonce('capitalizeText')) . '; ';
        if (wp_add_inline_script($this->pluginSlug . 'form', $script, 'before') === false)
        {
            // It throws error on the Post edit screen and I don't know why. It works on the frontend.
            //exit('wp_add_inline_script() failed. Inlined script: ' . $script);
        }

        // Show the Form
        $html = $this->getFormHtml();
        $this->processFormData();

        return $html;
    }

    /**
     * This is a template how to receive data from a script, then return data back.
     * In this case it returns a text in capitalized.
     *
     * @since   1.0.0
     */
    public function capitalizeText()
    {
        // Verifies the AJAX request
        if (check_ajax_referer('capitalizeText', 'nonce', false) === false)
        {
            wp_send_json_error('Failed nonce', 403); // Sends json_encoded success=false.
        }

        // Sanitize values
        $text = sanitize_text_field($_POST['text']);

        // Generate response data
        $responseData = array(
            'capitalizedText' => strtoupper($text)
        );

        // Send a JSON response back to an AJAX request, and die().
        wp_send_json($responseData, 200);
    }

    /**
     * The Form's HTML code.
     * @since    1.0.0
     * @return  The form's HTML code.
     */
    private function getFormHtml(): string
    {
        return '<div>
                    <label for="capitalized-subject">' . esc_html__('UPS Tracking', 'ups-tracking') . '</label>
                    <p id="capitalized-subject"></p>
                    <form action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="post">
                        <p>' . wp_nonce_field('getFormHtml', 'getFormHtml_nonce', true, false) . '</p>
                        <p>
                            <label for="inquiryNumber">' . esc_html__('Inquiry Number', 'ups-tracking') . '&nbsp;<span class="required">*</span></label>
                            <input type="text" id="inquiryNumber" name="inquiryNumber" value="' . (isset($_POST["inquiryNumber"]) ? esc_html($_POST["inquiryNumber"]) : '') . '" required />
                        </p>
                        <p><input type="submit" name="form-submitted" value="' . esc_html__('Submit', 'ups-tracking') . '"/></p>
                    </form>
                </div>';
    }

    /**
     * Validates and process the submitted data.
     * @since    1.0.0
     */
    private function processFormData(): void
    {
        // Check the Submit button is clicked
        if(isset($_POST['form-submitted']))
        {
            // Verify Nonce
            if (wp_verify_nonce($_POST['getFormHtml_nonce'], 'getFormHtml') !== false)
            {
                $inquiryNumber = sanitize_text_field($_POST["inquiryNumber"]);

                // Process the data.
                $response = UPSTracker::getUpsTracking($inquiryNumber);

                // Testing fixtures
                // $response = UPSTracker::testFixture($inquiryNumber);

                $upsResponse = UPS::fromArray($response);

                if ($upsResponse->getFault()) {
                    $formattedResponse = $this->formatErrorResponse($upsResponse);
                    echo $formattedResponse;
                    return;
                }

                $formattedResponse = $this->formatResponse($upsResponse);
                echo $formattedResponse;
            }
            else
            {
                exit(esc_html__('Failed security check.', 'ups-tracking'));
            }
        }
    }

    private function formatResponse(UPS $upsResponse): string
    {
        $html = '<table class="ups-results">';

        // Response
        $html .= '<tr>';
        $html .= '<td colspan="2"><strong>Status</strong></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td>Code '. $upsResponse->getResponseStatus()->getCode().'</td>';
        $html .= '<td>'.$upsResponse->getResponseStatus()->getDescription().'</td>';
        $html .= '</tr>';

        // Shipment
        $html .= '<tr>';
        $html .= '<td colspan="2"><strong>Shipment</strong></td>';
        $html .= '</tr>';

        $shipment = $upsResponse->getShipment();

        if ($shipment->getInquiryNumber()) {
            $inquiryNumber = $shipment->getInquiryNumber();

            $html .= '<tr>';
            $html .= '<td>Inquiry Number</td>';
            $html .= '<td>'.$inquiryNumber->getValue().'</td>';
            $html .= '</tr>';
        }

        if ($shipment->getShipmentType()) {
            $shipmentType = $shipment->getShipmentType();

            $html .= '<tr>';
            $html .= '<td>Type</td>';
            $html .= '<td>'.$shipmentType->getDescription().'</td>';
            $html .= '</tr>';
        }

        if ($shipment->getShipmentWeight()) {
            $shipmentWeight = $shipment->getShipmentWeight();

            $html .= '<tr>';
            $html .= '<td>Weight</td>';
            $html .= '<td>'.$shipmentWeight->getWeight().' '.$shipmentWeight->getUnitOfMeasurement().'</td>';
            $html .= '</tr>';
        }

        if ($shipment->getCurrentStatus()) {
            $currentStatus = $shipment->getCurrentStatus();

            $html .= '<tr>';
            $html .= '<td>Status</td>';
            $html .= '<td>'.$currentStatus->getDescription().'</td>';
            $html .= '</tr>';
        }

        if ($shipment->getPickupDate()) {
            $pickupDate = $shipment->getPickupDate();

            $html .= '<tr>';
            $html .= '<td>Pickup Date</td>';
            $html .= '<td>'.$pickupDate.'</td>';
            $html .= '</tr>';
        }

        if ($shipment->getService()) {
            $service = $shipment->getService();

            $html .= '<tr>';
            $html .= '<td>Service '.$service->getCode().'</td>';
            $html .= '<td>'.$service->getDescription().'</td>';
            $html .= '</tr>';
        }

        if ($shipment->getReferenceNumber()) {
            $referenceNumber = $shipment->getReferenceNumber();

            $html .= '<tr>';
            $html .= '<td>Ref. No. '.$referenceNumber->getCode().'</td>';
            $html .= '<td>'.$referenceNumber->getValue().'</td>';
            $html .= '</tr>';
        }

        if ($shipment->getShipperNumber()) {
            $hipperNumber = $shipment->getShipperNumber();

            $html .= '<tr>';
            $html .= '<td>Shipper No.</td>';
            $html .= '<td>'.$hipperNumber.'</td>';
            $html .= '</tr>';
        }

        // Shipment Activity
        if ($shipment->getActivities() !== null) {
            $html .= '<tr>';
            $html .= '<td colspan="2">Activities</td>';
            $html .= '</tr>';

            $activities = $shipment->getActivities();

            $html .= '<tr>';
            $html .= '<td colspan="2">';
            $html .= $this->getActivitiesTable($activities);
            $html .= '</td>';
            $html .= '</tr>';
        }

        // Package
        if ($shipment->getPackages()) {
            $html .= '<tr>';
            $html .= '<td colspan="2"><strong>Package</strong></td>';
            $html .= '</tr>';

            $packages = $shipment->getPackages();

            foreach ($packages as $package) {
                $html .= '<tr>';
                $html .= '<td>Tracking Number</td>';
                $html .= '<td>' . $package->getTrackingNumber() . '</td>';
                $html .= '</tr>';

                if ($package->getActivities()) {
                    $html .= '<tr>';
                    $html .= '<td colspan="2">Activities</td>';
                    $html .= '</tr>';

                    $activities = $package->getActivities();

                    $html .= '<tr>';
                    $html .= '<td colspan="2">';
                    $html .= $this->getActivitiesTable($activities);
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            }
        }

        // Disclaimer
        if ($upsResponse->getDisclaimer()) {
            $html .= '<tr>';
            $html .= '<td colspan="2"><strong>Disclaimer</strong></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="2">' . $upsResponse->getDisclaimer() . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }

    private function formatErrorResponse(UPS $upsResponse): string
    {
        $status = $upsResponse->getFault();

        $html = '<table>';
        $html .= '<th>Error Code</th>';
        $html .= '<th>Description</th>';

        $html .= '<tr>';
        $html .= '<td>'.$status->getCode().'</td>';
        $html .= '<td>'.$status->getDescription().'</td>';
        $html .= '</tr>';

        $html .= '</table>';

        return $html;
    }

    private function getActivitiesTable(array $activities): string {
        $html = '<table class="ups-package-activities">';

        foreach ($activities as $activity) {
            $dateTime = 'Date/Time: ' .$activity->getDate() . ' ' . $activity->getTime();
            if ($activity->getTrailer()) {
                $dateTime .= ' ['.$activity->getTrailer().']';
            }

            $html .= '<tr>';
            $html .= '<td colspan="2">' . $dateTime . '</td>';
            $html .= '</tr>';

            if ($activity->getDescription()) {
                $description = $activity->getDescription();

                $html .= '<tr>';
                $html .= '<td colspan="2">' . $description . '</td>';
                $html .= '</tr>';
            }

            if ($activity->getActivityStatus()) {
                $activityStatus = $activity->getActivityStatus();

                $html .= '<tr>';
                $html .= '<td colspan="2">' . $activityStatus->getDescription() . '</td>';
                $html .= '</tr>';
            }

            if ($activity->getActivityLocations()) {
                $activityLocations = $activity->getActivityLocations();
                foreach ($activityLocations as $activityLocation) {
                    $html .= '<tr>';
                    $html .= '<td colspan="2">' . $activityLocation . '</td>';
                    $html .= '</tr>';
                }
            }

            $html .= '<tr><td colspan="2"></td></tr>';
        }

        $html .= '</table>';

        return $html;
    }
}
