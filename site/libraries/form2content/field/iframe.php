<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use \Joomla\Registry\Registry;

class F2cFieldIFrame extends F2cFieldBase
{
	function __construct($field)
	{
		$this->reset();
		parent::__construct($field);
	}
	
	public function getPrefix()
	{
		return 'ifr';
	}
	
	public function reset()
	{
		$this->values['URL'] 				= '';
		$this->values['WIDTH'] 				= null;
		$this->values['HEIGHT'] 			= null;
		$this->internal['fieldcontentid']	= null;
	}
	
	public function render($translatedFields, $contentTypeSettings, $parms = array(), $form, $formId)
	{
		$displayData = array();
		
		if($this->values['WIDTH'] == 0)
		{
			$this->values['WIDTH'] = '';
		}
		
		if($this->values['HEIGHT'] == 0)
		{
			$this->values['HEIGHT'] = '';
		}
				
		$displayData['iFrameAttributes']	= $this->settings->get('ifr_attributes_iframe');
		$displayData['iFrameClass']			= $this->settings->get('ifr_attributes_iframe') ? '' : 'class="inputbox"';
		$displayData['widthAttributes']		= $this->settings->get('ifr_attributes_width');
		$displayData['widthClass']			= $this->settings->get('ifr_attributes_width') ? '' : 'class="inputbox"';
		$displayData['heightAttributes']	= $this->settings->get('ifr_attributes_height');
		$displayData['heightClass']			= $this->settings->get('ifr_attributes_height') ? '' : 'class="inputbox"';
		
		return $this->renderLayout('iframe', $displayData, $translatedFields, $contentTypeSettings);		
	}
	
	public function prepareSubmittedData($formId)
	{
		$jinput = JFactory::getApplication()->input;
		
		$this->internal['fieldcontentid'] = $jinput->getInt('hid'.$this->elementId);
		
		$this->values['URL'] = $jinput->getString($this->elementId);
		$this->values['WIDTH'] = $jinput->getInt($this->elementId . '_width');
		$this->values['HEIGHT'] = $jinput->getInt($this->elementId . '_height');
		
		return $this;
	}
	
	public function store($formid)
	{
		$content 		= array();
		$iframe			= new Registry();
		$fieldId 		= $this->internal['fieldcontentid'];
		
		$iframe->set('url', $this->values['URL']);
		$iframe->set('width', $this->values['WIDTH']);
		$iframe->set('height', $this->values['HEIGHT']);
		
		$value 			= $iframe->toString();
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
      	$xmlFieldContent = $xmlField->addChild('contentIframe');
      	$xmlFieldContent->url = $this->values['URL'];
      	$xmlFieldContent->width = $this->values['WIDTH'];
      	$xmlFieldContent->height = $this->values['HEIGHT'];
    }
    
	public function import($xmlField, $existingInternalData, &$data)
	{
		$this->values['URL'] = (string)$xmlField->contentIframe->url;
		$this->values['WIDTH'] = (string)$xmlField->contentIframe->width;
		$this->values['HEIGHT'] = (string)$xmlField->contentIframe->height;
		$this->internal['fieldcontentid'] = $data['id'] ? $existingInternalData['fieldcontentid'] : 0;
	}

	public function addTemplateVar($templateEngine, $form)
	{
		$iframeTag = '';
		
		if($this->values['URL'])
		{
			$iframeTag = '<iframe src="' . $this->values['URL'] . '" height="' . $this->values['HEIGHT'] . '" width="' . $this->values['WIDTH'] . '"></iframe>';
		}

		$templateEngine->addVar($this->fieldname, $iframeTag);
	}
	
	public function setData($data)
	{
		$this->internal['fieldcontentid']	= $data->fieldcontentid;
		$values 							= new Registry($data->content);
		$this->values['URL'] 				= $values->get('url');
		$this->values['WIDTH'] 				= $values->get('width');
		$this->values['HEIGHT'] 			= $values->get('height');
	}
}
?>