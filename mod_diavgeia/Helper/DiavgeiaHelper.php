<?php
/**
 * @package Joomla.Site
 * @subpackage mod_diavgeia
 * @author Rinenweb <info@rinenweb.eu>
 * @link https://www.rinenweb.eu
 * @license GNU General Public License v2
 */

namespace Joomla\Module\Diavgeia\Site\Helper;

// No direct access to this file
defined('_JEXEC') or die;

// Import necessary classes
use Joomla\Http\Transport\Curl;
use Joomla\Uri\Uri;
use Joomla\Uri\UriInterface;

class DiavgeiaHelper {
    
    public static function fetchData($keywords, $numDecisions, $acceptHeader = 'application/json') {
        $apiUrl = 'https://diavgeia.gov.gr/opendata/search?subject=' . urlencode($keywords) . '&page=0&size=' . $numDecisions;

        // Create an instance of Joomla\Http\Curl\Curl
        $curlTransport = new Curl;

        // Define request parameters
        $method = 'GET';
        $uri = new Uri($apiUrl);
        $data = null;
        $headers = [
          'Accept' => $acceptHeader,
        ];
        $timeout = 20;
        $userAgent = 'ReqBin Curl Client/1.0';

        $response = $curlTransport->request($method, $uri, $data, $headers, $timeout, $userAgent);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('cURL error: ' . $response->getBody());
        }
        $data = json_decode($response->getBody(), true);
        return $data;
    }
    public static function getDecisions($keywords, $numDecisions, $acceptHeader = 'application/json') {
        $rawData = self::fetchData($keywords, $numDecisions, $acceptHeader);        
        return $rawData;
    }
}