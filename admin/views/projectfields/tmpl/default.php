<?php 
defined('JPATH_PLATFORM') or die('Restricted acccess');

use \Joomla\Registry\Registry;

require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'shared.form2content.php');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'a.ordering';
$sortFields = $this->getSortFields();

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_form2content&task=projectfields.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'projectfieldsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_form2content&view=projectfields&projectid='.(int)$this->contentTypeId);?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
	<h2><?php echo $this->pageTitle; ?></h2>
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
		<table class="table table-striped" id="projectfieldsList">
			<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHtml::_('searchtools.sort', 'COM_FORM2CONTENT_FIELDNAME', 'a.fieldname', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHtml::_('searchtools.sort', 'COM_FORM2CONTENT_FIELD_CAPTION', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHtml::_('searchtools.sort', 'COM_FORM2CONTENT_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JText::_('COM_FORM2CONTENT_FIELDTYPE'); ?>
					</th>
					<th width="1%" class="nowrap hidden-phone">
						<?php echo JText::_('COM_FORM2CONTENT_FRONT_END_VISIBLE'); ?>
					</th>
					<th width="1%" class="nowrap hidden-phone">
						<?php echo JText::_('COM_FORM2CONTENT_REQUIRED_FIELD'); ?>
					</th>
					<th width="1%" class="nowrap hidden-phone">
						<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
				$item->max_ordering = 0; //??
				$ordering   		= ($listOrder == 'a.ordering');
				$canChange			= true;		
				$item->settings		= new Registry($item->settings);
				?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->projectid?>">
					<td class="order nowrap center hidden-phone">
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
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id);echo '<br/>';	?>
					</td>
					<td class="nowrap has-context">
						<a href="<?php echo JRoute::_('index.php?option=com_form2content&task=projectfield.edit&id=' . $item->id);?>" title="<?php echo JText::_('JACTION_EDIT');?>">
							<?php echo $this->escape($item->fieldname); ?>
						</a>
					</td>
					<td class="small hidden-phone">
						<?php echo JText::_($item->title); ?>
					</td>
					<td class="small hidden-phone">
						<?php echo $this->escape($item->description); ?>
					</td>
					<td class="small hidden-phone">
						<?php echo $this->escape($item->fieldtype); ?>
					</td>
					<td class="small hidden-phone">
						<?php echo $this->gridFrontVisible($item, $i, 'projectfields.', true, 'cb'); ?>						
					</td>
					<td class="small hidden-phone">
						<i class="icon-<?php echo $item->settings->get('requiredfield') ? 'publish' :  'unpublish'; ?>"></i>
					</td>
					<td class="center hidden-phone">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
		</table>
	<?php endif; ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="projectid" value="<?php echo $this->contentTypeId ?>" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo F2cViewHelper::displayCredits(); ?>
</div>
</form>
