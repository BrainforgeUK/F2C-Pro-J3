<?php
defined('_JEXEC') or die;
?>
<?php foreach($this->form->getGroup('metadata') as $field): ?>
<div class="control-group">
	<?php if (!$field->hidden): ?>
		<div class="control-label">
			<?php echo $field->label; ?>
		</div>
	<?php endif; ?>
	<div class="controls">
		<?php echo $field->input; ?>
	</div>
</div>
<?php endforeach; ?>
