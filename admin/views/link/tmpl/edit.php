<?php/** * prayer Component for Joomla * By Mike Leeper * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL * */// No direct accessdefined('_JEXEC') or die('Restricted access');JHtml::_('behavior.formvalidator');JHtml::_('behavior.keepalive');JHtml::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));JHtml::_('formbehavior.chosen', 'select');$prayeradmin = new PrayerAdmin;?>    <script language="javascript" type="text/javascript">		Joomla.submitbutton = function (task) {			Joomla.submitform(task, document.getElementById('pclink-form'));		}    </script>    <div class="span10 form-horizontal">        <form action="<?php echo JRoute::_('index.php?option=com_prayer&view=link&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="pclink-form">            <div class="tab-content">                <div class="tab-pane active" id="details">                    <div class="control-group">                        <div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>                        <div class="controls"><?php echo $this->form->getInput('name'); ?></div>                    </div>                    <div class="control-group">                        <div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>                        <div class="controls"><?php echo $this->form->getInput('alias'); ?></div>                    </div>                    <div class="control-group">                        <div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>                        <div class="controls"><?php echo $this->form->getInput('published'); ?></div>                    </div>                    <div class="control-group">                        <div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>                        <div class="controls"><?php echo $this->form->getInput('catid'); ?></div>                    </div>                    <div class="control-group">                        <div class="control-label"><?php echo $this->form->getLabel('url'); ?></div>                        <div class="controls"><?php echo $this->form->getInput('url'); ?></div>                    </div>                    <div class="control-group">                        <div class="control-label"><?php echo $this->form->getLabel('target'); ?></div>                        <div class="controls"><?php echo $this->form->getInput('target'); ?></div>                    </div>                </div>            </div>            <div class="clr"></div>            <input type="hidden" name="task" value=""/>			<?php echo JHTML::_('form.token'); ?>        </form><?phpecho $prayeradmin->PrayerFooter();