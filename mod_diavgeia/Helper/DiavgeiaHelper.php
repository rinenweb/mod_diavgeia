<?php
/**
 * @package Joomla.Site
 * @subpackage mod_diavgeia
 * @author     Rinenweb <info@rinenweb.eu>
 * @link       https://www.rinenweb.eu
 * @license GNU General Public License v2
 */

namespace Joomla\Module\Diavgeia\Site\Helper;

// No direct access to this file
defined('_JEXEC') or die;

class DiavgeiaHelper {

    public static function fetchData($keywords, $numDecisions) {
        $apiUrl = 'https://diavgeia.gov.gr/opendata/search?subject=' . urlencode($keywords) . '&size=' . $numDecisions;
  
        // Initialize cURL session
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }

        // Close cURL session
        curl_close($ch);

        // Handle the API response
        if ($response) {
            $data = json_decode($response, true);

            return $data;
        } else {
            throw new Exception('API request failed.');
        }
    }

    public static function processData($data) {
        // the code to process the data, e.g., format or filter it
    }

    public static function getDecisions($keywords, $numDecisions) {
        $rawData = self::fetchData($keywords, $numDecisions);
        $processedData = self::processData($rawData);

        return $processedData;
    }

    // Other helper functions as needed
}

