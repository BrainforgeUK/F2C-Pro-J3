<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use \Joomla\Registry\Registry;
use Joomla\String\StringHelper;

class F2cFieldHyperlink extends F2cFieldBase
{	
	function __construct($field)
	{
		$this->reset();
		parent::__construct($field);
	}
	
	public function getPrefix()
	{
		return 'lnk';
	}
	
	public function reset()
	{
		$this->values['URL'] 				= '';
		$this->values['DISPLAY_AS'] 		= '';
		$this->values['TITLE'] 				= '';
		$this->values['TARGET'] 			= '';
		$this->internal['fieldcontentid']	= null;
	}
	
	public function render($translatedFields, $contentTypeSettings, $parms = array(), $form, $formId)
	{
		$displayData	= array();
		$listTarget[] 	= JHTML::_('select.option', '_top', 'Parent window');
		$listTarget[] 	= JHTML::_('select.option', '_blank', 'New window');	
		
		$displayData['attribsUrl']			= $this->settings->get('lnk_attributes_url') ? $this->settings->get('lnk_attributes_url') : 'class="inputbox"';
		$displayData['attribsDisplayAs']	= $this->settings->get('lnk_attributes_display_as') ? $this->settings->get('lnk_attributes_display_as') : 'class="inputbox"';
		$displayData['attribsTitle']		= $this->settings->get('lnk_attributes_title') ? $this->settings->get('lnk_attributes_title') : 'class="inputbox"';
		$displayData['listTarget']			= $listTarget;
		
		return $this->renderLayout('hyperlink', $displayData, $translatedFields, $contentTypeSettings);				
	}
	
	public function prepareSubmittedData($formId)
	{
		$jinput = JFactory::getApplication()->input;
		
		$this->internal['fieldcontentid'] = $jinput->getInt('hid'.$this->elementId);
		
		$this->values['URL'] 		= $jinput->getString($this->elementId);
		$this->values['DISPLAY_AS'] = $jinput->getString($this->elementId . '_display');
		$this->values['TITLE'] 		= $jinput->getString($this->elementId . '_title');
		$this->values['TARGET'] 	= $jinput->getString($this->elementId . '_target');

		return $this;
	}
	
	public function store($formid)
	{
		$content 		= array();					
		$link 			= new Registry();
		$fieldId 		= $this->internal['fieldcontentid'];
				
		$link->set('url', $this->values['URL']);
		$link->set('display', $this->values['DISPLAY_AS']);
		$link->set('title', $this->values['TITLE']);
		$link->set('target', $this->values['TARGET']);
		
		$value 			= $link->toString();
		$action 		= ($value) ? (($fieldId) ? 'UPDATE' : 'INSERT') : (($fieldId) ? 'DELETE' : '');
		$content[] 		= new F2cFieldHelperContent($fieldId, 'VALUE', $value, $action);
		
		return $content;		
	}
	
	public function validate(&$data, $item)
	{
		$value = trim($this->values['URL']);
		
		if($this->settings->get('requiredfield') && empty($value))
		{
			throw new Exception($this->getRequiredFieldErrorMessage());
		}
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentHyperlink');
      	$xmlFieldContent->url = $this->values['URL'];
      	$xmlFieldContent->display_as = $this->values['DISPLAY_AS'];
      	$xmlFieldContent->title = $this->values['TITLE'];
      	$xmlFieldContent->target = $this->values['TARGET'];
    }
    
	public function import($xmlField, $existingInternalData, &$data)
	{
		$this->values['URL'] = (string)$xmlField->contentHyperlink->url;
		$this->values['DISPLAY_AS'] = (string)$xmlField->contentHyperlink->display_as;
		$this->values['TITLE'] = (string)$xmlField->contentHyperlink->title;
		$this->values['TARGET'] = (string)$xmlField->contentHyperlink->target;
		$this->internal['fieldcontentid'] = $data['id'] ? $existingInternalData['fieldcontentid'] : 0;
	}
	
	public function addTemplateVar($templateEngine, $form)
	{
		$linkTitle = '';
		$linkTarget = '';
		$linkDisplay = '';
		$linkUrl = '';
		
		if($this->values['URL'])
		{
			$display 		= $this->values['DISPLAY_AS'] ? $this->values['DISPLAY_AS'] : $this->values['URL'];
			$linkTitle 		= $this->values['TITLE'];
			$linkTarget 	= $this->values['TARGET'];
			$linkDisplay 	= $this->values['DISPLAY_AS'];
			$linkUrl 		= $this->values['URL'];
			
			if($this->settings->get('lnk_add_http_prefix', 0))
			{
				if(!strstr($linkUrl, '://'))
				{
					$linkUrl = 'http://' . $linkUrl;
				}
			}
			
			if($this->settings->get('lnk_output_mode') == 0)
			{
				$linkTag = $linkUrl;
			}
			else
			{
				$linkTag = '<a href="' . $linkUrl . '" target="' . $this->values['TARGET'] . '" title="' . $this->values['TITLE'] . '">' . $display . '</a>';					
			}
			
			$templateEngine->addVar($this->fieldname, $linkTag);
		}
		else
		{
			$templateEngine->addVar($this->fieldname, '');
		}
		
		$templateEngine->addVar($this->fieldname.'_URL', $linkUrl);		
		$templateEngine->addVar($this->fieldname.'_TITLE', $linkTitle);		
		$templateEngine->addVar($this->fieldname.'_TARGET', $linkTarget);		
		$templateEngine->addVar($this->fieldname.'_DISPLAY', $linkDisplay);					
	}
	
	public function getTemplateParameterNames()
	{
		$names = array(	StringHelper::strtoupper($this->fieldname).'_URL',
						StringHelper::strtoupper($this->fieldname).'_TITLE', 
						StringHelper::strtoupper($this->fieldname).'_DISPLAY',
						StringHelper::strtoupper($this->fieldname).'_TARGET');
		
		return array_merge($names, parent::getTemplateParameterNames());
	}
	
	public function setData($data)
	{
		$this->internal['fieldcontentid']	= $data->fieldcontentid;
		$values 							= new Registry($data->content);
		$this->values['URL'] 				= $values->get('url');
		$this->values['DISPLAY_AS'] 		= $values->get('display');
		$this->values['TITLE'] 				= $values->get('title');
		$this->values['TARGET'] 			= $values->get('target');
	}
}
?>