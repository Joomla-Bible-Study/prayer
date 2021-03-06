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

$prayeradmin = new CWMPrayerAdmin;

$newtopicarray = $prayeradmin->PCgetTopics();

$user = JFactory::getUser();

$userId = $user->get('id');

$listOrder = $this->escape($this->state->get('list.ordering'));

$listDirn = $this->escape($this->state->get('list.direction'));

$canOrder = $user->authorise('core.edit.state', 'com_cwmprayer.reqs');

/** @var CWMPrayerViewReqs $this */
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_cwmprayer&task=requests.saveOrderAjax&tmpl=component';

	JHtml::_('sortablelist.sortable', 'pcplansList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_cwmprayer&view=reqs'); ?>" method="post"
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
			<table class="table table-striped" id="pcplansList">
				<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>',
							'a.ordering', $listDirn, $listOrder, 'reqs', 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value=""
						       title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
						       onclick="Joomla.checkAll(this)"/>
					</th>

					<th width="5%" class="hidden-phone nowrap">
						<?php echo JHTML::_('grid.sort', 'JSTATUS', 'a.publishstate', $listDirn, $listOrder, 'reqs'); ?>
					</th>
					<th class="hidden-phone">
						<?php echo JHTML::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder, 'reqs'); ?>
					</th>
					<th class="hidden-phone">
						<?php echo JHTML::_('grid.sort', 'COM_CWMPRAYER_REQUESTER', 'a.requester', $listDirn, $listOrder, 'reqs'); ?>
					</th>
					<th class="hidden-phone">
						<?php echo JHTML::_('grid.sort', 'COM_CWMPRAYER_EMAIL_ADDRESS', 'a.email', $listDirn, $listOrder, 'reqs'); ?>
					</th>
					<th class="hidden-phone">
						<?php echo JHTML::_('grid.sort', 'COM_CWMPRAYER_TOPIC', 'a.topic', $listDirn, $listOrder, 'reqs'); ?>
					</th>
					<th class="hidden-phone">
						<?php echo JHTML::_('grid.sort', 'COM_CWMPRAYER_REQUEST', 'a.request', $listDirn, $listOrder, 'reqs'); ?>
					</th>
					<th class="hidden-phone">
						<?php echo JHTML::_('grid.sort', 'COM_CWMPRAYER_DATE_TIME', 'a.datetime', $listDirn, $listOrder, 'reqs'); ?>
					</th>
					<th class="hidden-phone">
						<?php echo JHTML::_('grid.sort', 'COM_CWMPRAYER_DISPLAY', 'a.displaystate', $listDirn, $listOrder, 'reqs'); ?>
					</th>
					<th width="5%" class="hidden-phone nowrap">
						<?php echo JHTML::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder, 'reqs'); ?>
					</th>
				</tr>
				</thead>
				<tfoot>
				<tr>
					<td colspan="10">
						<?php echo $this->pagination->getListFooter(); ?>
						<?php echo $prayeradmin->PrayerFooter(); ?>
					</td>
				</tr>
				</tfoot>
				<tbody>
				<?php
				foreach ($this->items as $i => $item)
				{
					$item->max_ordering = 0;
					$ordering           = ($listOrder == 'a.ordering');
					$canEdit            = $user->authorise('core.edit', 'com_content.article.' . $item->id);
					$canCheckin         = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					$canChange          = $user->authorise('core.edit.state', 'com_content.article.' . $item->id) && $canCheckin;

					$request = $item->request;
					$request = strip_tags($request, "<i><strong><u><em><strike>");
					$request = stripslashes($request);

					if (strlen($request) > 50)
					{
						$request = substr($request, 0, 48) . " ...";
					}

					$ordering = ($listOrder == 'a.ordering');

					$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;

					$canChange = true;

					?>
					<tr>
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
								       value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
							<?php endif; ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php echo JHtml::_('jgrid.published', $item->publishstate, $i, 'reqs.', $canChange,
									'cb'); ?>
								<?php // Create dropdown items and render the dropdown list.
								if ($canChange)
								{
									JHtml::_('actionsdropdown.' . ((int) $item->publishstate === 2 ? 'un' : '') . 'archive', 'cb' . $i, 'reqs');
									JHtml::_('actionsdropdown.' . ((int) $item->publishstate === -2 ? 'un' : '') .
										'trash', 'cb' . $i, 'reqs');
									echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
								}
								?>
							</div>
						</td>
						<td class="has-context">
							<div class="pull-left break-word">
								<?php if ($item->checked_out) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'articles.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($canEdit) : ?>
									<a class="hasTooltip"
									   href="<?php echo JRoute::_('index.php?option=com_cwmprayer&task=req.edit&id=' .
										   $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
										<?php echo $this->escape($item->title); ?></a>
								<?php else : ?>
								<span title="<?php echo $this->escape($item->title); ?></span>
									<?php endif; ?>
								</div>
							</td>
							<td class=" small hidden-phone"><?php echo $item->requester; ?></td>
						<td class="small hidden-phone"><?php echo $item->email; ?></td>
						<td class="small hidden-phone"><?php echo $newtopicarray[$item->topic + 1]['text']; ?></td>
						<td class="small hidden-phone">
							<?php echo $this->escape(JText::_($request)); ?>
							<?php if ($item->checked_out) : ?><br/>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, '', $canCheckin); ?>
							<?php endif; ?>
						</td>

						<td class="small hidden-phone"><?php echo $item->datetime; ?></td>

						<?php
						$displayimg = [];

						$displayimg[] = '<a class="btn btn-micro active" rel="tooltip"';

						if ($item->displaystate)
						{
							$displayimg[] = ' href="javascript:void(0);" onclick="return listItemTask(\'cb' . $i . '\', \'req.hidereq\')"';
							$displayimg[] = ' title="' . addslashes(htmlspecialchars(JText::_('Hide Request'))) . '">';
							$displayimg[] = '<i class="icon-publish">';
						}
						else
						{
							$displayimg[] = ' href="javascript:void(0);" onclick="return listItemTask(\'cb' . $i . '\', \'req.displayreq\')"';
							$displayimg[] = ' title="' . addslashes(htmlspecialchars(JText::_('Display Request'))) . '">';
							$displayimg[] = '<i class="icon-unpublish">';

						}

						$displayimg[] = '</i>';
						$displayimg[] = '</a>';
						?>
						<td class="small hidden-phone">
							<?php echo implode($displayimg); ?>
						</td>
						<td class="center hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
			<?php echo JHTML::_('form.token'); ?>
		</div>
	</div>
</form>