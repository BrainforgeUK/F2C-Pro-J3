<?php
// No direct access
defined('JPATH_PLATFORM') or die;

require_once(JPATH_SITE.'/components/com_form2content/utils.form2content.php');

jimport('joomla.application.component.controller');

class Form2ContentController extends JControllerLegacy
{
	protected $default_view = 'forms';

	public function ArticleImportCron()
	{
		$logFile	= Path::Combine(JFactory::getConfig()->get('log_path'), JFactory::getDate()->format('Ymd') . '_f2c_import.log');
		$log		= '';

		$db			= JFactory::getDbo();
		$f2cConfig	= F2cFactory::getConfig();
		$model 		= $this->getModel('Form');
		$import 	= new F2cIoImportarticle($db, $f2cConfig, $model);
		
		$import->processFiles();
		
		// Append the messages to the log file
		if(count($import->logMessages))
		{
			if(JFile::exists($logFile))
			{
				$log = file_get_contents($logFile);
			}
			
			foreach($import->logMessages as $logMessage) 
			{
				$log .= $logMessage. PHP_EOL;
			}
			
			JFile::write($logFile, $log);
		}
	
		JFactory::getApplication()->close();
	}
}
