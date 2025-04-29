<?php
defined('JPATH_BASE') or die;

// Rewritten Brainforge.uk 2025/04/29
//jform[settings][default]
?>
<div class="input-append">
    <select id="<?php echo $displayData['id'];?>"
            name="<?php echo $displayData['name'];?>"
    >
        <option value=""></option>
        <?php
        $templates = JFolder::files(JPATH_SITE.'/media/com_form2content/templates', '\.tpl');
        foreach($templates as $template)
        {
            $selected = ($template == $displayData['value']) ? ' selected="selected"' : '';

            echo '<option value="' . htmlspecialchars($template) . '"' . $selected . '>' . htmlspecialchars($template) . '</option>';
        }
        ?>
    </select>
	<?php
	/*
	<input type="text" id="<?php echo $displayData['id'];?>_name" value="<?php echo htmlspecialchars($displayData['value'], ENT_COMPAT, 'UTF-8');?>" disabled="disabled" <?php echo $displayData['attribute'];?> />

	<?php if($displayData['readonly'] != 'true') :?>
		<a id="templateSelect<?php echo $displayData['id'];?>" href="#modalTemplate<?php echo $displayData['id'];?>" class="btn btn-primary modal_<?php echo $displayData['id'];?>" data-toggle="modal" title="<?php echo JText::_('COM_FORM2CONTENT_SELECT_TEMPLATE');?>"><i class="icon-wand"></i></a>
		<?php echo JHTML::_('bootstrap.renderModal', 'modalTemplate'.$displayData['id'], $displayData['modal_params'], ''); ?>
	<?php endif;?>
	*/
    ?>
	
</div>
<?php
/*
<input type="hidden" id="<?php echo $displayData['id'];?>_id" name="<?php echo $displayData['name'];?>" value="<?php echo htmlspecialchars($displayData['value']);?>" />
*/
