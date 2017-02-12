<?php
/**
 * A template for homing a prefix in JHandleNet.
 *
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

$config = new JConfig;

$db = $config->db;
$dbUser = $config->user;
$dbPassword = $config->password;
$dbPrefix = $config->dbprefix;
$url = JFactory::getUri()->base();
?>

<label><?php echo JText::_("COM_JHANDLENET_CONFIG_DCT_LABEL"); ?></label>
<div><textarea class="span12" style="height: 550px;"><?php echo JText::sprintf('COM_JHANDLENET_CONFIG_DCT', $db, $dbUser, $dbPassword, $url, $dbPrefix, $dbPrefix); ?></textarea></div>
