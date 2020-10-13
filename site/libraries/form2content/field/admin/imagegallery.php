<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cFieldAdminImageGallery extends F2cFieldAdminBase
{
	function display($form, $item)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('allow_filetype', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('allow_filetype', 'settings'); ?></div>
		</div>							
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('igl_fieldclass_sfx', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('igl_fieldclass_sfx', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('igl_image_quality', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('igl_image_quality', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('igl_thumb_quality', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('igl_thumb_quality', 'settings'); ?></div>
		</div>			
		
		<div class="control-group">
			<div class="control-label"><?php echo JText::_('COM_FORM2CONTENT_SHRINK_IMAGE_WHEN'); ?></div>
			<div class="controls"><?php echo $form->getInput('igl_max_width', 'settings'); ?> X <?php echo $form->getInput('igl_max_height', 'settings'); ?> <?php echo JText::_('COM_FORM2CONTENT_PIXELS'); ?> (<?php echo JText::_('COM_FORM2CONTENT_WIDTH_X_HEIGHT'); ?>)</div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('igl_min_width', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('igl_min_width', 'settings'); ?> X <?php echo $form->getInput('igl_min_height', 'settings'); ?> <?php echo JText::_('COM_FORM2CONTENT_PIXELS'); ?> (<?php echo JText::_('COM_FORM2CONTENT_WIDTH_X_HEIGHT'); ?>)</div>
		</div>											
		<div class="control-group">
			<div class="control-label"><?php echo JText::_('COM_FORM2CONTENT_MAX_THUMBNAIL_SIZE'); ?></div>
			<div class="controls">
				<?php echo $form->getInput('igl_thumb_width', 'settings'); ?> X
				<?php echo $form->getInput('igl_thumb_height', 'settings'); ?> <?php echo JText::_('COM_FORM2CONTENT_PIXELS'); ?> (<?php echo JText::_('COM_FORM2CONTENT_WIDTH_X_HEIGHT'); ?>)
			</div>
		</div>	
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('igl_cropping', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('igl_cropping', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('igl_crop_aspect_width', 'settings'); ?></div>
			<div class="controls">
				<?php echo $form->getInput('igl_crop_aspect_width', 'settings'); ?> :	<?php echo $form->getInput('igl_crop_aspect_height', 'settings'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('igl_crop_thumb_only', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('igl_crop_thumb_only', 'settings'); ?></div>
		</div>						
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('igl_input_type', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('igl_input_type', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('igl_browseserver_root', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('igl_browseserver_root', 'settings'); ?></div>
		</div>			
		<!--
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('igl_attributes_image', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('igl_attributes_image', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('igl_attributes_alt_text', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('igl_attributes_alt_text', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('igl_attributes_title', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('igl_attributes_title', 'settings'); ?></div>
		</div>			
		-->
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('igl_show_alt_tag', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('igl_show_alt_tag', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('igl_show_title_tag', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('igl_show_title_tag', 'settings'); ?></div>
		</div>			
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('igl_max_num_images', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('igl_max_num_images', 'settings'); ?></div>
		</div>			
		<?php
	}
	
	public function delete($id)
	{
		JLoader::register('F2cFieldImageGallery', JPATH_COMPONENT_SITE.'/libraries/form2content/field/imagegallery.php');
		
		$db 	= JFactory::getDBO(); 
		$query 	= $db->getQuery(true);
		
		$query->select("pfl.projectid, fct.formid, fct.content")->from("#__f2c_projectfields pfl");
		$query->join("INNER", "#__f2c_fieldcontent fct ON pfl.id = fct.fieldid");
		$query->join("INNER", "#__f2c_fieldtype ftp ON pfl.fieldtypeid = ftp.id AND ftp.name = 'Imagegallery'");
		$query->where("pfl.id = ".(int)$id);
		
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		for ($i=0, $n=count($rows); $i < $n; $i++) 
		{
	  		$row = &$rows[$i];
			  		
	  		if($row->content)
	  		{
	  			$galleryDir = F2cFieldImageGallery::getPath($row->formid, $row->projectid, $id);

	  			if(JFolder::exists($galleryDir))
	  			{
	  				JFolder::delete($galleryDir);
	  			}
	  		}
		}					
	}
	
	public function prepareSave(&$data, $useRequestData)
	{
		if((int)$data['settings']['igl_max_num_images'] == 0)
		{
			$data['settings']['igl_max_num_images'] = '';
		}
	}
}
?>