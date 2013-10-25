#!/usr/bin/php
<?php
/**
 * @package JHandleNet
 * @copyright Copyright (C) 2013 KnowledgeARC Ltd. All rights reserved.
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
if (file_exists(dirname(dirname(__FILE__)) . '/defines.php')) {
        require_once dirname(dirname(__FILE__)) . '/defines.php';
}

if (!defined('_JDEFINES')) {
	define('JPATH_BASE', dirname(dirname(__FILE__)));
	require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
if (file_exists(JPATH_LIBRARIES . '/import.legacy.php'))
	require_once JPATH_LIBRARIES . '/import.legacy.php';	
else
	require_once JPATH_LIBRARIES . '/import.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

// Load the configuration
require_once JPATH_CONFIGURATION . '/configuration.php';


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

// Try the finder_cli file in the current language (without allowing the loading of the file in the default language)
$lang->load('jhandlenet_cli', JPATH_SITE, null, false, false)
// Fallback to the finder_cli file in the default language
|| $lang->load('jhandlenet_cli', JPATH_SITE, null, true);

jimport('joomla.application.component.helper');
jimport('joomla.log.log');
 
/**
 * Simple command line interface application class.
 *
 * @package JHandleNet.CLI
 */
class JHandleNet extends JApplicationCli
{
	private $db;
	
	public function __construct($input = null, JRegistry $config = null, JEventDispatcher $dispatcher = null) 
	{
		parent::__construct($input, $config, $dispatcher);
		
		
		$params = JComponentHelper::getParams('com_jhandlenet');
		
		$option['driver']   = 'mysqli';
		$option['host']     = $params->get('host').':'.$params->get('port');
		$option['user']     = $params->get('username');
		$option['password'] = $params->get('password');
		$option['database'] = $params->get('database');
		$option['prefix']   = '';
		
		$db = JDatabaseDriver::getInstance($option);
		
		$this->setDbo($db);
	}
	
    public function doExecute()
    {
    	if ($this->input->get('h') || $this->input->get('help')) {    		
			echo 'help';
    		return;
    	}
    	
    	// fool the system into thinking we are running as JSite with JHandleNet as the active component
		JFactory::getApplication('site');
		$_SERVER['HTTP_HOST'] = 'domain.com';

		// Disable caching.
		$config = JFactory::getConfig();
		$config->set('caching', 0);
		$config->set('cache_handler', 'file');
		
		try {			
		    if ($this->input->get('home')) {
    			$this->home($this->input->get('home'), JArrayHelper::getValue($this->input->args, 0));
    			return;
	    	}
	    	
	    	if ($this->input->get('unhome')) {
	    		$this->unhome($this->input->get('unhome'));
	    		return;
	    	}
		} catch (Exception $e) {
			$this->out('ERROR: '.$e->getMessage());			
		}
    }
    
    public function out($text = '', $nl = true)
    {
    	if (!($this->input->get('q', false) || $this->input->get('quiet', false))) {
    		parent::out($text, $nl);
    	}
    	
    	return $this;
    }
    
    public function home($na, $url)
    {
    	if (!$na) {
    		$this->out('No naming authority specified.');
    		return;
    	}
    	 
    	if (!$url) {
    		$this->out('No url specified');
    		return;
    	}
    
    	$table = $this->getTable();
    
    	if ($table->load($na)) {
    		$this->out(JText::sprintf('Cannot home handle prefix %s. Already exists.', $na));
    	} else {
    		$table->na = $na;
    		$table->url = $url;
    
    		if ($table->store()) {
    			if ($this->input->get('v') || $this->input->get('verbose')) {
    				$this->out(JText::sprintf('Handle prefix %s homed.', $na));
    			}
    		}
    	}
    }
    
    public function unhome($na)
    {
    	if (!$na) {
    		$this->out('No naming authority specified.');
    		return;
    	}

		$table = $this->getTable();
    	
    	if ($table->load($na)) {    		
    		if ($table->delete()) {
    			if ($this->input->get('v') || $this->input->get('verbose')) {
    				$this->out(JText::sprintf('Handle prefix %s unhomed.', $na));
    			}
    		}
    	} else {
    		$this->out(JText::sprintf("Cannot unhome handle prefix %s. Prefix doesn't exists.", $na));
    	}
    }
    
    public function setDbo($db)
    {
    	$this->db = $db;
    }
    
    public function getDbo()
    {
    	return $this->db;
    }
    
    public function getTable()
    {
    	JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jhandlenet/tables');
    	return JTable::getInstance('NA', 'JHandleNetTable', array('dbo'=>$this->getDbo()));
    }
}
 
JApplicationCli::getInstance('JHandleNet')->execute();