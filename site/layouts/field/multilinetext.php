<?php
defined('JPATH_BASE') or die;

$field = $displayData['field'];
?>

<textarea name="<?php echo $field->elementId ?>" id="<?php echo $field->elementId ?>" <?php echo $displayData['attribs']; ?> 
<?php if($displayData['maxNumChars']) : ?>
  onKeyDown="Form2Content.Fields.Multilinetext.LimitText('<?php echo $field->elementId; ?>',<?php echo $displayData['maxNumChars']; ?>);" 
  onKeyUp="Form2Content.Fields.Multilinetext.LimitText('<?php echo $field->elementId ?>',<?php echo $displayData['maxNumChars']; ?>);"	
<?php endif; ?>
><?php echo $field->values['VALUE']; ?></textarea>
		
<?php if($displayData['maxNumChars']) : ?>
	 <div style="clear:both;"><input readonly type="text" name="<?php echo $field->elementId ?>remLen" id="<?php echo $field->elementId ?>remLen" size="6" width="6" value="<?php echo $displayData['charsRemaining']; ?>" class="mlt_charsleft"><?php echo Jtext::_('COM_FORM2CONTENT_CHARACTERS_LEFT'); ?></div>		
<?php endif; ?>
		
<?php if(JFactory::getApplication()->isClient('site') && $displayData['requiredText'] != '') : ?>
	<span class="f2c_required">&nbsp;<?php echo $this->escape($displayData['requiredText']); ?></span>
<?php endif; ?>
		
<?php if(JFactory::getApplication()->isClient('site') && $displayData['description'] != '') : ?>
	&nbsp;<?php echo JHtml::tooltip($displayData['description'], $displayData['title']); ?>
<?php endif; ?>

<input type="hidden" name="hid<?php echo $field->elementId ?>" id="hid<?php echo $field->elementId ?>" value="<?php echo $field->internal['fieldcontentid']; ?>" >

