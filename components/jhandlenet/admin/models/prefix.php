<?php
/**
 * A model for managing a prefix in JHandleNet.
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

JLoader::register('JHandleNetHelper', JPATH_ADMINISTRATOR . '/components/com_jhandlenet/helpers/jhandlenet.php');

/**
 * Prefix model.
 *
 * @package		JHandleNet
 */
class JHandleNetModelPrefix extends JModelAdmin
{
	protected $text_prefix = 'COM_JHANDLENET';

	/**
	 * Creates an instance of the JHandleNetModelPrefix class.
	 *
	 * @param array An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$option = array();

		Jlog::addLogger(array('text_file'=>'jhandlenet.php'), JLog::ALL, 'jhandlenet');

		$params = JComponentHelper::getParams('com_jhandlenet');

		$option['driver']   = 'mysqli';
		$option['host']     = $params->get('host').':'.$params->get('port');
		$option['user']     = $params->get('username');
		$option['password'] = $params->get('password');
		$option['database'] = $params->get('database');
		$option['prefix']   = '';

		$db = JDatabaseDriver::getInstance($option);
		parent::setDbo($db);

		// force a connect so we can use $db->connected.
		$db->connect();
	}

	/**
	 * Auto-populate the model state.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$params = JComponentHelper::getParams('com_jhandlenet');
		$this->setState('params', $params);
	
		parent::populateState();
	}
	
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 */
	public function getTable($type = 'NA', $prefix = 'JHandleNetTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, array('dbo'=>$this->getDbo()));
	}
	
	/**
	 * Method to get the row form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$extension = $this->getState('tag');
		$jinput = JFactory::getApplication()->input;
	
		// Get the form.
		$form = $this->loadForm('com_jhandlenet.prefix', 'prefix', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jhandlenet.edit.prefix.data', array());
	
		if (empty($data)) {
			$data = $this->getItem();
		}
	
		$this->preprocessData('com_jhandlenet.prefix', $data);
	
		return $data;
	}
	
	/**
	 * Method to preprocess the form.
	 *
	 * @param   JForm   $form    A JForm object.
	 * @param   mixed   $data    The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import.
	 *
	 * @return  void
	 * 
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'jhandlenet')
	{
		parent::preprocessForm($form, $data, $group);
	}
	
	public function save($data)
	{
		$dispatcher = JDispatcher::getInstance();
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.na');
		$isNew = true;
		
		JPluginHelper::importPlugin('content');
	
		try {
			// if we can load it then it already exists so let's update.
			if ($table->load($pk)) {
				$isNew = false;
			}
	
			// Bind the data.
			if (!$table->bind($data)) {
				$this->setError($table->getError());
				return false;
			}
	
			// Prepare the row for saving
			$this->prepareTable($table);
	
			// Check the data.
			if (!$table->check()) {
				$this->setError($table->getError());
				return false;
			}
	
			// Trigger the onContentBeforeSave event.
			$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, &$table, $isNew));
			
			if (in_array(false, $result, true)) {
				$this->setError($table->getError());
				return false;
			}
	
			// Store the data.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}
	
			// Clean the cache.
			$this->cleanCache();
	
			// Trigger the onContentAfterSave event.
			$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew));
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	
		$pkName = $table->getKeyName();
	
		if (isset($table->$pkName)) {
			$this->setState($this->getName() . '.na', $table->$pkName);
		}
		
		$this->setState($this->getName() . '.new', $isNew);
	
		return true;
	}
}