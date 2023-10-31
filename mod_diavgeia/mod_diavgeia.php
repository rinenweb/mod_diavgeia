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
$showOrganization = $params->get('organization', 0); // Default to 0 (No)
$adadisplay = $params->get('ada_display', 0); // Default to 0 (No)

// Load language strings
Text::script('MOD_DIAVGEIA_KEYWORDS');
Text::script('MOD_DIAVGEIA_NUM_DECISIONS');
Text::script('MOD_DIAVGEIA_NO_DECISIONS');
Text::script('MOD_DIAVGEIA_INTRO_TEXT');
Text::script('MOD_DIAVGEIA_ORGANIZATION_DISPLAY_LABEL');
Text::script('MOD_DIAVGEIA_ORGANIZATION');
Text::script('MOD_DIAVGEIA_ADA_DISPLAY');
Text::script('MOD_DIAVGEIA_ADA');

try {
    // Call the helper function to get decisions
    $data = DiavgeiaHelper::getDecisions($keywords, $num_decisions, 'application/json');

    // Fetch the organization name based on organizationId    
    if ($showOrganization) {
        $organizationNames = array();
        foreach ($data['decisions'] as $decision) {
            $organizationName = DiavgeiaHelper::getOrganizationName($decision['organizationId'], 'application/json');
            $organizationNames += [$decision['organizationId'] => $organizationName];            
        }        
    }
    // Output the data by including the template file    
    require ModuleHelper::getLayoutPath('mod_diavgeia', $params->get('layout', 'default'));

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
