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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$prayeradmin = new CWMPrayerAdmin;

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';
$assoc     = JLanguageAssociations::isEnabled();

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_cwmprayer&task=prayers.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'pcdevsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>
	<form action="<?php echo JRoute::_('index.php?option=com_cwmprayer&view=devotions'); ?>" method="post"
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
					<table class="table table-striped" id="pcdevsList">
						<thead>
						<tr>
							<th width="1%" class="nowrap center hidden-phone">
								<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
							</th>
							<th width="1%" class="nowrap center">
								<?php echo JHtml::_('grid.checkall'); ?>
							</th>
							<th class="nowrap center hidden-phone" width="5%">
								<?php echo JHTML::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
							</th>
							<th class="title">
								<?php echo JHTML::_('searchtools.sort', 'COM_CWMPRAYER_NAME', 'a.name', $listDirn, $listOrder); ?>
							</th>
							<th class="nowrap hidden-phone">
								<?php echo JHTML::_('searchtools.sort', 'COM_CWMPRAYER_FEED', 'a.feed', $listDirn, $listOrder); ?>
							</th>
							<th width="1%" class="nowrap center hidden-phone">
								<?php echo JHTML::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
							</th>
						</tr>
						</thead>
						<tfoot>
						<tr>
							<td colspan="6">
								<?php echo $this->pagination->getListFooter(); ?>
								<?php echo $prayeradmin->PrayerFooter(); ?>
							</td>
						</tr>
						</tfoot>
						<tbody>
						<?php
						foreach ($this->items as $i => $item)
						{
							$canCreate  = $user->authorise('core.create', 'com_cwmprayer.category.' . $item->catid);
							$canEdit    = $user->authorise('core.edit', 'com_cwmprayer.category.' . $item->catid);
							$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
							$canEditOwn = $user->authorise('core.edit.own', 'com_cwmprayer.category.' . $item->catid) && $item->created_by == $userId;
							$canChange  = $user->authorise('core.edit.state', 'com_cwmprayer.category.' . $item->catid) && $canCheckin;

							$item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_cwmprayer&task=edit&type=other&id=' . $item->catid);
							?>
							<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">
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
									<span class="sortable-handler<?php echo $iconClass; ?>">
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
										<?php echo JHtml::_('jgrid.published', $item->published, $i, 'managedevotions.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
										<?php // Create dropdown items and render the dropdown list.
										if ($canChange)
										{
											JHtml::_('actionsdropdown.' . ((int) $item->published === 2 ? 'un' : '') . 'archive', 'cb' . $i, 'managedevotions');
											JHtml::_('actionsdropdown.' . ((int) $item->published === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'managedevotions');
											echo JHtml::_('actionsdropdown.render', $this->escape($item->name));
										}
										?>
									</div>
								</td>
								<td class="small hidden-phone">
									<div class="pull-left">
										<?php if ($item->checked_out) : ?>
											<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'managedevotions.', $canCheckin); ?>
										<?php endif; ?>
										<?php if ($canEdit || $canEditOwn) : ?>
											<a href="<?php echo JRoute::_('index.php?option=com_cwmprayer&task=devotion.edit&id=' .
												(int) $item->id); ?>"><?php echo $this->escape($item->name); ?></a>
										<?php else : ?>
											<?php echo $this->escape($item->name); ?>
										<?php endif; ?>
										<div class="small">
											<?php echo JText::_('JCATEGORY') . ': ' . $this->escape($item->category_title); ?>
										</div>
									</div>
								</td>
								<td class="small hidden-phone">
									<?php echo $item->feed; ?>
								</td>
								<td class="hidden-phone">
									<?php echo $item->id; ?>
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

