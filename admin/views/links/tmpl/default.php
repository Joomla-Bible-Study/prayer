<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */
defined('_JEXEC') or die('Restricted access');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$prayeradmin = new CWMPrayerAdmin;

$user = JFactory::getUser();

$userId = $user->get('id');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$canOrder  = $user->authorise('core.edit.state', 'com_preyer.managelink');
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_cwmprayer&task=links.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'pclinksList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>
	<form action="<?php echo JRoute::_('index.php?option=com_cwmprayer&view=links'); ?>" method="post"
	      name="adminForm" id="adminForm">
		<?php if (!empty($this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
			<?php else : ?>
			<div id="j-main-container">
				<?php endif; ?>
				<?php echo JLayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
				<div class="clearfix"></div>
				<?php if (empty($this->items)) : ?>
					<div class="alert alert-no-items">
						<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
					<table class="table table-striped" id="pclinksList">
						<thead>
						<tr>
							<th width="1%" class="nowrap center hidden-phone">
								<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
							</th>
							<th width="1%" class="hidden-phone">
								<?php echo JHtml::_('grid.checkall'); ?>
							</th>
							<th width="1%" class="nowrap center">
								<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
							</th>
							<th class="title">
								<?php echo JHTML::_('searchtools.sort', 'COM_CWMPRAYER_NAME', 'a.name', $listDirn, $listOrder); ?>
							</th>
							<th class="nowrap hidden-phone">
								<?php echo JHTML::_('searchtools.sort', 'COM_CWMPRAYER_URL', 'a.url', $listDirn, $listOrder); ?>
							</th>
							<th class="nowrap hidden-phone">
								<?php echo JHTML::_('searchtools.sort', 'JCATEGORY', 'a.catid', $listDirn, $listOrder); ?>
							</th>
							<th width="1%" class="nowrap center hidden-phone">
								<?php echo JHTML::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
							</th>
						</tr>
						</thead>
						<tfoot>
						<tr>
							<td colspan="7">
								<?php echo $this->pagination->getListFooter(); ?>
								<?php echo $prayeradmin->PrayerFooter(); ?>
							</td>
						</tr>
						</tfoot>
						<tbody>
						<?php
						foreach ($this->items as $i => $item)
						{
							$ordering   = ($listOrder == 'a.ordering');
							$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
							$canChange  = $user->authorise('core.edit.state', 'com_cwmcwmprayer.links.' . $item->catid) && $canCheckin;
							?>
							<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo md5($item->catid); ?>">
								<td class="order nowrap center hidden-phone">
									<?php
									$iconClass = '';
									if (!$canChange)
									{
										$iconClass = ' inactive';
									}
									elseif (!$saveOrder)
									{
										$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED');
									}
									?>
									<span class="sortable-handler<?php echo $iconClass ?>">
								<span class="icon-menu"></span>
							</span>
									<?php if ($canChange && $saveOrder) : ?>
										<input type="text" style="display:none" name="order[]" size="5"
										       value="<?php echo $item->ordering; ?>"
										       class="width-20 text-area-order "/>
									<?php endif; ?>
								</td>
								<td class="center">
									<?php echo JHtml::_('grid.id', $i, $item->id); ?>
								</td>
								<td class="center">
									<div class="btn-group">
										<?php echo JHtml::_('jgrid.published', $item->published, $i, 'links.', $canChange, 'cb'); ?>
									</div>
								</td>
								<td class="has-context">
									<a class="hasTooltip"
									   href="<?php echo JRoute::_('index.php?option=com_cwmprayer&task=link.edit&id=' .
										   $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
										<?php echo stripslashes($this->escape($item->name)); ?></a></span>
									<?php if ($item->checked_out) : ?><br/>
										<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'link.', $canCheckin); ?>
									<?php endif; ?>
								</td>
								<td class="small hidden-phone"><?php echo JText::_($item->url); ?></td>

								<td class="small hidden-phone"><?php echo JText::_($item->category_title); ?></td>
								<td class="hidden-phone">
									<?php echo (int) $item->id; ?>
								</td>
							</tr>
							<?php
						}
						?>
						</tbody>
					</table>
				<?php endif; ?>
				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="boxchecked" value="0"/>
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
				<?php echo JHTML::_('form.token'); ?>
			</div>
	</form>
