<?php 
defined('JPATH_PLATFORM') or die('Restricted acccess');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::stylesheet('com_form2content/admin.css', array('relative' => true));

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$f2cConfig	= F2cFactory::getConfig();
$dateFormat = str_replace('%', '', $f2cConfig->get('date_format'));
$sortFields = $this->getSortFields();
$saveOrder	= false;
?>
<script type="text/javascript">
Joomla.submitbutton = function(task) 
{
	if (task == 'projects.syncorder') 
	{
		if(!confirm('<?php echo JText::_('COM_FORM2CONTENT_SYNC_ORDER_CONFIRMATION', true); ?>'))
		{
			return false;
		}
	}
	else if(task == 'project.upload')
	{
		var upload = document.getElementById('upload');
		
		if(!upload.value)
		{
			alert('<?php echo JText::_('COM_FORM2CONTENT_ERROR_CONTENTTYPE_FILE_UPLOAD_EMPTY'); ?>');
			return false;
		}				
	}
	
	Joomla.submitform(task);
	return true;	
}

Joomla.orderTable = function() {
	table = document.getElementById("sortTable");
	direction = document.getElementById("directionTable");
	order = table.options[table.selectedIndex].value;
	if (order != '<?php echo $listOrder; ?>') {
		dirn = 'asc';
	} else {
		dirn = direction.options[direction.selectedIndex].value;
	}
	Joomla.tableOrdering(order, dirn, '');
}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_form2content&view=projects');?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php if (!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<div id="uploadform">
			<fieldset id="upload-noflash" class="actions">
				<label for="upload" class="control-label"><?php echo JText::_('COM_FORM2CONTENT_IMPORT_CONTENTTYPE'); ?></label>
				<input type="file" id="upload" name="upload" />
				<p class="help-block"><?php echo JText::_('COM_FORM2CONTENT_MAX_SIZE'); ?> =&nbsp;<?php echo ini_get('post_max_size'); ?></p>
			</fieldset>
		</div>	
		<?php 
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<div class="clearfix"> </div>	
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="articleList">
				<thead>
					<tr>
						<th width="1%" class="hidden-phone">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="1%" style="min-width:55px" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap">
							<?php echo JText::_('COM_FORM2CONTENT_FIELDS'); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort',  'JAUTHOR', 'u.name', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_FORM2CONTENT_CREATED', 'a.created', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_FORM2CONTENT_MODIFIED', 'a.modified', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JText::_('COM_FORM2CONTENT_EXPORT'); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$item->max_ordering = 0; //??
					$canChange = true;
					$ordering   		= ($listOrder == 'a.ordering');
					?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php echo JHtml::_('jgrid.published', $item->published, $i, 'projects.', $canChange, 'cb'); ?>
							</div>
						</td>
						<td class="nowrap has-context">
							<a href="<?php echo JRoute::_('index.php?option=com_form2content&task=project.edit&id=' . $item->id);?>" title="<?php echo JText::_('JACTION_EDIT');?>">
								<?php echo $this->escape($item->title); ?>
							</a>
						</td>
						<td class="small">
							<a href="<?php echo JRoute::_('index.php?option=com_form2content&view=projectfields&projectid='. $item->id); ?>">
								<i class="icon-cog f2cicon-large" title="<?php echo JText::_('COM_FORM2CONTENT_PROJECTFIELDS', true); ?>"></i>
							</a>
						</td>
						<td class="small hidden-phone">
							<?php echo $this->escape($item->username); ?>
						</td>
						<td class="nowrap small hidden-phone">
							<?php echo JHtml::_('date', $item->created, $dateFormat); ?>
						</td>
						<td class="nowrap small hidden-phone">
							<?php
							if($item->modified && ($item->modified != $this->nullDate))
							{
								echo JHtml::_('date',$item->modified, $dateFormat);
							} 
							?>
						</td>
						<td class="nowrap small hidden-phone">
							<a href="<?php echo JRoute::_('index.php?option=com_form2content&task=project.export&view=project&format=raw&id='.$item->id);?>" target="_blank" title="<?php echo JText::_('COM_FORM2CONTENT_EXPORT'); ?>">
								<i class="icon-download f2cicon-large" title="<?php echo JText::_('COM_FORM2CONTENT_EXPORT', true); ?>"></i>
							</a>
						</td>
						<td class="center hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>		
			</table>
		<?php endif; ?>
		<?php echo $this->pagination->getListFooter(); ?>
			
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
		<?php echo F2cViewHelper::displayCredits(); ?>	
	</div>
</form>
