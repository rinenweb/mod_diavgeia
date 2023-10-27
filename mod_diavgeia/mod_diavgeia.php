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
    // Construct the API URL with query parameters for GET request
    $apiUrl = 'https://diavgeia.gov.gr/opendata/search?subject=' . urlencode($keywords) . '&size=' . $num_decisions;

    // Debugging: Output the API URL
    echo 'API URL: ' . $apiUrl . '<br>';


    // Call the helper function to get decisions
    $data = DiavgeiaHelper::getDecisions($keywords, $num_decisions);

    // Debugging: Output the API response
echo '<pre>';
print_r($data);
echo '</pre>';

    // Output the data by including the template file
    ob_start();
    require ModuleHelper::getLayoutPath('mod_diavgeia', $params->get('layout', 'default'));
    $output = ob_get_clean();

    echo $output;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
