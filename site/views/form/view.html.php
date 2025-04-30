<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use \Joomla\Registry\Registry;

require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'form2content.php');

jimport('joomla.application.component.view');
jimport('joomla.language.helper');

JLoader::register('F2cViewHelper', JPATH_COMPONENT_SITE.'/helpers/view.php');

class Form2ContentViewForm extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $fields;
	protected $state;
	protected $jArticle;
	protected $jsScripts = array();
	protected $nullDate;
	protected $pageTitle;
	protected $contentTypeSettings;
	protected $submitForm = '';
	protected $itemId;
	protected $dateFormat = '';
	protected $params;
	protected $settings;
	protected $return_page;
	private $f2cConfig;
	protected $translatedFields;
	protected $contentType;
	protected $modalParams = array();
	
	function display($tpl = null)
	{
		$app				= JFactory::getApplication();
		$model 				= $this->getModel();		
		$db					= $this->get('Dbo');
		$this->f2cConfig	= F2cFactory::getConfig();
		$this->state		= $this->get('State');
		$this->params		= $app->getParams();
		$this->nullDate		= $db->getNullDate();		
		$this->dateFormat	= $this->f2cConfig->get('date_format');
		$this->itemId		= $app->input->getInt('Itemid');	
		
		$this->modalParams['title'] = JText::_('COM_FORM2CONTENT_CROP_IMAGE');
		$this->modalParams['backdrop'] = "false";
		$this->modalParams['url'] = '\'+modalUrl+\'';
		$this->modalParams['height'] = "640px";
		$this->modalParams['width'] = "900px";
		
		$this->PrepareSettings($model);
		
		if((int)$this->settings->get('editmode', -1) == 0 || (int)$this->settings->get('editmode', -1) == 1)
		{
			if((int)$this->settings->get('editmode') == 1)
			{
				// edit existing form or create a new one
				$formId = $model->getDefaultArticleId((int)$this->settings->get('contenttypeid'));
			}
			else
			{
				$formId = 0;
			}
			
			// Initialize the state -> For the first getState call,
			// the internal data will be overwritten
			$dummy = $model->getState($this->getName().'.id');
			$model->setState($this->getName().'.id', $formId);
			
			$ids[]	= $formId;
			$app->setUserState('com_form2content.edit.form.id', $ids);			
		}		
		
		if ((int)$this->settings->get('classic_layout', 0))
		{
			$this->setLayout('classic');
			$model->classicLayout = true;
		}

		$this->item			= $this->get('Item');		
		$this->form			= $this->get('Form');
		$this->return_page	= $this->get('ReturnPage');		
		
		$data = $app->getUserState('com_form2content.edit.form.data', array());
		
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
		}

		$lang = JFactory::getLanguage();
		// load com_content language file
		$lang->load('com_content', JPATH_ADMINISTRATOR);
				
		if($this->f2cConfig->get('custom_translations', false))
		{
			// load F2C custom translations
			$lang->load('com_form2content_custom', JPATH_SITE);
		}
		
		$this->translatedFields = $model->loadFieldTranslations($contentTypeId, $lang->getTag());	  	
		
		// set the state to indicate this is a new form or an existing one
		$app->setUserState('com_form2content.edit.form.new', $this->item->id ? false : true);
		
		$this->contentType = F2cFactory::getContentType($contentTypeId);
		
		$this->contentTypeSettings = new Registry();
		$this->contentTypeSettings->loadArray($this->contentType->settings);
		
		$this->prepareForm($this->contentType);
		$this->addToolbar($this->contentType);

		// Set the page title
		JFactory::getDocument()->setTitle(F2cViewHelper::getPageTitle($this->params->get('page_title', '')));
		
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
		if($this->settings->get('editmode', -1) == -1)
		{
			// coming from Article Manager menu entry
			$this->pageTitle = $this->contentTypeSettings->get('article_caption');
		}
		else 
		{
			// coming from single Article menu entry
			$this->pageTitle = $this->params->get('show_page_heading', 1) ? $this->escape($this->params->get('page_heading')) : $this->contentTypeSettings->get('article_caption');
		}
	}
	
	private function prepareForm($contentType)
	{
		$this->jsScripts['validation']	= 'var arrValidation=new Array;';
		$this->jsScripts['fieldInit']	= '';
				
		// check if changing the state is allowed for all options
		
		$app 			= JFactory::getApplication();
		$menu			= $app->getMenu();
		$activeMenu		= $menu->getActive();
		$canDo 			= Form2ContentHelper::getActions($contentType->id, $this->item->id);
		$canEditState	= $canDo->get('core.edit.state') || $canDo->get('form2content.edit.state.own');
		$canTrash		= $canDo->get('core.delete') || $canDo->get('form2content.delete.own');

		// Modified Brainforge.uk 2025/04/30
		if(!$activeMenu)
		{
			// no active menu => create an empty object to prevent errors
			$params = new JRegistry();
		}
		else
		{
			$params = $activeMenu->getParams();
		}
		
		if(!($canEditState && $params->get('show_archive_button', 0)))
		{
			// disable archive option
			$this->jsScripts['fieldInit'] .= 'jQuery(document).ready(function(){jQuery("#jform_state option[value='.F2C_STATE_ARCHIVED.']").attr("disabled","disabled");});';
		}		
		
		if(!($canTrash && $params->get('show_delete_button', 0)))
		{
			// disable archive option
			$this->jsScripts['fieldInit'] .= 'jQuery(document).ready(function(){jQuery("#jform_state option[value='.F2C_STATE_TRASH.']").attr("disabled","disabled");});';
		}		
		
		$this->form->setFieldAttribute('id', 'label', Jtext::_('COM_FORM2CONTENT_ID'));
		$this->form->setFieldAttribute('id', 'description', '');
		
	  	$translatedDateFormat 	= F2cDateTimeHelper::getTranslatedDateFormat();
		
		$validationCounter = 0;
		
		if(count($this->item->fields))
		{
			foreach($this->item->fields as $field)
			{
				if($field->frontvisible)
				{
					$this->jsScripts['validation'] 	.= $field->getClientSideValidationScript($validationCounter, $this->form);
					$this->jsScripts['fieldInit']	.= $field->getClientSideInitializationScript();
				}
			}
		}
						
		$this->submitForm = 'Joomla.submitform(task, document.getElementById(\'adminForm\'));';
	}
	
	protected function renderFormTemplate()
	{
		$parser = new F2cParser();
		$varsInTemplate = array();
		$formVars = array();

		if(count($this->item->fields))
		{
			foreach($this->item->fields as $field)
			{
				$formVars[strtoupper($field->fieldname)] = strtoupper($field->fieldname);
				
				if($field->classificationId != F2C_FIELD_F2C_NATIVE)
				{
					// Legacy prefixed
					$formVars['F2C_'.strtoupper($field->fieldname)] = strtoupper($field->fieldname);
					$formVars['JOOMLA_'.strtoupper($field->fieldname)] = strtoupper($field->fieldname);

					if(get_class($field) == 'F2cFieldJoomlaCategory')
					{
						$formVars['F2C_CATID'] = strtoupper($field->fieldname);
						$formVars['JOOMLA_CATID'] = strtoupper($field->fieldname);
					}
					
					if(get_class($field) == 'F2cFieldJoomlaPublishUp')
					{
						$formVars['F2C_PUBLISH_UP'] = strtoupper($field->fieldname);
						$formVars['JOOMLA_PUBLISH_UP'] = strtoupper($field->fieldname);
					}
					
					if(get_class($field) == 'F2cFieldJoomlaPublishDown')
					{
						$formVars['F2C_PUBLISH_DOWN'] = strtoupper($field->fieldname);
						$formVars['JOOMLA_PUBLISH_DOWN'] = strtoupper($field->fieldname);
					}		
			
					if(get_class($field) == 'F2cFieldJoomlaPublished')
					{
						$formVars['F2C_STATE'] = strtoupper($field->fieldname);
						$formVars['JOOMLA_STATE'] = strtoupper($field->fieldname);
					}										
				}
			}
		}

		if(!$parser->addTemplate($this->contentTypeSettings->get('form_template'), F2C_TEMPLATE_INTRO))
		{
			$this->setError($parser->getError());
			return false;				
		}

		$parser->getTemplateVars($formVars, $varsInTemplate);

		// add the buttons
		if($this->item->id == 0)
		{
			$parser->addVar('F2C_BUTTON_CANCEL', '<button type="button" class="f2c_button f2c_cancel" onclick="javascript:Joomla.submitbutton(\'form.cancel\')">'.JText::_('COM_FORM2CONTENT_TOOLBAR_CANCEL').'</button>');
		}
		else
		{
			$parser->addVar('F2C_BUTTON_CANCEL', '<button type="button" class="f2c_button f2c_cancel" onclick="javascript:Joomla.submitbutton(\'form.cancel\')">'.JText::_('COM_FORM2CONTENT_TOOLBAR_CLOSE').'</button>');
		}
		
		$parser->addVar('F2C_BUTTON_SAVE', '<button type="button" class="f2c_button f2c_save" onclick="javascript:Joomla.submitbutton(\'form.save\')">'.JText::_('COM_FORM2CONTENT_TOOLBAR_SAVE').'</button>');
		$parser->addVar('F2C_BUTTON_APPLY', '<button type="button" class="f2c_button f2c_apply" onclick="javascript:Joomla.submitbutton(\'form.apply\')">'.JText::_('COM_FORM2CONTENT_TOOLBAR_APPLY').'</button>');

		if($this->settings->get('show_save_and_new_button'))
		{
			$parser->addVar('F2C_BUTTON_SAVE_AND_NEW', '<button type="button" class="f2c_button f2c_saveandnew" onclick="javascript:Joomla.submitbutton(\'form.save2new\')">'.JText::_('COM_FORM2CONTENT_TOOLBAR_SAVE_AND_NEW').'</button>');
		}
		else 
		{
			$parser->addVar('F2C_BUTTON_SAVE_AND_NEW', '');
		}
		
		if($this->settings->get('show_save_as_copy_button'))
		{
			$parser->addVar('F2C_BUTTON_SAVE_AS_COPY', '<button type="button" class="f2c_button f2c_saveascopy" onclick="javascript:Joomla.submitbutton(\'form.save2copy\')">'.JText::_('COM_FORM2CONTENT_TOOLBAR_SAVE_AS_COPY').'</button>');
		}
		else 
		{
			$parser->addVar('F2C_BUTTON_SAVE_AS_COPY', '');
		}
		
		// User defined fields
		if(count($this->item->fields))
		{
			foreach ($this->item->fields as $field) 
			{
				// skip processing of hidden fields
				$parms 		= array();																		
				$fieldname 	= strtoupper($field->fieldname);

				if($field->frontvisible)
				{
					if(array_key_exists($fieldname, $varsInTemplate))
					{
						if($field->classificationId != F2C_FIELD_F2C_NATIVE)
						{
							// Legacy prefixed
							$parser->addVar('JOOMLA_'.$fieldname.'_CAPTION', $field->renderLabel($this->translatedFields, $this->form));
							$parser->addVar('JOOMLA_'.$fieldname, '<div class="f2c_field">'.$field->render($this->translatedFields, $this->contentType->settings, $parms, $this->form, $this->item->id).'</div>');
							$parser->addVar('F2C_'.$fieldname.'_CAPTION', $field->renderLabel($this->translatedFields, $this->form));
							$parser->addVar('F2C_'.$fieldname, '<div class="f2c_field">'.$field->render($this->translatedFields, $this->contentType->settings, $parms, $this->form, $this->item->id).'</div>');	

							if(get_class($field) == 'F2cFieldJoomlaCategory')
							{
								$parser->addVar('F2C_CATID_CAPTION', $field->renderLabel($this->translatedFields, $this->form));
								$parser->addVar('F2C_CATID', '<div class="f2c_field">'.$field->render($this->translatedFields, $this->contentType->settings, $parms, $this->form, $this->item->id).'</div>');								
							}
							
							if(get_class($field) == 'F2cFieldJoomlaPublishUp')
							{
								$parser->addVar('F2C_PUBLISH_UP_CAPTION', $field->renderLabel($this->translatedFields, $this->form));
								$parser->addVar('F2C_PUBLISH_UP', '<div class="f2c_field">'.$field->render($this->translatedFields, $this->contentType->settings, $parms, $this->form, $this->item->id).'</div>');
							}
							
							if(get_class($field) == 'F2cFieldJoomlaPublishDown')
							{
								$parser->addVar('F2C_PUBLISH_DOWN_CAPTION', $field->renderLabel($this->translatedFields, $this->form));
								$parser->addVar('F2C_PUBLISH_DOWN', '<div class="f2c_field">'.$field->render($this->translatedFields, $this->contentType->settings, $parms, $this->form, $this->item->id).'</div>');
							}		
					
							if(get_class($field) == 'F2cFieldJoomlaPublished')
							{
								$parser->addVar('F2C_STATE_CAPTION', $field->renderLabel($this->translatedFields, $this->form));
								$parser->addVar('F2C_STATE', '<div class="f2c_field">'.$field->render($this->translatedFields, $this->contentType->settings, $parms, $this->form, $this->item->id).'</div>');
							}																			
						}
					
						$parser->addVar($fieldname.'_CAPTION', $field->renderLabel($this->translatedFields, $this->form));
						$parser->addVar($fieldname, '<div class="f2c_field">'.$field->render($this->translatedFields, $this->contentType->settings, $parms, $this->form, $this->item->id).'</div>');
					}
					else 
					{
						throw new Exception(Jtext::_('COM_FORM2CONTENT_ERROR_F2C').' : '.sprintf(JText::_(COM_FORM2CONTENT_ERROR_TEMPLATE_FIELD_NOT_PRESENT), $fieldname));
					}
				}
				else 
				{
					if(array_key_exists($fieldname, $varsInTemplate))
					{
						throw new Exception(Jtext::_('COM_FORM2CONTENT_ERROR_F2C').' : '.sprintf(JText::_(COM_FORM2CONTENT_ERROR_TEMPLATE_FIELD_PRESENT), $fieldname));
					}
				}
			}
		}
		
		echo $parser->parseIntro();
	}
	
	private function addF2cJoomlaVar($parser, $varsInTemplate, $condition, $title, $field)
	{
		if($condition)
		{
			if(array_key_exists($title, $varsInTemplate))
			{
				$parser->addVar($title.'_CAPTION', $this->form->getLabel($field));
				$parser->addVar($title, $this->form->getInput($field));
			}
			else
			{
				throw new Exception(Jtext::_('COM_FORM2CONTENT_ERROR_F2C').' : '.sprintf(JText::_(COM_FORM2CONTENT_ERROR_TEMPLATE_FIELD_NOT_PRESENT), $title));
			}
		}
		else 
		{			
			// no display in front-end
			if(array_key_exists($title, $varsInTemplate))
			{
				throw new Exception(Jtext::_('COM_FORM2CONTENT_ERROR_F2C').' : '.sprintf(JText::_(COM_FORM2CONTENT_ERROR_TEMPLATE_FIELD_PRESENT), $title));
			}
		}
	}
	
	private function PrepareSettings($model)
	{		
		$this->settings	= null;
		$app 			= JFactory::getApplication();
		$menu 			= $app->getMenu();
		
		if(is_object($menu))
		{
			if ($item = $menu->getActive())
			{
				$this->settings = $menu->getParams($item->id);
			}
		}
		
		if(is_null($this->settings))
		{
			$this->settings = new Registry();
			$this->settings->set('editmode', 2); // direct edit
			
		}
		
		// Hide the Save (Apply) button when we are always creating new articles
		$this->settings->set('show_save_button', $this->settings->get('editmode', -1) != F2C_EDITMODE_ALWAYS_CREATE_NEW);
		
		if($this->settings->get('contenttypeid', 0) == 0)
		{
			// Retrieve the item so we can set the Content Type Id for the model
			$item = $model->getItem();
			$this->settings->set('contenttypeid', (int)$item->projectid);
		}
		
		$model->contentTypeId = (int)$this->settings->get('contenttypeid');
		
		$canDo = Form2ContentHelper::getActions($this->state->get('filter.category_id'));
		
		if($this->settings->get('editmode') != '' || !$canDo->get('core.create'))
		{
			// There's no menu-item (no parameters) or we are in Single Form mode or the user is not allowed to create new articles
			$this->settings->set('show_save_and_new_button', false);
			$this->settings->set('show_save_as_copy_button', false);
		}
	}
}
?>