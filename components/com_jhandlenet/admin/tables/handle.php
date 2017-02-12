<?php
/**
 * A class representing the "handle" table.
 *
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

class JHandleNetTableHandle extends JTable
{
    public function __construct(&$db)
    {
        parent::__construct('#__jhandlenet_handles', 'handle', $db);

        $this->_autoincrement = false;
    }
}
