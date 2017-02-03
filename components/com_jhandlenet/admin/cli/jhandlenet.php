#!/usr/bin/php
<?php
/**
 * @package    JHandleNet
 * @copyright  Copyright (C) 2013-2017 KnowledgeArc Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Make sure we're being called from the command line, not a web interface
if (array_key_exists('REQUEST_METHOD', $_SERVER)) die();

/**
 * This is a CRON script which should be called from the command-line, not the
 * web. For example something like:
 * /usr/bin/php /path/to/site/cli/jcrawl.php
 */

// Set flag that this is a parent file.
define('_JEXEC', 1);

// Load system defines
if (file_exists(dirname(dirname(__FILE__)).'/defines.php')) {
    require_once dirname(dirname(__FILE__)).'/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(dirname(__FILE__)));
    require_once JPATH_BASE.'/includes/defines.php';
}

// Get the framework.
if (file_exists(JPATH_LIBRARIES.'/import.legacy.php'))
    require_once JPATH_LIBRARIES.'/import.legacy.php';
else
    require_once JPATH_LIBRARIES.'/import.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES.'/cms.php';

// Load the configuration
require_once JPATH_CONFIGURATION.'/configuration.php';


if (version_compare(JVERSION, "3.0", "l")) {
    // Force library to be in JError legacy mode
    JError::$legacy = true;

    // Import necessary classes not handled by the autoloaders
    jimport('joomla.application.menu');
    jimport('joomla.environment.uri');
    jimport('joomla.event.dispatcher');
    jimport('joomla.utilities.utility');
    jimport('joomla.utilities.arrayhelper');
}

// System configuration.
$config = new JConfig;

// Configure error reporting to maximum for CLI output.
error_reporting(E_ALL & ~E_STRICT);
ini_set('display_errors', 1);

// Load Library language
$lang = JFactory::getLanguage();
$lang->load('com_jhandlenet', JPATH_SITE);

$path = JPATH_ADMINISTRATOR.'/components/com_jhandlenet/helpers/jhandlenet.php';
\JLoader::register('JHandleNetHelper', $path);

jimport('joomla.application.component.helper');
jimport('joomla.log.log');

use \Joomla\Utilities\ArrayHelper;
use \Joomla\Input\Input;
use \Joomla\Registry\Registry;

/**
 * Simple command line interface application class.
 *
 * @package JHandleNet.CLI
 */
class JHandleNetCli extends JApplicationCli
{
    private $db;

    public function __construct($input = null, JRegistry $config = null, JEventDispatcher $dispatcher = null)
    {
        parent::__construct($input, $config, $dispatcher);

        $GLOBALS['application'] = $this;

        // fool the system into thinking we are running as JSite with JHandleNet as the active component
        JFactory::getApplication('site');
        $_SERVER['HTTP_HOST'] = 'domain.com';

        // Disable caching.
        $config = JFactory::getConfig();
        $config->set('caching', 0);
        $config->set('cache_handler', 'file');

    }

    public function doExecute()
    {
        $command = ArrayHelper::getValue($this->input->args, 0, null, 'word');

        try {
            switch ($command) {
                case 'home':
                case 'unhome':
                case 'import':
                case 'purge':
                    $this->$command();
                    break;

                case 'help':
                    $this->help();
                    break;

                default:
                    $this->out(JText::sprintf("COM_JHANDLENET_CLI_COMMAND_NOT_FOUND", $command));
                    break;
            }
        } catch (Exception $e) {
            JHandleNetHelper::log($e->getMessage(), \JLog::ERROR);
            JHandleNetHelper::log($e->getTraceAsString(), \JLog::DEBUG);
        }
    }

    public function home()
    {
        $na = ArrayHelper::getValue($this->input->args, 1, null, 'word');

        if (!$na) {
            JHandleNetHelper::log('No naming authority specified.', \JLog::ERROR);
            return;
        }

        if ($na == "help") {
            $this->out(JText::_("COM_JHANDLENET_CLI_HOME_HELP"));
            return;
        }

        $table = $this->getTable();

        if ($table->load($na)) {
            JHandleNetHelper::log(JText::sprintf('Cannot home handle prefix %s. Already exists.', $na), \JLog::ERROR);
        } else {
            $table->na = $na;

            if ($table->store()) {
                JHandleNetHelper::log(JText::sprintf('Handle prefix %s homed.', $na), \JLog::DEBUG);
            }
        }
    }

    public function unhome()
    {
        $na = ArrayHelper::getValue($this->input->args, 1, null, 'word');

        if (!$na) {
            JHandleNetHelper::log('No naming authority specified.', \JLog::ERROR);
            return;
        }

        if ($na == "help") {
            $this->out(JText::_("COM_JHANDLENET_CLI_UNHOME_HELP"));
            return;
        }

        $table = $this->getTable();

        if ($table->load($na)) {
            if ($table->delete()) {
                $this->out(JText::sprintf('Handle prefix %s unhomed.', $na), \JLog::DEBUG);
            }
        } else {
            $this->out(JText::sprintf("Cannot unhome handle prefix %s. Prefix doesn't exists.", $na), \JLog::ERROR);
        }
    }

    public function import()
    {
        if (ArrayHelper::getValue($this->input->args, 1, null, 'word') == 'help') {
            $this->out(JText::_("COM_JHANDLENET_CLI_IMPORT_HELP"));
        }

        try {
            $this->fireEvent('onHandlesImport');
        } catch (Exception $e) {
            $this->out($e->getMessage(), \JLog::ERROR);
        }
    }

    public function purge()
    {
        $na = ArrayHelper::getValue($this->input->args, 1, null, 'word');

        if ($na == "help") {
            $this->out(JText::_("COM_JHANDLENET_CLI_PURGE_HELP"));
            return;
        }

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query->delete($db->qn('handles'));

        if ($na) {
            $query->where($db->qn('na').'='.$db->q($na));
        }

        $db->setQuery($query);
        $db->execute();
    }

    /**
     * Method to build and print the help screen text to stdout.
     */
    protected function help($commands = null)
    {
        $this->out(JText::_("COM_JHANDLENET_CLI_HELP"));
    }

    private function fireEvent($name, $args = array())
    {
        $dispatcher = JDispatcher::getInstance();

        JPluginHelper::importPlugin("jhandlenet", null, true, $dispatcher);

        return $dispatcher->trigger($name, $args);
    }

    public function getTable()
    {
        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_jhandlenet/tables');
        return JTable::getInstance('NA', 'JHandleNetTable', array());
    }
}

JApplicationCli::getInstance('JHandleNetCli')->execute();
