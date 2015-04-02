<?php
/**
 * A helper that provides assistance with permissions adn submenus for JHandleNet.
 * 
 * @package		JHandleNet
 * @copyright	Copyright (C) 2013 KnowledgeARC Ltd. All rights reserved.
 * @license     This file is part of the JHandleNet component for Joomla!.

   The JHandleNet component for Joomla! is free software: you can redistribute it 
   and/or modify it under the terms of the GNU General Public License as 
   published by the Free Software Foundation, either version 3 of the License, 
   or (at your option) any later version.

   The JHandleNet component for Joomla! is distributed in the hope that it will be 
   useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with the JHandleNet component for Joomla!.  If not, see 
   <http://www.gnu.org/licenses/>.

 * Contributors
 * Please feel free to add your name and email (optional) here if you have 
 * contributed any source code changes.
 * Name							Email 
 * 
 */

defined('_JEXEC') or die('Restricted access');

class JHandleNetHelper
{
	public static $extension = 'com_jhandlenet';

	/**
	 * Configure the Linkbar.
	 *
	 * @param string $vName The name of the active view.
	 *
	 * @return void
	 */
	public static function addSubmenu($vName)
	{

	}
	
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The community ID.
	 *
	 * @return	JObject
	 * @since	1.6
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
}