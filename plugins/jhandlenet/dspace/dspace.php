<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
* A plugin for creating handles from DSpace.
 *
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

jimport('joomla.error.log');
jimport('jspace.factory');

class PlgJHandleNetDSpace extends JPlugin
{
    private $na = null;

    private $connector = null;

    protected static $chunk;

    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);

        Jlog::addLogger(array('text_file'=>'jhandlenet.php'), JLog::ALL, 'jhandlenet');

        static::$chunk = 5;
    }

    private function getNA()
    {
        if ($this->na === null) {
            $params = JComponentHelper::getParams('com_jhandlenet');

            $db = JFactory::getDbo();

            JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jhandlenet/tables');
            $this->na = JTable::getInstance('NA', 'JHandleNetTable', array('dbo'=>$db));
        }

        return $this->na;
    }

    private function setConnector($params)
    {
        $options = array();
        $options['driver'] = 'DSpace';
        $options['url'] = $params->get('archive_endpoint');
        $options['username'] = $params->get('archive_username');
        $options['password'] = $params->get('archive_password');

        $this->connector = JSpaceFactory::getConnector($options);
    }

    private function getConnector()
    {
        return $this->connector;
    }

    public function onHandlesPurge()
    {

    }

    public function onHandlesCreate($na)
    {
        if (!$this->getNA()->load($na)) {
            throw new Exception('No such naming authority.');
        }

        if (!class_exists('JSpaceFactory')) {
            throw new Exception('JSpace library not installed.');
        }

        $this->setConnector($this->getNA());

        $db = $this->getNA()->getDbo();

        $this->insert($this->getItems());
    }

    public function onHandlesUpdate($na)
    {
        if (!$this->getNA()->load($na)) {
            throw new Exception('No such naming authority.');
        }

        if (!class_exists('JSpaceFactory')) {
            throw new Exception('JSpace library not installed.');
        }

        $this->setConnector($this->getNA());

        $db = $this->getNA()->getDbo();

        $query = $db->getQuery(true);
        $query
            ->select('MAX(data)')
            ->from('handles');

        $db->setQuery($query);

        $filters = array();

        if (($max = $db->loadResult()) !== null) {
            $filters[] = "search.resourceid:[".($max+1)." TO *]";
        }

        $this->insert($this->getItems(0, null, $filters));
    }

    public function onHandlesClean($na)
    {
        if (!$this->getNA()->load($na)) {
            throw new Exception('No such naming authority.');
        }

        if (!class_exists('JSpaceFactory')) {
            throw new Exception('JSpace library not installed.');
        }

        $this->setConnector($this->getNA());

        $table = $this->getNA();

        $db = $table->getDbo();

        $array = array();

        $query = $db->getQuery(true);
        $query
        ->select('na, handle')
        ->from('handles')
        ->order('handle asc');

        $db->setQuery($query);

        $filters = array();

        $items = $this->getItems();

        // @todo Need a better search algorithm?
        foreach ($db->loadObjectList() as $row) {
            $found = false;
            $handle = $row->handle;

            reset($items);

            while (($item = current($items)) && !$found) {
                $found = ($handle == $item->handle);
                next($items);
            }

            if (!$found) {
                $query = $db->getQuery(true);
                $query
                ->delete('handles')
                ->where("handle = '".$handle."'");
            }
        }
    }

    /**
     * Gets all DSpace items using the JSpace component and DSpace REST API.
     *
     * @return array A list of DSpace items.
     */
    protected function getItems($start = 0, $limit = null, $filters = array())
    {
        $items = array();

        try {
            $items = array();

            $connector = $this->getConnector();

            $fq = array();
            $fq[] = 'search.resourcetype:2';
            $fq = array_merge($fq, $filters);

            $vars = array();
            $vars['q'] = '*:*';
            $vars['fl'] = 'search.resourceid,handle';
            $vars['start'] = $start;

            if ($limit) {
                $vars['rows'] = $limit;
            } else {
                $vars['rows'] = '2147483647';
            }

            // for some reason we have to url encode here. Looks like the JSpace connector has some bugs.
            $vars['sort'] = rawurlencode('handle asc');

            $vars['fq'] = rawurlencode(implode(' AND ', $fq));

            $response = json_decode($connector->get(JSpaceFactory::getEndpoint('/discover.json', $vars), false));

            if (isset($response->response->docs)) {
                $items = $response->response->docs;
            }
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR);
        }

        return $items;
    }

    private function insert($items)
    {
        $na = $this->getNA();

        $db = $na->getDbo();

        $array = array();

        foreach ($items as $item) {
            $handle = $item->handle;
            $handle = JArrayHelper::getValue(explode('/', $handle), 1);
            $handle = $na->na.'/'.$handle;

            $id = $item->{"search.resourceid"};

            $row = array();

            $row[] = "'$handle'";       // handle
            $row[] = "1";               // idx (always 1 for this type of handle)
            $row[] = "'URL'";           // type (always URL)
            $row[] = "'$id'";           // data (should only be the id of the actual item)
            $row[] = '0';               // ttl_type (always 0)
            $row[] = '86400';           // ttl
            $row[] = 'NOW()';           // timestamp (NOW())
            $row[] = "''";              // refs (empty string)
            $row[] = '1';               // admin_read (always 1)
            $row[] = '0';               // admin_write (should be 0 to stop handle.net writing to the db)
            $row[] = '1';               // pub_read (always 1)
            $row[] = '0';               // pub_write (always 0)
            $row[] = "'".$na->na."'";   // na

            $array[] = implode(',',$row);

            if (count($array) == count($items) || count($array) == static::$chunk) {
                $query = $db->getQuery(true);
                $query
                ->insert('handles')
                ->values($array);

                $db->setQuery($query);
                $db->execute();

                $array = array();
            }
        }
    }
}
