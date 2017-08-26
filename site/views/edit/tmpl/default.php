<?php

defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();

$edit_own = false;

if ($user->get('id') == (int) $this->editreq->requesterid)
{
	$edit_own = true;
}

/** @var \CWMPrayerSitePrayer $prayer */
$prayer = $this->prayer;
$prayer->PCgetAuth('edit', $edit_own);

$document = JFactory::getDocument();

$document->addScript('media/com_cwmprayer/js/pc.js');

$itemid = $prayer->PCgetItemid();

$config_editor = $this->config_editor;

$editorenabled = $prayer->PCcheckEditor($config_editor);

if (!$editorenabled)
{
	$config_editor = 'none';
}
$input = JFactory::getApplication()->input;

$eid = $input->getInt('id');

$eid = JFilterOutput::cleanText($eid);

$print_link = "index.php?option=com_cwmprayer&amp;task=view_request&amp;id=" . $eid . "&amp;pop=1&amp;tmpl=component&amp;Itemid=" . $itemid;

$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=670,height=320,directories=no,location=no';

$imgpath = JURI::base() . 'media/system/images/';

$image = JHTML::_('image', $imgpath . 'printButton.png', JText::_('CWMPRAYERPRINT'), 'style=border:0;');

$attribs['title'] = JText::_('CWMPRAYERPRINT');

if ($this->config_use_gb)
{
	JHtml::_('behavior.modal');
	$attribs['rel']   = "{handler: 'iframe', size: {x: 800, y: 450}}";
	$attribs['class'] = 'modal';
}
else
{
	$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
	$attribs['rel']     = 'nofollow';
}

$attribs['style'] = 'float:right;margin-left:35px;vertical-align:middle;';

$elast = $input->getString('last');

$erequest = stripslashes(JText::_($this->editreq->request));

$erequester = JText::_($this->editreq->requester);

$eemail = $this->editreq->email;

$dateTime = $prayer->PCgetTimeZoneData($this->editreq);

$etime = $dateTime['time'];

if ($this->config_show_tz)
{
	$etime .= ' (' . $dateTime['tz'] . ')';
}

$edate = $dateTime['date'];

$etitle = JText::_($this->editreq->title);

$etopicnum = $this->editreq->topic;

$newtopicarray = $prayer->PCgetTopics();

$etopic = $newtopicarray[$etopicnum + 1]['text'];

if ($etitle == "")
{
	$etitle = htmlentities(JText::_('CWMPRAYERPRAYERREQUEST'));
}
?>
<script type="text/javascript">
	var confirm_act = "<?php echo JText::_('USRLCONFIRMACT');?>";
	var ertask = "editrequest";
	var cetask = "closeedit";
	var edrtask = "editdelrequest";
	var uprtask = "unpubrequest";
</script>
<?php

if ($this->config_show_page_headers)
{
	echo '<div class="componentheading"><h2>' . htmlentities($this->title) . '</h2></div>';
}

echo '<div>';

$prayer->buildPCMenu();

echo '</div><div>';
echo $prayer->writePCImage() . '</div><div>';
echo $prayer->writePCHeader($this->intro) . '</div>';
echo '<form method="post" action="' . $this->action . '" name="adminForm" id="adminForm">';
echo "<input type=\"hidden\" name=\"requester\" size=\"30\" class=\"inputbox\" value=\"$erequester\" readonly=\"readonly\" />";
echo "<input type=\"hidden\" name=\"date\" size=\"20\" class=\"inputbox\" value=\"$edate\" readonly=\"readonly\" />";
echo "<input type=\"hidden\" name=\"time\" size=\"20\" class=\"inputbox\" value=\"$etime\" readonly=\"readonly\" />";
echo "<input type=\"hidden\" name=\"id\" size=\"30\" class=\"inputbox\" value=\"$eid\">";
echo "<input type=\"hidden\" name=\"last\" size=\"30\" class=\"inputbox\" value=\"$elast\">";
echo '<fieldset class="pcmod">';

if ($this->editblock)
{
	echo '<legend>' . htmlentities(JText::_('CWMPRAYERVIEWREQUEST')) . '</legend>';
}
else
{
	echo '<legend>' . htmlentities(JText::_('CWMPRAYEREDITREQUEST')) . '</legend>';
}

echo '<div class="edittable">';
echo '<div class="key">' . htmlentities(JText::_('CWMPRAYERDATE')) . '</div><div class="key2">&nbsp;' . $edate . '</div>';
echo '<div class="key">' . htmlentities(JText::_('CWMPRAYERTIME')) . '</div><div class="key2">&nbsp;' . $etime . '</div>';
echo '<div class="key">' . htmlentities(JText::_('CWMPRAYERPRAYERREQUESTER')) . '</div><div class="key2">&nbsp;' . ucfirst($erequester) . '</div>';

if (empty($eemail))
{
	$eemail = 'None';
}

echo '<div class="key" nowrap>' . htmlentities(JText::_('CWMPRAYERPRAYERREQUESTEREMAIL')) . '</div><div class="key2">&nbsp;' . $eemail . '</div>';
echo '<div class="key">' . htmlentities(JText::_('CWMPRAYERPRAYERTOPIC')) . '</div><div class="key2">&nbsp;' . $etopic . '</div>';
echo '<div>&nbsp;</div>';
echo '<div class="key3">' . ucfirst($etitle);
echo JHTML::_('link', JRoute::_($print_link), $image, $attribs);
echo '</div><div class="pcrequestbox">';
echo $prayer->PCgetEditorBox($erequest);
echo '</div>';

if (!$this->editblock)
{
	echo "<div style=\"padding-left:10px;\">" .
		"<button type=\"button\" class='btn' onclick=\"javascript:document.adminForm.task.value=ertask;return validateEdit(this);return false;\">";

	echo JText::_('CWMPRAYERSAVE') . '</button>&nbsp;';

	echo "<button type=\"button\" class='btn' onclick=\"javascript:document.adminForm.task.value=edrtask;return validateEdit(this);return false;\">";

	echo JText::_('CWMPRAYERDELETE') . '</button>&nbsp;';

	if (($elast != 'moderate') && $pc_rights->get('pc.moderate'))
	{
		echo "<button type=\"button\" class='btn' onclick=\"javascript:document.adminForm.task.value=uprtask;return validateEdit(this);return false;\">";
		echo JText::_('CWMPRAYERUNPUBLISH') . '</button>&nbsp;';
	}

	echo "<button type=\"button\" class='btn' onclick=\"javascript:document.adminForm.task.value=cetask;document.adminForm.submit();\">";
	echo JText::_('CWMPRAYERCANCEL') . '</button></div>';
}

echo '<input type="hidden" name="option" value="COM_CWMPRAYER" />';
echo '<input type="hidden" name="controller" value="prayer" />';
echo '<input type="hidden" name="task" value="" />';
echo JHTML::_('form.token');
echo '</fieldset></form>';
echo '</div><br />';

$prayer->PrayerFooter();

?>
</div>
