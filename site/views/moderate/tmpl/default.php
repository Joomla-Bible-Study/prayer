<?php

defined('_JEXEC') or die('Restricted access');

/** @var \CWMPrayerSitePrayer $prayer */
$prayer = $this->prayer;
$prayer->PCgetAuth('moderate');

$itemid = $prayer->PCgetItemid();
echo '<script language="JavaScript" type="text/javascript" src="media/com_cwmprayer/js/pc.js"></script>';
jimport('joomla.html.pagination');
$pageNav = new JPagination($this->newtotal, $this->limitstart, $this->limit);
$pagelink = 'index.php?option=com_cwmprayer&amp;task=view&amp;Itemid=' . $itemid;

if (JRequest::getVar('return_msg', null, 'get', 'string'))
{
	$prayer->PCReturnMsg(JRequest::getVar('return_msg', null, 'get', 'string'));
}

echo '<div>';
echo '<form method="post" action="' . $this->action . '" name="adminForm" id="adminForm">';

if ($this->config_show_page_headers)
{
	echo '<div class="componentheading"><h2>' . htmlentities($this->title . ' - ' . JText::_('CWMPRAYERMODERATORS')) . '</h2></div>';
}

echo '<div>';
$prayer->buildPCMenu();
echo '</div><div>';
echo $prayer->writePCImage() . '</div><div>';
echo $prayer->writePCHeader($this->intro) . '</div>';
echo '<fieldset class="pcmod"><legend>' . htmlentities(JText::_('CWMPRAYERPRAYERLIST')) . '</legend>';

if ($this->newtotal < 1)
{
	echo '<table class="modlist">';

	echo '<thead><tr><th style="text-align:left;width:20%;" colspan="2">';
	echo '&nbsp;</th></tr></thead>';
	echo '<tbody><tr><td colspan="2"><strong><center><br /><br />' .
		htmlentities(JText::_('CWMPRAYERNONEWREQUESTS')) .
		'<br /><br /></center></strong><br /></td></tr></tbody>';
	echo '<tfoot><tr><td style="width:25%;font-size:x-small;">';
	echo '&nbsp;<br /></td></tr></table>';
}
else
{
	?>
    <script type="text/javascript">
		var choose_cb = "<?php echo JText::_('CWMPRAYERCHOOSECB');?>";
		var confirm_act = "<?php echo JText::_('CWMPRAYERCONFIRMACT');?>";
		var prtask = "pubrequest";
		var drtask = "delrequest";
    </script>
	<?php
	echo '<table class="modlist">';
	echo '<thead><tr><th width="5" class="title"><input type="checkbox" name="markall" title="' .
		htmlentities(JText::_('CWMPRAYERSELECTALL')) .
		'" onClick="selectAll(this)"></th><th width="5" class="title">' .
		htmlentities(JText::_('CWMPRAYERMODREQR')) . '</td><th width="5" class="title">' .
		htmlentities(JText::_('CWMPRAYERMODREQ')) . '</td><th width="5" class="title">' .
		htmlentities(JText::_('CWMPRAYERMODDATETIME')) . '</td></tr></thead>';

	$i = 1;

	if ($this->newtotal > 0)
	{
		$showresults = array_slice($this->newresults, $this->limitstart, $this->limit);

		foreach ($showresults as $showrequest)
		{
			$request = strip_tags($showrequest->request, "<u><i><b>");

			if (strlen($request) > 80)
			{
				$request = substr($request, 0, 78) . " ...";
			}

			$evenodd = $i % 2;

			if ($evenodd == 1)
			{
				$usrl_class = "row0";
			}
			else
			{
				$usrl_class = "row1";
			}

			echo '<tbody><tr class="' . $usrl_class . '">';
			echo '<td width="5" align="center"><input type="checkbox" id="delcb" name="delete[' . $showrequest->id . ']" value="select"></td>';
			echo '<td nowrap>' . $showrequest->requester . '</td>';

			$dateTime = $prayer->PCgetTimeZoneData($showrequest);

			echo '<td width="40%" align="center"><a href="index.php?option=com_cwmprayer&task=edit&last=moderate&id=' .
				$showrequest->id . '&Itemid=' . $itemid . '" />' . nl2br(stripslashes($request)) .
				'</a><td nowrap align="center">' . $dateTime['date'] . ' - ' . $dateTime['time'];

			if ($this->config_show_tz)
			{
				echo ' (' . $dateTime['tz'] . ')';
			}

			echo '</td></tr>';

			$i++;
		}

		echo '</tbody>';
		echo '<tfoot><tr>';
		echo '<td style="text-align:center;" colspan="4">';
		echo '<div class="paginate" style="text-align:center;"><span style="padding-right:100px !important;"><b>';
		echo $pageNav->getPagesLinks() . '</span>';
		echo JText::_('CWMPRAYER_DISPLAY_NUM') . $pageNav->getLimitBox();
		echo '</b></div>';
		echo '</td></tr></tfoot>';
		echo '</table>';
		echo "<br /><div style=\"padding-left:10px;\"><button type=\"button\" class='btn'" .
			" onclick=\"javascript:document.adminForm.task.value=drtask;return validateMod(this);return false;\">";
		echo htmlentities(JText::_('CWMPRAYERDELETE')) . '</button>';
		echo "&nbsp;<button type=\"button\" class='btn'" .
			" onclick=\"javascript:document.adminForm.task.value=prtask;return validateMod(this);return false;\">";
		echo htmlentities(JText::_('CWMPRAYERPUBLISH')) . '</button>';
		echo '</div><input type="hidden" name="option" value="COM_CWMPRAYER" />';
		echo '<input type="hidden" name="controller" value="prayer" />';
		echo '<input type="hidden" name="task" value="moderate" />';
		echo JHTML::_('form.token');
	}
}

echo '</fieldset></form>';
echo '</div><br />';
$prayer->PrayerFooter();
