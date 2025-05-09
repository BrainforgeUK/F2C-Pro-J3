<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldCaptcha extends F2cFieldBase
{	
	public function getPrefix()
	{
		return '';
	}
	
	public function reset()
	{
	}
	
	// Modified Brainforge.uk 20250509
	public function render($translatedFields, $contentTypeSettings, $parms, $form, $formId)
	{
		return '<div class="g-recaptcha" data-sitekey="'.$this->settings->get('public_key').'"></div>';
	}
	
	public function prepareSubmittedData($formId)
	{
		return $this;
	}
	
	public function store($formid)
	{
		return array();		
	}

	public function validate(&$data, $item)
	{
		// skip checks for cron import
		if(!isset($data['isCron']))
		{		
			$app 		= JFactory::getApplication();
			$response 	= $app->input->getString('g-recaptcha-response');
			
			if($this->isFieldVisible() && empty($response))
			{
				throw new Exception($this->getRequiredFieldErrorMessage());
			}
		}		
	}
	
	public function export($xmlFields, $form)
	{
	}
	
	public function import($xmlField, $existingInternalData, &$data)
	{
	}	
	
	public function addTemplateVar($templateEngine, $form)
	{
	}
	
	public function getTemplateParameterNames()
	{
		return array();
	}

	public function setData($data)
	{
	}	
	
	public function getCssClass()
	{
		return 'f2c_captcha';
	}
	
	public function getClientSideInitializationScript()
	{
		if($this->isFieldVisible())
		{
			$doc = JFactory::getDocument();
			$doc->addScript('https://www.google.com/recaptcha/api.js', 'text/javascript', true, true);
	
			return parent::getClientSideInitializationScript();
		}
		
		return '';
	}	
	
	/**
	 * Indicates whether the captcha field is visible on the screen
	 * 
	 * @return  bool	flag to indicate the visibility	 
	 * 
	 * @since   6.16.0
	 */
	protected function isFieldVisible()
	{
		$app = JFactory::getApplication();
		
		return ($app->isClient('site') && $this->frontvisible);
	}
	
	public function canBeHiddenInFrontEnd()
	{
		// can't be hidden in front-end
		return false;
	}
}
?>