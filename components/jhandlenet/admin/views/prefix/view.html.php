<?php
/**
 * A view that homes a prefix in JHandleNet.
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
 * A view that homes a prefix in JHandleNet.
 *
 * @package		JHandleNet
 */
class JHandleNetViewPrefix extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;
	
	protected $isNew;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
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
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$canDo		= JHandleNetHelper::getActions(0);

		JToolbarHelper::title($this->isNew ? JText::_('COM_JHANDLENET_MANAGER_PREFIX_NEW') : JText::_('COM_JHANDLENET_MANAGER_PREFIX_EDIT'), 'banners.png');

		// If not checked out, can save the item.
		if ($canDo->get('core.edit')) {
			JToolbarHelper::save('prefix.save');
		}

		if (empty($this->item->na)) {
			JToolbarHelper::cancel('prefix.cancel');
		} else {
			JToolbarHelper::cancel('prefix.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}