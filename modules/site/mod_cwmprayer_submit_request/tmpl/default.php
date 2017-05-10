<?php

defined('_JEXEC') or die;
$user       = JFactory::getUser();
$access_gid = $user->get('gid');
$lang       = Jfactory::getLanguage();
$lang->load('com_cwmprayer', JPATH_SITE);

if (file_exists(JPATH_ROOT . "/administrator/components/com_cwmprayer/config.xml"))
{
	require_once JPATH_ROOT . "/components/com_cwmprayer/helpers/admin_includes.php";
	require_once JPATH_ROOT . "/components/com_cwmprayer/helpers/prayer.php";

	$prayercentermsr        = new CWMPrayerSitePrayer;
	$pc_rights              = $prayercentermsr->intializePCRights();
	$pcConfig               = $prayercentermsr->pcConfig;
	$config_captcha         = $pcConfig['config_captcha'];
	$config_captcha_bypass  = $pcConfig['config_captcha_bypass_4member'];
	$show_email_option      = $pcConfig['config_email_option'];
	$show_priv_option       = $pcConfig['config_show_priv_option'];
	$maxattempts            = $pcConfig['config_captcha_maxattempts'];
	$config_use_admin_alert = $pcConfig['config_use_admin_alert'];
	$livesite               = JURI::base();
	?>
	<script language="JavaScript" type="text/javascript">
		var enter_req = '<?php echo JText::_('CWMPRAYERENTERREQ');?>';
		var confirm_enter_email = '<?php echo JText::_('CWMPRAYERCONFIRMENTEREMAIL');?>';
		var enter_sec_code = '<?php echo JText::_('CWMPRAYERENTERSECCODE');?>';
		var enter_valid_email = '<?php echo JText::_('CWMPRAYERINVALIDEMAIL');?>';
		var livesite = '<?php echo $livesite;?>';
	</script>
	<style type="text/css">
		div#pcmodreqsub input[type="text"] {
			width: 90%;
			}

		div#pcmodreqsub textarea {
			width: 90%;
			}

		div#pcmodreqsub select {
			width: 95%;
			}
	</style>
	<?php
	$document = JFactory::getDocument();
	$document->addScript('media/com_cwmprayer/js/pc.js');

	if ($pc_rights->get('pc.post'))
	{
		$id        = $user->name;
		$email     = $user->email;
		$sendpriv  = 1;
		$subpraise = 0;
		$js_script = "";

		if (session_id() == "")
		{
			session_start();
		}

		echo '<div class="moduletable' . $moduleclasssfx . '" id="pcmodreqsub">';
		echo '<a name="pcmsr"></a>';
		echo '<form method="post" action="index.php?option=com_cwmprayer&modtype=return_submsg&mod=pcmsr" name="pcmsr">';
		echo '<label for="newrequester">' . JText::_('CWMPRAYERNAME') . ': (' . JText::_('CWMPRAYEROPTIONAL') . ')</label>';
		echo '<input type="text" name="newrequester" id="newrequester" value="' . $id . '" />';

		if ($show_email_option == '1')
		{
			echo '<label for="newemail">' . JText::_('CWMPRAYEREMAIL') . ': ';

			if ($config_use_admin_alert != 1)
			{
				echo '(' . JText::_('CWMPRAYEROPTIONAL') . ')';
			}

			echo '</label>';
			echo '<input type="text" name="newemail" id="newemail" value="' . $email . '" />';
		}

		echo '<label for="newtitle">' . JText::_('CWMPRAYERREQTITLE') . '</label>';
		echo '<INPUT TYPE="TEXT" name="newtitle" id="newtitle" class="inputbox" value="" />';
		echo '<label for="newtopic">' . JText::_('CWMPRAYERREQTOPIC') . '</label>';
		$newtopicarray = $prayercentermsr->PCgetTopics();
		echo '<select name="newtopic" id="newtopic">';
		$topics = '<option value="">' . JText::_('CWMPRAYERSELECTTOPIC') . '</option>';

		foreach ($newtopicarray as $nt)
		{
			$topics .= '<option value="' . $nt['val'] . '">' . $nt['text'] . '</option>';
		}

		echo $topics;
		echo '</select><br /><br />';
		echo '<label for="newrequest">' . htmlentities(JText::_('CWMPRAYERREQUEST')) . ':</label>';
		echo '<textarea name="newrequest" id="mnewrequest" class="inputbox" rows="8" style="resize:none;"></textarea>';
		echo '<input type="hidden" name="sendpriv" size="5" class="inputbox" value="' . $sendpriv . '" />';
		echo '<span style="display:none;visibility:hidden;">';
		echo '<input type="text" name="temail" size="5" class="inputbox" value="" />';
		echo '<input type="text" name="formtime" size="5" class="inputbox" value="' . time() . '\" />';
		echo '</span>';

		if ($show_priv_option == '1')
		{
			echo '<input type="checkbox" class=radio style="margin:0;padding:0;" name="msend"' .
				' id="msend" onClick="javascript:if(document.adminForm.msend.checked){document.adminForm.sendpriv.value=0;}else{document.' .
				'adminForm.sendpriv.value=1;}" />';
			echo '&nbsp;<span style="font-size:x-small;white-space:nowrap;">' . JText::_('CWMPRAYERPRIV') . '</span>';
		}

		$user = JFactory::getUser();

		if ((!$config_captcha_bypass && $config_captcha) || ($config_captcha_bypass && $user->get('id') == 0 && $config_captcha))
		{
			if ($config_use_admin_alert == 1)
			{
				$js_script = "document.getElementById('valreq').value=document.getElementById('mnewrequest').value;return validateNewE(" .
					$config_captcha . ", 'none', livesite, this.form, 'pcmsr');";
			}
			else
			{
				$js_script = "document.getElementById('valreq').value=document.getElementById('mnewrequest').value;return validateNew(" .
					$config_captcha . ", 'none', livesite, this.form, 'pcmsr');";
			}

			if ($config_captcha)
			{
				echo $prayercentermsr->PCgetCaptchaImg('pcmsr', 'pcmsr');
			}
		}
		else
		{
			if ($config_use_admin_alert == 1)
			{
				$js_script = 'document.getElementById(\'valreq\').value=document.getElementById(\'mnewrequest\').value;return validateNewE(0,' .
					' \'none\', livesite, this.form, \'pcmsr\');';
			}
			else
			{
				$js_script = 'document.getElementById(\'valreq\').value=document.getElementById(\'mnewrequest\').value;return validateNew(0,' .
					' \'none\', livesite, this.form, \'pcmsr\');';
			}

			echo '<br /><br />';
		}

		echo '&nbsp;<button type="button" onclick="javascript:' . $js_script . '">';
		echo JText::_('CWMPRAYERSUBMIT') . '</button>';
		echo '<input type="hidden" name="valreq" id="valreq" value="" />';
		echo '<input type="hidden" name="option" value="com_cwmprayer" />';
		echo '<input type="hidden" name="controller" value="prayer" />';
		echo '<input type="hidden" name="task" value="newreqsubmit" />';
		$defaultcaptcha = JFactory::getConfig()->get('captcha');
		echo '<input type="hidden" name="jcap" id="jcap" class="inputbox" value="' . $defaultcaptcha . '" />';
		echo JHTML::_('form.token');
		echo '</form>';
		$return_submsg = "";

		if (JFactory::getApplication()->input->get('return_submsg'))
		{
			$return_submsg = JFactory::getApplication()->input->get('return_submsg');
		}

		echo '<div style="text-align:center; color:red;font-weight: bold;">' . wordwrap($return_submsg, 22, "<br />") . '</div>';
		echo '</div>';
	}
}
else
{
	if (!defined('CWMPRAYERCOMNOTINSTALL'))
	{
		define('CWMPRAYERCOMNOTINSTALL', 'Prayer Component Not Installed');
	}

	echo '<div style="text-align:center; color:red;font-weight: bold;">' . JText::_('CWMPRAYERCOMNOTINSTALL') . '</div>';
}
