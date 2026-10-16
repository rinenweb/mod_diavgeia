<?php
/**
 * @package     Mod_Diavgeia
 * @author      Rinenweb <info@rinenweb.eu>
 * @copyright   (C) 2023-2026 Rinenweb
 * @license     GNU General Public License version 2 or later
 * @link        https://www.rinenweb.eu
 *
 * Available variables: $params, $app, $module, $introText, $decisions, $organizationNames,
 * $showOrganization, $adadisplay, $hasError
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$customCss = trim((string) $params->get('customCss', ''));

if ($customCss !== '') {
    $app->getDocument()->getWebAssetManager()->addInlineStyle($customCss);
}
?>
<?php if ($introText !== '') : ?>
    <div class="mod-diavgeia-intro-text"><?php echo $introText; ?></div>
<?php endif; ?>

<?php if ($hasError) : ?>
    <p class="mod-diavgeia-unavailable"><?php echo Text::_('MOD_DIAVGEIA_UNAVAILABLE'); ?></p>
<?php elseif (empty($decisions)) : ?>
    <p class="mod-diavgeia-no-decisions"><?php echo Text::_('MOD_DIAVGEIA_NO_DECISIONS'); ?></p>
<?php else : ?>
    <ul class="mod-diavgeia-desicions">
        <?php foreach ($decisions as $decision) : ?>
            <?php
            $subject        = isset($decision['subject']) ? (string) $decision['subject'] : '';
            $ada            = isset($decision['ada']) ? (string) $decision['ada'] : '';
            $organizationId = isset($decision['organizationId']) ? (string) $decision['organizationId'] : '';
            $timestamp      = isset($decision['publishTimestamp']) ? intdiv((int) $decision['publishTimestamp'], 1000) : 0;
            ?>
            <li>
                <?php echo $escape($subject); ?><br>
                <?php if ($showOrganization && !empty($organizationNames[$organizationId])) : ?>
                    <?php echo Text::_('MOD_DIAVGEIA_ORGANIZATION'); ?>: <?php echo $escape($organizationNames[$organizationId]); ?><br>
                <?php endif; ?>
                <?php if ($adadisplay && $ada !== '') : ?>
                    <strong><?php echo Text::_('MOD_DIAVGEIA_ADA'); ?>: </strong><a href="https://diavgeia.gov.gr/decision/view/<?php echo rawurlencode($ada); ?>" target="_blank" rel="noopener"><?php echo $escape($ada); ?></a><br>
                <?php endif; ?>
                <?php if ($timestamp > 0) : ?>
                    <strong><em><time datetime="<?php echo HTMLHelper::_('date', $timestamp, 'c'); ?>"><?php echo HTMLHelper::_('date', $timestamp, 'd/m/Y'); ?></time></em></strong><br>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
