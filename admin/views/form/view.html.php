<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');
jimport('joomla.language.helper');

class Form2ContentViewForm extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $fields;
	protected $fieldsNew;
	protected $fieldValues = array();
	protected $state;
	protected $jArticle;
	protected $jsScripts = array();
	protected $nullDate;
	protected $translatedFields;
	protected $contentType;
	protected $modalParams = array();
	
	function display($tpl = null)
	{
		$model 				= $this->getModel();
		$db					= $this->get('Dbo');		
		$this->form			= $this->get('Form');	
		$this->item			= $this->get('CachedItem');
		$this->state		= $this->get('State');		
		$this->canDo		= Form2ContentHelperAdmin::getActions($this->state->get('filter.category_id'));
		$this->jArticle 	= $model->getJarticle($this->item->id);
		$this->nullDate		= $db->getNullDate();		

		$this->modalParams['title'] = JText::_('COM_FORM2CONTENT_CROP_IMAGE');
		$this->modalParams['backdrop'] = "false";   
		$this->modalParams['url'] = '\'+modalUrl+\'';
		$this->modalParams['height'] = "640px";
		$this->modalParams['width'] = "900px";

		$data = JFactory::getApplication()->getUserState('com_form2content.edit.form.data', array());
		
		if(!empty($data))
		{
			$this->item->fields = unserialize($data['fieldData']);
			$contentTypeId = $data['projectid'];
		}
		else 
		{
			$contentTypeId = $this->item->projectid;
		}
	
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			throw new Exception(implode("\n", $errors));
			return false;
		}

		// load com_content language file
		$lang = JFactory::getLanguage();
		$lang->load('com_content', JPATH_ADMINISTRATOR);
		
		if(F2cFactory::getConfig()->get('custom_translations', false))
		{
			// load F2C custom translations
			$lang->load('com_form2content_custom', JPATH_SITE);
		}
		
		$this->translatedFields = $model->loadFieldTranslations($this->item->projectid, $lang->getTag());
		
		$contentType = F2cFactory::getContentType($contentTypeId);
		$this->contentType = $contentType;
		
		$this->checkOnlyJoomlaFields($contentType);
		
		$this->prepareForm($contentType);
		$this->addToolbar($contentType);
		
		// add various Javascript declarations to the script
		$jsDeclarations = array();
		$jsDeclarations[] = 'var rootUrl = \''.JURI::root(true).'/\';';
		$jsDeclarations[] = 'var messages;';
		$jsDeclarations[] = 'var modalCropUrl;';
		$jsDeclarations[] = 'var modalCropUrls = [];';
		$jsDeclarations[] = 'var jBusyUploading = \'<p class="blockUI"><img src="'.JURI::root(true).'/media/com_form2content/images/busy.gif" />'.JText::_('COM_FORM2CONTENT_BUSY_UPLOADING', true).'</p>\';';
		$jsDeclarations[] = 'jBusyDeleting = \'<p class="blockUI"><img src="'.JURI::root(true).'/media/com_form2content/images/busy.gif" />'.JText::_('COM_FORM2CONTENT_BUSY_DELETING', true).'\';';
		
		JFactory::getDocument()->addScriptDeclaration(implode('',$jsDeclarations));
		
		parent::display($tpl);		
	}	
	
	protected function addToolbar($contentType)
	{		
		JFactory::getApplication()->input->set('hidemainmenu', true);
		
		$canDo		= Form2ContentHelperAdmin::getActions($this->state->get('filter.category_id'), $this->item->id);		
		$isNew 		= ($this->item->id == 0);
		$formTitle 	= JText::_('COM_FORM2CONTENT_ARTICLE_MANAGER') . ' : ';
		$formTitle .= ($isNew ? JText::_('COM_FORM2CONTENT_NEW') : JText::_('COM_FORM2CONTENT_EDIT')) . ' ';		
		$formTitle .= $contentType->settings['article_caption'] ? $contentType->settings['article_caption'] : JText::_('COM_FORM2CONTENT_FORM');
		
		JToolBarHelper::title($formTitle);
		JToolBarHelper::apply('form.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('form.save', 'JTOOLBAR_SAVE');
		
		if ($canDo->get('core.create')) 
		{
			JToolBarHelper::save2new('form.save2new');
			JToolBarHelper::save2copy('form.save2copy');
		}
		
		if ($isNew)  
		{
			JToolBarHelper::cancel('form.cancel', 'JTOOLBAR_CANCEL');
		} 
		else 
		{
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel('form.cancel', 'JTOOLBAR_CLOSE');
		}
		
		JToolbarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER', false, F2C_DOCUMENTATION_URL);
	}
	
	private function prepareForm($contentType)
	{
		$dateFormat						= F2cFactory::getConfig()->get('date_format');
		$this->jsScripts['validation']	= 'var arrValidation=new Array;';
		$this->jsScripts['fieldInit']	= '';
		
		$translatedDateFormat	= F2cDateTimeHelper::getTranslatedDateFormat();
		
		$validationCounter = 0;

		if(count($this->item->fields))
		{
			foreach($this->item->fields as $field)
			{
				// no captcha in the back-end
				if($field->fieldtypename != 'Captcha')
				{
					$this->jsScripts['validation'] 	.= $field->getClientSideValidationScript($validationCounter, $this->form);
					$this->jsScripts['fieldInit']	.= $field->getClientSideInitializationScript();
				}
			}
		}
	}
	
	protected function renderCustomField($fieldName)
	{
		$html = '';
		
		if(array_key_exists($fieldName, $this->item->fields))
		{
			$field = $this->item->fields[$fieldName];
			$html .= '<div class="control-group">';
			$html .= '<div class="control-label">'.$field->renderLabel($this->translatedFields, $this->form).'</div>';
			$html .= '<div class="controls f2cfield '.$field->getCssClass().'">'.$field->render($this->translatedFields, $this->contentType->settings, array(), $this->form, $this->item->id).'</div>';
			$html .= '</div>';
		}
		
		return $html;
	}
	
    /**
     * Checks if the contentType only contains Joomla fields and no F2C custom fields yet.
     * Generates a warning when only Joomla fields are found
     *
     * @param    object        $contentType        The Content Type to be examined
     *
     * @return  void
     *
     * @since   6.17.0
     */
    private function checkOnlyJoomlaFields($contentType)
    {
        $onlyJoomlaFields = true;

        foreach($contentType->fields as $field)
        {
            if($field->classificationId == F2C_FIELD_F2C_NATIVE)
            {
                $onlyJoomlaFields = false;
                break;
            }
        }

        if($onlyJoomlaFields)
        {
 			JFactory::getApplication()->enqueueMessage(JText::_('COM_FORM2CONTENT_WARNING_ONLY_JOOMLA_FIELDS'),'warning');
        }
    }
}
?>