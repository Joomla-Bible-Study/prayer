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

defined('_JEXEC') or die('Restricted access');

/** @var CWMPrayerViewShowReq $this */

$document = JFactory::getDocument();

$document->addScript('media/com_cwmprayer/js/pc.js');

$document->addStyleSheet(JURI::base() . 'media/com_cwmprayer/css/cwmprayer.css');

jimport('joomla.filesystem.folder');

if (!$this->prv)
{
	$this->prayer->PCgetAuth('view');
}

$ulang = $this->prayer->PCgetUserLang();

$itemid = $this->prayer->PCgetItemid();

$dispatcher = JEventDispatcher::getInstance();

if (count($this->results) < 1)
{
	echo '<div class="componentheading">' . JText::_('CWMPRAYERTITLE') . '</div>';
	echo '<h5 style="text-align: center">' . JText::_('JERROR_ALERTNOAUTHOR') . '<br />' . JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST') . '</h5>';
	echo '<br /><br /><br /><br />';
	echo '<div align="center"></div>';
}
else
{
	$erequester = JText::_($this->results->requester);

	$eemail = $this->results->email;

	$etitle = JText::_($this->results->title);

	$etopicnum = $this->results->topic;

	$newtopicarray = $this->prayer->PCgetTopics();

	$etopic = $newtopicarray[$etopicnum + 1]['text'];

	$dateTime = $this->prayer->PCgetTimeZoneData($this->results);

	if ($etitle == "")
	{
		$etitle = htmlentities(rtrim(JText::_('PRAYERPRAYERREQUEST'), ":"));
	}

	if (!$this->pop)
	{
		if ($this->input->getString('return_msg', null))
		{
			$this->prayer->PCReturnMsg($this->input->getString('return_msg', null));
		}

		echo '<div>';

		if ($this->prayer->pcConfig['config_show_page_headers'])
		{
			echo '<div class="componentheading"><h2>' . htmlentities($this->title . ' - ' . JText::_('PRAYERVIEWREQUEST')) . '</h2></div>';
		}

		echo '<div>';
		$this->prayer->buildPCMenu();
		echo '</div><div>';
		echo $this->prayer->writePCImage() . '</div><div>';
		echo $this->prayer->writePCHeader($this->prayer->PCkeephtml($this->intro)) . '</div>';
	}
	else
	{
		echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	}

	echo '<fieldset class="pcmod">';
	echo '<legend>' . htmlentities(JText::_('PRAYERVIEWREQUEST')) . '</legend>';
	echo '<div class="showreqtable" style="width:100%;height="80px;"><div style="width:70%;float:left;">';
	echo '<div class="key">' . htmlentities(JText::_('PRAYERDATE')) . '</div><div class="key2">&nbsp;' . $dateTime['date'] . '</div>';
	echo '<div class="key clr-left">' . htmlentities(JText::_('PRAYERTIME')) . '</div><div class="key2">&nbsp;' .
		$dateTime['time'] . ' (' . $dateTime['tz'] . ')</div>';

	if (empty($eemail))
	{
		$eemail = 'None';
	}

	if ($this->prv && $this->pop)
	{
		echo '<div class="key">' . htmlentities(JText::_('PRAYERPRAYERREQUESTEREMAIL')) . '</div><div class="key2">&nbsp;' .
			$eemail . '</div>';
	}

	if ($this->prayer->pcConfig['config_show_requester']
		&& $this->prayer->pcConfig['config_show_comprofile']
		&& JFolder::exists('components/com_comprofiler')
		&& $this->prayer->pcConfig['config_community'] == 1
		|| $this->prayer->pcConfig['config_show_requester']
		&& $this->prayer->pcConfig['config_show_comprofile']
		&& JFolder::exists('components/com_community')
		&& $this->prayer->pcConfig['config_community'] == 2
	)
	{
		echo '<div class="key clr-left">' . htmlentities(JText::_('PRAYERPRAYERTOPIC')) . '</div><div class="key2">&nbsp;' . $etopic . '</div>';
		echo '<div width="25%">&nbsp;</div><div width="200px">&nbsp;</div>';
	}

	if ($this->prayer->pcConfig['config_show_requester']
		&& !$this->prayer->pcConfig['config_show_comprofile']
		|| $this->prayer->pcConfig['config_show_requester']
		&& !JFolder::exists('components/com_comprofiler')
		|| $this->prayer->pcConfig['config_show_requester']
		&& !$this->prayer->pcConfig['config_community']
	)
	{
		echo '<div class="key clr-left">' . htmlentities(JText::_('PRAYERPRAYERREQUESTER')) .
			'</div><div class="key2">&nbsp;' . ucfirst($erequester) . '</div>';
		echo '<div class="key clr-left">' . htmlentities(JText::_('PRAYERPRAYERTOPIC')) . '</div><div class="key2">&nbsp;' . $etopic . '</div>';
	}

	if (JFolder::exists('components/com_comprofiler')
		&& $this->prayer->pcConfig['config_show_comprofile']
		&& !$this->pop
		&& $this->prayer->pcConfig['config_community'] == 1
	)
	{
		echo '</div><div class="profilebox">' . $this->prayer->PCgetProfileBox($this->results, true) .
			'</div><p style="clear:left;line-height:0px;height:0px;"></p></div>';
	}
	elseif (
		JFolder::exists('components/com_community')
		&& $this->prayer->pcConfig['config_show_comprofile']
		&& !$this->pop
		&& $this->prayer->pcConfig['config_community'] == 2
	)
	{
		echo '</div><div class="profilebox">' . $this->prayer->PCgetProfileBox($this->results, true) .
			'</div><p style="clear:left;line-height:0px;height:0px;"></p></div>';
	}
	else
	{
		echo '</div><p style="clear:left;line-height:0px;height:0px;"></p></div>';
	}

	$printimage = "";

	$print_link = "index.php?option=com_cwmprayer&amp;task=view_request&amp;id=" . $this->results->id .
		"&amp;pop=1&amp;prt=1&amp;tmpl=component&amp;Itemid=" . $itemid;

	$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=670,height=320,directories=no,location=no';

	$image = JHTML::image(JURI::base() . 'media/system/images/printButton.png', htmlentities(JText::_('PRAYERPRINT')), 'style="border:0;"');

	if ($this->prayer->pcConfig['config_show_print'] && !$this->pop)
	{
		$attribs['title'] = htmlentities(JText::_('PRAYERPRINT'));

		if ($this->prayer->pcConfig['config_use_gb'])
		{
			JHtml::_('behavior.modal');
			$attribs['rel'] = "{handler: 'iframe', size: {x: 800, y: 450}}";
			$attribs['class'] = 'modal';
		}
		else
		{
			$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
			$attribs['rel'] = 'nofollow';
		}

		$attribs['style'] = 'float:right;vertical-align:middle;';
		$printimage = JHTML::_('link', JRoute::_($print_link), $image, $attribs);
	}

	echo '<div class="clr-left">&nbsp;</div>';
	echo '<div class="key3 clr-left">' . ucfirst($etitle);
	echo $printimage;

	if ($this->prayer->pcConfig['config_show_bookmarks'] && !$this->pop)
	{
		$this->prayer->PCgetSocialBookmarks(true);
	}

	echo '</div>';

	if ($this->prayer->pcConfig['config_enable_plugins'] && !empty($this->config_allowed_plugins))
	{
		foreach ($this->config_allowed_plugins as $aplug)
		{
			JPluginHelper::importPlugin('content', $aplug);
		}

		$plugparams = new JObject;

		$tresults = $dispatcher->trigger('onContentPrepare', ['text', &$this->results, &$plugparams, 0]);
	}

	if ($this->prayer->pcConfig['config_use_wordfilter'] > 0)
	{
		$this->results->text = $this->prayer->PCbadword_replace($this->results->text);
	}

	echo '<div class="requestbox clr-left" id="pcrequest">' . $this->prayer->PCkeephtml(rtrim(stripslashes($this->results->text))) . '</div>';

	if ($this->prayer->pcConfig['config_show_translate'] && !$this->pop)
	{
		$this->prayer->getTranslation($ulang, $this->eid);

		echo "<div><script>showLanguageDropDown('tol','" . $ulang . "');</script></div>";
	}

	if (!$this->pop)
	{
		echo $this->prayer->PCgetComments($this->results, true);
	}

	echo '<div><br />';

	if ($this->pop)
	{
		?>
		<button type="button" onclick="javascript:void window.print();return false;">
			<?php echo htmlentities(JText::_('PRAYERPRINT')); ?></button>
		<?php
		if ($this->prayer->pcConfig['config_use_gb'] && $this->prt)
		{
			?><button type="button" name="closeedit" onclick="javascript:window.parent.SqueezeBox.close();">
			<?php
		}
		else
		{
			?>
			<button type="button" name="closeedit" onclick="javascript:void window.parent.close();">
			<?php
		}

		echo htmlentities(JText::_('PRAYERCANCEL')) . "</button>";
	}

	echo '</div></fieldset><br />';

	if (!$this->pop)
	{
		$this->prayer->PrayerFooter();
	}

	echo '</div>';
}
