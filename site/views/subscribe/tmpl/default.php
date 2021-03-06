<?php

defined('_JEXEC') or die('Restricted access');

/** @var \CWMPrayerSitePrayer $prayer */
$prayer = $this->prayer;

$prayer->PCgetAuth('subscribe');

echo '<script language="JavaScript" type="text/javascript" src="media/com_cwmprayer/js/pc.js"></script>';

$document = JFactory::getDocument();

$document->addScript('media/com_cwmprayer/js/pc.js');

$user = JFactory::getUser();

$input = JFactory::getApplication()->input;

$js_script = "";
?>
    <script type="text/javascript">
		var enter_email = "<?php echo JText::_('CWMPRAYERENTEREMAIL');?>";
		var enter_sec_code = "<?php echo JText::_('CWMPRAYERENTERSECCODE');?>";
		var livesite = '<?php echo $livesite;?>';
    </script>
<?php
if ($input->getString('return_msg'))
{
	$prayer->PCReturnMsg($input->getString('return_msg'));
}

echo '<div>';

if ($this->config_show_page_headers)
{
	echo '<div class="componentheading"><h2>' . htmlentities($this->title . ' - ' . JText::_('CWMPRAYERSUBSCRIBE')) . '</h2></div>';
}

echo '<div>';
$prayer->buildPCMenu();
echo '</div><div>';
echo $prayer->writePCImage() . '</div><div>';
echo $prayer->writePCHeader($this->intro, false, htmlentities(JText::_('CWMPRAYERSUBPAGEMSG')));
echo '<br /><br /></div>';
echo '<fieldset class="pcmod"><legend>' . htmlentities(JText::_('CWMPRAYERSUBSCRIBE')) . '</legend>';
echo '<div>';

echo '<form method="post" action="' . $this->action . '" name="adminForm" id="adminForm">';

echo '<div><label for="newsubaddr">' . htmlentities(JText::_('CWMPRAYEREMAIL')) . ': </label>';

echo "<div><input type=\"text\" name=\"newsubscribe\" id=\"newsubaddr\" size=\"60\" class=\"inputbox\" value=\"" .
	$user->get('email') . "\" onBlur=\"javascript:PCchgClassNameOnBlur('newsubaddr');\" /></div>";

echo '<div style="padding-left:10px;">' .
	'<input type="radio" name="subscribe" value="subscribesubmit" style="margin:0 2px 0 0" checked="checked"' .
	' onclick="javascript:document.adminForm.task.value=this.value;"/>' . htmlentities(JText::_('CWMPRAYERSUBSCRIBE'), ENT_COMPAT, 'UTF-8');

echo '<input type="radio" name="subscribe" value="unsubscribesubmit" style="margin:0 2px 0 10px"' .
	' onclick="javascript:document.adminForm.task.value=this.value;" />' .
	htmlentities(JText::_('CWMPRAYERUNSUBSCRIBE'), ENT_COMPAT, 'UTF-8') . '<br /><br /></div>';

if (!$this->config_captcha_bypass || ($this->config_captcha_bypass && $user->guest))
{
	echo $prayer->PCgetCaptchaImg();
	$js_script = 'return validateSub(' . $this->config_captcha . ', livesite, this.form, \'pccomp\')';
}
else
{
	$js_script = 'return validateSub(0, livesite, this.form, \'pccomp\')';
	echo '<div><br /></div>';
}

echo '<div style="padding-left:10px;"><br /><button type="button" class="btn" onclick="javascript:' . $js_script . ';return false;">';


echo htmlentities(JText::_('CWMPRAYERSUBMIT')) . '</button>';

$defaultcaptcha = JFactory::getConfig()->get('captcha');

echo '<input type="hidden" name="jcap" id="jcap" class="inputbox" value="' . $defaultcaptcha . '" />';
echo '<input type="hidden" name="option" value="COM_CWMPRAYER" />';
echo '<input type="hidden" name="controller" value="prayer" />';
echo '<input type="hidden" name="task" value="subscribesubmit" />';
echo JHTML::_('form.token');
echo '</form></div></fieldset>';
echo '<br /></div>';
$prayer->PrayerFooter();
