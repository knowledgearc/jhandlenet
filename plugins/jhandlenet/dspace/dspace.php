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

    private function getNa()
    {
        if ($this->na === null) {
            $params = JComponentHelper::getParams('com_jhandlenet');

            $db = JFactory::getDbo();

            JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jhandlenet/tables');
            $this->na = JTable::getInstance('Na', 'JHandleNetTable', array('dbo'=>$db));
        }

        return $this->na;
    }

    public function onHandlesImport()
    {
        $timestamp = JFactory::getDate();

        $start = 0;
        $limit = 1000;
        $total = $this->getTotal();

        while ($start < $total) {
            $items = $this->getItems($start, $limit);

            foreach ($items as $item) {
                $handle = $item->handle;
                $na = JArrayHelper::getValue(explode('/', $handle), 0);
                $id = "dspace:".$item->{"search.resourceid"};

                if ($this->getNa()->load($na)) {
                    $table = JTable::getInstance('Handle', 'JHandleNetTable', array());
                    $table->load($handle);

                    $table->handle = $handle;       // handle
                    $table->idx = "1";              // idx (always 1 for this type of handle)
                    $table->type = "URL";           // type (always URL)
                    $table->data = $id;             // data (should only be the id of the actual item)
                    $table->ttl_type = '0';         // ttl_type (always 0)
                    $table->ttl = '86400';          // ttl
                    $table->timestamp = $timestamp->toUnix();   // timestamp (NOW())
                    $table->refs = "";              // refs (empty string)
                    $table->admin_read = '1';       // admin_read (always 1)
                    $table->admin_write = '0';      // admin_write (should be 0 to stop handle.net writing to the db)
                    $table->pub_read = '1';         // pub_read (always 1)
                    $table->pub_write = '0';        // pub_write (always 0)
                    $table->na = $na;               // na
                    $table->context = "jcar.item";  // context

                    if ($table->store()) {
                        JHandleNetHelper::log(JText::sprintf("handle %s imported", $handle), \JLog::DEBUG);
                    } else {
                        JHandleNetHelper::log($table->getError(), \JLog::ERROR);
                    }
                } else {
                    JHandleNetHelper::log(JText::sprintf("Naming authority %s doesn't exist for handle %s. Ignoring...", $na, $handle), \JLog::DEBUG);
                }

                $start++;
            }
        }

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query
            ->select('COUNT(handle)')
            ->from($db->qn("handles"))
            ->where("NOT ".$db->qn('timestamp')." = '".$timestamp->toUnix()."'");

        $count = $db->setQuery($query)->loadResult();

        if ($count > 0) {
            JHandleNetHelper::log(JText::sprintf("deleting %s stale handles...", $count), \JLog::DEBUG);

            $query = $db->getQuery(true);

            $query
                ->delete($db->qn("handles"))
                ->where("NOT ".$db->qn('timestamp')." = '".$timestamp->toUnix()."'");

            $db->setQuery($query)->execute();
        }
    }

    public function onHandleResolve($item)
    {
        if ($item->context == 'jcar.item') {
            require_once(JPATH_ROOT."/components/com_jcar/helpers/route.php");

            if (class_exists("JCarHelperRoute")) {
                return JCarHelperRoute::getItemRoute($item->data);
            }
        }

        return false;
    }

    /**
     * Gets all DSpace items using the DSpace REST API.
     *
     * @return array A list of DSpace items.
     */
    protected function getItems($start = 0, $limit = 10)
    {
        $items = array();

        try {
            $items = array();

            $vars = array();

            $vars['q'] = "*:*";

            $vars['fl'] = 'search.resourceid,search.uniqueid,read,handle';

            $vars['fq'] = 'search.resourcetype:2';

            $vars['start'] = $start;
            $vars['rows'] = $limit;

            if ($this->get('params')->get('private_access', "") == "") {
                $vars['fq'] .= ' AND read:g0';
            } else {
                // only get items with read set.
                $vars['fq'] .= ' AND read:[* TO *]';
            }

            $vars['fq'] = urlencode($vars['fq']);

            $url = new JUri($this->params->get('url').'/discover.json');

            $url->setQuery($vars);

            $http = JHttpFactory::getHttp();

            $response = $http->get((string)$url);

            if ((int)$response->code !== 200) {
                throw new Exception($response->body, $response->code);
            }

            $response = json_decode($response->body);

            if (isset($response->response->docs)) {
                $items = $response->response->docs;
            }
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'jsolr');
        }

        return $items;
    }

    /**
     * Get the total number of DSpace items.
     *
     * @return  int  The total number of DSpace items.
     */
    protected function getTotal()
    {
        $total = 0;

        try {
            $vars = array();

            $vars['q'] = "*:*";
            $vars['fq'] = 'search.resourcetype:2';
            $vars['rows'] = '0';

            if ($this->get('params')->get('private_access', "") == "") {
                $vars['fq'] .= ' AND read:g0';
            } else {
                // only get items with read set.
                $vars['fq'] .= ' AND read:[* TO *]';
            }

            $vars['fq'] = urlencode($vars['fq']);

            $url = new JUri($this->params->get('url').'/discover.json');

            $url->setQuery($vars);

            $http = JHttpFactory::getHttp();

            $response = $http->get((string)$url);

            if ((int)$response->code !== 200) {
                throw new Exception($response->body, $response->code);
            }

            $response = json_decode($response->body);

            return (int)$response->response->numFound;
        } catch (Exception $e) {
            JHandleNetHelper::log($e->getMessage(), \JLog::ERROR);
        }
    }
}
