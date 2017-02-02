<?php
/**
 * Installation scripts.
 *
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class com_JHandleNetInstallerScript
{
    public function postflight($type, $adapter)
    {
        error_log(print_r($adapter->getParent(),1));
        $src = $adapter->getParent()->getPath('extension_administrator').'/cli/jhandlenet.php';

        $cli = JPATH_ROOT.'/cli/jhandlenet.php';

        if (JFile::exists($src)) {
            if (JFile::move($src, $cli)) {
                JFolder::delete($adapter->getParent()->getPath('extension_administrator').'/cli');
            }
        }
    }

    public function uninstall(JAdapterInstance $adapter)
    {
        $src = JPATH_ROOT."/cli/jhandlenet.php";

        if (JFile::exists($src)){
            if (JFile::delete($src)) {
                echo "<p>JHandleNet uninstalled from ".$src." successfully.</p>";
            } else {
                echo "<p>Could not uninstall jhandlenet from ".$src.". You will need to manually remove it.</p>";
            }
        }
    }
}
