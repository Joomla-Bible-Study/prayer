<?php/** * prayer Component * * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL * */defined('_JEXEC') or die('Restricted access');class PrayerAdmin{	/**	 * @param $perms	 *	 *	 * @since version	 */	public function fullfileperms($perms)	{		if (($perms & 0xC000) == 0xC000)		{			// Socket			$info = 's';		}		elseif (($perms & 0xA000) == 0xA000)		{			// Symbolic Link			$info = 'l';		}		elseif (($perms & 0x8000) == 0x8000)		{			// Regular			$info = '-';		}		elseif (($perms & 0x6000) == 0x6000)		{			// Block special			$info = 'b';		}		elseif (($perms & 0x4000) == 0x4000)		{			// Directory			$info = 'd';		}		elseif (($perms & 0x2000) == 0x2000)		{			// Character special			$info = 'c';		}		elseif (($perms & 0x1000) == 0x1000)		{			// FIFO pipe			$info = 'p';		}		else		{			// Unknown			$info = 'u';		}		// Owner		$info .= (($perms & 0x0100) ? 'r' : '-');		$info .= (($perms & 0x0080) ? 'w' : '-');		$info .= (($perms & 0x0040) ?			(($perms & 0x0800) ? 's' : 'x') :			(($perms & 0x0800) ? 'S' : '-'));		// Group		$info .= (($perms & 0x0020) ? 'r' : '-');		$info .= (($perms & 0x0010) ? 'w' : '-');		$info .= (($perms & 0x0008) ?			(($perms & 0x0400) ? 's' : 'x') :			(($perms & 0x0400) ? 'S' : '-'));		// World		$info .= (($perms & 0x0004) ? 'r' : '-');		$info .= (($perms & 0x0002) ? 'w' : '-');		$info .= (($perms & 0x0001) ?			(($perms & 0x0200) ? 't' : 'x') :			(($perms & 0x0200) ? 'T' : '-'));		echo $info;	}	/**	 * @param $filename	 *	 * @return array	 *	 * @since version	 */	public function findext($filename)	{		$filename = strtoupper($filename);		$ext = preg_split("[\.]", $filename, -1, PREG_SPLIT_NO_EMPTY);		$ext = $ext[1];		return $ext;	}	/**	 * @param $filename	 *	 * @return array	 *	 * @since version	 */	public function findimageext($filename)	{		$filename = strtoupper($filename);		$ext      = preg_split("[\.]", $filename, -1, PREG_SPLIT_NO_EMPTY);		$n        = count($ext) - 1;		$ext      = $ext[$n];		return $ext;	}	/**	 * @param      $name	 * @param      $active	 * @param null $javascript	 * @param null $directory	 *	 * @return array|mixed	 *	 * @since version	 */	public function FileSel($name, &$active, $javascript = null, $directory = null)	{		$pmsrefarray = [			1 => ['val' => 'jim', 'desc' => '' . JText::_(' - (Requires JIM 1.0.1 or above)') . ''],			2 => ['val' => 'joomla', 'desc' => '' . JText::_(' - (Built-in Joomla Messaging Component. Requires Joomla 1.6 or above)') . ''],			3 => ['val' => 'messaging', 'desc' => '' . JText::_(' - (Requires Messaging 1.5 or above)') . ''],			4 => ['val' => 'missus', 'desc' => '' . JText::_(' - (Requires Missus 1.0 or above)') . ''],			5 => ['val' => 'mypms2', 'desc' => '' . JText::_(' - (Requires MyPMS II 2.0)') . ''],			6 => ['val' => 'primezilla', 'desc' => '' . JText::_(' - (Requires Primezilla 1.0.5 or above)') . ''],			7 => ['val' => 'privmsg', 'desc' => '' . JText::_(' - (Requires PrivMSG 2.1.0 or above)') . ''],			8 => ['val' => 'uddeim', 'desc' => '' . JText::_(' - (Requires uddeIM 1.8 or above)') . '']		];		$SelFiles = JFolder::files(JPATH_COMPONENT . DS . $directory);		$files = [JHTML::_('select.option', 0, '- Select -')];		foreach ($SelFiles as $file)		{			preg_match('/^plg\.pms\.(.*)\.php$/', $file, $match);			if ($match)			{				$keyarr = $this->pc_array_search_recursive($match[1], $pmsrefarray);				$key    = $keyarr[0];				$pmsfile = $pmsrefarray[$key]['desc'];				$files[] = JHTML::_('select.option', $match[1], ucfirst($match[1] . $pmsfile));			}		}		$files = JHTML::_('select.genericlist', $files, $name, 'class="inputbox" size="1" ' . $javascript, 'value', 'text', $active);		return $files;	}	/**	 * @param $needle	 * @param $haystack	 *	 * @return array|null	 *	 * @since version	 */	public function pc_array_search_recursive($needle, $haystack)	{		$path = null;		$keys = array_keys($haystack);		while (!$path && (list($toss, $k) = each($keys)))		{			$v = $haystack[$k];			if (is_scalar($v))			{				if (strtolower($v) === strtolower($needle))				{					$path = [$k];				}			}			elseif (is_array($v))			{				if ($path = $this->pc_array_search_recursive($needle, $v))				{					array_unshift($path, $k);				}			}		}		return $path;	}	/**	 * @param     $val	 * @param int $colour	 * @param int $yn	 *	 * @return int|string	 *	 * @since version	 */	public function pc_get_php_setting($val, $colour = 0, $yn = 1)	{		$r = (ini_get($val) == '1' ? 1 : 0);		if ($colour)		{			if ($yn)			{				$r = $r ? '<span style="color: green;">ON</span>' : '<span style="color: red;">OFF</span>';			}			else			{				$r = $r ? '<span style="color: red;">ON</span>' : '<span style="color: green;">OFF</span>';			}			return $r;		}		else		{			return $r ? 'ON' : 'OFF';		}	}	/**	 *	 * @return array|false|string	 *	 * @since version	 */	public function pc_get_server_software()	{		if (isset($_SERVER['SERVER_SOFTWARE']))		{			return $_SERVER['SERVER_SOFTWARE'];		}		elseif (($sf = phpversion() <= '4.2.1' ? getenv('SERVER_SOFTWARE') : $_SERVER['SERVER_SOFTWARE']))		{			return $sf;		}		else		{			return 'n/a';		}	}	/**	 * Footer info.	 *	 * @return string	 *	 * @since version	 */	public static function PrayerFooter()	{		$pcversion = new PrayerVersion;		$string = '<div align="center" class="small">';		$string .= '<i>"Lift up holy hands in prayer,and praise the Lord."</i>&nbsp;<a';		$string .= 'href="http://www.biblegateway.com/passage/?search=Psalm%20134:2;&version=51;" target="_blank"><b>Psalm';		$string .= '134:2</b></a>';		$string .= '<br/>';		$string .= '<a href="' . $pcversion->getUrl() . '" target="_blank">Prayer - ';		$string .= $pcversion->getShortCopyright() . '</a>';		$string .= '</div>';		return $string;	}	/**	 * @param      $newrequester	 * @param      $newrequest	 * @param      $newemail	 * @param      $sendpriv	 * @param null $lastid	 * @param null $sessionid	 *	 *	 * @since version	 */	public function PCAsendPM($newrequester, $newrequest, $newemail, $sendpriv, $lastid = null, $sessionid = null)	{		$pcpmsclassname = 'PC' . ucfirst($pcConfig['config_pms_plugin']) . 'PMSPlugin';		if (!empty($pcConfig['config_pms_plugin']) && file_exists(JPATH_ROOT .				'/administrator/components/com_prayer/plugins/pms/plg.pms.' . $pcConfig['config_pms_plugin'] . '.php')		)		{			$PrayerPluginHelper = new PrayerPluginHelper();			$pluginfile = 'plg.pms.' . $pcConfig['config_pms_plugin'] . '.php';			$PrayerPluginHelper->importPlugin('pms', $pluginfile);			$PrayerPMSPlugin = new $pcpmsclassname();		}		else		{			return;		}		$PrayerPMSPlugin->send_private_messaging($newrequester, $newrequest, $newemail, $sendpriv, $lastid, $sessionid);	}	public function PCgetTopics()	{		$topicArray = [			1  => ['val' => '0', 'text' => '' . JText::_('PRAYERSELECTTOPIC0') . ''],			2  => ['val' => '1', 'text' => '' . JText::_('PRAYERSELECTTOPIC1') . ''],			3  => ['val' => '2', 'text' => '' . JText::_('PRAYERSELECTTOPIC2') . ''],			4  => ['val' => '3', 'text' => '' . JText::_('PRAYERSELECTTOPIC3') . ''],			5  => ['val' => '4', 'text' => '' . JText::_('PRAYERSELECTTOPIC4') . ''],			6  => ['val' => '5', 'text' => '' . JText::_('PRAYERSELECTTOPIC5') . ''],			7  => ['val' => '6', 'text' => '' . JText::_('PRAYERSELECTTOPIC6') . ''],			8  => ['val' => '7', 'text' => '' . JText::_('PRAYERSELECTTOPIC7') . ''],			9  => ['val' => '8', 'text' => '' . JText::_('PRAYERSELECTTOPIC8') . ''],			10 => ['val' => '9', 'text' => '' . JText::_('PRAYERSELECTTOPIC9') . ''],			11 => ['val' => '10', 'text' => '' . JText::_('PRAYERSELECTTOPIC10') . ''],			12 => ['val' => '11', 'text' => '' . JText::_('PRAYERSELECTTOPIC11') . ''],			13 => ['val' => '12', 'text' => '' . JText::_('PRAYERSELECTTOPIC12') . ''],			14 => ['val' => '13', 'text' => '' . JText::_('PRAYERSELECTTOPIC13') . ''],			15 => ['val' => '14', 'text' => '' . JText::_('PRAYERSELECTTOPIC14') . ''],			16 => ['val' => '15', 'text' => '' . JText::_('PRAYERSELECTTOPIC15') . ''],			17 => ['val' => '16', 'text' => '' . JText::_('PRAYERSELECTTOPIC16') . ''],			18 => ['val' => '17', 'text' => '' . JText::_('PRAYERSELECTTOPIC17') . ''],			19 => ['val' => '18', 'text' => '' . JText::_('PRAYERSELECTTOPIC18') . ''],			20 => ['val' => '19', 'text' => '' . JText::_('PRAYERSELECTTOPIC19') . ''],			21 => ['val' => '20', 'text' => '' . JText::_('PRAYERSELECTTOPIC20') . ''],			22 => ['val' => '21', 'text' => '' . JText::_('PRAYERSELECTTOPIC21') . ''],			23 => ['val' => '22', 'text' => '' . JText::_('PRAYERSELECTTOPIC22') . '']		];		return $topicArray;	}	public function PCquickiconButton($link, $image, $text, $attrib = "")	{		$lang     = JFactory::getLanguage();		$template = JFactory::getApplication()->getTemplate();		?>		<div class="icon-wrapper">			<div class="icon">				<?php				$image = JHTML::_('image', $image, '/templates/' . $template . '/images/header/', null, null, strip_tags($text));				$image .= '<span>' . $text . '</span>';				echo JHTML::_('link', JRoute::_($link), $image, $attrib);				?>			</div>		</div>		<?php	}	public function PCparseXml($xmlfile)	{		$data = "";		if (file_exists($xmlfile))		{			$data = JApplicationHelper::parseXMLInstallFile($xmlfile);		}		return $data;	}	public function PCChangeLog()	{		$output                = '';		$options               = [];		$options['rssUrl']     = 'http://www.mlwebtechnologies.com/index.php?option=com_content&view=category&id=53&format=feed';		$options['cache_time'] = 86400;		// @todo need to replacie this.		$rssDoc = JFactory::getXMLparser('RSS', $options);		if ($rssDoc == false)		{			$output = JText::_('Error: Feed not retrieved');		}		else		{			$title    = $rssDoc->get_title();			$link     = $rssDoc->get_link();			$output   = '<table class="adminlist">';			$items    = array_slice($rssDoc->get_items(), 0, 3);			$numItems = count($items);			if ($numItems == 0)			{				$output .= '<tr><th>' . JText::_('prayer change log not available at this time') . '</th></tr>';			}			else			{				$output .= '<tr><td><textarea cols="70" rows="40">';				$k      = 0;				for ($j = 0; $j < $numItems; $j++)				{					$item = $items[$j];					if ($item->get_description())					{						$output .= ltrim($this->PCp2nl($item->get_description()), "pcnews\npcconfig\npclang\n");					}					$k = 1 - $k;				}				$output .= '</textarea></td></tr>';			}			$output .= '</table>';		}		return $output;	}	public function PCp2nl($str)	{		return preg_replace(["/<p[^>]*>/iU", "/<\/p[^>]*>/iU", "/<br[^>]*>/iU"], ["\n", "", "\n"], $str);	}	public function PClimitText($text, $wordcount)	{		if (!$wordcount)		{			return $text;		}		$texts = explode(' ', $text);		$count = count($texts);		if ($count > $wordcount)		{			$text = '';			for ($i = 0; $i < $wordcount; $i++)			{				$text .= ' ' . $texts[$i];			}			$text .= '...';		}		return $text;	}	public function PCkeephtml($string)	{		$res = htmlentities($string, ENT_COMPAT, 'UTF-8');		$res = str_replace("&lt;", "<", $res);		$res = str_replace("&gt;", ">", $res);		$res = str_replace("&quot;", '"', $res);		$res = str_replace("&amp;", '&', $res);		return $res;	}	public function PCRedirect($str, $msg = null)	{		$app = JFactory::getApplication();		$app->redirect($str, $msg);	}	public function PCarray_flatten($array)	{		if (!is_array($array))		{			return false;		}		$result = [];		foreach ($array as $key => $value)		{			if (is_array($value))			{				$result = array_merge($result, $this->PCarray_flatten($value));			}			else			{				$result[$key] = $value;			}		}		return $result;	}}