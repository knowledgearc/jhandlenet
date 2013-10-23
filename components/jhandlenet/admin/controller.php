<?php
/**
 * A controller for managing Handle.net configuration.
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

class JHandleNetController extends JControllerLegacy
{
	protected $default_view = 'prefixes';
	protected $canDo;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->canDo = JHandleNetHelper::getActions();
	}
	
	public function home()
	{
		if ($this->canDo->get('core.admin')) {
			if (!$this->getModel('configuration')->homePrefix()) {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_JHANDLENET_ERROR_UNEXPECTED_HOMING_SUCCESS'));
				$this->setRedirect(JRoute::_('index.php?option=com_jhandlenet'));
			} else {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_JHANDLENET_ERROR_UNEXPECTED_HOMING_ERROR'), 'error');
			}
		} else {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JHANDLENET_NO_ADMIN_PERMISSION'), 'error');
		}		
	}

	public function display($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $urlparams);
		
		return $this;
	}
}