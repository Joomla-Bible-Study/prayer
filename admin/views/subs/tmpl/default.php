<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user        = JFactory::getUser();
$prayeradmin = new CWMPrayerAdmin;
$userId      = $user->get('id');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
	<form action="<?php echo JRoute::_('index.php?option=com_cwmprayer&view=subs'); ?>" method="post"
	      name="adminForm" id="adminForm">
		<?php if (!empty($this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span12">
			<?php else : ?>
			<div id="j-main-container">
				<?php endif; ?>
				<?php echo JLayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
				<div class="clearfix"></div>
				<table class="table table-striped" id="pcsubsList">
					<thead>
					<tr>
						<th width="1%" class="hidden-phone">
							<input type="checkbox" name="checkall-toggle" value=""
							       title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
							       onclick="Joomla.checkAll(this)"/>
						</th>
						<th class="hidden-phone">
							<?php echo JHTML::_('grid.sort', 'CWMPRAYERSENDEMAIL', 'a.email', $listDirn, $listOrder, 'subs'); ?>
						</th>
						<th class="hidden-phone">
							<?php echo JHTML::_('grid.sort', 'JDATE', 'a.date', $listDirn, $listOrder, 'subs'); ?>
						</th>
						<th class="title" width="50">&nbsp;</th>
						<th class="hidden-phone center">
							<?php echo JHTML::_('grid.sort', 'COM_CWMPRAYER_APPROVED', 'a.approved', $listDirn, $listOrder, 'subs'); ?>
						</th>
						<th class="hidden-phone center">
							<?php echo JHTML::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder, 'subs'); ?>
						</th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="7">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
					<tbody>
					<?php
					foreach ($this->items as $i => $item)
					{
						?>
						<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->id; ?>">
							<td class="center hidden-phone" width="5">
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							</td>
							<td class="small hidden-phone"><?php echo $item->email; ?></td>
							<td class="small hidden-phone"><?php echo $item->date; ?></td>
							<td align="center">&nbsp;</td>
							<?php
							$approvedimg = [];
							$approvedimg[] = '<a class="btn btn-micro active" rel="tooltip"';
							if ($item->approved)
							{
								$approvedimg[] = ' href="javascript:void(0);" onclick="return listItemTask(\'cb' . $i . '\', \'subs.unapprove\')"';
								$approvedimg[] = ' title="' . JText::_('COM_CWMPRAYER_UNAPPROVE') . '">';
								$approvedimg[] = '<i class="icon-publish">';
							}
							else
							{
								$approvedimg[] = ' href="javascript:void(0);" onclick="return listItemTask(\'cb' . $i . '\', \'subs.approve\')"';
								$approvedimg[] = ' title="' . JText::_('COM_CWMPRAYER_APPROVE') . '">';
								$approvedimg[] = '<i class="icon-unpublish">';
							}

							$approvedimg[] = '</i>';
							$approvedimg[] = '</a>';
							?>
							<td class="center small hidden-phone"><?php echo implode($approvedimg); ?></td>
							<td class="center hidden-phone">
								<?php echo (int) $item->id; ?>
							</td>
						</tr>
						<?php
					} ?>
					</tbody>
				</table>
				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="boxchecked" value="0"/>
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
				<?php echo JHtml::_('form.token'); ?>
			</div>
	</form><br/>
<?php
echo $prayeradmin->PrayerFooter();
