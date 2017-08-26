<?php
/* *************************************************************************************
Title          prayer Component for Joomla
Author         Mike Leeper
License        This program is free software: you can redistribute it and/or modify
               it under the terms of the GNU General Public License as published by
               the Free Software Foundation, either version 3 of the License, or
               (at your option) any later version.
               This program is distributed in the hope that it will be useful,
               but WITHOUT ANY WARRANTY; without even the implied warranty of
               MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
               GNU General Public License for more details.
               You should have received a copy of the GNU General Public License
               along with this program.  If not, see <http://www.gnu.org/licenses/>.
Copyright      2006-2014 - Mike Leeper (MLWebTechnologies) 
****************************************************************************************
No direct access*/
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');

JHtml::_('behavior.tabstate');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', '#jform_catid', null, ['disable_search_threshold' => 0]);
JHtml::_('formbehavior.chosen', 'select');

$app   = JFactory::getApplication();
$input = $app->input;

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'prayer.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			Joomla.submitform(task);
		}
	}
");

/** @var \CWMPrayerSitePrayer $prayer */
$prayer = $this->prayer;

$document = JFactory::getDocument();
$document->addScript('media/com_cwmprayer/js/pc.js');

$conf          = JFactory::getConfig();
$config_editor = $this->config_editor;

if ($config_editor == 'default')
{
	$config_editor = $conf->get('editor');
	$user          = new JUser;
	$userparams    = $user->getParam($user->id);
	$usereditor    = $userparams->get('editor');

	if (!empty($usereditor))
	{
		$config_editor = $userparams->get('editor');
	}
}

$editorenabled = $prayer->PCcheckEditor($config_editor);

if (!$editorenabled)
{
	$config_editor = 'none';
}

$user                   = JFactory::getUser();
$config_use_admin_alert = $this->config_use_admin_alert;
$erequired              = "";

if ($config_use_admin_alert == 1)
{
	$erequired = "PCchgClassNameOnBlur('newemail');";
}

if (session_id() == "")
{
	session_start();
}

if ($input->getString('return_msg'))
{
	$prayer->PCReturnMsg($input->getString('return_msg'));
}
echo '<div>';
echo '<form action="' . $this->action . '" method="post" name="adminForm" id="adminForm" class="form-validate form-vertical">';

if ($this->config_show_page_headers)
{
	echo '<div class="componentheading"><h2>' . htmlentities($this->title) . ' - ' . htmlentities(JText::_('CWMPRAYERSUBMITREQUEST')) . '</h2></div>';
}

echo '<div>';
$prayer->buildPCMenu();
echo '</div><div>';
echo $prayer->writePCImage() . '</div><div>';
echo $prayer->writePCHeader($this->directions) . '</div>';
echo '<fieldset class="pcmod"><legend>' . htmlentities(JText::_('CWMPRAYERSUBMITREQUEST')) . '</legend>';
echo '<div>';
echo '<label for="newrequester">' . JText::_('CWMPRAYERNAME') . ': (' . htmlentities(JText::_('CWMPRAYERANONMSG')) . ')</label><br />';
echo '<input type="text" name="newrequester" id="newrequester" size="54" class="inputbox" value="' . $user->get('name') . '" /></div>';
if ($this->email_option == '1')
{
	echo '<div style="padding-top:4px;"><label for="newemail">' . htmlentities(JText::_('CWMPRAYEREMAIL')) . ':';

	if ($config_use_admin_alert != 1)
	{
		echo ' (' . htmlentities(JText::_('CWMPRAYEROPTIONAL')) . ')';
	}

	echo '</label><br />';
	echo '<input type="text" name="newemail" id="newemail" size="54" class="inputbox" value="' .
		$user->get('email') . '" onBlur="' . $erequired . '" /></div>';
}
echo '<div style="padding-top:4px;"><label for="newtitle">' . htmlentities(JText::_('CWMPRAYERREQTITLE')) . ': </label><br />';
echo '<input type="text" name="newtitle" id="newtitle" size="40" class="inputbox" value="" /></div>';
echo '<div style="padding-top:4px;"><label for="newtopic">' . htmlentities(JText::_('CWMPRAYERREQTOPIC')) . ': </label><br />';
$newtopicarray = $prayer->PCgetTopics();
echo '<select name="newtopic">';
$topics = '<option value="">' . htmlentities(JText::_('CWMPRAYERSELECTTOPIC')) . '</option>';

foreach ($newtopicarray as $nt)
{
	$topics .= '<option value="' . $nt['val'] . '">' . $nt['text'] . '</option>';
}

echo $topics;
echo '</select></div>';
echo '<div style="padding-top:10px;"><label for="newrequest">' . htmlentities(JText::_('CWMPRAYERREQUEST')) . ':</label></div>';
echo '<div>';
echo $prayer->PCgetEditorBox();
echo '</div>';

if ($this->show_priv_option == '1')
{
	echo '<div style="white-space:nowrap;margin-left:5px;padding-left:0;padding-bottom:8px;text-align:left;font-weight:bold;">';
	echo '<input type="checkbox" name="psend" id="psend"' .
		' onClick="if(document.adminForm.psend.checked){document.adminForm.sendpriv.value=0;}else{document.adminForm.sendpriv.value=1;}" />';
	echo '<span style="font-size: small; padding-left: 5px;">' . JText::_('CWMPRAYERPRIV') . '</span>';
	echo '</div>';
}

?>
	<div class="btn-toolbar">
		<div class="btn-group">
			<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('prayer.newreqsubmit')">
				<span class="icon-ok"></span><?php echo JText::_('CWMPRAYERSEND') ?>
			</button>
		</div>
		<div class="btn-group">
			<button type="button" class="btn" onclick="Joomla.submitbutton('prayer.cancel')">
				<span class="icon-cancel"></span><?php echo JText::_('JCANCEL') ?>
			</button>
		</div>
	</div>
<?php
echo '</fieldset>';
echo '<input type="hidden" name="sendpriv" id="sendpriv" size="5" class="inputbox" value="1" />';
//echo '<span style="display:none;visibility:hidden;">';
//echo '<input type="hidden" name="temail" size="5" class="inputbox" value="" />';
//echo '<input type="hidden" name="formtime" size="5" class="inputbox" value="' . time() . '" />';
//echo '</span>';
echo '<input type="hidden" name="valreq" size="5" class="inputbox" value="" />';
echo '<input type="hidden" name="requesterid" size="5" class="inputbox" value="' . $user->get('id') . '" />';
echo '<input type="hidden" name="jcap" id="jcap" class="inputbox" value="' . JFactory::getConfig()->get('captcha') . '" />';
echo '<input type="hidden" name="controller" value="prayer" />';
echo '<input type="hidden" name="task" value="newreqsubmit" />';
echo JHTML::_('form.token');
echo '</form>';
echo '</div><br />';
$prayer->PrayerFooter();
