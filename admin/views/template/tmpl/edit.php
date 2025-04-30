<?php 
defined('JPATH_PLATFORM') or die;

// Removed Brainforge.uk 2025/04/29
// JHtml::_('behavior.tooltip');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');

JText::script('COM_FORM2CONTENT_ERROR_TEMPLATE_TITLE_INVALID_CHARS');

// Added Brainforge.uk 2025/04/30
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useStyle('form2content.admin');

require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'shared.form2content.php');
?>
<script type="text/javascript">
//<!--
jQuery(document).ready(function() {
	jQuery("#jform_template").width(750);
	jQuery("#jform_title").width(750);
	jQuery('#jform_templateType0').on('change', function () { loadTemplateParameters(); });	
	jQuery('#jform_templateType1').on('change', function () { loadTemplateParameters(); });	

	// Template title validation
   	document.formvalidator.setHandler('templatetitle', function(value)
   	{   			
		regex=/^[A-Za-z0-9_]+$/;
		if(regex.test(value)) return true;

		error = {"error": []};
      	error.error.push(Joomla.JText._('COM_FORM2CONTENT_ERROR_TEMPLATE_TITLE_INVALID_CHARS'));
      	Joomla.renderMessages(error);
      	return false;
	});	

   	loadTemplateParameters();
});

function loadTemplateParameters()
{
	var data = new FormData();
	
	data.append('option', 'com_form2content');
	data.append('task', 'project.gettemplateparameters');
	data.append('format', 'raw');
	data.append('contenttypeid', jQuery('#jform_contentTypeId').val());
	data.append('templatetype', jQuery("input:radio[name='jform[templateType]']:checked").val());

	jQuery.ajax({
	    type: 'POST',
	    dataType: 'JSON',
	    data: data,
	    url: 'index.php',
	    cache: false,
	    contentType: false,
	    processData: false,
	    success: function(data)
	    {	
		    jQuery('#templateParameters').html(data);

		    jQuery('#templateParameters option').dblclick(function(){
		        // Insert the double-clicked item at the cursor position
		        elm =jQuery('#jform_template');
		        var cursorPos = elm.prop('selectionStart');
		        var v = elm.val();
		        var textBefore = v.substring(0, cursorPos);
		        var textAfter  = v.substring(cursorPos, v.length);
		        elm.val(textBefore + this.innerText + textAfter);
		        // Set the cursor after the inserted text
		        cursorPos += this.innerText.length;
		        elm.prop('selectionStart', cursorPos);
		        elm.prop('selectionEnd', cursorPos); 
		    });		    
	    },
		error: function(jqXHR, textStatus, errorThrown)
		{
			alert(textStatus + '\r\n' + errorThrown);
		}
	});		
}

Joomla.submitbutton = function(task) 
{
	if (task == 'template.cancel' || document.formvalidator.isValid(document.getElementById("adminForm"))) 
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
		return true;
	}
}
//-->	
</script>
<form action="<?php echo JRoute::_('index.php?option=com_form2content&task=template.edit&layout=edit&id='.urlencode($this->item->id)); ?>" 
	  method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="row-fluid">
	<!-- Begin Content -->
	<div class="span7 form-horizontal">
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('template'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('template'); ?></div>
		</div>
	</div>
	<div class="span5 form-horizontal">
		<div class="alert alert-info">
		<h3><?php echo JText::_('COM_FORM2CONTENT_TEMPLATE_PARAMETERS_HELP_HEADING'); ?></h3>
		<p><?php echo JText::_('COM_FORM2CONTENT_TEMPLATE_PARAMETERS_HELP'); ?></p><p><br/></p>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('contentTypeId'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('contentTypeId'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('templateType'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('templateType'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo JText::_('COM_FORM2CONTENT_TEMPLATE_PARAMETERS'); ?></div>
			<div class="controls"><select id="templateParameters" multiple style="height: 600px; width: 350px;"></select></div>
		</div>
		</div>
	</div>
	<!--  End Content -->
</div>
<?php echo $this->form->getInput('id'); ?>
<input type="hidden" name="task" value="" />
<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->getCmd('return');?>" />
<?php echo JHtml::_('form.token'); ?>
<?php echo F2cViewHelper::displayCredits(); ?>
</form>
