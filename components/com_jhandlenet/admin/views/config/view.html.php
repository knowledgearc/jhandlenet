<?php
/**
 * A view for generating the config.dct definition with JHandleNet.
 *
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

/**
 * A view that homes a prefix in JHandleNet.
 *
 * @package  JHandleNet
 */
class JHandleNetViewConfig extends JViewLegacy
{
    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        JToolbarHelper::title(JText::_('COM_JHANDLENET_CONFIG'), 'equalizer.png');

        parent::display($tpl);
    }
}
