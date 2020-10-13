<?php
defined('JPATH_BASE') or die;
?>
<div class="input-append">
	<input type="text" id="<?php echo $displayData['id'];?>_name" value="<?php echo htmlspecialchars($displayData['value'], ENT_COMPAT, 'UTF-8');?>" disabled="disabled" <?php echo $displayData['attribute'];?> />
	
	<?php if($displayData['readonly'] != 'true') :?>		
		<a id="templateSelect<?php echo $displayData['id'];?>" href="#modalTemplate<?php echo $displayData['id'];?>" class="btn btn-primary modal_<?php echo $displayData['id'];?>" data-toggle="modal" title="<?php echo JText::_('COM_FORM2CONTENT_SELECT_TEMPLATE');?>"><i class="icon-wand"></i></a>
		<?php echo JHTML::_('bootstrap.renderModal', 'modalTemplate'.$displayData['id'], $displayData['modal_params'], ''); ?>
	<?php endif;?>		
	
</div>
<input type="hidden" id="<?php echo $displayData['id'];?>_id" name="<?php echo $displayData['name'];?>" value="<?php echo htmlspecialchars($displayData['value']);?>" />

