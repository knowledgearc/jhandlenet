<?php
/**
 * A model that lists homed prefixes in JHandleNet.
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
 * A model that lists homed prefixes in JHandleNet.
 *
 * @package JHandleNet
 */
class JHandleNetModelPrefixes extends JModelList
{
	/**
	 * Creates an instance of the JHandleNetModelPrefixes class.
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
		try {
			$db->connect();
		} catch (Exception $e) {
			// ignore connection error and continue.
		}
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
	 * Build an SQL query to load the list data.
	 *
	 * @return JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
	
		$select = array('a.na', 'a.url', 'COUNT(b.handle) AS count');
		$query
			->select($select)
			->from($db->quoteName('nas') . ' AS a')
			->join('left', $db->quoteName('handles') . ' AS b ON a.na = b.na')
			->group('na');

		return $query;
	}
	
	public function getItems()
	{
		$db = $this->getDbo();
		
		try {
			if ($db->connected()) {
				return parent::getItems();
			}				
		} catch (Exception $e) {
			return array();
		}

		return array();
	}
	
	public function getTotal()
	{
		$db = $this->getDbo();

		try {
			if ($db->connected()) {
				return parent::getTotal();
			}				
		} catch (Exception $e) {
			return 0;
		}

		return 0;
	}
}