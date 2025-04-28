<?php
// no direct access
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die;

$published 	= $this->state->get('filter.published');
?>
<div class="modal hide fade" id="collapseModal">
	<div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3><?php echo JText::_('COM_FORM2CONTENT_BATCH_OPTIONS');?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo JText::_('COM_FORM2CONTENT_BATCH_TIP'); ?></p>
		<div class="control-group">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.access', []); ?>
				<?php /* Deleted Brainforge.uk 2025/04/28 echo JHtml::_('batch.access');*/?>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.tag', []); ?>
				<?php /* Deleted Brainforge.uk 2025/04/28 echo JHtml::_('batch.tag');*/?>
            </div>
         </div>
		<div class="control-group">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.language', []); ?>
				<?php /* Deleted Brainforge.uk 2025/04/28 echo JHtml::_('batch.language');*/?>
			</div>
		</div>
		<?php if ($published >= 0) : ?>
		<div class="control-group">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.item', 'com_content'); ?>
				<?php /* Deleted Brainforge.uk 2025/04/28 echo JHtml::_('batch.item', 'com_content'); */?>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.id('batch-category-id').value='';document.id('batch-access').value='';document.id('batch-language-id').value=''" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('form.batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>
