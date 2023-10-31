<?php
/**
 * @package    Mod_Diavgeia
 * @author     Rinenweb <info@rinenweb.eu>
 * @license    GNU General Public License v2
 * @link       https://www.rinenweb.eu
 */

// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Language\Text;

$introText = $params->get('intro_text');
echo '<div class="mod-diavgeia-intro-text">' . $introText . '</div>';

// Check if there is data to display
if (!empty($data)) {
    if (!empty($data['decisions'])) {
    echo '<ul class="mod-diavgeia-desicions">';
    foreach ($data['decisions'] as $decision) {
        echo '<li>';
		echo $decision['subject'] . '<br>';
        // Check if we should display the organization name
        if ($showOrganization) {
            // Display the organization name
            echo Text::_('MOD_DIAVGEIA_ORGANIZATION') . ': ' . $organizationNames[$decision['organizationId']] . '<br>';
        }
        if ($adadisplay) {
          echo '<strong>' . Text::_('MOD_DIAVGEIA_ADA') . ': ' . '</strong>' . '<a target="_blank" href="https://diavgeia.gov.gr/decision/view/' . htmlspecialchars($decision['ada']) . '" title="' . htmlspecialchars($decision['ada']) . '">' . htmlspecialchars($decision['ada']) . '</a><br>';
        }
        echo '<strong><em>' . date('d/m/Y', substr($decision['publishTimestamp'], 0,10)) . '</em></strong><br>';
        echo '</li>';
    }
    echo '</ul>';
} else {
    echo Text::_('MOD_DIAVGEIA_NO_DECISIONS');
}
}
