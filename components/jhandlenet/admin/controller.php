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

defined('_JEXEC') or die('Restricted access');

/**
 * JHandleNet master display controller.
 *
 * @package		JHandleNet
 */
class JHandleNetController extends JControllerLegacy
{
	protected $default_view = 'prefixes';
	
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
		require_once JPATH_COMPONENT.'/helpers/jhandlenet.php';

		$view   = $this->input->get('view', 'prefixes');
		$layout = $this->input->get('layout', 'default');
		$na     = $this->input->getInt('na');

		// Check for edit form.
		if ($view == 'prefix' && $layout == 'edit' && !$this->checkEditId('com_jhandlenet.edit.prefix', $na)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('COM_JHANDLENET_ERROR_UNHELD_NA', $na));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_jhandlenet&view=prefixes', false));

			return false;
		}

		parent::display($cachable, $urlparams);		
	}
}