<?php
/**
 * JHandleNet master display controller.
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
defined('_JEXEC') or die;

/**
 * JHandleNet Component Controller
 *
 * @package     JHandleNet
 */
class JHandleNetController extends JControllerLegacy
{
	public function resolve()
	{
		$handle = JFactory::getApplication()->input->getString('handle');
		
		$item = $this->getModel('Handle')->getItem($handle);

		if (isset($item->data)) {
			JFactory::getApplication()->redirect($item->url.'/index.php?option=com_jspace&task=resolve&id='.$item->data);
		} else {
			JError::raiseWarning(404, JText::_('COM_JHANDLENET_ORPHANED_HANDLE'));
		}
	}
	
	/**
	 * Method to display a view.
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// At the moment we only have one task; resolve.
		$this->resolve();
	}
}