<?php
defined('JPATH_PLATFORM') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
// Removed Brainforge.uk 2025/04/29
// JHtml::_('behavior.tooltip');
JHtml::stylesheet('com_form2content/modal.css', array('relative' => true));

// Added Brainforge.uk 2025/04/30
if (method_exists($this, 'getDocument'))
{
	/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
	$wa = $this->getDocument()->getWebAssetManager();
	$wa->useStyle('form2content.admin');
	$wa->useStyle('form2content.site');
}

$jinput		= JFactory::getApplication()->input;
$field		= $jinput->getCmd('field');
$function	= 'jSelectUser_'.$field;
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
	<form action="<?php echo JRoute::_('index.php?option=com_form2content&task=users.display&layout=modal&view=users&tmpl=component&groups='.$jinput->get('groups', '', 'BASE64').'&excluded='.$jinput->get('excluded', '', 'BASE64'));?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="filter">
		<div class="left" style="width: 500px;">
			<label for="filter_search"><?php echo JText::_('COM_FORM2CONTENT_USERS_FILTER'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" size="40" title="<?php echo JText::_('COM_FORM2CONTENT_USERS_SEARCH_IN_NAME'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			<button type="button" onclick="if (window.parent) window.parent.<?php echo $function;?>('', '<?php echo JText::_('JLIB_FORM_SELECT_USER') ?>');"><?php echo JText::_('COM_FORM2CONTENT_USERS_NO_USER')?></button>
		</div>
		<div class="right">
			<ol>
				<li>
					<label for="filter_group_id">
						<?php echo JText::_('COM_FORM2CONTENT_USERS_FILTER_USER_GROUP'); ?>
					</label>
					<?php echo $this->model->usergroup('filter_group_id', $this->state->get('filter.group_id'), 'onchange="this.form.submit()"'); ?>
				</li>
			</ol>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th class="nowrap" style="text-align:left">
					<?php echo JHtml::_('grid.sort', 'COM_FORM2CONTENT_USERS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="25%">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_USERNAME', 'a.username', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="25%">
					<?php echo JHtml::_('grid.sort', 'COM_FORM2CONTENT_USERS_HEADING_GROUPS', 'group_names', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$i = 0;
			foreach ($this->items as $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $function;?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)); ?>');">
						<?php echo $item->name; ?></a>
				</td>
				<td align="center">
					<?php echo $item->username; ?>
				</td>
				<td align="left">
					<?php echo nl2br($item->group_names); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="field" value="<?php echo $field; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
