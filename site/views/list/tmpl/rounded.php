<?php
/**
 * Core Site CWMPrayer file
 *
 * @package    CWMPrayer.Site
 * @copyright  2007 - 2015 (C) CWM Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.christianwebministries.org/
 * */
defined('_JEXEC') or die;

/** @var CWMPrayerViewList $this */
$this->prayer->PCgetAuth('view');

echo '<script language="JavaScript" type="text/javascript" src="media/com_cwmprayer/js/pc.js"></script>';
$itemid = $this->prayer->PCgetItemid();
$input = JFactory::getApplication()->input;
$JVersion = new JVersion;
$dispatcher = JEventDispatcher::getInstance();
$pageNav = new JPagination($this->total, $this->limitstart, $this->limit);

if ($string = $input->getString('return_msg', null))
{
	$this->prayer->PCReturnMsg($string);
}

echo '<div>';

if ($this->prayer->pcConfig['pconfig_show_page_headers'])
{
	echo '<div class="componentheading"><h2>' . htmlentities($this->title . ' - ' . JText::_('PCVIEWLIST')) . '</h2></div>';
}

echo '<div>';
$this->prayer->buildPCMenu();
echo '</div><div>';
echo $this->prayer->writePCImage() . '</div><div>';
echo $this->prayer->writePCHeader($this->intro) . '</div>';
echo '<fieldset class="pcmod"><legend>' . htmlentities(JText::_('PCVIEWLIST')) . '</legend>';

if ($this->total < 1)
{
	echo '<br /><table class="modlist">';

	if ($this->totalresults > 0)
	{
		echo '<thead><tr><th style="text-align:left;border-right:1px solid #ccc;" colspan="2">';
		echo $this->prayer->PCgetSearchbox();
		echo '</th></tr></thead>';
		echo '<tbody><tr class="row1"><td colspan="3"><strong><center><br /><br />' .
			htmlentities(JText::_('PCNOREQUESTSORT')) . '<br /><br /></center></strong><br /></td></tr></tbody>';
		echo '<tfoot><tr><td style="width:25%;font-size:x-small;">';
		echo $this->prayer->PCgetSortbox($this->action, $this->sort);
		echo '</td><td>&nbsp;';
		echo '</td></tr></tfoot>';
	}
	else
	{
		echo '<thead><tr><th style="border-right:1px solid #ccc;">';
		echo '&nbsp;</th>';
		echo '</tr></thead>';
		echo '<tbody><tr class="row1"><td colspan="2"><strong><center><br /><br />' .
			htmlentities(JText::_('PCNOREQUEST')) . '<br /><br /></center></strong><br /></td></tr></tbody>';
		echo '<tfoot><tr><td>&nbsp;<br />';
		echo '</td></tr></tfoot>';
	}

	echo '</table><br />';
}
else
{
	echo '<div><br /><span style="width:25%;float:left;">';
	echo $this->prayer->PCgetSearchbox();
	echo '</span><span style="text-align:left;">';

	if ($this->prayer->pcConfig['config_show_dwprint'])
	{
		echo $this->prayer->PCgetDWPrintButtons();
	}

	echo '</span><span style="float:right;">';

	if ($this->prayer->pcConfig['config_show_bookmarks'])
	{
		$this->prayer->PCgetSocialBookmarks(false);
	}

	echo '</span></div><br /><br />';
	$i = 1;
	echo '<div class="modlistrounded" style="height:auto;">';

	if ($this->total > 0)
	{
		$showresults = array_slice($this->results, $this->limitstart, $this->limit);

		foreach ($showresults as $showrequest)
		{
			echo '<div style="text-align:left;">';
			$showrequest->text = $this->prayer->PCgetSizeRequest($showrequest);

			if ($this->prayer->pcConfig['config_enable_plugins'] && !empty($this->prayer->pcConfig['config_allowed_plugins']))
			{
				foreach ($this->prayer->pcConfig['config_allowed_plugins'] as $aplug)
				{
					JPluginHelper::importPlugin('content', $aplug);
				}

				$plugparams = new JObject;
				$tresults = $dispatcher->trigger('onContentPrepare', array('text', &$showrequest, &$plugparams, 0));
			}

			if ($this->prayer->pcConfig['config_use_wordfilter'] > 0)
			{
				$showrequest->text = $this->prayer->PCbadword_replace($showrequest->text);
			}

			echo '<div class="rounded_border request_border"><b class="top"><b><b><b></b></b></b></b>';
			echo '<div class="titlerounded">&nbsp;&nbsp;';

			if ($showrequest->title == '')
			{
				echo '<a href="' . JRoute::_("index.php?option=com_cwmprayer&task=view_request&id=$showrequest->id&pop=0&Itemid=$itemid") . '" />' .
					htmlentities(rtrim(JText::_('PCPRAYERREQUEST'), ":")) . '</a>';
			}
			else
			{
				echo '<a href="' . JRoute::_("index.php?option=com_cwmprayer&task=view_request&id=$showrequest->id&pop=0&Itemid=$itemid") . '" />' .
					ucfirst($showrequest->title) . '</a>';
			}

			echo '<span style="float:right;">';
			$this->prayer->PCgetButtons($showrequest);
			echo '&nbsp;&nbsp;</span></div>';
			echo $showrequest->text;
			$showcomments_link = $this->prayer->PCgetComments($showrequest);

			if ($this->prayer->pcConfig['config_show_viewed'] || $this->prayer->pcConfig['config_show_commentlink'])
			{
				echo '<div class="viewedcommentrounded" style="float:left;padding-left:4px;">';
			}

			if ($this->prayer->pcConfig['config_show_viewed'])
			{
				echo htmlentities(JText::_('PCVIEWED')) . '&nbsp;(' . $showrequest->hits . ')';
			}

			if ($this->prayer->pcConfig['config_show_viewed']
				&& $this->prayer->pcConfig['config_show_commentlink']
				&& $this->prayer->pcConfig['config_comments'])
			{
				echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
			}

			if ($this->prayer->pcConfig['config_show_commentlink'])
			{
				echo $showcomments_link;
			}

			if ($this->prayer->pcConfig['config_show_viewed'] || $this->prayer->pcConfig['config_show_commentlink'])
			{
				echo '</div>';
			}

			if ($this->prayer->pcConfig['config_show_requester'] || $this->prayer->pcConfig['config_show_date'])
			{
				echo '<div class="daterounded" style="text-align:right;font-size:x-small;padding-right:4px;">';
			}

			if ($this->prayer->pcConfig['config_show_requester'])
			{
				echo htmlentities(JText::_('PCPOSTEDBY')) . '&nbsp;' . $this->prayer->PCgetProfileLink($showrequest, false);
			}

			if ($this->prayer->pcConfig['config_show_requester'] && $this->prayer->pcConfig['config_show_date'])
			{
				echo ', ';
			}

			if ($this->prayer->pcConfig['config_show_date'])
			{
				$dateTime = $this->prayer->PCgetTimeZoneData($showrequest);
				echo $dateTime['date'] . ' - ' . $dateTime['time'];
			}

			if ($this->prayer->pcConfig['config_show_requester'] || $this->prayer->pcConfig['config_show_date'])
			{
				echo '</div>';
			}

			echo '<b class="bottom"><b><b><b></b></b></b></b></div><br />';
			$i++;
		}

		echo '</div></fieldset><div><span style="width:25%;float:left;padding-top:9px;">';
		echo $this->prayer->PCgetSortbox($this->action, $this->sort);
		echo '</span>';

		if ($this->prayer->pcConfig['config_show_tz'])
		{
			echo '<span style="float:right;padding-top:9px;" class="date"><b>Timezone:</b> ' . $dateTime['tzid'] . ' (' . $dateTime['tz'] . ')</span>';
		}

		echo '</div><div><br /><br /><br /><center><form method="post" action="' . $this->action . '" name="lboxlist" id="lboxlist">';
		echo '<span class="pcpagelinks">' . $pageNav->getListFooter() . '</span>';
		echo '</span></form>';
		echo '</center></div>';
		echo '<div style="clear:both;"><br/><br/></div>';
	}
}

echo '</div>';
echo $this->prayer->PrayerFooter();
