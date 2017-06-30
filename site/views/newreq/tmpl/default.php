<?php
/* *************************************************************************************
Title          prayer Component for Joomla
Author         CWM Team as a fork from a component by Mike Leeper
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
Copyright      Christian Web Ministries
****************************************************************************************
No direct access*/
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidator');
jimport('joomla.filesystem.folder');

$app = JFactory::getApplication();
$input = $app->input;

/** @var \CWMPrayerSitePrayer $prayer */
$prayer = $this->prayer;
$prayer->PCgetAuth('post');

$document = JFactory::getDocument();
$document->addScript('media/com_cwmprayer/js/pc.js');

$conf          = JFactory::getConfig();
$config_editor = $this->config_editor;

$editorcontent = '';

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
	$erequired = "javascript:PCchgClassNameOnBlur('newemail');";
}
$js_script = "";
?>
    <script type="text/javascript">
		var enter_req = "<?php echo JText::_('CWMPRAYERENTERREQ');?>";
		var confirm_enter_email = "<?php echo JText::_('CWMPRAYERCONFIRMENTEREMAIL');?>";
		var enter_sec_code = "<?php echo JText::_('CWMPRAYERENTERSECCODE');?>";
		var editor = "<?php echo $config_editor;?>";
		var livesite = "<?php echo $livesite;?>";
    </script>
<?php

if (session_id() == "")
{
	session_start();
}

if ($input->getString('return_msg'))
{
	$prayer->PCReturnMsg($input->getString('return_msg'));
}
echo '<div>';
echo '<form method="post" action="' . $this->action . '" name="adminForm">';

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
	' onClick="javascript:if(document.adminForm.psend.checked){document.adminForm.sendpriv.value=0;}else{document.adminForm.sendpriv.value=1;}" />';
	echo '<span style="font-size: small; padding-left: 5px;">' . JText::_('CWMPRAYERPRIV') . '</span>';
	echo '</div>';
}
if (!$this->config_captcha_bypass || ($this->config_captcha_bypass && $user->guest))
{
	echo $prayer->PCgetCaptchaImg();
	if ($this->config_use_admin_alert == 1)
	{
		$js_script = "return validateNewE(" . $this->config_captcha . ",'" . $config_editor . "', livesite, this.form, 'pccomp')";
	}
	else
	{
		$js_script = "return validateNew(" . $this->config_captcha . ",'" . $config_editor . "', livesite, this.form, 'pccomp')";
	}
}
else
{
	if ($this->config_use_admin_alert == 1)
	{
		$js_script = "return validateNewE(0,'" . $config_editor . "', livesite, this.form, 'pccomp')";
	}
	else
	{
		$js_script = "return validateNew(0,'" . $config_editor . "', livesite, this.form, 'pccomp')";
	}
}

if ($config_editor == 'none' || !$editorenabled)
{
	echo "<div style=\"padding-left:10px;\"><br />" .
		"<button type=\"button\" class='btn' onclick=\"javascript:document.adminForm.valreq.value=document.adminForm.newrequest.value;" .
		$js_script . ";return false;\">";
}
else
{
	echo "<div style=\"padding-left:10px;\"><br /><button type=\"button\" class='btn' onclick=\"javascript:document.adminForm.valreq.value=" .
		$editorcontent . $js_script . ";return false;\">";
}
echo JText::_('CWMPRAYERSEND') . '</button>';
echo '<button type="submit" class="btn btn-primary validate">'.JText::_('CWMPRAYERSUBMIT').'</button>';
?> <input type="hidden" name="option" value="com_cwmprayer" />
    <input type="hidden" name="task" value="registration.register" />
<?php
echo '</div>';
echo '</fieldset>';
echo '<input type="hidden" name="sendpriv" id="sendpriv" size="5" class="inputbox" value="1" />';
echo '<span style="display:none;visibility:hidden;">';
echo '<input type="text" name="temail" size="5" class="inputbox" value="" />';
echo '<input type="text" name="formtime" size="5" class="inputbox" value="' . time() . '" />';
echo '</span>';
echo '<input type="hidden" name="valreq" size="5" class="inputbox" value="" />';
echo '<input type="hidden" name="requesterid" size="5" class="inputbox" value="' . $user->get('id') . '" />';
echo '<input type="hidden" name="jcap" id="jcap" class="inputbox" value="' . JFactory::getConfig()->get('captcha') . '" />';
echo '<input type="hidden" name="option" value="COM_CWMPRAYER" />';
echo '<input type="hidden" name="controller" value="prayer" />';
echo '<input type="hidden" name="task" value="newreqsubmit" />';
echo JHTML::_('form.token');
echo '</form>';
echo '</div><br />';
$prayer->PrayerFooter();
