<?php
/**
 * A class representing the "na" table.
 *
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

class JHandleNetTableNA extends JTable
{
    public function __construct(&$db)
    {
        parent::__construct('#__jhandlenet_nas', 'na', $db);
    }

    public function store($updateNulls = false)
    {
        $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);

        return true;
    }

    public function exists($keys)
    {
        if (!is_array($keys)) {
            // Load by primary key.
            $keys = array($this->_tbl_key => $keys);
        }

        // Initialise the query.
        $query = $this->_db->getQuery(true);
        $query->select('*');
        $query->from($this->_tbl);
        $fields = array_keys($this->getProperties());

        foreach ($keys as $field => $value) {
            // Check that $field is in the table.
            if (!in_array($field, $fields)) {
                throw new UnexpectedValueException(sprintf('Missing field in database: %s &#160; %s.', get_class($this), $field));
            }
            // Add the search tuple to the query.
            $query->where($this->_db->quoteName($field) . ' = ' . $this->_db->quote($value));
        }

        $this->_db->setQuery($query);

        $row = $this->_db->loadAssoc();

        // Check that we have a result.
        if (empty($row)) {
            return false;
        }

        // Bind the object with the row and return.
        return true;
    }
}
