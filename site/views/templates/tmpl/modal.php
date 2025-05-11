<?php
defined('JPATH_PLATFORM') or die;

JHtml::stylesheet('com_form2content/modal.css', array('relative' => true));

// Added Brainforge.uk 2025/04/30
if (method_exists($this, 'getDocument'))
{
	/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
	$wa = $this->getDocument()->getWebAssetManager();
	$wa->useStyle('form2content.admin');
	$wa->useStyle('form2content.site');
}

$field = JFactory::getApplication()->input->get('field');
?>
<form action="<?php echo JRoute::_('index.php?option=com_form2content&task=templates.select&layout=modal&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="table table-striped">
		<tbody>
		<?php
		$i = 0;
		foreach ($this->items as $item)
		{
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a class="pointer" onclick="if (window.parent) window.parent.f2cSelectTemplate('<?php echo $field;?>', '<?php echo $this->escape(addslashes($item->fileName)); ?>');">
						<?php echo $item->fileName; ?></a>
				</td>
			</tr>
		<?php
			$i++;
		}
		?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="field" value="<?php echo $field; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<script>
jQuery(document).ready(function(){
	// Prevent double scroll bar
	jQuery(".modal-body",parent.document).css("max-height", 670);
});

</script>