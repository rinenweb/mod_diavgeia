<?php
/**
 * @package     Mod_Diavgeia
 * @author      Rinenweb <info@rinenweb.eu>
 * @copyright   (C) 2023-2026 Rinenweb
 * @license     GNU General Public License version 2 or later
 * @link        https://www.rinenweb.eu
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Rinenweb\Module\Diavgeia\Site\Helper\DiavgeiaHelper;

$keywords = trim((string) $params->get('keywords', ''));

// No keywords configured: produce no output at all, so Joomla does not render the module.
if ($keywords === '') {
    return;
}

$introText        = (string) $params->get('intro_text', '');
$num_decisions    = max(1, min(100, (int) $params->get('num_decisions', 5)));
$showOrganization = (bool) $params->get('organization', 0);
$adadisplay       = (bool) $params->get('ada_display', 0);
$cacheLifetime    = max(0, (int) $params->get('cache_lifetime', 15));

$decisions         = [];
$organizationNames = [];
$hasError          = false;

try {
    $decisions = DiavgeiaHelper::getDecisions($keywords, $num_decisions, $cacheLifetime);

    if ($showOrganization && $decisions) {
        $organizationNames = DiavgeiaHelper::getOrganizationNames($decisions);
    }
} catch (\Throwable $e) {
    DiavgeiaHelper::log('Diavgeia search failed: ' . $e->getMessage());
    $hasError = true;
}

require ModuleHelper::getLayoutPath('mod_diavgeia', $params->get('layout', 'default'));
