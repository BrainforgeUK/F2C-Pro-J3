<?php 
defined('JPATH_PLATFORM') or die('Restricted acccess');

$f2cConfig	= F2cFactory::getConfig();
$dateFormat = str_replace('%', '', $f2cConfig->get('date_format'));
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_form2content&view=translations');?>" method="post" name="adminForm" id="adminForm">

<?php if (!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
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
			<table class="table table-striped" id="translationList">
				<thead>
					<tr>
						<th width="1%" class="hidden-phone">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_FORM2CONTENT_DEFAULT_FIELD_NAME', 'f.title', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_FORM2CONTENT_PROJECT', 'p.title', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'l.lang_code', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_FORM2CONTENT_TRANSLATION', 't.title_translation', $listDirn, $listOrder); ?>							
						</th>
						<th class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_FORM2CONTENT_DATE_MODIFIED', 't.modified', $listDirn, $listOrder); ?>							
						</th>
						<th class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_FORM2CONTENT_MODIFIED_BY', 't.modified_by', $listDirn, $listOrder); ?>							
						</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
				 
			  		if($item->translation_id)
			  		{
			  			$link = 'index.php?option=com_form2content&task=translation.edit&id='.$item->translation_id.'&reference_id='.$item->fieldid;
			  		}
			  		else
			  		{
			  			$link = 'index.php?option=com_form2content&task=translation.add&reference_id='.$item->fieldid.'&lang_code='.urlencode($item->lang_code);
			  		}
				?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->translation_id ? $item->translation_id : 'R'.$item->fieldid.'L'.$item->lang_code); ?>						
						</td>
						<td>
							<a href="<?php echo JRoute::_($link);?>">
								<?php echo $this->escape($item->fieldtitle . ' (' . $item->fieldname . ')'); ?>
							</a>
						</td>
						<td>
							<?php echo $item->projecttitle; ?>
						</td>
						<td>
							<?php echo $item->lang_code; ?>
						</td>
						<td>
							<?php echo $item->title_translation; ?>
						</td>
						<td>
							<?php
							if($item->modified)
							{
								echo JHTML::_('date',$item->modified, $dateFormat);
							}
							?>
						</td>
						<td>
							<?php echo $item->modifier; ?>
						</td>
					</tr>			
				<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="7">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
			</table>
		<?php endif; ?>	
		<?php echo F2cViewHelper::displayCredits(); ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
