<?php
/**
 * @package     Mod_Diavgeia
 * @author      Rinenweb <info@rinenweb.eu>
 * @copyright   (C) 2023-2026 Rinenweb
 * @license     GNU General Public License version 2 or later
 * @link        https://www.rinenweb.eu
 */

namespace Rinenweb\Module\Diavgeia\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Log\Log;

class DiavgeiaHelper
{
    const SEARCH_URL       = 'https://diavgeia.gov.gr/opendata/search';
    const ORGANIZATION_URL = 'https://diavgeia.gov.gr/luminapi/opendata/organizations/';
    const USER_AGENT       = 'mod_diavgeia/1.2 (+https://www.rinenweb.eu)';
    const TIMEOUT          = 10;

    /** Organization labels rarely change, so they are cached for a day. */
    const ORGANIZATION_CACHE_MINUTES = 1440;

    /**
     * Returns the list of decisions matching the keywords (empty array if none).
     *
     * @param   string  $keywords       Search terms for the decision subject.
     * @param   int     $numDecisions   Number of decisions to fetch (1-100).
     * @param   int     $cacheLifetime  Cache lifetime in minutes; 0 disables caching.
     *
     * @return  array
     *
     * @throws  \RuntimeException  When the API cannot be reached or returns invalid data.
     */
    public static function getDecisions($keywords, $numDecisions, $cacheLifetime = 15)
    {
        $keywords = trim((string) $keywords);

        if ($keywords === '') {
            return [];
        }

        $numDecisions = max(1, min(100, (int) $numDecisions));
        $cache        = self::getCache('mod_diavgeia', (int) $cacheLifetime);
        $cacheKey     = 'search.' . md5($keywords . '|' . $numDecisions);

        if ($cache && $cache->contains($cacheKey)) {
            $cached = $cache->get($cacheKey);

            if (\is_array($cached)) {
                return $cached;
            }
        }

        $url = self::SEARCH_URL . '?' . http_build_query([
            'subject' => $keywords,
            'page'    => 0,
            'size'    => $numDecisions,
        ]);

        $data      = self::request($url);
        $decisions = (isset($data['decisions']) && \is_array($data['decisions'])) ? $data['decisions'] : [];

        if ($cache) {
            $cache->store($decisions, $cacheKey);
        }

        return $decisions;
    }

    /**
     * Returns organizationId => label for the organizations in the given decisions.
     * Each organization is requested once and cached; a failed lookup is logged and skipped
     * instead of breaking the whole module.
     *
     * @param   array  $decisions  Decisions as returned by getDecisions().
     *
     * @return  array
     */
    public static function getOrganizationNames(array $decisions)
    {
        $names = [];
        $cache = self::getCache('mod_diavgeia_organizations', self::ORGANIZATION_CACHE_MINUTES);

        foreach ($decisions as $decision) {
            $organizationId = isset($decision['organizationId']) ? (string) $decision['organizationId'] : '';

            if ($organizationId === '' || isset($names[$organizationId])) {
                continue;
            }

            $cacheKey = 'organization.' . md5($organizationId);

            if ($cache && $cache->contains($cacheKey)) {
                $cached = $cache->get($cacheKey);

                if (\is_string($cached)) {
                    $names[$organizationId] = $cached;
                    continue;
                }
            }

            try {
                $data  = self::request(self::ORGANIZATION_URL . rawurlencode($organizationId));
                $label = isset($data['label']) ? (string) $data['label'] : '';
            } catch (\Throwable $e) {
                self::log('Diavgeia organization lookup failed for ' . $organizationId . ': ' . $e->getMessage());
                $names[$organizationId] = '';
                continue;
            }

            $names[$organizationId] = $label;

            if ($cache && $label !== '') {
                $cache->store($label, $cacheKey);
            }
        }

        return $names;
    }

    /**
     * Writes a warning to the site's log folder (mod_diavgeia.php).
     *
     * @param   string  $message  The message to log.
     *
     * @return  void
     */
    public static function log($message)
    {
        static $loggerAdded = false;

        if (!$loggerAdded) {
            Log::addLogger(['text_file' => 'mod_diavgeia.php'], Log::ALL, ['mod_diavgeia']);
            $loggerAdded = true;
        }

        Log::add((string) $message, Log::WARNING, 'mod_diavgeia');
    }

    /**
     * Performs a GET request and returns the decoded JSON body.
     *
     * @param   string  $url  The URL to fetch.
     *
     * @return  array
     *
     * @throws  \RuntimeException
     */
    private static function request($url)
    {
        $response = HttpFactory::getHttp()->get($url, [
            'Accept'     => 'application/json',
            'User-Agent' => self::USER_AGENT,
        ], self::TIMEOUT);

        $status = (int) $response->getStatusCode();

        if ($status !== 200) {
            throw new \RuntimeException('HTTP ' . $status . ' from ' . $url);
        }

        $data = json_decode((string) $response->getBody(), true);

        if (!\is_array($data)) {
            throw new \RuntimeException('Invalid JSON from ' . $url);
        }

        return $data;
    }

    /**
     * Returns an output cache controller that works independently of the global caching setting,
     * or null when caching is disabled or unavailable.
     *
     * @param   string  $group     Cache group.
     * @param   int     $lifetime  Lifetime in minutes.
     *
     * @return  \Joomla\CMS\Cache\Controller\OutputController|null
     */
    private static function getCache($group, $lifetime)
    {
        if ($lifetime <= 0) {
            return null;
        }

        try {
            return Factory::getContainer()
                ->get(CacheControllerFactoryInterface::class)
                ->createCacheController('output', [
                    'defaultgroup' => $group,
                    'lifetime'     => $lifetime,
                    'caching'      => true,
                ]);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
