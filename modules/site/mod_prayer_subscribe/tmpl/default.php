<?php

defined('_JEXEC') or die;
$user = JFactory::getUser();
$lang = Jfactory::getLanguage();
$lang->load('com_cwmprayer', JPATH_SITE);

if (file_exists(JPATH_ROOT . "/administrator/components/com_cwmprayer/config.xml"))
{
	require_once JPATH_ROOT . "/components/com_cwmprayer/helpers/admin_includes.php";
	require_once JPATH_ROOT . "/components/com_cwmprayer/helpers/prayer.php";

	$prayercentermsub      = new PrayerSitePrayer;
	$pcConfig              = $prayercentermsub->pcConfig;
	$pc_rights             = $prayercentermsub->intializePCRights();
	$config_captcha        = $pcConfig['config_captcha'];
	$config_captcha_bypass = $pcConfig['config_captcha_bypass_4member'];
	$show_subscribe        = $pcConfig['config_show_subscribe'];
	$livesite              = JURI::base();
	?>
	<script language="JavaScript" type="text/javascript">
		var enter_email = '<?php echo JText::_('CWMPRAYERENTEREMAIL');?>';
		var enter_sec_code = '<?php echo JText::_('CWMPRAYERENTERSECCODE');?>';
		var enter_valid_email = '<?php echo JText::_('CWMPRAYERINVALIDEMAIL');?>';
		var livesite = '<?php echo $livesite;?>';
	</script>
	<style type="text/css">
		div#pcmodsub input[type="text"] {
			width: 170px;
			}
	</style>
	<?php
	$document = JFactory::getDocument();
	$document->addScript('media/com_cwmprayer/js/pc.js');
	$js_script = "";

	if (session_id() == "")
	{
		session_start();
	}

	if ($show_subscribe == 1 && $pc_rights->get('pc.subscribe') == 1)
	{
		echo '<div class="moduletable' . $moduleclasssfx . '" id="pcmodsub">';
		echo '<a name="pcmsub"></a>';
		echo '<form method="post" action="index.php?option=com_cwmprayer&modtype=return_subscribmsg&mod=pcmsub" name="msub">';
		echo '<label for="newsubscribe">' . htmlentities(JText::_('CWMPRAYEREMAIL')) . ': </label>';
		echo '<input type="text" name="newsubscribe" id="newsubscribe" class="inputbox" value="' . $user->email . '" />';
		echo '<span style="white-space:nowrap;"><input type="radio" class="radio" style="padding-top:0;padding-left:0 !important;"' .
			' name="subscribe" value="subscribesubmit" onClick="javascript:document.msub.task.value=this.value;" checked="checked" />' .
			JText::_('CWMPRAYERSUBSCRIBE') . '</span>';
		echo '<span style="white-space:nowrap;"><input type="radio" class="radio" name="subscribe" value="unsubscribesubmit"' .
			' style="padding-top:0;padding-left:6px !important;" onClick="javascript:document.msub.task.value=this.value;" />' .
			JText::_('CWMPRAYERUNSUBSCRIBE') . '</span>';

		if ((!$config_captcha_bypass && $config_captcha) || ($config_captcha_bypass && $user->get('id') == 0 && $config_captcha))
		{
			$js_script = 'return validateSub(' . $config_captcha . ', livesite, this.form, \'pcmsub\');';
			echo $prayercentermsub->PCgetCaptchaImg('pcmsub', 'msub');
		}
		else
		{
			$js_script = 'return validateSub(0, livesite, this.form, \'pcmsub\');';
			echo '<br /><br />';
		}

		echo '&nbsp;<button type="button" onclick="javascript:' . $js_script . '">';
		echo JText::_('CWMPRAYERSUBMIT') . '</button>';
		echo '<input type="hidden" name="option" value="com_cwmprayerr" />';
		echo '<input type="hidden" name="controller" value="prayer" />';
		echo '<input type="hidden" name="task" value="subscribesubmit" />';
		$defaultcaptcha = JFactory::getConfig()->get('captcha');
		echo '<input type="hidden" name="jcap" id="jcap" class="inputbox" value="' . $defaultcaptcha . '" />';
		echo JHTML::_('form.token');
		echo '</form>';
		$return_subscribmsg = "";

		if (JFactory::getApplication()->input->get('return_subscribmsg'))
		{
			$return_subscribmsg = JFactory::getApplication()->input->get('return_subscribmsg');
		}

		echo '<div style="text-align:center; color:red;font-weight: bold;">' . wordwrap($return_subscribmsg, 22, "<br />") . '</div>';
		echo '</div>';
	}
}
else
{
	if (!defined('CWMPRAYERCOMNOTINSTALL'))
	{
		define('CWMPRAYERCOMNOTINSTALL', 'Prayer Component Not Installed');
	}

	echo '<div style="text-align:center; color:red;font-weight: bold;">' . htmlentities(JText::_('CWMPRAYERCOMNOTINSTALL')) . '</div>';
}
