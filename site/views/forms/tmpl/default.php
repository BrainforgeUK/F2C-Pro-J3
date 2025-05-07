<?php
// No direct access 
defined('JPATH_PLATFORM') or die('Restricted access');

// Removed Brainforge.uk 2025/04/29
// JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::stylesheet('com_form2content/f2cjui.css', array('relative' => true));
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Added Brainforge.uk 2025/04/30
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useStyle('form2content.admin');
$wa->useStyle('form2content.site');

// Added Brainforge.uk 2025/04/30
$itemId = empty($this->activeMenu->id) ? '' : '&Itemid=' .  $this->activeMenu->id;
$actionUrl = JRoute::_(	'index.php?option=com_form2content&view=forms' . $itemId);

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;

$saveOrder	= $listOrder == 'a.ordering';
$f2cConfig	= F2cFactory::getConfig();
$dateFormat = str_replace('%', '', $f2cConfig->get('date_format'));
$sortFields = $this->getSortFields();
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_form2content&task=forms.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>
<script type="text/javascript">
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
<div class="f2c-articlemanager<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>
	<div id="f2c_form">
		<form action="<?php
            // Modified Brainforge.uk 2025/12/29
            echo $actionUrl;?>"
              method="post" name="adminForm" id="adminForm">
			<div id="filter-bar" class="btn-toolbar">
				<div class="pull-left hidden-phone">
					<?php if($this->menuParams->get('show_new_button', 0)) : ?>
					<button class="btn f2c_button f2c_new" type="button" onclick="javascript:Joomla.submitbutton('form.add')"><?php echo JText::_('COM_FORM2CONTENT_NEW'); ?></button>
					<?php endif; ?>
					<?php if($this->menuParams->get('show_copy_button', 1)) : ?>
					<button class="btn f2c_button f2c_copy" type="button" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENT_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO'), JText::_('COM_FORM2CONTENT_COPY')); ?>');}else{  Joomla.submitbutton('forms.copy')}"><?php echo JText::_('COM_FORM2CONTENT_COPY'); ?></button>
					<?php endif; ?>
					<?php if($this->menuParams->get('show_edit_button', 1)) : ?>
					<button class="btn f2c_button f2c_edit" type="button" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENT_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO'), JText::_('COM_FORM2CONTENT_EDIT')); ?>');}else{  Joomla.submitbutton('form.edit')}"><?php echo JText::_('COM_FORM2CONTENT_EDIT'); ?></button>
					<?php endif; ?>
					<?php if($this->menuParams->get('show_publish_button', 1)) : ?>
					<button class="btn f2c_button f2c_publish" type="button" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENT_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO'), JText::_('COM_FORM2CONTENT_PUBLISH')); ?>');}else{  Joomla.submitbutton('forms.publish')}"><?php echo JText::_('COM_FORM2CONTENT_PUBLISH'); ?></button>
					<button class="btn f2c_button f2c_unpublish" type="button" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENT_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO'), JText::_('COM_FORM2CONTENT_UNPUBLISH')); ?>');}else{  Joomla.submitbutton('forms.unpublish')}"><?php echo JText::_('COM_FORM2CONTENT_UNPUBLISH'); ?></button>
					<?php endif; ?>
					<?php if($this->menuParams->get('show_archive_button', 0)) : ?>
					<button class="btn f2c_button f2c_archive" type="button" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENT_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO'), JText::_('COM_FORM2CONTENT_ARCHIVE')); ?>');}else{  Joomla.submitbutton('forms.archive')}"><?php echo JText::_('COM_FORM2CONTENT_ARCHIVE'); ?></button>
					<?php endif; ?>			
					<?php if($this->menuParams->get('show_delete_button', 1)) : ?>
					<button class="btn f2c_button f2c_delete" type="button" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENT_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO'), JText::_('COM_FORM2CONTENT_DELETE')); ?>');}else{  Joomla.submitbutton('forms.delete')}"><?php echo JText::_('COM_FORM2CONTENT_DELETE'); ?></button>
					<?php endif; ?>			
					<?php if($this->menuParams->get('show_trash_button', 1)) : ?>
					<button class="btn f2c_button f2c_trash" type="button" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENT_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO'), JText::_('COM_FORM2CONTENT_TRASH')); ?>');}else{  Joomla.submitbutton('forms.trash')}"><?php echo JText::_('COM_FORM2CONTENT_TRASH'); ?></button>
					<?php endif; ?>			
				</div>
			</div>
		
			<div class="clearfix"></div>
			
			<div id="filter-bar2" class="btn-toolbar">	
				<?php if($this->menuParams->get('show_search_filter')) : ?>
				<div class="filter-search btn-group pull-left">		
					<label class="element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
					<input type="text" name="filter_search" id="filter_search" class="f2c_filter" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_FORM2CONTENT_FILTER_SEARCH_DESC'); ?>" />
					<button type="submit" class="btn tip f2c_button f2c_submit" style="margin-top: -10px;"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
					<button type="button" class="btn tip f2c_button f2c_reset" style="margin-top: -10px;" onclick="document.getElementById('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
				</div>
				<?php endif; ?>
			
				<div class="clearfix"></div>
				
				<div class="filter-search pull-right">
					<?php if($this->menuParams->get('show_category_filter')) : ?>
					<select name="filter_category_id" id="filter_category_id" class="inputbox f2c_catfilter" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
						<?php echo JHtml::_('select.options', $this->categoryOptions, 'id', 'title', $this->state->get('filter.category_id'));?>						
					</select>
					<?php endif; ?>
				</div>
				<div class="filter-search pull-right">
					<?php if($this->menuParams->get('show_published_filter')) : ?>
					<select name="filter_published" id="filter_published" class="inputbox f2c_pubfilter" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
						<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array()), 'value', 'text', $this->state->get('filter.published'), true); ?>
					</select>
					<?php endif; ?>
				</div>
			
				<div class="clearfix"></div>
			</div>
			
			<table class="table table-striped" id="articleList">
				<thead>
					<tr class="f2c_header">
						<?php if($this->menuParams->get('show_ordering')) : ?>
						<th width="1%" class="nowrap center hidden-phone f2c_ordering">
							<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
						</th>
						<?php endif; ?>
						<th width="1%" class="hidden-phone f2c_toggle">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<?php if($this->menuParams->get('show_published_column') || $this->menuParams->get('show_featured_column')) : ?>
						<th width="1%" style="min-width:55px" class="nowrap center f2c_published">
							<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
						<th class="f2c_title">
							<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<?php if($this->menuParams->get('show_category')) : ?>
						<th width="10%" class="nowrap hidden-phone f2c_category">
							<?php echo JHtml::_('grid.sort', 'COM_FORM2CONTENT_CATEGORY', 'category_title', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_author_column')) : ?>
						<th width="10%" class="nowrap hidden-phone f2c_author">
							<?php echo JHtml::_('grid.sort',  'JAUTHOR', 'a.created_by', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_created_column')) : ?>
						<th width="10%" class="nowrap hidden-phone f2c_created">
							<?php echo JHtml::_('grid.sort',  'COM_FORM2CONTENT_CREATED', 'a.created', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_modified_column')) : ?>
						<th width="10%" class="nowrap hidden-phone f2c_modified">
							<?php echo JHtml::_('grid.sort',  'COM_FORM2CONTENT_MODIFIED', 'a.modified', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_publish_up_column', 0)) : ?>
						<th width="10%" class="nowrap hidden-phone f2c_publish_up">
							<?php echo JHtml::_('grid.sort',  'COM_FORM2CONTENT_PUBLISH_UP', 'a.publish_up', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_publish_down_column', 0)) : ?>
						<th width="10%" class="nowrap hidden-phone f2c_publish_down">
							<?php echo JHtml::_('grid.sort',  'COM_FORM2CONTENT_PUBLISH_DOWN', 'a.publish_down', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_language_column')) : ?>
						<th width="5%" class="f2c_language">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
						</th>						
						<?php endif; ?>
						<?php if($this->menuParams->get('show_joomla_id_column')) : ?>
						<th width="1%" class="nowrap hidden-phone joomla_id">
							<?php echo JHtml::_('grid.sort',  'COM_FORM2CONTENT_JOOMLA_ID', 'a.reference_id', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>						
						<?php if($this->menuParams->get('show_f2c_id_column')) : ?>
						<th width="1%" class="nowrap hidden-phone f2c_id">
							<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td class="f2c_pagination" colspan="<?php echo $this->numCols; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
							<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
							<?php echo $this->pagination->getLimitBox(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$item->max_ordering = 0; //??
					
					if(!$this->contentTypeSettings->get('create_joomla_article'))
					{
						// Use the F2C form state for Content Types that do not generate Joomla articles
						$itemState = $item->state;
					}
					else 
					{
						$itemState = empty($item->contentState) ? $item->state : $item->contentState;
					}
					
					$ordering   = ($listOrder == 'a.ordering');
					$canEdit	= $user->authorise('core.edit', 'com_form2content.form.'.$item->id);
					$canCheckin = true;
					$canEditOwn	= $user->authorise('core.edit.own', 'com_form2content.form.'.$item->id) && $item->created_by == $userId;
					$canChange	= ($user->authorise('core.edit.state', 'com_form2content.form.'.$item->id) ||
								  ($user->authorise('form2content.edit.state.own', 'com_form2content.form.'.$item->id) && $item->created_by == $userId)) && $canCheckin;
					$canTrash	= ($user->authorise('form2content.trash', 'com_form2content.form.'.$item->id) ||
								  ($user->authorise('form2content.trash.own', 'com_form2content.form.'.$item->id) && $item->created_by == $userId));
				?>
					<tr class="f2c_row row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
						<?php if($this->menuParams->get('show_ordering')) : ?>
						<td class="order nowrap center hidden-phone f2c_ordering">
						<?php if ($canChange) :
							$disableClassName = '';
							$disabledLabel	  = '';
		
							if (!$saveOrder) :
								$disabledLabel    = JText::_('JORDERINGDISABLED');
								$disableClassName = 'inactive tip-top';
							endif; ?>
							<span class="sortable-handler <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>" rel="tooltip">
								<i class="icon-menu"></i>
							</span>
							<input type="text" style="display:none"  name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
						<?php else : ?>
							<span class="sortable-handler inactive" >
								<i class="icon-menu"></i>
							</span>
						<?php endif; ?>
						</td>
						<?php endif; ?>
						<td class="center hidden-phone f2c_id">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<?php if($this->menuParams->get('show_published_column') || $this->menuParams->get('show_featured_column')) : ?>
						<td class="center f2c_published">
							<div class="btn-group">
								<?php 
								if($this->menuParams->get('show_published_column'))
								{
									echo JHtml::_('jgrid.published', $itemState, $i, 'forms.', $canChange, 'cb', $item->publish_up, $item->publish_down);
								}
								
								if($this->menuParams->get('show_featured_column'))
								{
									echo Form2ContentHelper::CreateFeaturedButton($item->featured, $i, $canChange);
								} 
								
								$renderActions = false;
								
								// Create dropdown items
								if($this->menuParams->get('show_archive_button') && $canChange)
								{
									$action = $archived ? 'unarchive' : 'archive';
									JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'forms');
									$renderActions = true;
								}
								
								if($this->menuParams->get('show_trash_button') && $canTrash)
								{
									$action = $trashed ? 'untrash' : 'trash';
									JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'forms');
									$renderActions = true;
								}
								
								if($renderActions)
								{
									// Render dropdown list
									echo str_replace('icon-', 'icon-f2c', JHtml::_('actionsdropdown.render', $this->escape($item->title)));
								}
								?>
							</div>
						</td>
						<?php endif; ?>	
						<td class="nowrap has-context f2c_title">
							<div class="pull-left">
								<?php if ($item->language == '*'):?>
									<?php $language = JText::alt('JALL', 'language'); ?>
								<?php else:?>
									<?php $language = $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
								<?php endif;?>
								<?php if ($canEdit || $canEditOwn) : ?>
									<a href="<?php echo JRoute::_('index.php?option=com_form2content&task=form.edit' .
                                                ' &id=' . $item->id . $itemId .
                                                '&return=' . base64_encode($actionUrl));?>"
                                       title="<?php echo JText::_('JACTION_EDIT') . ' (' . JText::_('JFIELD_ALIAS_LABEL'). ': '. $this->escape($item->alias) . ')';?>">
										<?php echo $this->escape($item->title); ?></a>
								<?php else : ?>
									<span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias));?>"><?php echo $this->escape($item->title); ?></span>
								<?php endif; ?>
							</div>						
						</td>
						<?php if($this->menuParams->get('show_category')) : ?>
						<td class="center hidden-phone f2c_category">
							<?php echo $this->escape($item->category_title); ?>
						</td>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_author_column')) : ?>	
						<td class="hidden-phone">
							<?php echo $this->escape($item->author_name); ?>
							<?php if ($item->created_by_alias) : ?>
								<div class="small">
									<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->created_by_alias));?>
								</div>
							<?php endif; ?>	
						</td>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_created_column')) : ?>	
						<td class="nowrap small hidden-phone">
							<?php echo JHtml::_('date', $item->created, $dateFormat); ?>
						</td>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_modified_column')) : ?>
						<td class="nowrap small hidden-phone">
							<?php
							if($item->modified && ($item->modified != $this->nullDate))
							{
								echo JHtml::_('date',$item->modified, $dateFormat);
							} 
							?>
						</td>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_publish_up_column')) : ?>	
						<td class="nowrap small hidden-phone">
							<?php echo JHtml::_('date', $item->publish_up, $dateFormat); ?>
						</td>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_publish_down_column')) : ?>
						<td class="nowrap small hidden-phone">
							<?php
							if($item->publish_down && ($item->publish_down != $this->nullDate))
							{
								echo JHtml::_('date',$item->publish_down, $dateFormat);
							} 
							?>
						</td>
						<?php endif; ?>												
						<?php if($this->menuParams->get('show_language_column')) : ?>
						<td class="center hidden-phone f2c_language">
							<?php if ($item->language=='*'):?>
								<?php echo JText::alt('JALL','language'); ?>
							<?php else:?>
								<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('COM_FORM2CONTENT_UNDEFINED'); ?>
							<?php endif;?>
						</td>
						<?php endif; ?>		
						<?php if($this->menuParams->get('show_joomla_id_column')) : ?>	
						<td class="center hidden-phone">
							<?php echo empty($item->reference_id) ? '' : $item->reference_id; ?>
						</td>
						<?php endif; ?>						
						<?php if($this->menuParams->get('show_f2c_id_column')) : ?>	
						<td class="center hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<input type="hidden" name="task" value="forms.display" />
			<input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="return" value="<?php echo base64_encode($actionUrl); ?>" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>