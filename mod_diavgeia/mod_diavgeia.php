<?php
defined('_JEXEC') or die;

// Import namespaces
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\Diavgeia\Site\Helper\DiavgeiaHelper;

// Get the module parameters
$module = Factory::getDocument();
$introText = $params->get('intro_text');
$keywords = $params->get('keywords');
$num_decisions = $params->get('num_decisions', 5); // Default to 5 if not specified

// Load language strings
Text::script('MOD_DIAVGEIA_KEYWORDS');
Text::script('MOD_DIAVGEIA_NUM_DECISIONS');
Text::script('MOD_DIAVGEIA_NO_DECISIONS');
Text::script('MOD_DIAVGEIA_INTRO_TEXT');

try {
    // Call the helper function to get decisions
    $data = DiavgeiaHelper::getDecisions($keywords, $num_decisions, 'application/json');

    // Output the data by including the template file
    require ModuleHelper::getLayoutPath('mod_diavgeia', $params->get('layout', 'default'));

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
