<?php
/**
 * @package     Mod_Diavgeia
 * @author      Rinenweb <info@rinenweb.eu>
 * @copyright   (C) 2023-2026 Rinenweb
 * @license     GNU General Public License version 2 or later
 * @link        https://www.rinenweb.eu
 */

\defined('_JEXEC') or die;

/**
 * Removes files left behind by version 1.1 when updating.
 */
class mod_diavgeiaInstallerScript
{
    public function postflight($type, $parent)
    {
        if ($type !== 'update') {
            return true;
        }

        $obsolete = [
            // Old tag-prefixed Greek language files
            JPATH_SITE . '/language/el-GR/el-GR.mod_diavgeia.ini',
            JPATH_SITE . '/language/el-GR/el-GR.mod_diavgeia.sys.ini',
            // Duplicate language copies inside the module folder
            JPATH_SITE . '/modules/mod_diavgeia/language',
            // index.html stubs, not needed since Joomla 4
            JPATH_SITE . '/modules/mod_diavgeia/index.html',
            JPATH_SITE . '/modules/mod_diavgeia/tmpl/index.html',
            JPATH_SITE . '/modules/mod_diavgeia/Helper/index.html',
        ];

        foreach ($obsolete as $path) {
            $this->removePath($path);
        }

        return true;
    }

    private function removePath($path)
    {
        if (is_dir($path) && !is_link($path)) {
            foreach (scandir($path) as $item) {
                if ($item !== '.' && $item !== '..') {
                    $this->removePath($path . '/' . $item);
                }
            }

            @rmdir($path);
        } elseif (is_file($path) || is_link($path)) {
            @unlink($path);
        }
    }
}
