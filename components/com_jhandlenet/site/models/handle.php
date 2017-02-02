<?php
/**
 * A model for managing a handle in JHandleNet.
 *
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * A model for managing a handle in JHandleNet.
 *
 * @package  JHandleNet
 */
class JHandleNetModelHandle extends JModelItem
{
    /**
     * Model context string.
     *
     * @access  protected
     * @var     string
     */
    protected $_context = 'com_jhandlenet.handle';

    /**
     * Creates an instance of the JHandleNetModelHandle class.
     *
     * @param  array  An optional associative array of configuration settings.
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

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
     * Method to get an object.
     *
     * @param   integer  The id of the object to get.
     *
     * @return  mixed    Object on success, false on failure.
     */
    public function getItem($id = null)
    {
        if ($this->_item === null) {
            $this->_item = false;

            $db = $this->getDbo();

            $query = $db->getQuery(true);
            $query
                ->select('a.*, b.url')
                ->from('#__jhandlenet_handles AS a')
                ->join('inner', '#__jhandlenet_nas as b ON (a.na = b.na)')
                ->where("handle='".$id."'");

            $db->setQuery($query);

            $this->_item = $db->loadObject();
            $this->_item->url = preg_replace('{/$}', '', $this->_item->url);
            print_r($this->_item);
        }

        return $this->_item;
    }
}
