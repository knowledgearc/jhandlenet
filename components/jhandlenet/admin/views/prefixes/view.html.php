<?php
/**
 * A view that lists homed prefixes in JHandleNet.
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

/**
 * A view that lists homed prefixes in JHandleNet.
 *
 * @package     JHandleNet
 */
class JHandleNetViewPrefixes extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
			
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JToolbarHelper::title(JText::_('COM_JHANDLENET_MANAGER_PREFIXES'), 'banners.png');
		
		$user = JFactory::getUser();

		$bar = JToolBar::getInstance('toolbar');

		$canDo = JHandleNetHelper::getActions();
		
		if (!count($errors = $this->get('Errors')) && $this->get('Dbo')->connected()) {		
			if (count($user->authorise('core.create', 'com_jhandlenet')) > 0) {
				JToolbarHelper::addNew('prefix.home');
			}
	
			if (count($user->authorise('core.delete', 'com_jhandlenet')) > 0) {
				JToolbarHelper::deleteList(JText::_('COM_JHANDLENET_CONFIRM_DELETE'), 'prefixes.delete');
			}
		}
		
		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_jhandlenet');
		}

	}
}