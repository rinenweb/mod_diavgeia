<?php
/**
 * @package    Mod_Diavgeia
 * @author     Rinenweb <info@rinenweb.eu>
 * @license    GNU General Public License v2
 * @link       https://www.rinenweb.eu
 */

// No direct access to this file
defined('_JEXEC') or die;

// Check if there is data to display
if (isset($data) && is_array($data) && !empty($data)) {
    echo '<ul>';
    foreach ($data as $decision) {
        echo '<li>';
//        echo 'Title: ' . $decision['subject'] . '<br>'; // Use htmlspecialchars to escape output
        echo 'Title: ' . htmlspecialchars($decision['subject'] ?? 'N/A') . '<br>'; // Use htmlspecialchars to escape output
//        echo 'Date: ' . date('d/m/Y', substr($decision['publishTimestamp'], 0,10)) . '<br>';
//        echo 'Date: ' . date('d/m/Y', intval(substr($decision['publishTimestamp'], 0,10))) . '<br>';
        echo 'Date: ' . date('d/m/Y', intval(substr($decision['publishTimestamp'] ?? 0,10))) . '<br>';
        // Add more fields as needed
        echo '</li>';
    }
    echo '</ul>';
} else {
    echo JText::_('MOD_DIAVGEIA_NO_DECISIONS');
}

