<?php
defined('_JEXEC') or die;

// Import namespaces
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\Diavgeia\Site\Helper\DiavgeiaHelper;

// Get the module parameters
$module = Factory::getDocument();
$params = $module->params;
$keywords = $params->get('keywords');
$num_decisions = $params->get('num_decisions', 5); // Default to 5 if not specified

// Load language strings
JText::script('MOD_DIAVGEIA_KEYWORDS');
JText::script('MOD_DIAVGEIA_NUM_DECISIONS');
JText::script('MOD_DIAVGEIA_NO_DECISIONS');

try {
    // Call the helper function to get decisions
    $data = DiavgeiaHelper::getDecisions($keywords, $num_decisions);

    // Output the data by including the template file
    ob_start();
    require Joomla\CMS\Helper\ModuleHelper::getLayoutPath('mod_diavgeia', $params->get('layout', 'default'));
    $output = ob_get_clean();

    echo $output;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
