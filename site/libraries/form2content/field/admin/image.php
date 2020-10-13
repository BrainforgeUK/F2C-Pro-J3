<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use \Joomla\Registry\Registry;

class F2cFieldAdminImage extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('allow_filetype', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('allow_filetype', 'settings'); ?></div>
		</div>					
		<div class="control-group">
			<div class="control-label"><?php echo JText::_('COM_FORM2CONTENT_SHRINK_IMAGE_WHEN'); ?></div>
			<div class="controls"><?php echo $form->getInput('img_max_width', 'settings'); ?> X <?php echo $form->getInput('img_max_height', 'settings'); ?> <?php echo JText::_('COM_FORM2CONTENT_PIXELS'); ?> (<?php echo JText::_('COM_FORM2CONTENT_WIDTH_X_HEIGHT'); ?>)</div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('img_min_width', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('img_min_width', 'settings'); ?> X <?php echo $form->getInput('img_min_height', 'settings'); ?> <?php echo JText::_('COM_FORM2CONTENT_PIXELS'); ?> (<?php echo JText::_('COM_FORM2CONTENT_WIDTH_X_HEIGHT'); ?>)</div>
		</div>					
		<div class="control-group">
			<div class="control-label"><?php echo JText::_('COM_FORM2CONTENT_MAX_THUMBNAIL_SIZE'); ?></div>
			<div class="controls">
				<?php echo $form->getInput('img_thumb_width', 'settings'); ?> X
				<?php echo $form->getInput('img_thumb_height', 'settings'); ?> <?php echo JText::_('COM_FORM2CONTENT_PIXELS'); ?> (<?php echo JText::_('COM_FORM2CONTENT_WIDTH_X_HEIGHT'); ?>)
			</div>
		</div>	
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('img_cropping', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('img_cropping', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('img_crop_aspect_width', 'settings'); ?></div>
			<div class="controls">
				<?php echo $form->getInput('img_crop_aspect_width', 'settings'); ?> :	<?php echo $form->getInput('img_crop_aspect_height', 'settings'); ?>
			</div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('img_crop_thumb_only', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('img_crop_thumb_only', 'settings'); ?></div>
		</div>						
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('img_input_type', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('img_input_type', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('img_browseserver_root', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('img_browseserver_root', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('img_output_mode', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('img_output_mode', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('img_attributes_image', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('img_attributes_image', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('img_attributes_delete', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('img_attributes_delete', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('img_attributes_alt_text', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('img_attributes_alt_text', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('img_attributes_title', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('img_attributes_title', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('img_show_alt_tag', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('img_show_alt_tag', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('img_show_title_tag', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('img_show_title_tag', 'settings'); ?></div>
		</div>			
		<?php
	}
	
	public function clientSideValidation($view)
	{
		?>
		var aspectWidth = $('jform_settings_img_crop_aspect_width').value;
		var aspectHeight = $('jform_settings_img_crop_aspect_height').value;

		if(aspectWidth != '' || aspectHeight != '')
		{
			regex=/^0*$/;
			
			if(regex.test(aspectWidth) || regex.test(aspectHeight))
			{
				alert('<?php echo $view->escape(JText::_('COM_FORM2CONTENT_ERROR_CROPPING_ASPECT_RATIO', true)); ?>');
				return false;
			}
	
			regex=/^\d*$/;
	
			if(!(regex.test(aspectWidth) && regex.test(aspectHeight)))
			{
				alert('<?php echo $view->escape(JText::_('COM_FORM2CONTENT_ERROR_CROPPING_ASPECT_RATIO', true)); ?>');
				return false;
			}		
		}
		<?php
	}
	
	public function delete($id)
	{
		JLoader::register('F2cFieldImage', JPATH_COMPONENT_SITE.'/libraries/form2content/field/image.php');
		
		$db 	= JFactory::getDBO(); 
		$query 	= $db->getQuery(true);
		
		$query->select("pfl.projectid, fct.formid, fct.content")->from("#__f2c_projectfields pfl");
		$query->join("INNER", "#__f2c_fieldcontent fct ON pfl.id = fct.fieldid");
		$query->join("INNER", "#__f2c_fieldtype ftp ON pfl.fieldtypeid = ftp.id AND ftp.name = 'Image'");
		$query->where("pfl.id = ".(int)$id);
		
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		for ($i=0, $n=count($rows); $i < $n; $i++) 
		{
	  		$row = &$rows[$i];
			  		
	  		if($row->content)
	  		{
	  			$imageData = new Registry();
	  			$imageData->loadString($row->content);
	  			
				// delete thumbnail
				$img = Path::Combine(F2cFieldImage::GetThumbnailsPath($row->projectid, $row->formid), $imageData->get('filename'));
			
				if(JFile::exists($img))
				{
					JFile::delete($img);
				}
		
				// delete image
				$img = Path::Combine(F2cFieldImage::GetImagesPath($row->projectid, $row->formid), $imageData->get('filename'));
			
				if(JFile::exists($img))
				{
					JFile::delete($img);
				}
	  		}
		}					
	}
}
?>