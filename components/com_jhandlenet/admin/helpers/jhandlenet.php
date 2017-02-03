<?php
/**
 * A helper that provides assistance with permissions adn submenus for JHandleNet.
 *
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

class JHandleNetHelper
{
    public static $extension = 'com_jhandlenet';

    /**
     * Configure the Linkbar.
     *
     * @param   string  $vName  The name of the active view.
     *
     * @return  void
     */
    public static function addSubmenu($vName)
    {

    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @param   int      The community ID.
     *
     * @return	JObject
     */
    public static function getActions($na = 0)
    {
        $user	= JFactory::getUser();
        $result	= new JObject();

        if (empty($na)) {
            $assetName = 'com_jhandlenet';
        } else {
            $assetName = 'com_jhandlenet.prefix.'.$na;
        }

        $level = 'component';

        $actions = JAccess::getActions($assetName, $level);

        foreach ($actions as $action) {
            $result->set($action->name, $user->authorise($action, $assetName));
        }

        return $result;
    }

    public static function log($msg, $type = JLog::ERROR)
    {
        $app = \Joomla\Utilities\ArrayHelper::getValue($GLOBALS, 'application');

        $verbose = (bool)($app->input->get('v') || $app->input->get('verbose'));
        $quiet = (bool)($app->input->get('q') || $app->input->get('quiet'));

        if (get_class($app) == "JHandleNetCli") {
            if ($type == JLog::ERROR) {
                if (!$quiet) {
                    $app->out($msg);
                }
            } else {
                if ($verbose) {
                    $app->out($msg);
                }
            }
        } else {
            JLog::add($msg, $type, 'jhandlenet');
        }
    }
}
