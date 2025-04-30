<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

JHtml::_('bootstrap.framework');
JHtml::script('com_form2content/jquery.Jcrop.min.js', array('relative' => true));
JHtml::script('com_form2content/jquery.blockUI.js', array('relative' => true));
JHtml::script('com_form2content/crop.js', array('relative' => true));
JHtml::stylesheet('com_form2content/jquery.Jcrop.min.css', array('relative' => true));
JHtml::stylesheet('com_form2content/main.css', array('relative' => true));

JText::script('COM_FORM2CONTENT_ERROR_CROPPING_EMPTY_REGION');
JText::script('COM_FORM2CONTENT_ERROR_IMAGE_CROP_MIN_WIDTH');
JText::script('COM_FORM2CONTENT_ERROR_IMAGE_CROP_MIN_HEIGHT');

// Added Brainforge.uk 2025/04/30
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useStyle('form2content.admin');

?>
<script type="text/javascript">
var tmpImage = '<?php echo $this->tmpImage; ?>';
var jBusyCroppingImage = '<p class="blockUI"><img src="<?php echo JURI::root(true).'/media/com_form2content/images/'; ?>busy.gif" /> <?php echo JText::_('COM_FORM2CONTENT_BUSY_CROPPING_IMAGE', true)?></p>';
var MAXPREVIEWSIZE = <?php echo MAXPREVIEWSIZE;?>;
</script>
<style type="text/css">
/* The Javascript code will set the aspect ratio of the crop
   area based on the size of the thumbnail preview,
   specified here */
#preview-pane .preview-container {
  width: <?php echo $this->previewWidth; ?>px;
  height: <?php echo $this->previewHeight; ?>px;
  overflow: hidden;
}

.jcrop-holder #preview-pane {
  display: block;
  z-index: 2000;
  position: absolute;
  top: <?php echo -$this->top; ?>px;
  left: <?php echo 10 + MAXCROPSIZE - $this->left; ?>px;  

  padding: 6px;
  border: 1px rgba(0,0,0,.4) solid;
  background-color: white;

  -webkit-border-radius: 6px;
  -moz-border-radius: 6px;
  border-radius: 6px;

  -webkit-box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
  -moz-box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
  box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
}

#cropButtons
{
	position: absolute;
	top: 270px;
}
</style>
<form action="" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div class="span8">
			<div id="cropcontainer">
				<img id="cropimage" src="" width="<?php echo $this->imageWidth; ?>" height="<?php echo $this->imageHeight; ?>" />
			</div>
		</div>
		<div class="span4">
			<div id="preview-pane" style="height:250px;width:250px;display: block;">
			    <div class="preview-container">
			    	<img src="<?php echo JURI::root(true).'/media/com_form2content/images/1x1_transparent.png'; ?>" class="jcrop-preview" alt="" id="preview" />
			    </div>
			    <div id="cropButtons">
					<div id="crop_instructions">
						<h2><?php echo JText::_('COM_FORM2CONTENT_CROP_IMAGE'); ?></h2>
						<p><?php echo JText::_('COM_FORM2CONTENT_CROP_INSTRUCTIONS'); ?></p>	
					</div>	
					<div id="buttonbar">
						<input type="button" class="btn" onclick="crop();" value="<?php echo JText::_('COM_FORM2CONTENT_CROP'); ?>" />
						<button type="button" class="btn btn-default" onclick="jQuery('#modalCropWindow button.close', parent.document).trigger('click');"><?php echo JText::_('JCANCEL'); ?></button>
					</div>	
				</div>		 
			</div>	
		</div>
	</div>
	<input id="x" type="hidden" name="x">
	<input id="y" type="hidden" name="y">
	<input id="w" type="hidden" name="w">
	<input id="h" type="hidden" name="h">
	<input id="fieldid" type="hidden" name="fieldid" value="<?php echo $this->field->id; ?>">
	<input id="contenttypeid" type="hidden" name="contenttypeid" value="<?php echo $this->field->projectid; ?>">
	<input id="row" type="hidden" name="row" value="<?php echo $this->row; ?>">
	<input id="cropthumbonly" type="hidden" name="cropthumbonly" value="<?php echo $this->cropThumbOnly; ?>">
	<input id="minSelectionWidth" type="hidden" name="minSelectionWidth" value="<?php echo $this->minSelectionWidth; ?>">
	<input id="minSelectionHeight" type="hidden" name="minSelectionHeight" value="<?php echo $this->minSelectionHeight; ?>">
	<input id="imageDir" type="hidden" name="imageDir" value="<?php echo $this->imageDir; ?>">
	<input id="aspectWidth" type="hidden" value="<?php echo $this->aspectWidth; ?>">
	<input id="aspectHeight" type="hidden" value="<?php echo $this->aspectHeight; ?>">
	<input id="top" type="hidden" value="<?php echo $this->top; ?>">
	<input id="left" type="hidden" value="<?php echo $this->left; ?>">	
</form>