<?php
// No direct access 
defined('JPATH_PLATFORM') or die('Restricted access');

// Removed Brainforge.uk 2025/04/29
// JHtml::_('behavior.tooltip');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::stylesheet('com_form2content/f2cfrontend.css', array('relative' => true));
JHtml::stylesheet('com_form2content/f2cjui.css', array('relative' => true));

// Added Brainforge.uk 2025/04/30
if (method_exists($this, 'getDocument'))
{
	/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
	$wa = $this->getDocument()->getWebAssetManager();
	$wa->useStyle('form2content.admin');
	$wa->useStyle('form2content.site');
}

// Added Brainforge.uk 2025/04/30
$itemId = empty($this->activeMenu->id) ? '' : '&Itemid=' .  $this->activeMenu->id;
$actionUrl = JRoute::_(	'index.php?option=com_form2content&view=forms' . $itemId);

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $listOrder == 'a.ordering';
$f2cConfig	= F2cFactory::getConfig();
$dateFormat = str_replace('%', '', $f2cConfig->get('date_format'));
?>
<div class="f2c-articlemanager<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>
	<div id="f2c_form">
        <form action="<?php
		    // Modified Brainforge.uk 2025/12/29
		    echo $actionUrl;?>"
              method="post" name="adminForm" id="adminForm">
			<table class="f2c_header" style="width:100%;">
			<tr class="f2c_buttons">
				<td>
					<div style="float: left;">
						<?php if($this->menuParams->get('show_new_button', 0)) : ?>
						<button type="button" class="f2c_button f2c_new" onclick="javascript:Joomla.submitbutton('form.add')"><?php echo JText::_('COM_FORM2CONTENT_NEW'); ?></button>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_copy_button', 1)) : ?>
						<button type="button" class="f2c_button f2c_copy" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENT_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO'), JText::_('COM_FORM2CONTENT_COPY')); ?>');}else{  Joomla.submitbutton('forms.copy')}"><?php echo JText::_('COM_FORM2CONTENT_COPY'); ?></button>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_edit_button', 1)) : ?>
						<button type="button" class="f2c_button f2c_edit" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENT_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO'), JText::_('COM_FORM2CONTENT_EDIT')); ?>');}else{  Joomla.submitbutton('form.edit')}"><?php echo JText::_('COM_FORM2CONTENT_EDIT'); ?></button>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_publish_button', 1)) : ?>
						<button type="button" class="f2c_button f2c_publish" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENT_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO'), JText::_('COM_FORM2CONTENT_PUBLISH')); ?>');}else{  Joomla.submitbutton('forms.publish')}"><?php echo JText::_('COM_FORM2CONTENT_PUBLISH'); ?></button>
						<button type="button" class="f2c_button f2c_unpublish" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENT_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO'), JText::_('COM_FORM2CONTENT_UNPUBLISH')); ?>');}else{  Joomla.submitbutton('forms.unpublish')}"><?php echo JText::_('COM_FORM2CONTENT_UNPUBLISH'); ?></button>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_archive_button', 0)) : ?>
						<button type="button" class="f2c_button f2c_archive" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENT_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO'), JText::_('COM_FORM2CONTENT_ARCHIVE')); ?>');}else{  Joomla.submitbutton('forms.archive')}"><?php echo JText::_('COM_FORM2CONTENT_ARCHIVE'); ?></button>
						<?php endif; ?>			
						<?php if($this->menuParams->get('show_trash_button', 1)) : ?>
						<button type="button" class="f2c_button f2c_delete" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENT_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO'), JText::_('COM_FORM2CONTENT_DELETE')); ?>');}else{  Joomla.submitbutton('forms.trash')}"><?php echo JText::_('COM_FORM2CONTENT_DELETE'); ?></button>
						<?php endif; ?>			
						<?php if($this->menuParams->get('show_delete_button', 1)) : ?>
						<button type="button" class="f2c_button f2c_trash" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENT_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO'), JText::_('COM_FORM2CONTENT_EMPTY_TRASH')); ?>');}else{  Joomla.submitbutton('forms.delete')}"><?php echo JText::_('COM_FORM2CONTENT_EMPTY_TRASH'); ?></button>
						<?php endif; ?>			
					</div>
				</td>
			</tr>
			</table>
			<fieldset id="filter-bar" class="f2c_search">	
				<div class="filter-search fltlft">
					<?php if($this->menuParams->get('show_search_filter')) : ?>
						<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
						<input type="text" name="filter_search" id="filter_search" class="f2c_filter" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_FORM2CONTENT_FILTER_SEARCH_DESC'); ?>" />
						<button type="submit" class="btn f2c_button f2c_submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
						<button type="button" class="btn f2c_button f2c_reset" onclick="document.getElementById('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
					<?php endif; ?>
				</div>
				<div class="filter-select fltrt">
					<?php if($this->menuParams->get('show_published_filter')) : ?>
					<select name="filter_published" class="inputbox f2c_pubfilter" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
						<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array()), 'value', 'text', $this->state->get('filter.published'), true);?>
					</select>
					<?php endif; ?>
					<?php if($this->menuParams->get('show_category_filter')) : ?>
					<select name="filter_category_id" class="inputbox f2c_catfilter" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
						<?php echo JHtml::_('select.options', $this->categoryOptions, 'id', 'title', $this->state->get('filter.category_id'));?>
					</select>
					<?php endif; ?>
				</div>
			</fieldset>
			<div class="clr"></div>	
			<table class="adminlist f2c_list">
				<thead>
					<tr class="f2c_header">
						<?php if($this->menuParams->get('show_f2c_id_column')) : ?>
						<th width="1%" class="nowrap f2c_id">
							<?php echo JHtml::_('grid.sort', 'COM_FORM2CONTENT_GRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>				
						<?php endif; ?>	
						<?php if($this->menuParams->get('show_joomla_id_column')) : ?>
						<th width="1%" class="nowrap joomla_id">
							<?php echo JHtml::_('grid.sort', 'COM_FORM2CONTENT_JOOMLA_ID', 'a.reference_id', $listDirn, $listOrder); ?>
						</th>				
						<?php endif; ?>	
						<th width="1%" class="f2c_toggle">
							<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
						</th>
						<th class="f2c_title">
							<?php echo JHtml::_('grid.sort', 'COM_FORM2CONTENT_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<?php if($this->menuParams->get('show_published_column')) : ?>
						<th width="5%" class="f2c_published">
							<?php echo JHtml::_('grid.sort', 'COM_FORM2CONTENT_PUBLISHED', 'a.state', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>						
						<?php if($this->menuParams->get('show_featured_column')) : ?>
						<th width="5%" class="f2c_featured">
							<?php echo JHtml::_('grid.sort', 'JFEATURED', 'a.featured', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>						
						<?php if($this->menuParams->get('show_ordering')) : ?>
						<th width="15%" class="f2c_ordering">
							<?php echo JHtml::_('grid.sort',  'COM_FORM2CONTENT_GRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
							<?php if ($saveOrder) :?>
								<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'forms.saveorder'); ?>
							<?php endif; ?>
						</th>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_category')) : ?>
						<th width="10%" class="f2c_category">
							<?php echo JHtml::_('grid.sort', 'COM_FORM2CONTENT_CATEGORY', 'category_title', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_author_column')) : ?>
						<th width="10%" class="f2c_author">
							<?php echo JHtml::_('grid.sort', 'COM_FORM2CONTENT_GRID_HEADING_CREATED_BY', 'a.created_by', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_created_column')) : ?>
						<th width="5%" class="f2c_created">
							<?php echo JHtml::_('grid.sort', 'COM_FORM2CONTENT_CREATED', 'a.created', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_modified_column')) : ?>
						<th width="5%" class="f2c_modified">
							<?php echo JHtml::_('grid.sort', 'COM_FORM2CONTENT_MODIFIED', 'a.modified', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_publish_up_column', 0)) : ?>
						<th width="5%" class="f2c_publish_up">
							<?php echo JHtml::_('grid.sort',  'COM_FORM2CONTENT_PUBLISH_UP', 'a.publish_up', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_publish_down_column', 0)) : ?>
						<th width="5%" class="f2c_publish_down">
							<?php echo JHtml::_('grid.sort',  'COM_FORM2CONTENT_PUBLISH_DOWN', 'a.publish_down', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_language_column')) : ?>
						<th width="5%" class="f2c_language">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
						</th>
						<?php endif; ?>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td class="f2c_pagination" colspan="<?php echo $this->numCols; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
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
					
					$ordering	= ($listOrder == 'a.ordering');
					$canEdit	= $user->authorise('core.edit', 'com_form2content.form.'.$item->id);
					$canCheckin = true;
					$canEditOwn	= $user->authorise('core.edit.own', 'com_form2content.form.'.$item->id) && $item->created_by == $userId;
					$canChange	= ($user->authorise('core.edit.state', 'com_form2content.form.'.$item->id) ||
								   ($user->authorise('form2content.edit.state.own', 'com_form2content.form.'.$item->id) && $item->created_by == $userId)) && 
								   $canCheckin;
					?>
					<tr class="f2c_row row<?php echo $i % 2; ?>">
						<?php if($this->menuParams->get('show_f2c_id_column')) : ?>
						<td class="center f2c_id">
							<?php echo (int) $item->id; ?>
						</td>	
						<?php endif; ?>		
						<?php if($this->menuParams->get('show_joomla_id_column')) : ?>
						<td class="center joomla_id">
							<?php echo empty($item->reference_id) ? '' : $item->reference_id; ?>
						</td>	
						<?php endif; ?>		
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="f2c_title">
							<?php if ($canEdit || $canEditOwn) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_form2content&task=form.edit&id='.$item->id);?>">
									<?php echo $this->escape($item->title); ?></a>
							<?php else : ?>
								<?php echo $this->escape($item->title); ?>
							<?php endif; ?>
						</td>
						<?php if($this->menuParams->get('show_published_column')) : ?>
						<td class="center f2c_published">
							<?php echo $this->legacyPublished(JHtml::_('jgrid.published', $itemState, $i, 'forms.', $canChange, 'cb', $item->publish_up, $item->publish_down)); ?>
						</td>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_featured_column')) : ?>
						<td class="center">
							<?php echo $this->legacyFeatured($item->featured, $i, $canChange); ?>
						</td>
						<?php endif; ?>										
						<?php if($this->menuParams->get('show_ordering')) : ?>				
						<td class="order f2c_ordering">
							<?php if ($canChange) : ?>
								<?php if ($saveOrder) :?>
									<?php if ($listDirn == 'asc') : ?>
										<span><?php echo $this->legacyOrdering($this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid), 'forms.orderup', 'JLIB_HTML_MOVE_UP', $ordering)); ?></span>
										<span><?php echo $this->legacyOrdering($this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i+1]->catid), 'forms.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering)); ?></span>
									<?php elseif ($listDirn == 'desc') : ?>
										<span><?php echo $this->legacyOrdering($this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid), 'forms.orderdown', 'JLIB_HTML_MOVE_UP', $ordering)); ?></span>
										<span><?php echo $this->legacyOrdering($this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i+1]->catid), 'forms.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering)); ?></span>
									<?php endif; ?>
								<?php endif; ?>
								<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
								<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
							<?php else : ?>
								<?php echo $item->ordering; ?>
							<?php endif; ?>
						</td>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_category')) : ?>
						<td class="center f2c_category">
							<?php echo $this->escape($item->category_title); ?>
						</td>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_author_column')) : ?>								
						<td class="center f2c_author">
							<?php echo $this->escape($item->author_name); ?>
						</td>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_created_column')) : ?>				
						<td class="center nowrap f2c_created">
							<?php echo JHTML::_('date',$item->created, $dateFormat); ?>
						</td>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_modified_column')) : ?>				
						<td class="center nowrap f2c_modified">
							<?php
							if($item->modified && ($item->modified != $this->nullDate))
							{
								echo JHTML::_('date',$item->modified, $dateFormat);
							} 
							?>
						</td>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_publish_up_column')) : ?>	
						<td class="center nowrap f2c_publish_up">
							<?php echo JHtml::_('date', $item->publish_up, $dateFormat); ?>
						</td>
						<?php endif; ?>
						<?php if($this->menuParams->get('show_publish_down_column')) : ?>
						<td class="center nowrap f2c_publish_down">
							<?php
							if($item->publish_down && ($item->publish_down != $this->nullDate))
							{
								echo JHtml::_('date',$item->publish_down, $dateFormat);
							} 
							?>
						</td>
						<?php endif; ?>												
						<?php if($this->menuParams->get('show_language_column')) : ?>				
						<td class="center f2c_language">
							<?php if ($item->language=='*'):?>
								<?php echo JText::alt('JALL','language'); ?>
							<?php else:?>
								<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('COM_FORM2CONTENT_UNDEFINED'); ?>
							<?php endif;?>
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
			</div>	
		</form>
	</div>
</div>