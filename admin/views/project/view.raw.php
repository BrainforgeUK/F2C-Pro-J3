<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentViewProject extends JViewLegacy
{
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$document->setMimeEncoding('text/xml');
		
		$version 			= new JVersion;
		$componentInfo 		= JInstaller::parseXMLInstallFile(JPATH_COMPONENT_ADMINISTRATOR.'/manifest.xml');
		$componentVersion 	= $componentInfo['version'];
		
		$contentType = F2cFactory::getContentType(JFactory::getApplication()->input->getInt('id'));
		$exporter	= new F2cIoExportcontenttype($contentType, $componentInfo, F2cFactory::getConfig());
		
		$xml		= $exporter->getXml();
		$filename 	= $xml->title.'_f2c_pro_'.$componentInfo['version'].'_'.$version->getShortVersion().'.xml';
		
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		
		echo $xml->asXML();
	}
}