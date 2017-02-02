<?php
/**
 * A model that lists homed prefixes in JHandleNet.
 *
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

/**
 * A model that lists homed prefixes in JHandleNet.
 *
 * @package  JHandleNet
 */
class JHandleNetModelPrefixes extends JModelList
{
    /**
     * Creates an instance of the JHandleNetModelPrefixes class.
     *
     * @param  array  An optional associative array of configuration settings.
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

        $option = array();

        Jlog::addLogger(array('text_file'=>'jhandlenet.php'), JLog::ALL, 'jhandlenet');
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
     * @return  JDatabaseQuery
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();

        $query = $db->getQuery(true);

        $select = array('a.na', 'a.url', 'COUNT(b.handle) AS count');
        $query
            ->select($select)
            ->from($db->quoteName('#__jhandlenet_nas') . ' AS a')
            ->join('left', $db->quoteName('#__jhandlenet_handles') . ' AS b ON a.na = b.na')
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
