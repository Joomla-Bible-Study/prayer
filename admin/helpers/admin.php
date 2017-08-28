<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */

defined('_JEXEC') or die('Restricted access');

/**
 * Prayer Admin Class
 *
 * @package  Prayer.Amdin
 *
 * @since    4.0
 */
class CWMPrayerAdmin
{
	public $pcConfig;

	/**
	 * CWMPrayerSitePrayer constructor.
	 *
	 * @since 4.0
	 */
	public function __construct()
	{
		$comp           = JComponentHelper::getParams('com_cwmprayer');
		$this->pcConfig = $comp->toArray()['params'];
	}

	/**
	 * Full File perms
	 *
	 * @param   string  $perms  ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function fullfileperms($perms)
	{
		if (($perms & 0xC000) == 0xC000)
		{
			// Socket
			$info = 's';
		}
		elseif (($perms & 0xA000) == 0xA000)
		{
			// Symbolic Link
			$info = 'l';
		}
		elseif (($perms & 0x8000) == 0x8000)
		{
			// Regular
			$info = '-';
		}
		elseif (($perms & 0x6000) == 0x6000)
		{
			// Block special
			$info = 'b';
		}
		elseif (($perms & 0x4000) == 0x4000)
		{
			// Directory
			$info = 'd';
		}
		elseif (($perms & 0x2000) == 0x2000)
		{
			// Character special
			$info = 'c';
		}
		elseif (($perms & 0x1000) == 0x1000)
		{
			// FIFO pipe
			$info = 'p';
		}
		else
		{
			// Unknown
			$info = 'u';
		}

		// Owner
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ?
			(($perms & 0x0800) ? 's' : 'x') :
			(($perms & 0x0800) ? 'S' : '-'));

		// Group
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ?
			(($perms & 0x0400) ? 's' : 'x') :
			(($perms & 0x0400) ? 'S' : '-'));

		// World
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ?
			(($perms & 0x0200) ? 't' : 'x') :
			(($perms & 0x0200) ? 'T' : '-'));

		echo $info;
	}

	/**
	 * Find Ext
	 *
	 * @param   string  $filename  File Name
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function findext($filename)
	{
		$filename = strtoupper($filename);

		$ext = preg_split("[\.]", $filename, -1, PREG_SPLIT_NO_EMPTY);
		$ext = $ext[1];

		return $ext;
	}

	/**
	 * Find Image ext
	 *
	 * @param   string  $filename  File Name
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function findimageext($filename)
	{
		$filename = strtoupper($filename);
		$ext      = preg_split("[\.]", $filename, -1, PREG_SPLIT_NO_EMPTY);
		$n        = count($ext) - 1;
		$ext      = $ext[$n];

		return $ext;
	}

	/**
	 * File Select
	 *
	 * @param   string  $name        ?
	 * @param   string  &$active     ?
	 * @param   string  $javascript  ?
	 * @param   string  $directory   ?
	 *
	 * @return array|mixed
	 *
	 * @since 4.0
	 */
	public function FileSel($name, &$active, $javascript = null, $directory = null)
	{
		$pmsrefarray = [
			1 => ['val' => 'jim', 'desc' => '' . JText::_(' - (Requires JIM 1.0.1 or above)') . ''],
			2 => ['val' => 'joomla', 'desc' => '' . JText::_(' - (Built-in Joomla Messaging Component. Requires Joomla 1.6 or above)') . ''],
			3 => ['val' => 'messaging', 'desc' => '' . JText::_(' - (Requires Messaging 1.5 or above)') . ''],
			4 => ['val' => 'missus', 'desc' => '' . JText::_(' - (Requires Missus 1.0 or above)') . ''],
			5 => ['val' => 'mypms2', 'desc' => '' . JText::_(' - (Requires MyPMS II 2.0)') . ''],
			6 => ['val' => 'primezilla', 'desc' => '' . JText::_(' - (Requires Primezilla 1.0.5 or above)') . ''],
			7 => ['val' => 'privmsg', 'desc' => '' . JText::_(' - (Requires PrivMSG 2.1.0 or above)') . ''],
			8 => ['val' => 'uddeim', 'desc' => '' . JText::_(' - (Requires uddeIM 1.8 or above)') . '']
		];

		$SelFiles = JFolder::files(JPATH_COMPONENT . DS . $directory);

		$files = [JHTML::_('select.option', 0, '- Select -')];

		foreach ($SelFiles as $file)
		{
			preg_match('/^plg\.pms\.(.*)\.php$/', $file, $match);

			if ($match)
			{
				$keyarr = $this->pc_array_search_recursive($match[1], $pmsrefarray);
				$key    = $keyarr[0];

				$pmsfile = $pmsrefarray[$key]['desc'];

				$files[] = JHTML::_('select.option', $match[1], ucfirst($match[1] . $pmsfile));
			}
		}

		$files = JHTML::_('select.genericlist', $files, $name, 'class="inputbox" size="1" ' . $javascript, 'value', 'text', $active);

		return $files;
	}

	/**
	 * Prayer Array search Recursive
	 *
	 * @param   string  $needle    Search string
	 * @param   array   $haystack  Array to search
	 *
	 * @return array|null
	 *
	 * @since 4.0
	 */
	public function pc_array_search_recursive($needle, $haystack)
	{
		$path = null;

		$keys = array_keys($haystack);

		while (!$path && (list($toss, $k) = each($keys)))
		{
			$v = $haystack[$k];

			if (is_scalar($v))
			{
				if (strtolower($v) === strtolower($needle))
				{
					$path = [$k];
				}
			}
			elseif (is_array($v))
			{
				if ($path = $this->pc_array_search_recursive($needle, $v))
				{
					array_unshift($path, $k);
				}
			}
		}

		return $path;
	}

	/**
	 * Prayer get PHP setting
	 *
	 * @param   string  $val     ?
	 * @param   int     $colour  ?
	 * @param   int     $yn      ?
	 *
	 * @return int|string
	 *
	 * @since 4.0
	 */
	public function pc_get_php_setting($val, $colour = 0, $yn = 1)
	{
		$r = (ini_get($val) == '1' ? 1 : 0);

		if ($colour)
		{
			if ($yn)
			{
				$r = $r ? '<span style="color: green;">ON</span>' : '<span style="color: red;">OFF</span>';
			}
			else
			{
				$r = $r ? '<span style="color: red;">ON</span>' : '<span style="color: green;">OFF</span>';
			}

			return $r;
		}
		else
		{
			return $r ? 'ON' : 'OFF';
		}
	}

	/**
	 * Prayer Get Server Software
	 *
	 * @return array|false|string
	 *
	 * @since 4.0
	 */
	public function pc_get_server_software()
	{
		if (isset($_SERVER['SERVER_SOFTWARE']))
		{
			return $_SERVER['SERVER_SOFTWARE'];
		}
		elseif (($sf = phpversion() <= '4.2.1' ? getenv('SERVER_SOFTWARE') : $_SERVER['SERVER_SOFTWARE']))
		{
			return $sf;
		}
		else
		{
			return 'n/a';
		}
	}

	/**
	 * Footer info.
	 *
	 * @return string
	 *
	 * @since version
	 */
	public function PrayerFooter()
	{
		$pcversion = new CWMPrayerVersion;

		$string = '<div style="clear:both;"></div>';
		$string .= '<div align="center" class="small">';
		$string .= '<i>"Lift up holy hands in prayer,and praise the Lord."</i>&nbsp;<a';
		$string .= 'href="http://www.biblegateway.com/passage/?search=Psalm%20134:2;&version=51;" target="_blank"><b>Psalm';
		$string .= '134:2</b></a>';
		$string .= '<br/>';
		$string .= '<a href="' . $pcversion->getUrl() . '" target="_blank">Prayer - ';
		$string .= $pcversion->getShortCopyright() . '</a>';
		$string .= '</div>';

		return $string;
	}

	/**
	 * CWM Prayer Asend PM
	 *
	 * @param   string  $newrequester  ?
	 * @param   string  $newrequest    ?
	 * @param   string  $newemail      Email Address
	 * @param   string  $sendpriv      If Privite
	 * @param   int     $lastid        Last ID
	 * @param   int     $sessionid     Sesstion ID
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function PCAsendPM($newrequester, $newrequest, $newemail, $sendpriv, $lastid = null, $sessionid = null)
	{
		$pcpmsclassname = 'PC' . ucfirst($this->pcConfig['config_pms_plugin']) . 'PMSPlugin';

		if (!empty($pcConfig['config_pms_plugin'])
			&& file_exists(JPATH_ROOT . '/administrator/components/com_cwmprayer/plugins/pms/plg.pms.' . $this->pcConfig['config_pms_plugin'] . '.php'))
		{
			$PrayerPluginHelper = new CWMPrayerPluginHelper;

			$pluginfile = 'plg.pms.' . $pcConfig['config_pms_plugin'] . '.php';

			$PrayerPluginHelper->importPlugin('pms', $pluginfile);

			$PrayerPMSPlugin = new $pcpmsclassname;
		}
		else
		{
			return;
		}

		$PrayerPMSPlugin->send_private_messaging($newrequester, $newrequest, $newemail, $sendpriv, $lastid, $sessionid);
	}

	/**
	 * PC Get Topics
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function PCgetTopics()
	{
		$topicArray = [
			1  => ['val' => '0', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC0') . ''],
			2  => ['val' => '1', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC1') . ''],
			3  => ['val' => '2', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC2') . ''],
			4  => ['val' => '3', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC3') . ''],
			5  => ['val' => '4', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC4') . ''],
			6  => ['val' => '5', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC5') . ''],
			7  => ['val' => '6', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC6') . ''],
			8  => ['val' => '7', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC7') . ''],
			9  => ['val' => '8', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC8') . ''],
			10 => ['val' => '9', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC9') . ''],
			11 => ['val' => '10', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC10') . ''],
			12 => ['val' => '11', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC11') . ''],
			13 => ['val' => '12', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC12') . ''],
			14 => ['val' => '13', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC13') . ''],
			15 => ['val' => '14', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC14') . ''],
			16 => ['val' => '15', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC15') . ''],
			17 => ['val' => '16', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC16') . ''],
			18 => ['val' => '17', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC17') . ''],
			19 => ['val' => '18', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC18') . ''],
			20 => ['val' => '19', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC19') . ''],
			21 => ['val' => '20', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC20') . ''],
			22 => ['val' => '21', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC21') . ''],
			23 => ['val' => '22', 'text' => '' . JText::_('CWMPRAYERSELECTTOPIC22') . '']
		];

		return $topicArray;
	}

	/**
	 * CWM Prayer Quick Icon Button
	 *
	 * @param   string  $link    ?
	 * @param   string  $image   ?
	 * @param   string  $text    ?
	 * @param   string  $attrib  ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function PCquickiconButton($link, $image, $text, $attrib = "")
	{
		$lang     = JFactory::getLanguage();
		$template = JFactory::getApplication()->getTemplate();
		?>
		<div class="icon-wrapper">
			<div class="icon">
				<?php
				$image = JHTML::_('image', $image, '/templates/' . $template . '/images/header/', null, null, strip_tags($text));
				$image .= '<span>' . $text . '</span>';
				echo JHTML::_('link', JRoute::_($link), $image, $attrib);
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * CWM Prayer Parse XML file
	 *
	 * @param   String  $xmlfile  XML File Path
	 *
	 * @return array|string
	 *
	 * @since 4.0
	 */
	public function PCparseXml($xmlfile)
	{
		$data = "";

		if (file_exists($xmlfile))
		{
			$data = JInstaller::parseXMLInstallFile($xmlfile);
		}

		return $data;
	}

	/**
	 * CWM Prayer Chang Log Output
	 *
	 * @return string
	 *
	 * @since 4.0
	 * @todo Need to Fix this
	 */
	public function PCChangeLog()
	{
		$options               = [];
		$options['rssUrl']     = 'http://www.mlwebtechnologies.com/index.php?option=com_content&view=category&id=53&format=feed';
		$options['cache_time'] = 86400;

		$simplepie = new SimplePie();


		$simplepie->enable_cache(false);
		$simplepie->set_feed_url($options['rssUrl']);
		$simplepie->force_feed(true);
		$simplepie->init();

		$rssDoc = $simplepie;

		if ($rssDoc == false)
		{
			$output = JText::_('Error: Feed not retrieved');
		}
		else
		{
			$title    = $rssDoc->get_title();
			$link     = $rssDoc->get_link();
			$output   = '<table class="adminlist">';
			$items    = array_slice($rssDoc->get_items(), 0, 3);
			$numItems = count($items);

			if ($numItems == 0)
			{
				$output .= '<tr><th>' . JText::_('prayer change log not available at this time') . '</th></tr>';
			}
			else
			{
				$output .= '<tr><td><textarea cols="70" rows="40">';
				$k      = 0;

				for ($j = 0; $j < $numItems; $j++)
				{
					$item = $items[$j];

					if ($item->get_description())
					{
						$output .= ltrim($this->PCp2nl($item->get_description()), "pcnews\npcconfig\npclang\n");
					}

					$k = 1 - $k;
				}

				$output .= '</textarea></td></tr>';
			}

			$output .= '</table>';
		}

		return $output;
	}

	/**
	 * CWM Prayer p2nl converter
	 *
	 * @param   string  $str  ?
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	public function PCp2nl($str)
	{
		return preg_replace(["/<p[^>]*>/iU", "/<\/p[^>]*>/iU", "/<br[^>]*>/iU"], ["\n", "", "\n"], $str);
	}

	/**
	 * CWM Prayer Limit text
	 *
	 * @param   string  $text       Test to limit
	 * @param   int     $wordcount  Word Count to limit string to
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function PClimitText($text, $wordcount)
	{
		if (!$wordcount)
		{
			return $text;
		}

		$texts = explode(' ', $text);
		$count = count($texts);

		if ($count > $wordcount)
		{
			$text = '';

			for ($i = 0; $i < $wordcount; $i++)
			{
				$text .= ' ' . $texts[$i];
			}

			$text .= '...';
		}

		return $text;
	}

	/**
	 * CWM Prayer Keep HTML
	 *
	 * @param   string  $string  Clean up for XML output
	 *
	 * @return mixed|string
	 *
	 * @since 4.0
	 */
	public function PCkeephtml($string)
	{
		$res = htmlentities($string, ENT_COMPAT, 'UTF-8');
		$res = str_replace("&lt;", "<", $res);
		$res = str_replace("&gt;", ">", $res);
		$res = str_replace("&quot;", '"', $res);
		$res = str_replace("&amp;", '&', $res);

		return $res;
	}

	/**
	 * CWM Prayer Redirect to URL
	 *
	 * @param   string  $str  URL to go to
	 * @param   string  $msg  Message
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function PCRedirect($str, $msg = null)
	{
		$app = JFactory::getApplication();
		$app->redirect($str, $msg);
	}

	/**
	 * CWM Prayer Array Merging into on array
	 *
	 * @param   array  $array  Array to Flatten
	 *
	 * @return array|bool
	 *
	 * @since 4.0
	 */
	public function PCarray_flatten($array)
	{
		if (!is_array($array))
		{
			return false;
		}

		$result = [];

		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				$result = array_merge($result, $this->PCarray_flatten($value));
			}
			else
			{
				$result[$key] = $value;
			}
		}

		return $result;
	}

	/**
	 * Get Admin Data
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function PCgetAdminData()
	{
		$db          = JFactory::getDBO();
		$adminusers  = [];

		$access = new JAccess;
		$db->setQuery("SELECT id FROM #__usergroups");
		$groups = $db->loadObjectList();

		foreach ($groups as $group)
		{
			if ($access->checkGroup($group->id, 'core.manage') || $access->checkGroup($group->id, 'core.admin'))
			{
				$adminusers[] = $access->getUsersByGroup($group->id);
			}
		}

		$result = $this->PCarray_flatten($adminusers);
		$result = implode(',', $result);
		$db->setQuery("SELECT name,email FROM #__users WHERE id IN (" . $result . ")");
		$resultusers = $db->loadObjectList();

		return $resultusers;
	}
}
