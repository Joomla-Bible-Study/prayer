<?php

defined('_JEXEC') or die;

$JVersion = new JVersion;


$document = JFactory::getDocument();


$document->addScript('media/com_cwmprayer/js/pc.js');


$document->addStyleSheet(JURI::base() . 'media/com_cwmprayer/css/cwmprayer.css');


/** @var CWMPrayerViewList $this */
$itemid = $this->prayer->PCgetItemid();

jimport('joomla.pagination.pagination');


$dispatcher = JEventDispatcher::getInstance();


$pageNav = new JPagination($this->total, $this->limitstart, $this->limit);
$app = JFactory::getApplication();
$mess = $app->input->getString('return_msg', null);

if ($mess)
{
	echo '<h3>' . $mess . '</h3>';
	$this->prayer->PCReturnMsg($mess);
}

echo '<div>';

if ($this->prayer->pcConfig['config_show_page_headers'])
{
	echo '<div class="componentheading"><h2>' . htmlentities($this->title . ' - ' . JText::_('CWMPRAYERVIEWLIST')) .
		'</h2></div>';
}


echo '<div>';


$this->prayer->buildPCMenu();


echo '</div><div>';


echo $this->prayer->writePCImage() . '</div><div>';


echo $this->prayer->writePCHeader($this->intro) . '</div>';


echo '<fieldset class="pcmod"><legend>' . htmlentities(JText::_('CWMPRAYERVIEWLIST')) . '</legend>';


if ($this->total < 1)
{
	echo '<table class="modlist">';

	if ($this->totalresults > 0)
	{
		echo '<thead><tr><th style="text-align:left;width:20%;" colspan="2">';
		echo $this->prayer->PCgetSearchbox();
		echo '</th></tr></thead>';
		echo '<tbody><tr><td colspan="2"><strong><center><br /><br />' .
			htmlentities(JText::_('CWMPRAYERNOREQUESTSORT')) . '<br /><br /></center></strong><br /></td></tr></tbody>';
		echo '<tfoot><tr><td style="width:25%;font-size:x-small;">';
		echo $this->prayer->PCgetSortbox($this->action, $this->sort);
		echo '</td><td>&nbsp;';
		echo '</td></tr></tfoot>';
	}
	else
	{
		echo '<thead><tr><th>';

		echo '&nbsp;</th>';

		echo '</tr></thead>';

		echo '<tbody><tr><td colspan="2"><strong><center><br /><br />' .
			htmlentities(JText::_('CWMPRAYERNOREQUEST')) . '<br /><br /></center></strong><br /></td></tr></tbody>';

		echo '<tfoot><tr><td>&nbsp;<br />';

		echo '</td></tr></tfoot>';

	}
	echo '</table>';
}
else
{
	echo '<table class="modlist">';

	echo '<thead><tr><th style="text-align:left;">';

	echo $this->prayer->PCgetSearchbox();

	echo '<th><div style="text-align:left;vertical-align:bottom;">';

	if ($this->prayer->pcConfig['config_show_dwprint'])
	{
		echo $this->prayer->PCgetDWPrintButtons();
	}

	echo '<span style="text-align:right;">';

	if ($this->prayer->pcConfig['config_show_bookmarks'])
	{
		$this->prayer->PCgetSocialBookmarks(false);
	}

	echo "</span</div></th></tr></thead>";
}


if ($this->total > 0)
{
	$showresults = array_slice($this->results, $this->limitstart, $this->limit);

	foreach ($showresults as $showrequest)
	{
		$usrl_class = "";


		echo '<tr class="default">';


		echo '<td width="26%" valign="top">';


		$newtopicarray = $this->prayer->PCgetTopics();


		$header = htmlentities(JText::_('CWMPRAYERREQTOPIC')) . ':</b><br />' . $newtopicarray[$showrequest->topic + 1]['text'];


		echo '<table width="100%" border="1" cellspacing="0" cellpadding="2"><tbody><tr class="profilerow"><td><b>' . $header . '&nbsp;&nbsp;</b>';


		echo '<br /><br />';


		if ($this->prayer->pcConfig['config_show_requester'] == "1")
		{
			echo '<b>' . htmlentities(JText::_('CWMPRAYEROVERLIBSUBBY')) . '</b><br />';

			echo $this->prayer->PCgetProfileLink($showrequest, true);
		}

		echo '</td></tr></table></td><td width="74%" valign="top">';

		echo '<table width="100%" border="0" cellspacing="0" cellpadding="2">';

		echo '<tr class="titlerow">';

		echo '<td align="left" width="100%" colspan="2">&nbsp;';

		if ($showrequest->title == '')
		{
			echo '<b><i><a href="' .
				JRoute::_("index.php?option=com_cwmprayer&task=view_request&id=" . $showrequest->id . "&pop=0&Itemid=" . $itemid) . '" />' .
				htmlentities(rtrim(JText::_('CWMPRAYERPRAYERREQUEST'), ":")) . '</a></i></b>';
		}
		else
		{
			echo '<b><i><a href="' . JRoute::_("index.php?option=com_cwmprayer&task=view_request&id=" .
					$showrequest->id . "&pop=0&Itemid=" . $itemid) . '" />' .
				ucfirst($showrequest->title) . '</a></i></b>';
		}

		echo '</td><td align="right" width="100%" nowrap="nowrap">';

		$this->prayer->PCgetButtons($showrequest);

		echo '</td></tr>';

		echo '<tr><td colspan="3"><div class="reqcontent">';

		$showrequest->text = $this->prayer->PCgetSizeRequest($showrequest);

		if (isset($this->prayer->pcConfig['config_enable_plugin']) && !empty($this->prayer->pcConfig['config_allowed_plugins']))
		{
			foreach ($this->prayer->pcConfig['config_allowed_plugins'] as $aplug)
			{
				JPluginHelper::importPlugin('content', $aplug);
			}

			$plugparams = new JObject();

			$tresults = $dispatcher->trigger('onContentPrepare', ['text', &$showrequest, &$plugparams, 0]);
		}

		if ($this->prayer->pcConfig['config_use_wordfilter'] > 0)
		{
			$showrequest->text = $this->prayer->PCbadword_replace($showrequest->text);
		}

		echo $showrequest->text;

		$showcomments_link = $this->prayer->PCgetComments($showrequest);

		echo '</div></td></tr><tr>';

		if ($this->prayer->pcConfig['config_show_date'] == '1')
		{
			echo '<td class="date" align="left" colspan="1">';

			if ($this->prayer->pcConfig['config_show_viewed'])
			{
				echo htmlentities(JText::_('CWMPRAYERVIEWED')) . '&nbsp;(' . $showrequest->hits . ')';
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

			echo '</td>';

			$dateTime = $this->prayer->PCgetTimeZoneData($showrequest);

			echo '<td align="right" class="date" colspan="2" nowrap><i>' . $dateTime['date'] . ' - ' . $dateTime['time'];

			echo '</i>';
		}
		else
		{
			echo '<td class="date" align="left" colspan="3"><small>';

			if ($this->prayer->pcConfig['config_show_viewed'])
			{
				echo htmlentities(JText::_('CWMPRAYERVIEWED')) . '&nbsp;(' . $showrequest->hits . ')';
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
		}
		echo '</td></tr></table>';
	}

	echo '</td></tr><tr><td><br />' . $this->prayer->PCgetSortbox($this->action, $this->sort) .
		'</td><td class="date" align="right">';

	if ($this->prayer->pcConfig['config_show_tz'])
	{
		echo '<b>Timezone:</b> ' . $dateTime['tzid'] . ' (' . $dateTime['tz'] . ')';
	}
	else
	{
		echo '&nbsp;';
	}

	echo '</td></tr></tbody><tr><td colspan="2"><center>';

	echo '<form method="post" action="' . $this->action . '" name="lboxlist" id="lboxlist">';

	echo '<span class="pcpagelinks">' . $pageNav->getListFooter() . '</span>';

	echo '</span></form>';

	echo '</center></td></tr></tfoot></table>';

	echo '<br/></fieldset>';

}
echo '</div><br/>';
echo $this->prayer->PrayerFooter();
