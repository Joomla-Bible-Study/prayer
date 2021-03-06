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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.tooltip');

$field = JInput::getCmd('field');

$function = 'jSelectUser_' . $field;

$listOrder = $this->escape($this->lists['order']);
$listDirn  = $this->escape($this->lists['order_Dir']);
?>
<form action="<?php echo JRoute::_('index.php?option=com_cwmprayer&view=users&layout=modal&tmpl=component&groups=' .
	JInput::get('groups', '', 'default', 'BASE64') . '&excluded=' .
	JInput::get('excluded', '', 'default', 'BASE64')); ?>"
      method="post" name="adminForm" id="adminForm">
	<fieldset class="filter">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER'); ?></label>
				<input type="text" name="filter_search" id="filter_search"
				       value="<?php echo $this->escape($this->lists['search']); ?>" size="40"
				       title="<?php echo JText::_('Search'); ?>"/>
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button"
				        onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
				<button type="button"
				        onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('',
						        '<?php echo JText::_('JLIB_FORM_SELECT_USER') ?>');"><?php echo JText::_('JOPTION_NO_USER') ?></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="filter_group_id"
				       class="element-invisible"><?php echo JText::_('Filter By Group'); ?></label>
				<?php echo JHtml::_('access.usergroup', 'filter_group_id', $this->groupId, 'onchange="this.form.submit()"'); ?>
			</div>
		</div>
	</fieldset>
	<table class="table table-striped table-condensed">
		<thead>
		<tr>
			<th class="left">
				<?php echo JHtml::_('grid.sort', 'Name', 'a.name', $listDirn, $listOrder); ?>
			</th>
			<th class="nowrap" width="25%">
				<?php echo JHtml::_('grid.sort', 'Username', 'a.username', $listDirn, $listOrder); ?>
			</th>
			<th class="nowrap" width="25%">
				<?php echo JHtml::_('grid.sort', 'Email Address', 'a.email', $listDirn, $listOrder); ?>
			</th>
			<th class="nowrap" width="25%">
				<?php echo JHtml::_('grid.sort', 'Group', 'group_names', $listDirn, $listOrder); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="15">
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
					<a class="pointer"
					   onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->id; ?>',
							   '<?php echo $this->escape(addslashes($item->name)); ?>', '<?php echo $this->escape(addslashes($item->email)); ?>');">
						<?php echo $item->name; ?></a>
				</td>
				<td align="center">
					<?php echo $item->username; ?>
				</td>
				<td align="center">
					<?php echo $item->email; ?>
				</td>
				<td align="center">
					<?php echo nl2br($item->group_names); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="field" value="<?php echo $this->escape($field); ?>"/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
