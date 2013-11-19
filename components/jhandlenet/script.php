<?php
/**
 * Installation scripts.
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