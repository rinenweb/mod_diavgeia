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
if (isset($data) && is_array($data) && !empty($data)) {
    echo '<div  class="diavgeia"><ul>';
    foreach ($data as $decision) {
        echo '<li>';
//        echo 'Title: ' . $decision['subject'] . '<br>'; // Use htmlspecialchars to escape output
        echo 'Title: ' . htmlspecialchars($decision['subject'] ?? 'N/A') . '<br>'; // Use htmlspecialchars to escape output
        echo 'Date: ' . date('d/m/Y', intval(substr($decision['publishTimestamp'] ?? 0,10))) . '<br>';
        // Add more fields as needed
        echo '</li>';
    }
    echo '</ul></div>';
} else {
    echo Text::_('MOD_DIAVGEIA_NO_DECISIONS');
}

