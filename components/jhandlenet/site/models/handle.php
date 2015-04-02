<?php
/**
 * A model for managing a handle in JHandleNet.
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
 * A model for managing a handle in JHandleNet.
 *
 * @package     JHandleNet
 */
class JHandleNetModelHandle extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_jhandlenet.handle';

	/**
	 * Creates an instance of the JHandleNetModelHandle class.
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
	 * Method to get an object.
	 *
	 * @param   integer	The id of the object to get.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($id = null)
	{
		if ($this->_item === null) {
			$this->_item = false;
	
			$db = $this->getDbo();

			$query = $db->getQuery(true);
			$query
				->select('a.*, b.url')
				->from('handles AS a')
				->join('inner', 'nas as b ON (a.na = b.na)')
				->where("handle='".$id."'");

			$db->setQuery($query);
			
			$this->_item = $db->loadObject();
			$this->_item->url = preg_replace('{/$}', '', $this->_item->url);
			print_r($this->_item);
		}
	
		return $this->_item;
	}
}