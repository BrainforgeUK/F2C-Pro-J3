<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use \Joomla\Registry\Registry;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;


/**
 * Script file of Form2Content component
 */
class com_Form2ContentInstallerScript
{
        /**
         * method to run before an install/update/uninstall method
         *
         * @return void
         */
        function preflight($type, $parent) 
        {
        	$joomlaVersionRequired = '3.1.5';
        	
        	if(!$this->checkJoomlaVersion($joomlaVersionRequired))
        	{
        		JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_FORM2CONTENT_JOOMLA_VERSION_TOO_LOW'), $joomlaVersionRequired), 'error');
        		return false;
        	}

		 	if(!(extension_loaded('gd') && function_exists('gd_info')))
		 	{
		 		JFactory::getApplication()->enqueueMessage(JText::_('COM_FORM2CONTENT_GDI_NOT_INSTALLED'), 'warning');
		 	}
		 			 	
        	return true;
        }
	
    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent) 
    {
    	$this->__createPath(JPATH_SITE . '/images/stories/com_form2content');
    	$this->__createPath(JPATH_SITE . '/media/com_form2content/templates');
    	$this->__createPath(JPATH_SITE . '/media/com_form2content/documents');
    	$this->__createPath(JPATH_SITE . '/media/com_form2content/import/archive');
    	$this->__createPath(JPATH_SITE . '/media/com_form2content/import/error');
    	$this->__createPath(JPATH_SITE . '/media/com_form2content/export');
		?>	
		<div align="left">
		<img src="../media/com_form2content/images/OSD_logo.png" width="350" height="180" border="0">
		<h2><?php JText::_('COM_FORM2CONTENT_WELCOME_TO_F2C'); ?></h2>
		<p>&nbsp;</p>	
		<p><strong><?php echo JText::_('COM_FORM2CONTENT_INSTALL_SAMPLE_DATA_QUESTION'); ?></strong></p>
		<p><?php echo JText::_('COM_FORM2CONTENT_INSTALL_SAMPLE_DATA_RECOMMEND'); ?></p>
		<p>
			<button class="btn btn-large btn-success" onclick="location.href='index.php?option=com_form2content&task=projects.installsamples';return false;" href="#">
				<i class="icon-apply icon-white"></i>
				<?php echo JText::_('COM_FORM2CONTENT_YES_INSTALL_SAMPLE_DATA'); ?>
			</button>
			<button class="btn btn-large btn-danger" onclick="location.href='index.php?option=com_form2content';return false;" href="#">
				<i class="icon-apply icon-white"></i>
				<?php echo JText::_('COM_FORM2CONTENT_NO_DO_NOT_INSTALL_SAMPLE_DATA'); ?>
			</button>
		</p>
		</div>
		<?php        	
        }
 
        /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent) 
    {
    }
 
        /**
     * method to update the component
     *
     * @return void
     */
        function update($parent) 
        {
        	// Update F2C Lite to F2C Pro
	    	$this->__createPath(JPATH_SITE . '/media/com_form2content/documents');
        	$this->__createPath(JPATH_SITE . '/media/com_form2content/import/archive');
	    	$this->__createPath(JPATH_SITE . '/media/com_form2content/import/error');
	    	$this->__createPath(JPATH_SITE . '/media/com_form2content/export');
        				
			$db = JFactory::getDBO();
						
			// Remove the sectionid column
			$db->setQuery('SHOW COLUMNS FROM #__f2c_form LIKE \'sectionid\'');
			
			if($db->loadResult())
			{
				$db->setQuery('ALTER TABLE #__f2c_form DROP COLUMN `sectionid`');
				$db->execute();
			}
			
			// add extended column (release 6.3.0)
			$db->setQuery('SHOW COLUMNS FROM #__f2c_form LIKE \'extended\'');
			
			if(!$db->loadResult())
			{
				$db->setQuery('ALTER TABLE #__f2c_form ADD COLUMN `extended` TEXT NOT NULL  AFTER `language`');
				$db->execute();
			}	

			// add name column to fieldtype table (release 6.8.0)
			$db->setQuery('SHOW COLUMNS FROM #__f2c_fieldtype LIKE \'name\'');
			
			if(!$db->loadResult())
			{
				$db->setQuery('ALTER TABLE #__f2c_fieldtype ADD COLUMN `name` VARCHAR(45) NOT NULL  AFTER `description`');
				$db->execute();

				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Singlelinetext' WHERE description = 'Single line text (textbox)'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Multilinetext' WHERE description = 'Multi-line text (text area)'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Editor' WHERE description = 'Multi-line text (editor)'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Checkbox' WHERE description = 'Check box'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Singleselectlist' WHERE description = 'Single select list'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Image' WHERE description = 'Image'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Iframe' WHERE description = 'IFrame'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Email' WHERE description = 'E-mail'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Hyperlink' WHERE description = 'Hyperlink'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Multiselectlist' WHERE description = 'Multi select list (checkboxes)'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Infotext' WHERE description = 'Info Text'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Datepicker' WHERE description = 'Date Picker'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Displaylist' WHERE description = 'Display List'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Fileupload' WHERE description = 'File Upload'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Databaselookup' WHERE description = 'Database Lookup'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Geocoder' WHERE description = 'Geo Coder'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Databaselookupmulti' WHERE description = 'Database Lookup (Multi select)'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Imagegallery' WHERE description = 'Image Gallery'");
				$db->execute();
				$db->setQuery("UPDATE #__f2c_fieldtype set `name` = 'Colorpicker' WHERE description = 'Color Picker'");
				$db->execute();				
			}
			
			// Change the id column of FieldType to auto increment
			$db->setQuery('ALTER TABLE #__f2c_fieldtype MODIFY COLUMN id int(10) unsigned NOT NULL auto_increment');
			$db->execute();
			
			// Check if we need to upgrade to the new field structure where Joomla fields are modeled as F2C Fields
			$db->setQuery('SHOW COLUMNS FROM #__f2c_fieldtype LIKE \'classification_id\'');
			
			if(!$db->loadResult())
			{
				$db->setQuery('ALTER TABLE #__f2c_fieldtype ADD COLUMN `classification_id` smallint(6) NOT NULL DEFAULT \'1\' AFTER `name`');
				$db->execute();
				
				// All existing fields are custom fields
				$db->setQuery('UPDATE #__f2c_fieldtype SET `classification_id` = 1');
				$db->execute();
				
				// Add the new Joomla fields
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Id', 'Joomlaid', 0 FROM #__f2c_fieldtype WHERE name='Joomlaid' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Title', 'Joomlatitle', 0 FROM #__f2c_fieldtype WHERE name='Joomlatitle' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Alias', 'Joomlaalias', 0 FROM #__f2c_fieldtype WHERE name='Joomlaalias' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Meta Description', 'Joomlametadescription', 0 FROM #__f2c_fieldtype WHERE name='Joomlametadescription' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Meta Keywords', 'Joomlametakeywords', 0 FROM #__f2c_fieldtype WHERE name='Joomlametakeywords' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Category', 'Joomlacategory', 0 FROM #__f2c_fieldtype WHERE name='Joomlacategory' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Author', 'Joomlacreatedby', 0 FROM #__f2c_fieldtype WHERE name='Joomlacreatedby' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Author Alias', 'Joomlacreatedbyalias', 0 FROM #__f2c_fieldtype WHERE name='Joomlacreatedbyalias' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Access', 'Joomlaaccess', 0 FROM #__f2c_fieldtype WHERE name='Joomlaaccess' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Created Date', 'Joomlacreated', 0 FROM #__f2c_fieldtype WHERE name='Joomlacreated' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Start Publishing Date', 'Joomlapublishup', 0 FROM #__f2c_fieldtype WHERE name='Joomlapublishup' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'End Publishing Date', 'Joomlapublishdown', 0 FROM #__f2c_fieldtype WHERE name='Joomlapublishdown' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Featured', 'Joomlafeatured', 0 FROM #__f2c_fieldtype WHERE name='Joomlafeatured' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Language', 'Joomlalanguage', 0 FROM #__f2c_fieldtype WHERE name='Joomlalanguage' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'State', 'Joomlapublished', 0 FROM #__f2c_fieldtype WHERE name='Joomlapublished' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Tags', 'Joomlatags', 0 FROM #__f2c_fieldtype WHERE name='Joomlatags' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Associatons', 'Joomlaassociations', 0 FROM #__f2c_fieldtype WHERE name='Joomlaassociations' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
								
				$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Template Selection', 'Joomlatemplate', 0 FROM #__f2c_fieldtype WHERE name='Joomlatemplate' HAVING COUNT(*) = 0";
				$db->setQuery($sql);
				$db->execute();
				
				$this->convertContentTypesToNewFieldStructure();
			}
			
			// Add missing fields
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Check box', 'Checkbox', 1 FROM #__f2c_fieldtype WHERE name = 'Checkbox' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();			
			
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'IFrame', 'Iframe', 1 FROM #__f2c_fieldtype WHERE name = 'Iframe' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'E-mail', 'Email', 1 FROM #__f2c_fieldtype WHERE name = 'Email' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Hyperlink', 'Hyperlink', 1 FROM #__f2c_fieldtype WHERE name = 'Hyperlink' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Multi select list (checkboxes)', 'Multiselectlist', 1 FROM #__f2c_fieldtype WHERE name = 'Multiselectlist' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
		
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Info Text', 'Infotext', 1 FROM #__f2c_fieldtype WHERE name = 'Infotext' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
		
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Date Picker', 'Datepicker', 1 FROM #__f2c_fieldtype WHERE name = 'Datepicker' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
		
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Display List', 'Displaylist', 1 FROM #__f2c_fieldtype WHERE name = 'Displaylist' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
		
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'File Upload', 'Fileupload', 1 FROM #__f2c_fieldtype WHERE name = 'Fileupload' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Database Lookup', 'Databaselookup', 1 FROM #__f2c_fieldtype WHERE name = 'Databaselookup' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
		
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Geo Coder', 'Geocoder', 1 FROM #__f2c_fieldtype WHERE name = 'Geocoder' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();		
			
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Database Lookup (Multi select)', 'Databaselookupmulti', 1 FROM #__f2c_fieldtype WHERE name = 'Databaselookupmulti' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();

			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Image Gallery', 'Imagegallery', 1 FROM #__f2c_fieldtype WHERE name='Imagegallery' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Color Picker', 'Colorpicker', 1 FROM #__f2c_fieldtype WHERE name='Colorpicker' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "INSERT INTO #__f2c_fieldtype (`description`, `name`, `classification_id`) SELECT 'Captcha', 'Captcha', 1 FROM #__f2c_fieldtype WHERE name='Captcha' HAVING COUNT(*) = 0";
			$db->setQuery($sql);
			$db->execute();
			
			// Add the modified_by column to the #__f2c_form table (release 6.13.0)
			$db->setQuery('SHOW COLUMNS FROM #__f2c_form LIKE \'modified_by\'');
			
			if(!$db->loadResult())
			{
				$db->setQuery('ALTER TABLE #__f2c_form ADD COLUMN `modified_by` int(10) unsigned NOT NULL DEFAULT \'0\' AFTER `modified`');
				$db->execute();
			}
			
			// Increase attribute column size from 10 to to 32
			$db->setQuery('ALTER TABLE #__f2c_fieldcontent MODIFY COLUMN attribute VARCHAR(32)');
			$db->execute();

			$adminFieldPath = JPATH_ADMINISTRATOR.'/components/com_form2content/models/fields/';
			
			// remove files that were present from an earlier version
			if(JFile::exists($adminFieldPath.'f2ccategory.php'))
			{
				JFile::delete($adminFieldPath.'f2ccategory.php');
			}
			
			if(JFile::exists($adminFieldPath.'f2ctemplate.php'))
			{
				JFile::delete($adminFieldPath.'f2ctemplate.php');
			}

			// Added Brainforge.uk 2025/04/29
            foreach([JPATH_SITE, JPATH_ADMINISTRATOR] as $folder)
			{
				foreach(Folder::files($folder . '/language', 'com_form2content', true, true) as $file)
				{
                    File::delete($file);
				}
			}
      }
 
    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent) 
    {
    	if($type == 'install' || $type == 'update')
    	{
    		$this->__setImportExportDefaults();
    	}
    	
    	if($type == 'install')
    	{
    		// Set the custom translations on
    		$this->enableCustomTranslations();
    	}
    }
	
    function __createPath($path)
    {
		if(!JFolder::exists($path))
		{
			JFolder::create($path, 0775);
		}
    }
    
    function __setImportExportDefaults()
    {
		$db = JFactory::getDBO();		
		$db->setQuery('SELECT extension_id FROM #__extensions WHERE name=\'com_form2content\'');
		
		$extensionId = $db->loadResult();

    	$configTable =  JTable::getInstance('extension');
		$configTable->load($extensionId);
		
		$params = new Registry($configTable->params);

    	if($params->get('import_dir') == '' && $params->get('export_dir') == '' && 
    	   $params->get('import_archive_dir') == '' && $params->get('import_error_dir') == '')
  		{
  			$params->set('import_dir', JPATH_SITE . '/media/com_form2content/import');
  			$params->set('export_dir', JPATH_SITE . '/media/com_form2content/export');
  			$params->set('import_archive_dir', JPATH_SITE . '/media/com_form2content/import/archive');
  			$params->set('import_error_dir', JPATH_SITE . '/media/com_form2content/import/error');
  		}
	
  		$configTable->params = $params->toString();
		$configTable->store();  		
    }
    
    function enableCustomTranslations()
    {
		$db = JFactory::getDBO();		
		$db->setQuery('SELECT extension_id FROM #__extensions WHERE name=\'com_form2content\'');
		
		$extensionId = $db->loadResult();

    	$configTable =  JTable::getInstance('extension');
		$configTable->load($extensionId);
		
		$params = new Registry($configTable->params);
		$params->set('custom_translations', 1);
		
 		$configTable->params = $params->toString();
		$configTable->store();  				
    }
    
    private function checkJoomlaVersion($versionNumber)
    {
    	$version = new JVersion();
    	return $version->isCompatible($versionNumber);
    }
    
    private function convertContentTypesToNewFieldStructure()
    {
		if(!class_exists('F2cFactory'))
		{
			require_once(JPATH_SITE.'/components/com_form2content/factory.form2content.php');		
			require_once(JPATH_SITE.'/administrator/components/com_form2content/tables/project.php');			
			JLoader::registerPrefix('F2c', JPATH_SITE.'/components/com_form2content/libraries/form2content');			
		}
    	
		$legacyContentType = new F2cLegacyProject();
		$legacyContentType->upgradeAllContentTypes();
    }    	
}
?>