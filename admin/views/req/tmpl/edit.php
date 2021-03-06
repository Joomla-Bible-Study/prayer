<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */
defined('_JEXEC') or die('');

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
JHtml::_('formbehavior.chosen', 'select');

$prayeradmin = new CWMPrayerAdmin;
?>
<style type="text/css">
	.icon-32-print {
		background-image: url('templates/bluestork/images/toolbar/icon-32-print.png');
		}
</style>
<?php
$print_link = "index.php?option=com_cwmprayer&amp;view=req&amp;id=" . (int) $this->form->getInput('id') . "&amp;tmpl=component";

$sb = JToolBar::getInstance('toolbar');

$sb->appendButton('Popup', 'print', 'Print', $print_link, 600, 380);
?>

<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function (task) {
		Joomla.submitform(task, document.getElementById('req-form'));
	}
</script>

<div class="span10 form-horizontal">
	<form action="<?php echo JRoute::_('index.php?option=com_cwmprayer&view=edit&id=' .
		(int) $this->item->id); ?>" method="post" name="adminForm" id="req-form">
		<div class="tab-content">
			<div class="tab-pane active" id="details">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('requester'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('requester'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('email'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('email'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('date'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('date'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('topic'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('topic'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('request'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('request'); ?></div>
				</div>
			</div>
		</div>
		<div class="clr"></div>
		<?php echo $this->form->getInput('id'); ?>
		<input type="hidden" name="task" value=""/>
		<?php echo JHTML::_('form.token'); ?>
	</form>
	<?php
	echo '<br /><br />';
	echo $prayeradmin->PrayerFooter();
	?>
</div>