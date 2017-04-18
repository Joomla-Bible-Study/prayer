<?php
/* *************************************************************************************
Title          prayer Component for Joomla
Author         Mike Leeper
Enhancements   Christina Ishii
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

class PrayerSitePrayer extends PrayerAdmin
{
	public $pcConfig;

	/** @var \JObject */
	public $pc_rights = null;

	/**
	 * PrayerSitePrayer constructor.
	 *
	 * @since 4.0
	 */
	public function __construct()
	{
		$comp           = JComponentHelper::getParams('com_prayer');
		$this->pcConfig = $comp->toArray()['params'];

		$this->intializePCRights();
	}

	/**
	 * Intializing Class
	 *
	 * @return \JObject
	 *
	 * @since 4.0
	 */
	public function intializePCRights()
	{
		if (!empty($this->pc_rights))
		{
			return $this->pc_rights;
		}

		$config_moderator_list = strip_tags($this->pcConfig['config_moderator_user_list']);
		$moderatorArray        = preg_split('/[,]/', $config_moderator_list, -1, PREG_SPLIT_NO_EMPTY);
		$user                  = JFactory::getUser();
		$pc_rights             = new JObject;

		foreach ($moderatorArray as $mod)
		{
			preg_match('#(\d+)[-]#', $mod, $matches);

			if ((int) $matches[1] == $user->get('id'))
			{
				$pc_rights->set('pc.moderate', true);
			}
		}

		if (JAccess::check($user->id, 'prayer.view', 'com_prayer'))
		{
			$pc_rights->set('pc.view', true);
		}

		if (JAccess::check($user->id, 'prayer.post', 'com_prayer'))
		{
			$pc_rights->set('pc.post', true);
		}

		if (JAccess::check($user->id, 'prayer.publish', 'com_prayer'))
		{
			$pc_rights->set('pc.publish', true);
		}

		if (JAccess::check($user->id, 'prayer.subscribe', 'com_prayer'))
		{
			$pc_rights->set('pc.subscribe', true);
		}

		if (JAccess::check($user->id, 'prayer.devotional', 'com_prayer'))
		{
			$pc_rights->set('pc.view_devotional', true);
		}

		if (JAccess::check($user->id, 'prayer.links', 'com_prayer'))
		{
			$pc_rights->set('pc.view_links', true);
		}

		$this->pc_rights = $pc_rights;

		return $this->pc_rights;
	}

	/**
	 * Get Translate
	 *
	 * @param   string  $ulang  ?
	 * @param   int     $reqid  ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function getTranslation($ulang, $reqid)
	{
		$translator = $this->pcConfig['config_show_translate'];
		?>
		<script type="text/javascript">var langtranmsg = "<?php echo JText::_('PRAYERSELECTTRANS');?>";</script><?php

		if ($translator == 1 || $translator == 4)
		{
			// Google Translate v2
			echo "<br /><a href=\"http://translate.google.com\"><img src=\"http://www.google.com/intl/" . $ulang .
				"/images/logos/translate_logo_sm.png\" style=\"height:15px;margin-top:2px;vertical-align:middle;border:0;\"" .
				" title=\"Google Translate\" /></a>&nbsp;&nbsp;";

			if ($translator == 1)
			{
				echo "<select style=\"font-size:7pt;\" id=\"tol\" value=\"\" onChange=\"javascript:getTranslator2('" . $ulang .
					"'," . $reqid . ",'" . JURI::base() . "');\" title=\"" . JText::_('PRAYERPOPUPBLOCKER') . "\"></select>";
			}
			elseif ($translator == 4)
			{
				echo "<select style=\"font-size:7pt;\" id=\"tol\" value=\"\" onChange=\"javascript:getTranslator('" . $ulang .
					"');\"></select>";
			}

			$document = JFactory::getDocument();
			$document->addScript('media/com_prayer/js/gtranslate.js');
		}
		elseif ($translator == 2 || $translator == 5)
		{
			//Microsoft Bing Translator
			echo "<br /><a href=\"http://www.bing.com/translator//\"><img src=\"" . JURI::base() .
				"media/com_prayer/fe-images/bing-logo.png\" style=\"height:15px;margin-top:2px;vertical-align:middle;border:0;\"" .
				" title=\"Bing Translator\" /></a><span style=\"color:orange;font-size:7pt;font-weight:bold;\">Translator</span>&nbsp;";

			if ($translator == 2)
			{
				echo "<select style=\"font-size:7pt;\" id=\"tol\" value=\"\" onChange=\"javascript:getTranslator2(" . $reqid . ",'" .
					JURI::base() . "');\" title=\"" . JText::_('PRAYERPOPUPBLOCKER') . "\"></select>";
			}
			elseif ($translator == 5)
			{
				echo "<select style=\"font-size:7pt;\" id=\"tol\" value=\"\" onChange=\"javascript:getTranslator();\"></select>";
			}

			$document = JFactory::getDocument();
			$document->addScript('media/com_prayer/js/mstranslate.js');
		}
	}

	/**
	 * Get Buttons
	 *
	 * @param   string  $showrequest  ?
	 * @param   bool    $editonly     ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function PCgetButtons($showrequest, $editonly = false)
	{
		$user   = JFactory::getUser();
		$itemid = $this->PCgetItemid();
		jimport('joomla.environment.browser');
		jimport('joomla.user.helper');
		$browser = JBrowser::getInstance();
		$app     = JFactory::getApplication();
		$imgpath = '/media/system/images/';

		if (!$editonly)
		{
			$status   = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
			$pdf_link = 'index.php?option=com_prayer&amp;task=pdf&amp;listtype=0&amp;id=' . $showrequest->id . '&amp;title=' .
				htmlentities(JText::_('PRAYERTITLE')) . '&amp;format=pdf';
			$image    = JHTML::image($imgpath . 'pdf_button.png', htmlentities(JText::_('PRAYERPDF')), 'style=vertical-align:top;border:0;');

			if ($this->pcConfig['config_show_pdf'])
			{
				$user_browser = $browser->getBrowser() . $browser->getMajor();
				$user_browser = strtolower($user_browser);

				if ($user_browser != 'msie7')
				{
					$pdfattribs['target'] = '_blank';
				}
				else
				{
					$pdfattribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
				}

				$pdfattribs['title'] = htmlentities(JText::_('PRAYERPDF'));
				$pdfattribs['rel']   = 'nofollow';
				echo JHTML::_('link', JRoute::_($pdf_link), $image, $pdfattribs);
				echo '&nbsp;';
			}

			$print_link = "index.php?option=com_prayer&amp;task=view_request&amp;id=" . $showrequest->id .
				"&amp;pop=1&amp;prt=1&amp;tmpl=component&amp;Itemid=" . $itemid;
			$status     = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
			$image      = JHtml::image($imgpath . 'printButton.png', htmlentities(JText::_('PRAYERPRINT')), 'style=vertical-align:top;border:0;');

			if ($this->pcConfig['config_show_print'])
			{
				$prtattribs['title'] = htmlentities(JText::_('PRAYERPRINT'));

				if ($this->pcConfig['config_use_gb'])
				{
					JHtml::_('behavior.modal');
					$prtattribs['rel']   = "{handler: 'iframe', size: {x: 800, y: 450}}";
					$prtattribs['class'] = 'modal';
				}
				else
				{
					$prtattribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
					$prtattribs['rel']     = 'nofollow';
				}

				echo JHTML::_('link', JRoute::_($print_link), $image, $prtattribs);
				echo '&nbsp;';
			}

			$sitename = $app->get('sitename');
			$mailto   = str_replace('%s', $sitename, htmlentities(JText::_('PRAYERMAILTO')));
			$status   = 'width=400,height=300,menubar=yes,resizable=yes';
			$link     = $mailto . htmlentities($showrequest->request, ENT_QUOTES);
			$image    = JHTML::image($imgpath . 'emailButton.png', htmlentities(JText::_('PRAYERSENDEMAIL')), 'style=vertical-align:top;border:0;');

			if ($this->pcConfig['config_show_email'])
			{
				$mtattribs['title']   = htmlentities(JText::_('PRAYERSENDEMAIL'));
				$mtattribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
				echo JHTML::_('link', JRoute::_($link), $image, $mtattribs);
			}
		}

		if (($this->pcConfig['config_use_admin_alert'] > 1 && $this->pc_rights->get('pc.moderate'))
			|| ($showrequest->requesterid == $user->get('id') && $user->get('id') > 0))
		{
			$icon   = $showrequest->publishstate ? 'edit.png' : 'edit_unpublished.png';
			$link   = 'index.php?option=com_prayer&task=edit&last=view&id=' . $showrequest->id . '&Itemid=' . $itemid;
			$image  = JHTML::_('image', $icon, $imgpath, null, null, htmlentities(ucfirst(JText::_('PRAYEREDIT'))), 'style=vertical-align:top;border:0;');
			$button = JHTML::_('link', JRoute::_($link), $image);
			$output = '<span class="hasTip" title="' . ucfirst(JText::_('PRAYEREDIT')) . '">' . $button . '</span>';
			echo '&nbsp;' . $output;
		}
	}

	/**
	 * Get ItemID
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	public function PCgetItemid()
	{
		$JSite     = new JSite;
		$menu      = $JSite->getMenu();
		$component = JComponentHelper::getComponent('com_prayer');
		$items     = $menu->getItems('component_id', $component->id);
		$itemid    = $items[0]->id;

		return $itemid;
	}

	/**
	 * Get Profile Box using Community Builder or JamSocial
	 *
	 * @param   object  $requestarr  Requester Info
	 * @param   bool    $showavatar  Show Avator True or False
	 *
	 * @return mixed
	 *
	 * @since version
	 */
	public function PCgetProfileBox($requestarr, $showavatar = true)
	{
		$user      = JFactory::getUser();
		$db        = JFactory::getDBO();
		$livesite  = JURI::base();
		$requester = ucfirst($requestarr->requester);
		$reqemail  = $requestarr->email;
		$reqid     = $requestarr->requesterid;

		// Community Builder Profile
		if ($this->pcConfig['config_community'] == 1)
		{
			if (defined('JPATH_ADMINISTRATOR'))
			{
				if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php'))
				{
					//        echo 'Community Builder component is not installed';
					return;
				}
			}

			include_once JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php';
			cbimport('cb.tabs');
			cbimport('cb.database');
			cbimport('language.front');
			$db->setQuery("SELECT COUNT(*) FROM #__comprofiler_plugin WHERE element='profileflags'");
			$flagsplugin = $db->loadResult();

			if ($reqid == 0 || $reqid == null)
			{
				$db->setQuery("SELECT COUNT(*) FROM #__users WHERE name='" . $requester . "'");
				$reqrcount = $db->loadResult();

				if ($reqrcount == 1)
				{
					$reqid = $_CB_framework->getUserIdFrom('name', $requester);
					$db->setQuery("SELECT COUNT(*) FROM #__prayer WHERE requester='" . $requester . "'");
					$reqcount = $db->loadResult();
				}

				if ($reqrcount > 1)
				{
					$reqid = $_CB_framework->getUserIdFrom('email', $reqemail);

					if (count($reqid) < 1)
					{
						$reqid    = 0;
						$reqcount = 0;
					}
					else
					{
						$db->setQuery("SELECT COUNT(*) FROM #__prayer WHERE email='" . $reqemail . "'");
						$reqcount = $db->loadResult();
					}
				}
				else
				{
					$reqcount = 0;
				}
			}
			else
			{
				$db->setQuery("SELECT COUNT(*) FROM #__prayer WHERE requesterid='" . $reqid . "'");
				$reqcount = $db->loadResult();
			}

			if ($flagsplugin)
			{
				$fcountry = ", #__flags_countries.Location AS countryloc, #__flags_countries.Flag AS countryflag ";
				$cjoin    = "INNER JOIN #__flags_countries ON #__comprofiler.country=#__flags_countries.Location ";
			}
			else
			{
				$fcountry = "";
				$cjoin    = "";
			}

			$db->setQuery("SELECT #__comprofiler.hits$fcountry FROM #__comprofiler $cjoin WHERE user_id='" . $reqid . "'");
			$cbresults = $db->loadObjectList();
			$cbcount   = count($cbresults);

			if ($reqid > 0)
			{
				$cbUser = CBuser::getInstance($reqid);
			}

			$isOnline       = $_CB_framework->userOnlineLastTime($reqid);
			$cbprofile_link = $_CB_framework->userProfileUrl($reqid);

			$db->setQuery("SELECT (SELECT accepted FROM #__comprofiler_members WHERE referenceid='" . $reqid . "' AND memberid='" .
				$_CB_framework->myId() . "') AS accepted,pending,membersince,type,description FROM #__comprofiler_members WHERE memberid='" .
				$reqid . "' AND referenceid='" . $_CB_framework->myId() . "'");
			$cbconnect = $db->loadObject();
			$results   = '<b>' . htmlentities(JText::_('PRAYEROVERLIBSUBBY')) . '</b><br />';

			if (!$reqid)
			{
				if ($showavatar)
				{
					$noavatar = $livesite . 'components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png';
					$results  .= "<img src=\"" . $noavatar . "\" alt=\"" . $requester . "\" title=\"" . $requester .
						"\" class=\"profileimage\" />&nbsp;" . $requester;
				}
				else
				{
					$results .= $requester;
				}
			}
			elseif ($showavatar)
			{
				$cbAvatarPath = $cbUser->avatarFilePath();

				if ($ueConfig['allowConnections'] && $_CB_framework->myId() != $reqid && $cbconnect && $_CB_framework->myId() > 0)
				{
					$tipTitle = _UE_CONNECTEDDETAIL;
					$tipField = "<b>" . _UE_CONNECTEDSINCE . "</b> : " . dateConverter($cbconnect->membersince, 'Y-m-d', $ueConfig['date_format']);

					if (getLangDefinition($cbconnect->type) != null)
					{
						$tipField .= "<br /><b>" . _UE_CONNECTIONTYPE . "</b>&nbsp;:&nbsp;" . getConnectionTypes($cbconnect->type);
					}

					if ($cbconnect->description != null)
					{
						$tipField .= "<br /><b>" . _UE_CONNECTEDCOMMENT . "</b>&nbsp;:&nbsp;" . htmlspecialchars($cbconnect->description);
					}

					$cbcount ? $tipField .= "<br /><b>" . _UE_HITS_DESC . "</b>&nbsp;:&nbsp;" . $cbresults[0]->hits : $tipField .= "<br />";
					$htmltext      = "<img src=\"" . $cbAvatarPath . "\" class=\"profileimage\" />";
					$tooltipAvatar = cbFieldTip(1, $tipField, $tipTitle, '250', '', $htmltext, '', '', '', false);
					$results       .= "&nbsp;&nbsp;" . $tooltipAvatar . ucfirst($requester);
				}
				elseif ($ueConfig['allowConnections'] && $_CB_framework->myId() != $reqid && !$cbconnect && $_CB_framework->myId() > 0)
				{
					$tipTitle = _UE_CONNECTEDDETAIL;
					$tipField = "<b>" . _UE_NODIRECTCONNECTION . "</b>";
					$cbcount ? $tipField .= "<br /><b>" . _UE_HITS_DESC . "</b>&nbsp;:&nbsp;" . $cbresults[0]->hits : $tipField .= "<br />";
					$htmltext      = "<img src=\"" . $cbAvatarPath . "\" class=\"profileimage\" />";
					$tooltipAvatar = cbFieldTip(1, $tipField, $tipTitle, '250', '', $htmltext, '', '', '', false);
					$results       .= "&nbsp;&nbsp;" . $tooltipAvatar . ucfirst($requester);
				}
				elseif (!$ueConfig['allowConnections'] && $_CB_framework->myId() != $reqid && $_CB_framework->myId() > 0)
				{
					$results .= "&nbsp;&nbsp;<a href=\"" . $cbprofile_link . "\"><img src=\"" . $cbAvatarPath . "\" alt=\"" .
						$requester . "\" title=\"" . ucfirst($requester) . "\" class=\"profileimage\" />" . ucfirst($requester) .
						"</a>";
				}
				elseif ($_CB_framework->myId() == $reqid && $_CB_framework->myId() > 0)
				{
					$results .= "&nbsp;&nbsp;<a href=\"" . $cbprofile_link . "\"><img src=\"" . $cbAvatarPath . "\" alt=\"" .
						$requester . "\" title=\"" . ucfirst($requester) . "\" class=\"profileimage\" />" . ucfirst($requester) .
						"</a>";
				}
				elseif ($_CB_framework->myId() == 0)
				{
					$results .= "&nbsp;&nbsp;<img src=\"" . $cbAvatarPath . "\" alt=\"" . $requester . "\" title=\"" .
						ucfirst($requester) . "\" class=\"profileimage\" />" . ucfirst($requester);
				}

				if ($ueConfig['allow_onlinestatus'] == 1 && $_CB_framework->myId() > 0)
				{
					$isOnline ? $cbstatus = _UE_ISONLINE : $cbstatus = _UE_ISOFFLINE;
					$results .= "&nbsp;&nbsp;<span class=\"cb_" . strtolower($cbstatus) . "\" title=\"" . ucfirst(strtolower($cbstatus)) .
						"\"><span>&nbsp;</span></span>";
				}

				if ($flagsplugin && $cbcount && $showavatar)
				{
					if (basename($cbresults[0]->countryflag) != 'none.gif' && basename($cbresults[0]->countryflag) != '')
					{
						$cimg    = $livesite . 'components/com_comprofiler/plugin/user/plug_cbprofileflags/countries/' . $cbresults[0]->countryflag;
						$results .= "<br />&nbsp;&nbsp;" . $cbresults[0]->countryloc . "&nbsp;<img src=\"" . $cimg .
							"\" title=\"" . $cbresults[0]->countryloc . "\" class=\"profileflag\" />";
					}
				}
			}
			else
			{
				$results .= "&nbsp;&nbsp;<a href=\"" . $cbprofile_link . "\">" . $requester . "</a>";

				if ($ueConfig['allow_onlinestatus'] == 1)
				{
					$isOnline ? $cbstatus = _UE_ISONLINE : $cbstatus = _UE_ISOFFLINE;
					$results .= "&nbsp;&nbsp;<span class=\"cb_" . strtolower($cbstatus) . "\" title=\"" . ucfirst(strtolower($cbstatus)) .
						"\"><span>&nbsp;</span></span>";
				}
			}

			$reqcount ? $results .= "<br />&nbsp;&nbsp;" . JText::_('PRAYERPRAYERREQUESTS') .
				"&nbsp;<a href=\"" . JRoute::_('index.php?option=com_prayer&task=view&searchrequester=' . $requester . '&searchrequesterid=' . $reqid) . "\">" .
				$reqcount . "</a>" : $results .= "<br />";

			if ($flagsplugin && !$cbcount)
			{
				$results .= "<br />";
			}
			?>
			<script type="text/javascript">
				function cbConnSubmReq() {
					cClick();
					document.connOverForm.submit();
				}
			</script><?php
			if ($_CB_framework->myId() > 0)
			{
				$results .= "<script type=\"text/javascript\" src=\"components/com_comprofiler/js/overlib_all_mini.js\"></script>" .
					"<script type=\"text/javascript\" src=\"components/com_comprofiler/js/overlib_anchor_mini.js\"></script>" .
					"<script type=\"text/javascript\" src=\"components/com_comprofiler/js/overlib_centerpopup_mini.js\"></script><br />";

				if ($ueConfig['allowConnections'] && $_CB_framework->myId() != $reqid && $cbconnect)
				{
					if ($cbconnect->accepted && !$cbconnect->pending)
					{
						$results .= "<a href=\"" . $cbprofile_link . "\"><img src=\"" . $livesite . "components/com_comprofiler/images/profiles.gif\" alt=\"" .
							_UE_VIEWPROFILE . "\" title=\"" . _UE_VIEWPROFILE . "\" /></a><span>&nbsp;</span>";
						$results .= "<a href=\"index.php?option=com_comprofiler&amp;act=connections&amp;task=removeConnection&amp;connectionid=" .
							$reqid . "\" onclick=\"return confirmSubmit();\" ><img src=\"" . $livesite .
							"components/com_comprofiler/images/publish_x.png\" border=\"0\" alt=\"" .
							_UE_REMOVECONNECTION . "\" title=\"" . _UE_REMOVECONNECTION . "\" /></a><span>&nbsp;</span>";

						if ($ueConfig['allow_email'] == 1)
						{
							$cbemail     = $cbUser->_cbuser->email;
							$linkItemImg = "<img src=\"" . $livesite . "components/com_comprofiler/images/email.gif\" border=\"0\" alt=\"" .
								_UE_SENDEMAIL . "\" title=\"" . _UE_SENDEMAIL . "\" />";
							$linkItemSep = null;
							$linkItemTxt = null;

							switch ($ueConfig['allow_email_display'])
							{
								case 1:
									$results .= moscomprofilerHTML::emailCloaking(htmlspecialchars($cbemail), 0);
									break;
								case 2:
									if (!$linkItemImg && $linkItemTxt == htmlspecialchars($cbemail))
									{
										$results .= moscomprofilerHTML::emailCloaking(htmlspecialchars($cbemail), 1, '', 0);
									}
									elseif ($linkItemImg && $linkItemTxt != htmlspecialchars($cbemail))
									{
										$results .= moscomprofilerHTML::emailCloaking(htmlspecialchars($cbemail), 1, $linkItemImg . $linkItemSep . $linkItemTxt, 0, false);
									}
									elseif ($linkItemImg && $linkItemTxt == htmlspecialchars($cbemail))
									{
										$results .= moscomprofilerHTML::emailCloaking(htmlspecialchars($cbemail), 1, $linkItemImg, 0, false) . $linkItemSep;
										$results .= moscomprofilerHTML::emailCloaking(htmlspecialchars($cbemail), 1, '', 0);
									}
									elseif (!$linkItemImg && $linkItemTxt != htmlspecialchars($cbemail))
									{
										$results .= moscomprofilerHTML::emailCloaking(htmlspecialchars($cbemail), 1, $linkItemTxt, 0);
									}
									break;
								case 3:
									$results .= "<a href=\""
										. cbSef("index.php?option=com_comprofiler&amp;task=emailUser&amp;uid=" . $reqid . getCBprofileItemid(true))
										. "\" title=\"" . _UE_MENU_SENDUSEREMAIL_DESC . "\">" . $linkItemImg . $linkItemSep;

									if ($linkItemTxt && ($linkItemTxt != _UE_SENDEMAIL))
									{
										$results .= moscomprofilerHTML::emailCloaking($linkItemTxt, 0);
									}
									else
									{
										$results .= $linkItemTxt;
									}

									$results .= "</a><span>&nbsp;</span>";
									break;
							}
						}

						$pmIMG   = '<img src="' . $livesite . 'components/com_comprofiler/images/pm.gif" border="0" alt="' .
							_UE_PM_USER . '" title="' . _UE_PM_USER . '" />';
						$_CB_PMS = new cbPMS;

						$resultArray = $_CB_PMS->getPMSlinks($reqid, $_CB_framework->myId(), "", "", 1);
						$imgMode     = 1;

						if (count($resultArray) > 0)
						{
							foreach ($resultArray as $res)
							{
								if (is_array($res))
								{
									switch ($imgMode)
									{
										case 0:
											$linkItem = getLangDefinition($res["caption"]);
											break;
										case 1:
											$linkItem = $pmIMG;
											break;
										case 2:
											$linkItem = $pmIMG . ' ' . getLangDefinition($res["caption"]);
											break;
									}

									$results .= "&nbsp;<a href=\"" . cbSef($res["url"]) . "\" title=\"" . getLangDefinition($res["tooltip"]) . "\">" . $linkItem . "</a>";
								}
							}
						}
					}
					elseif (!$cbconnect->accepted && $cbconnect->pending)
					{
						$results .= "<img src=\"" . $livesite . "components/com_comprofiler/images/pending.png\" title=\"" . _UE_CONNECTIONPENDING .
							"\" /><span>&nbsp;</span>";
						$results .= "<a href=\"index.php?option=com_comprofiler&amp;act=connections&amp;task=removeConnection&amp;connectionid=" .
							$reqid . "\" onclick=\"return confirmSubmit();\" ><img src=\"" . $livesite .
							"components/com_comprofiler/images/publish_x.png\" border=\"0\" alt=\"" . _UE_REMOVECONNECTION . "\" title=\"" .
							_UE_REMOVECONNECTION . "\" /></a><span>&nbsp;</span>";
						$results .= "<a href=\"" . $cbprofile_link . "\"><img src=\"" . $livesite . "components/com_comprofiler/images/profiles.gif\" alt=\"" .
							_UE_VIEWPROFILE . "\" title=\"" . _UE_VIEWPROFILE . "\" /></a><span>&nbsp;</span>";
					}
				}
				elseif ($ueConfig['allowConnections'] && $_CB_framework->myId() != $reqid && $reqid)
				{
					$results .= "<a href=\"javascript:void(0)\" onclick=\"return overlib('" . _UE_CONNECTIONINVITATIONMSG .
						"&lt;br /&gt;&lt;form action=&quot;" . JURI::base() .
						"/index.php?option=com_comprofiler&amp;act=connections&amp;task=addConnection&amp;connectionid=" .
						$reqid . "&amp;title=" . rtrim(htmlentities(JText::_('PRAYERPRAYERREQUEST')), ':') .
						"&quot; method=&quot;post&quot; id=&quot;connOverForm&quot; name=&quot;connOverForm&quot;&gt;" . _UE_MESSAGE .
						":&lt;br /&gt;&lt;textarea cols=&quot;40&quot; rows=&quot;8&quot; name=&quot;message&quot;&gt;&lt;/textarea&gt;&lt;br /&gt;&lt;input" .
						" type=&quot;button&quot; class=&quot;inputbox&quot; onclick=&quot;cbConnSubmReq();&quot; value=&quot;" . _UE_SENDCONNECTIONREQUEST .
						"&quot; /&gt;&nbsp;&nbsp;&lt;input type=&quot;button&quot; class=&quot;inputbox&quot; onclick=&quot;cClick();&quot;  value=&quot;" .
						_UE_CANCELCONNECTIONREQUEST . "&quot; /&gt;&lt;/form&gt;', STICKY, CAPTION,'" . sprintf(_UE_CONNECTTO, $requester) .
						"', CENTER,CLOSECLICK,CLOSETEXT,'CLOSE',WIDTH,350, ANCHOR,'cbAddConn',CENTERPOPUP,'LR','UR');\" name=\"cbAddConn\" title=\"" .
						_UE_ADDCONNECTIONREQUEST . "\"><img src=\"" . $livesite . "components/com_comprofiler/images/newavatar.gif\" /></a><span>&nbsp;</span>";
					$results .= "<a href=\"" . $cbprofile_link . "\"><img src=\"" . $livesite . "components/com_comprofiler/images/profiles.gif\" alt=\"" .
						_UE_VIEWPROFILE . "\" title=\"" . _UE_VIEWPROFILE . "\" /></a><span>&nbsp;</span>";
				}
			}

			return $results;

			// JomSocial Profile
		}
		elseif ($this->pcConfig['config_community'] == 2)
		{
			if ($reqid == 0 || $reqid == null)
			{
				$db->setQuery("SELECT COUNT(*) FROM #__users WHERE name='" . $requester . "'");
				$reqrcount = $db->loadResult();

				if ($reqrcount == 1)
				{
					$db->setQuery("SELECT id FROM #__users WHERE name='" . $requester . "'");
					$reqid = $db->loadResult();
					$db->setQuery("SELECT COUNT(*) FROM #__prayer WHERE requester='" . $requester . "'");
					$reqcount = $db->loadResult();
				}

				if ($reqrcount > 1)
				{
					$db->setQuery("SELECT id FROM #__users WHERE email='" . $reqemail . "'");
					$reqid = $db->loadResult();

					if (count($reqid) < 1)
					{
						$reqid    = 0;
						$reqcount = 0;
					}
					else
					{
						$db->setQuery("SELECT COUNT(*) FROM #__prayer WHERE email='" . $reqemail . "'");
						$reqcount = $db->loadResult();
					}
				}
			}
			else
			{
				$db->setQuery("SELECT COUNT(*) FROM #__prayer WHERE requesterid='" . $reqid . "'");
				$reqcount = $db->loadResult();
			}

			if (file_exists(JPATH_BASE . '/components/com_community/libraries/core.php'))
			{
				require_once JPATH_ROOT . '/components/com_community/helpers/string.php';
				require_once JPATH_BASE . '/components/com_community/libraries/core.php';
				require_once JPATH_ROOT . '/components/com_community/libraries/window.php';
			}
			else
			{
				return null;
			}

			if (!$reqid || $user->get('id') == 0)
			{
				$results .= $requester;
			}
			else
			{
				$jsuser          = CFactory::getUser($reqid);
				$js_profile_link = CRoute::_('index.php?option=com_community&amp;view=profile&amp;Userid=' . $reqid);

				if ($showavatar && $user->get('id') > 0)
				{
					$jsavatar = '<img src="' . $jsuser->getThumbAvatar() . '" alt="" border="0" title="' . $requester . '" class=\"profileimage\" />';
					$results  = '<b>' . htmlentities(JText::_('PRAYEROVERLIBSUBBY')) . '</b><br />';
					$results  .= "&nbsp;&nbsp;<a href=\"" . $js_profile_link . "\">" . $jsavatar . ucfirst($requester) . "</a>";
				}
				elseif (!$showavatar && $user->get('id') > 0)
				{
					$results = "&nbsp;&nbsp;<a href=\"" . $js_profile_link . "\">" . $requester . "</a>";
				}

				$isOnline = $jsuser->isOnline();
				$isOnline ? $jsstatus = 'online' : $jsstatus = 'offline';
				$results   .= "&nbsp;<span class=\"cb_" . $jsstatus . "\" title=\"" . ucfirst($jsstatus) . "\"><span>&nbsp;</span></span>";
				$viewcount = $jsuser->getViewCount();
				$results   .= "<br />&nbsp;&nbsp;Profile views: " . $viewcount;

				// Friend Count
				$friendcount = $jsuser->getFriendCount();
				$results     .= "<br />&nbsp;&nbsp;Friends: " . $friendcount;

				// User Status (set by user)
				$userstatus = $jsuser->getStatus();
				$results    .= "<br />&nbsp;&nbsp;User Status: " . $userstatus;

				// To retrieve any user-specific information from custom field
				//      $data = $jsuser->getInfo('FIELD_CODE');
				$reqcount ? $results .= "<br />&nbsp;&nbsp;" . ucfirst(strtolower(htmlentities(JText::_('PRAYERPRAYERREQUESTS')))) .
					":&nbsp;<a href=\"" . JRoute::_('index.php?option=com_prayer&task=view&searchrequester=' . $requester .
						'&searchrequesterid=' . $reqid) . "\">" . $reqcount . "</a>" : $results .= "<br />";

				// Send message
				include_once(JPATH_ROOT . '/components/com_community/libraries/messaging.php');
				$onclick = CMessaging::getPopup($reqid);
				$results .= '<br /><a href="javascript:void(0)" onclick="' . $onclick . '">Send message</a>';
			}

			return $results;
		}

		return null;
	}

	/**
	 * Get Profile Link
	 *
	 * @param   object  $requestarr  ?
	 * @param   bool    $showavatar  ?
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function PCgetProfileLink($requestarr, $showavatar = true)
	{
		jimport('joomla.filesystem.folder');
		$requester = ucfirst($requestarr->requester);
		$reqemail  = $requestarr->email;
		$reqid     = $requestarr->requesterid;
		$user      = JFactory::getUser();
		$userid    = $user->get('id');
		$db        = JFactory::getDBO();
		$livesite  = JURI::base();
		$cprofiler = JFolder::exists('components/com_comprofiler');

		if ($this->pcConfig['config_community'] == 1 && $cprofiler && $userid > 0)
		{
			include_once JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php';
			cbimport('cb.tabs');
			cbimport('cb.database');

			if ($reqid == 0 || $reqid == null)
			{
				$db->setQuery("SELECT COUNT(*) FROM #__users WHERE name REGEXP '" . $requester . "'");
				$reqrcount = $db->loadResult();

				if ($reqrcount == 1)
				{
					$reqid = $_CB_framework->getUserIdFrom('name', $requester);
				}
				elseif ($reqrcount > 1)
				{
					$reqid = $_CB_framework->getUserIdFrom('email', $reqemail);
				}
			}

			$cbprofile_link = $_CB_framework->userProfileUrl($reqid);
			$db->setQuery("SELECT COUNT(*) FROM #__comprofiler_plugin WHERE element='profileflags'");
			$flagsplugin = $db->loadResult();

			if ($flagsplugin)
			{
				$fcountry = ", #__flags_countries.Location AS countryloc, #__flags_countries.Flag AS countryflag ";
				$cjoin    = "INNER JOIN #__flags_countries ON #__comprofiler.country=#__flags_countries.Location ";
			}
			else
			{
				$fcountry = "";
				$cjoin    = "";
			}

			$db->setQuery("SELECT #__comprofiler.avatar$fcountry FROM #__comprofiler $cjoin WHERE user_id='" . $reqid . "' AND avatarapproved='1'");
			$cbresults = $db->loadObjectList();
			$cbcount   = count($cbresults);

			if (!$reqid)
			{
				if ($showavatar)
				{
					$noavatar = $livesite . 'components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png';
					$results  = "<img src=\"" . $noavatar . "\" alt=\"" . $requester . "\" title=\"" . $requester . "\" class=\"profileimage\" />" . $requester;
				}
				else
				{
					$results = $requester;
				}
			}
			elseif ($showavatar)
			{
				if ($cbcount)
				{
					if ($cbresults[0]->avatar == '')
					{
						$avatar = $livesite . 'components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png';
					}
					else
					{
						$avatar = $livesite . 'images/comprofiler/' . $cbresults[0]->avatar;
					}
				}
				else
				{
					$avatar = $livesite . 'components/com_comprofiler/plugin/templates/default/images/avatar/nophoto_n.png';
				}

				$results = "<a href=\"" . $cbprofile_link . "\"><img src=\"" . $avatar . "\" alt=\"" . $requester . "\" title=\"" .
					$requester . "\" class=\"profileimage\" />" . $requester . "</a>";
			}
			else
			{
				$results = "<a href=\"" . $cbprofile_link . "\">" . $requester . "</a>";
			}

			if ($flagsplugin && $cbcount && $showavatar)
			{
				if ($cbresults[0]->countryflag != 'none.gif' && $cbresults[0]->countryflag != '')
				{
					$cimg    = $livesite . 'components/com_comprofiler/plugin/user/plug_cbprofileflags/countries/' . $cbresults[0]->countryflag;
					$results .= "<br /><b>" . htmlentities(JText::_('PRAYERREQLOCATION')) . ":</b>&nbsp;&nbsp;<img src=\"" .
						$cimg . "\" title=\"" . $cbresults[0]->countryloc . "\" class=\"profileflag\" />";
				}
			}

			return $results;

			// JomSocial
		}
		elseif ($this->pcConfig['config_community'] == 2 && $userid > 0)
		{
			$db->setQuery("SELECT id FROM #__users WHERE name='" . $requester . "'");
			$reqid = $db->loadResult();

			if (file_exists(JPATH_BASE . '/components/com_community/libraries/core.php'))
			{
				require_once JPATH_ROOT . '/components/com_community/helpers/string.php';
				require_once JPATH_BASE . '/components/com_community/libraries/core.php';
				require_once JPATH_ROOT . '/components/com_community/libraries/window.php';
				$jsuser = CFactory::getUser($reqid);
			}
			else
			{
				return '';
			}

			if ($showavatar)
			{
				$jsavatar        = '<img src="' . $jsuser->getThumbAvatar() . '" alt="" border="0" title="' . $requester . '" class=\"profileimage\" />';
				$js_profile_link = CRoute::_('index.php?option=com_community&amp;view=profile&amp;Userid=' . $reqid);
				$results         = '<b>' . htmlentities(JText::_('PRAYEROVERLIBSUBBY')) . '</b><br />';
				$results         .= "&nbsp;&nbsp;<a href=\"" . $js_profile_link . "\">" . $jsavatar . ucfirst($requester) . "</a>";
			}
			else
			{
				$results = "<a href=\"" . JRoute::_("index.php?option=com_comprofiler&task=userProfile&user=$reqid") . "\">" . $requester . "</a>";
			}

			return $results;
		}

		return $requester;
	}

	/**
	 * Get Comments
	 *
	 * @param   object  $showrequest   ?
	 * @param   bool    $showcomments  ?
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function PCgetComments($showrequest, $showcomments = false)
	{
		$return     = "";
		$document   = JFactory::getDocument();
		$itemid     = $this->PCgetItemid();

		if ($this->pcConfig['config_comments'] == 1)
		{
			// JComments
			$jcomments = JPATH_SITE . '/components/com_jcomments/jcomments.php';

			if (file_exists($jcomments))
			{
				require_once $jcomments;
				require_once JCOMMENTS_BASE . '/jcomments.config.php';
				require_once JCOMMENTS_BASE . '/jcomments.class.php';
				require_once JCOMMENTS_HELPERS . '/content.php';
				include_once JCOMMENTS_HELPERS . '/system.php';
				$jcommentsconfig   = JCommentsFactory::getConfig();
				$jcommentsEnabled  = JCommentsContentPluginHelper::isEnabled($showrequest, true);
				$jcommentsDisabled = JCommentsContentPluginHelper::isDisabled($showrequest, true);
				$jcommentsLocked   = JCommentsContentPluginHelper::isLocked($showrequest, true);
				$jcommentsconfig->set('comments_on', intval($jcommentsEnabled));
				$jcommentsconfig->set('comments_off', intval($jcommentsDisabled));
				$jcommentsconfig->set('comments_lock', intval($jcommentsLocked));
				JCommentsContentPluginHelper::clear($showrequest, true);
				$commentsCount = JComments::getCommentsCount($showrequest->id, 'com_prayer');
				$showForm      = ($jcommentsconfig->getInt('form_show') == 1) || ($jcommentsconfig->getInt('form_show') == 2 && $commentsCount == 0);
				$isEnabled     = ($jcommentsconfig->getInt('comments_on', 0) == 1) && ($jcommentsconfig->getInt('comments_off', 0) == 0);
				$document->addScript(JCommentsSystemPluginHelper::getCoreJS());
				$document->addScript(JCommentsSystemPluginHelper::getAjaxJS());
				$tmpl = JCommentsFactory::getTemplate($showrequest->id, 'com_prayer');
				$tmpl->load('tpl_index');
				$tmpl->addVar('tpl_index', 'comments-css', 1);

				if ($jcommentsconfig->get('template_view') == 'tree')
				{
					$tmpl->addVar('tpl_index', 'comments-list', $commentsCount > 0 ? JComments::getCommentsTree($showrequest->id, 'com_prayer') : '');
				}
				else
				{
					$tmpl->addVar('tpl_index', 'comments-list', $commentsCount > 0 ? JComments::getCommentsList($showrequest->id, 'com_prayer') : '');
				}

				if ($this->pc_rights->get('pc.post') == 1 && !$jcommentsLocked)
				{
					$tmpl->addVar('tpl_index', 'comments-form', JComments::getCommentsForm($showrequest->id, 'com_prayer', $showForm));
				}

				$tmpl->addVar('tpl_index', 'comments-gotocomment', 1);
				$result = '<br />' . $tmpl->renderTemplate('tpl_index');
				$tmpl->freeAllTemplates();
			}

			if (!$showcomments)
			{
				if ((file_exists($jcomments) && $jcommentsDisabled) || !file_exists($jcomments))
				{
					$jcomment = "";
				}
				else
				{
					$return = '<a href="' . JRoute::_("index.php?option=com_prayer&task=view_request&id=" . $showrequest->id . "&pop=0&Itemid=" . $itemid) .
						'#comments" />' . JText::_('PRAYERCOMMENTS') .
						'&nbsp;(' . $commentsCount . ')</a>';
				}
			}
			else
			{
				$return = '<div>' . $result . '</div>';
			}

			return $return;
		}

		if ($this->pcConfig['config_comments'] == 2)
		{
			// JSiteComments
			$jsc = JPATH_SITE . '/components/com_jsitecomments/helpers/jsc_class.php';

			if (file_exists($jsc))
			{
				require_once $jsc;
				$jsitecomments = new jsitecomment;
				$commentsCount = $jsitecomments->JSCgetCommentsCount('com_prayer', $showrequest->id);

				if (!$showcomments)
				{
					$return = '<a href="' . JRoute::_("index.php?option=com_prayer&task=view_request&id=" . $showrequest->id . "&pop=0&Itemid=" . $itemid) .
						'#comments" />' . JText::_('PRAYERCOMMENTS') . '&nbsp;(' . $commentsCount . ')</a>';
				}
				else
				{
					$return = '<br /><a name="comments"></a>' . $jsitecomments->JSCshow('com_prayer', $showrequest->id);
				}
			}

			return $return;
		}

		return '';
	}

	/**
	 * Get DW Print Buttons
	 *
	 * @return mixed|string
	 *
	 * @since  4.0
	 */
	public function PCgetDWPrintButtons()
	{
		jimport('joomla.environment.browser');
		$browser      = JBrowser::getInstance();
		$status       = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
		$pdf_link1    = 'index.php?option=com_prayer&amp;task=pdf&amp;listtype=1';
		$pdf_link2    = 'index.php?option=com_prayer&amp;task=pdf&amp;listtype=2';

		$image1 = JHTML::image(JURI::base() . 'media/system/images/printButton.png', JText::_('PRAYERPRINTTODAY'), 'style="border:0;"');
		$image2 = JHTML::image(JURI::base() . 'media/system/images/printButton.png', JText::_('PRAYERPRINTWEEK'), 'style="border:0;"');

		$user_browser = $browser->getBrowser() . $browser->getMajor();
		$user_browser = strtolower($user_browser);

		if ($user_browser != 'msie7')
		{
			$attribs['target'] = '_blank';
		}
		else
		{
			$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
		}

		$attribs['title'] = htmlentities(JText::_('PRAYERPRINTTODAY'));
		$attribs['rel']   = 'nofollow';

		$return  = JHTML::_('link', JRoute::_($pdf_link1), $image1 . '&nbsp;<small>' . htmlentities(JText::_('PRAYERDAILY')) . '</small>', $attribs);
		$return .= '&nbsp;&nbsp;&nbsp;';

		$attribs['title'] = htmlentities(JText::_('PRAYERPRINTWEEK'));
		$return .= JHTML::_('link', JRoute::_($pdf_link2), $image2 . '&nbsp;<small>' . htmlentities(JText::_('PRAYERWEEKLY')) . '</small>', $attribs);

		return $return;
	}

	/**
	 * Get Search Box
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function PCgetSearchbox()
	{
		$return  = '<div class="pcsearch" id="pcsearchbox"><form action="' .
			JRoute::_('index.php?option=com_prayer&task=view') . '" name="searchPC" method="post">';
		$boxsize = strlen(htmlentities(JText::_('PRAYERSEARCH...')));

		if ($boxsize <= 15)
		{
			$boxsize = 15;
		}

		$return .= '<span title="' . JText::_('PRAYERSEARCHMSG') .
			'" class="popup"><input class="pc_search_inputbox" type="text" name="searchword" size="' .
			$boxsize . '" value="' . JText::_('PRAYERSEARCH...') . '" onblur="if(this.value==\'\') this.value=\'' .
			JText::_('PRAYERSEARCH...') . '\';" onfocus="if(this.value==\'' . JText::_('PRAYERSEARCH...') .
			'\') this.value=\'\';" />';
		$return .= '</span></form></div>';

		return $return;
	}

	/**
	 * Get Sort Box
	 *
	 * @param   string  $action  ?
	 * @param   string  $sort    "
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function PCgetSortbox($action, $sort)
	{
		$return        = '<div class="pcsort"><form method="post" action="' . $action . '" name="viewlist" id="viewlist">';
		$return        .= "<input type=\"hidden\" id=\"sort\" name=\"sort\" size=\"5\" class=\"inputbox\" value=\"" . $sort . "\" />";
		$newtopicarray = $this->PCgetTopics();
		$return        .= '<select name="sorter" onChange="var sortval=this.options[selectedIndex].value;sortingList(sortval);">';
		$topics        = "";

		if ($sort == 99)
		{
			$topics = '<option value="-1">' . htmlentities(JText::_('PRAYERSORTBY')) . '</option>';
		}

		foreach ($newtopicarray as $nt)
		{
			$tselected = "";

			if ($sort == $nt['val'])
			{
				$tselected = ' selected';
			}

			$topics .= '<option value="' . $nt['val'] . '"' . $tselected . '>' . $nt['text'] . '</option>';
		}

		$topics .= '<option value="99">' . htmlentities(JText::_('PRAYERSELECTTOPIC99')) . '</option>';
		$return .= $topics;
		$return .= '</select>';
		$return .= '</form></div>';

		return $return;
	}

	/**
	 * Get Topics
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function PCgetTopics()
	{
		$topicArray = [
			1  => ['val' => '0', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC0')) . ''],
			2  => ['val' => '1', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC1')) . ''],
			3  => ['val' => '2', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC2')) . ''],
			4  => ['val' => '3', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC3')) . ''],
			5  => ['val' => '4', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC4')) . ''],
			6  => ['val' => '5', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC5')) . ''],
			7  => ['val' => '6', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC6')) . ''],
			8  => ['val' => '7', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC7')) . ''],
			9  => ['val' => '8', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC8')) . ''],
			10 => ['val' => '9', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC9')) . ''],
			11 => ['val' => '10', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC10')) . ''],
			12 => ['val' => '11', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC11')) . ''],
			13 => ['val' => '12', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC12')) . ''],
			14 => ['val' => '13', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC13')) . ''],
			15 => ['val' => '14', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC14')) . ''],
			16 => ['val' => '15', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC15')) . ''],
			17 => ['val' => '16', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC16')) . ''],
			18 => ['val' => '17', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC17')) . ''],
			19 => ['val' => '18', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC18')) . ''],
			20 => ['val' => '19', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC19')) . ''],
			21 => ['val' => '20', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC20')) . ''],
			22 => ['val' => '21', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC21')) . ''],
			23 => ['val' => '22', 'text' => '' . htmlentities(JText::_('PRAYERSELECTTOPIC22')) . '']
		];

		return $topicArray;
	}

	/**
	 * Get Editor Box
	 *
	 * @param   string  $text  ?
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function PCgetEditorBox($text = null)
	{
		$conf                    = JFactory::getConfig();
		$config_show_xtd_buttons = $this->pcConfig['config_show_xtd_buttons'];
		$config_editor           = $this->pcConfig['config_editor'];
		$config_editor_width     = $this->pcConfig['config_editor_width'];
		$config_editor_height    = $this->pcConfig['config_editor_height'];
		$userparams              = new stdClass;

		if (is_numeric($config_editor_width))
		{
			$config_editor_width .= 'px';
		}

		if (is_numeric($config_editor_height))
		{
			$config_editor_height .= 'px';
		}

		if ($config_editor == 'default')
		{
			$config_editor = $conf->get('editor');
			$user          = JFactory::getUser();
			$userid        = $user->get('id');
			$juser         = new JUser($userid);
			$usereditor    = $juser->getParam('editor');

			if (!empty($usereditor))
			{
				$config_editor = $userparams->get('editor');
			}
		}

		$editorenabled = $this->PCcheckEditor($config_editor);

		if ($editorenabled && $config_editor != 'xinha')
		{
			// Xinha editor is not currently supported.
			$editor  = JEditor::getInstance($config_editor);
			$eparams = ['mode' => $this->pcConfig['config_editor_mode']];

			if ($config_editor == 'none')
			{
				$config_show_xtd_buttons = 0;
			}

			$return = $editor->display('newrequest',
				$text,
				$config_editor_width,
				$config_editor_height,
				'70',
				'15',
				$config_show_xtd_buttons,
				'newrequest',
				'',
				'',
				$eparams
			);

			$editorcontent = $editor->getContent('newrequest');
		}
		else
		{
			$return        = '<textarea name="newrequest" id="newrequest" cols="70" rows="15" style="width: ' .
				$config_editor_width . '; height: ' . $config_editor_height . ';">' . $text . '</textarea>';
			$editorcontent = "document.getElementById('newrequest').value;";
		}

		return $return;
	}

	/**
	 * Check Editor
	 *
	 * @param $config_editor
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function PCcheckEditor($config_editor)
	{
		jimport('joomla.plugin.plugin');
		$editorenabled = JPluginHelper::isEnabled('editors', $config_editor);

		return $editorenabled;
	}

	/**
	 * Get Size Request
	 *
	 * @param   object  $showrequest  ?
	 *
	 * @return string
	 *
	 * @since version
	 */
	public function PCgetSizeRequest($showrequest)
	{
		$itemid            = $this->PCgetItemid();
		$showrequest->text = preg_replace("'<\/?p[^>]*>'si", '', $showrequest->text);

		if (($this->pcConfig['config_req_length'] > 0) && (strlen($showrequest->text) > $this->pcConfig['config_req_length']))
		{
			$showrequest->text = substr($showrequest->text, 0, $this->pcConfig['config_req_length'] - 4) . " ...";
			$showrequest->text = $this->PCwordWrapIgnoreHTML($showrequest->text, 65, '<br />');
			$return            = '<div class="reqcontent">"' . $this->PCkeephtml(JText::_($this->PCstripslashes($showrequest->text))) .
				'"<small>&nbsp;&nbsp;<a href="index.php?option=com_prayer&task=view_request&id=' . $showrequest->id . '&Itemid=' .
				$itemid . '" /><i><span style="white-space:nowrap;">' . htmlentities(JText::_('PRAYERREADMORE')) .
				'</span></i></a></small><br /><br />';
		}
		else
		{
			$return = '<div class="reqcontent">' . $this->PCkeephtml(JText::_($this->PCstripslashes($showrequest->text))) .
				'<br /><br />';
		}

		$return .= '</div>';

		return $return;
	}

	/**
	 * Word Wrap Ignore HTML
	 *
	 * @param   string  $string      ?
	 * @param   int     $length      ?
	 * @param   string  $wrapString  ?
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function PCwordWrapIgnoreHTML($string, $length = 45, $wrapString = "\n")
	{
		$wrapped = '';
		$word    = '';
		$html    = false;
		$string  = (string) $string;

		for ($i = 0; $i < strlen($string); $i += 1)
		{
			$char = $string[$i];

			if ($char === '<')
			{
				if (!empty($word))
				{
					$wrapped .= $word;
					$word    = '';
				}

				$html    = true;
				$wrapped .= $char;
			}
			elseif ($char === '>')
			{
				$html    = false;
				$wrapped .= $char;
			}
			elseif ($html)
			{
				$wrapped .= $char;
			}
			elseif ($char === ' ' || $char === "\t" || $char === "\n")
			{
				$wrapped .= $word . $char;
				$word    = '';
			}
			else
			{
				$word .= $char;

				if (strlen($word) > $length)
				{
					$wrapped .= $word . $wrapString;
					$word    = '';
				}
			}
		}

		if ($word !== '')
		{
			$wrapped .= $word;
		}

		return $wrapped;
	}

	/**
	 * Keep Html ????
	 *
	 * @param   string  $string  ?
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	public function PCkeephtml($string)
	{
		$res = $string;
		$res = str_replace("&lt;", "<", $res);
		$res = str_replace("&gt;", ">", $res);
		$res = str_replace("&quot;", '"', $res);
		$res = str_replace("&amp;", '&', $res);

		return $res;
	}

	/**
	 * Strip Slashes
	 *
	 * @param   string  $str  String to work
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function PCstripslashes($str)
	{
		$cd1 = substr_count($str, "\"");
		$cd2 = substr_count($str, "\\\"");
		$cs1 = substr_count($str, "'");
		$cs2 = substr_count($str, "\\'");
		$tmp = strtr($str, ["\\\"" => "", "\\'" => ""]);
		$cb1 = substr_count($tmp, "\\");
		$cb2 = substr_count($tmp, "\\\\");

		if ($cd1 == $cd2 && $cs1 == $cs2 && $cb1 == 2 * $cb2)
		{
			return strtr($str, ["\\\"" => "\"", "\\'" => "'", "\\\\" => "\\"]);
		}

		return $str;
	}

	/**
	 * Get Socail Bookmarks
	 *
	 * @param   bool  $bmshowreq  ??
	 *
	 * @return void
	 *
	 * @since version
	 */
	public function PCgetSocialBookmarks($bmshowreq)
	{
		$service   = $this->pcConfig['config_bm_service'];
		$serviceid = $this->pcConfig['config_bm_service_id'];
		$usegcode  = $this->pcConfig['config_use_gcode'];
		$googleid  = $this->pcConfig['config_google_id'];
		$bmlang    = $this->PCgetUserLang();

		if ($usegcode)
		{
			?>
			<!-- Google Analytics BEGIN -->
			<script type="text/javascript">   var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
				document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));</script>
			<script type="text/javascript"> try {
					var pageTracker = _gat._getTracker("<?php echo $googleid;?>");
					pageTracker._trackPageview();
				} catch (err) {
				}</script><!-- Google Analytics END -->
			<?php
		}

		if ($service == 1)
		{
			// AddThis Service (http://www.addthis.com)
			$usegcode == 1 ? $addthisga = '<script type="text/javascript">var addthis_config={data_ga_tracker: pageTracker};</script>' : $addthisga = '';

			if ($bmshowreq)
			{
				echo '<div style="float:right;vertical-align:bottom;margin-right:5px;"><script type="text/javascript">var addthis_config = {ui_language:"' .
					$bmlang . '",services_exclude:"print,email"}</script><a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=250&amp;username=' .
					$serviceid . '"><img src="http://s7.addthis.com/static/btn/v2/lg-share-' . $bmlang . '.gif" width="125" height="16" title="' .
					htmlentities(JText::_('PRAYERBMSHAREREQ')) . '" style="border:0;"/></a>' . $addthisga .
					'<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username' . $serviceid .
					'"></script></div>';
			}
			else
			{
				echo '<div style="float:right;vertical-align:bottom;"><script type="text/javascript">var addthis_config = {ui_language:"' .
					$bmlang . '",services_exclude:"print,email"}</script><a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=250&amp;username=' .
					$serviceid . '"><img src="http://s7.addthis.com/static/btn/v2/lg-share-' . $bmlang . '.gif" width="125" height="16" title="' .
					htmlentities(JText::_('PRAYERBMSHAREREQLIST')) . '" style="border:0;"/>' . $addthisga .
					'</a><script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username' . $serviceid .
					'"></script></div>';
			}
		}

		if ($service == 2)
		{
			// AddToAny Service (http://www.addtoany.com)
			$usegcode == 1 ? $addtoanygq = '<script type="text/javascript">var a2a_config = a2a_config || {};' .
				' a2a_config.track_links = \'ga\';</script>' : $addtoanygq = '';

			if ($bmshowreq)
			{
				echo '<div style="float:right;vertical-align:bottom;"><style type="text/css">#a2apage_EMAIL {display:none !important;}</style>' .
				'<a class="a2a_dd" href="http://www.addtoany.com/share_save"><img src="http://static.addtoany.com/buttons/share_save_120_16.gif"' .
				'width="120" height="16" border="0" title="' . htmlentities(JText::_('PRAYERBMSHAREREQ')) . '"/></a>' . $addtoanygq .
					'<script type="text/javascript" src="http://static.addtoany.com/menu/locale/' . $bmlang .
					'.js"></script><script type="text/javascript" src="http://static.addtoany.com/menu/page.js"></script></div>';
			}
			else
			{
				echo '<div style="float:right;vertical-align:bottom;"><style type="text/css">#a2apage_EMAIL {display:none !important;}</style>' .
				'<a class="a2a_dd" href="http://www.addtoany.com/share_save"><img src="http://static.addtoany.com/buttons/share_save_120_16.gif" width="120" height="16" border="0" title="' .
					htmlentities(JText::_('PRAYERBMSHAREREQLIST')) . '"/></a>' . $addtoanygq . '<script type="text/javascript" src="http://static.addtoany.com/menu/locale/' .
					$bmlang . '.js"></script><script type="text/javascript" src="http://static.addtoany.com/menu/page.js"></script></div>';
			}
		}

		if ($service == 3)
		{
			// ShareThis Service (http://www.sharethis.com)
			if ($bmshowreq)
			{
				echo '<div style="float:right;vertical-align:bottom;"><script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=' .
					$serviceid . '&amp;type=website&amp;buttonText=' . htmlentities(JText::_('PRAYERBMSHAREREQ')) . '"></script></div>';
			}
			else
			{
				echo '<div style="float:right;vertical-align:bottom;"><script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=' .
					$serviceid . '&amp;type=website&amp;buttonText=' . htmlentities(JText::_('PRAYERBMSHAREREQLIST')) . '"></script></div>';
			}
		}

		if ($service == 4 && $serviceid)
		{
			// TellAFriend/SocialTwist Service (http://tellafriend.socialtwist.com)
			$serviceid = $serviceid . '/';

			if ($bmshowreq)
			{
				echo '<div style="float:right;vertical-align:bottom;"><script type="text/javascript" src="http://cdn.socialtwist.com/' . $serviceid .
					'/script.js"></script><a class="st-taf" href="http://tellafriend.socialtwist.com:80" onclick="return false;"' .
					'style="border:0;padding:0;margin:0;"><img alt="' . htmlentities(JText::_('PRAYERBMSHAREREQ')) .
					'" style="border:0;padding:0;margin:0;" src="http://images.socialtwist.com/' .
					$serviceid . 'button.png"onmouseout="STTAFFUNC.hideHoverMap(this)" onmouseover="STTAFFUNC.showHoverMap(this, \'' . $serviceid .
					'\', window.location, document.title)" onclick="STTAFFUNC.cw(this, {id:\'' . $serviceid .
					'\', link: window.location, title: document.title });"/></a></div>';
			}
			else
			{
				echo '<div style="float:right;vertical-align:bottom;"><script type="text/javascript" src="http://cdn.socialtwist.com/' . $serviceid .
					'/script.js"></script><a class="st-taf" href="http://tellafriend.socialtwist.com:80" onclick="return false;"' .
					'style="border:0;padding:0;margin:0;"><img alt="' . htmlentities(JText::_('PRAYERBMSHAREREQLIST')) .
					'" style="border:0;padding:0;margin:0;" src="http://images.socialtwist.com/' . $serviceid .
					'button.png"onmouseout="STTAFFUNC.hideHoverMap(this)" onmouseover="STTAFFUNC.showHoverMap(this, \'' . $serviceid .
					'\', window.location, document.title)" onclick="STTAFFUNC.cw(this, {id:\'' . $serviceid .
					'\', link: window.location, title: document.title });"/></a></div>';
			}
		}
	}

	/**
	 * Get User Language
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	public function PCgetUserLang()
	{
		$user       = JFactory::getUser();
		$userid     = $user->get('id');
		$juser      = new JUser($userid);
		$userfelang = $juser->getParam('language');

		if (!empty($userfelang))
		{
			preg_match("#([a-zA-Z])[^-]#", $userfelang, $felangmatches);
			$lcname = $felangmatches[0];
		}
		else
		{
			$langclient    = JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
			$langparams    = JComponentHelper::getParams('com_languages');
			$defaultfelang = $langparams->get($langclient->name, 'en-GB');
			preg_match("#([a-zA-Z])[^-]#", $defaultfelang, $felangmatches);
			$lcname = $felangmatches[0];
		}

		return $lcname;
	}

	/**
	 * Get Auther
	 *
	 * @param   string $page
	 * @param   string $edit_own
	 *
	 * @return bool
	 *
	 * @since version
	 */
	public function PCgetAuth($page = null, $edit_own = null)
	{
		$itemid               = $this->PCgetItemid();
		$returnmsg            = JFactory::getApplication()->input->getString('return_msg', null);

		if ($page != null)
		{
			$page = 'pc.' . $page;

			if (!$this->pc_rights->get($page) && !$edit_own && !$this->pc_rights->get('pc.moderate'))
			{
				if (empty($returnmsg))
				{
					$returnurl = JRoute::_('index.php?option=com_prayer&Itemid=' . $itemid);
					$this->PCRedirect($returnurl, JText::_('JERROR_ALERTNOAUTHOR'));

					return false;
				}
				else
				{
					$returnurl = JRoute::_('index.php?option=com_prayer&Itemid=' . $itemid . '&return_msg=' . $returnmsg);
					$this->PCRedirect($returnurl);

					return true;
				}
			}
		}

		return true;
	}

	/**
	 * Redirect function
	 *
	 * @param   string  $str  URL to redirect to
	 * @param   string  $msg  Message to present
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
	 * Return Message HTML format
	 *
	 * @param $ret_msg
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function PCReturnMsg($ret_msg)
	{
		$return_msg = '<div class="return_msg"><hr><br /><span class="center">' . $ret_msg . '</span><br /><hr></div>';
		echo $return_msg;
	}

	/**
	 * Get Captcha Img
	 *
	 * @param   string  $action  ?
	 * @param   string  $form    ?
	 *
	 * @return null|string
	 *
	 * @since 4.0
	 * @deprecated 4.0 Moving to Natave Plugin Capatche
	 */
	public function PCgetCaptchaImg($action = "pccomp", $form = 'adminForm')
	{
		return null;
	}

	/**
	 * Captcha Validate
	 *
	 * @param   object  $usercode  ?
	 * @param   string  $page      ?
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function PCCaptchaValidate($usercode, $page)
	{
		// Still need to build
		return true;
	}

	/**
	 * SID Validate not sure where this is used
	 *
	 * @param   string  $var  ?
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function PCSIDvalidate($var)
	{
		jexit('SIDvalidate lookup');

		if (!preg_match("^[A-Za-z0-9]{1,32}^", $var))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Check Email not sure if needed
	 *
	 * @param   string  $email  Email address only
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function PCcheckEmail($email)
	{
		jexit('Find usage of checkEmail');
		$config_domain_list = $this->pcConfig['config_domain_list'];
		$domArray           = preg_split('/[,]/', $config_domain_list, -1, PREG_SPLIT_NO_EMPTY);

		list($username, $domaintld) = preg_split("@", $email);
		$domaintld = strtolower($domaintld);

		if (!empty($domArray))
		{
			if (in_array($domaintld, $domArray))
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return true;
		}
	}

	/**
	 * Check if Blocked Email address
	 *
	 * @param   string  $email  Email Address to check
	 *
	 * @return bool
	 *
	 * @since version
	 */
	public function PCcheckBlockedEmail($email)
	{
		$config_emailblock_list = $this->pcConfig['config_emailblock_list'];
		$ebArray                = preg_split('/[,]/', strtolower($config_emailblock_list), -1, PREG_SPLIT_NO_EMPTY);

		if (!empty($ebArray))
		{
			if (in_array($email, $ebArray))
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return true;
		}
	}

	/**
	 * Spam Checker
	 *
	 * @param   string  $string  URL of website that could be bad
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function PCspamcheck($string)
	{
		jimport('joomla.environment.browser');
		$browser  = JBrowser::getInstance($_SERVER['HTTP_USER_AGENT']);
		$spam     = 0;

		if ($browser->isRobot())
		{
			$spam = 1;
		}

		$config_use_spamcheck = $this->pcConfig['config_use_spamcheck'];

		if (preg_match("/^bcc:|cc:|multipart|\[url|Content-Type:|MIME-Version:|content-transfer-encoding:|to:/i", $string, $out))
		{
			$spam = 1;
		}

		if (preg_match("/^<a|http|https|www\.|ftp:/i", $string, $out))
		{
			$spam = 1;
		}

		if (preg_match("/(%0A|%0D|\n+\r+)/i", $string, $out))
		{
			$spam = 1;
		}

		if ((isset($_SERVER['HTTP_REFERER']) && !stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])))
		{
			$spam = 1;
		}

		if (!empty($_POST['temail']))
		{
			$spam = 1;
		}

		if ($spam > 0 && $config_use_spamcheck > 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Bad Sord Replacer
	 *
	 * @param   string  $string  String to check for Bad Words
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	public function PCbadword_replace($string)
	{
		$dispatcher = JEventDispatcher::getInstance();

		if ($this->pcConfig['config_use_wordfilter'] == 1)
		{
			$config_bad_words    = trim($this->pcConfig['config_bad_words']);
			$config_replace_word = $this->pcConfig['config_replace_word'];

			if (!empty($config_bad_words))
			{
				$arr = preg_split('/[,]/', $config_bad_words, -1, PREG_SPLIT_NO_EMPTY);

				foreach ($arr as $array)
				{
					if ($array != " ")
					{
						$arrayStr = '#' . $array . '#i';
						$string   = preg_replace($arrayStr, $config_replace_word, $string);
					}
				}
			}
		}
		elseif ($this->pcConfig['config_use_wordfilter'] == 2 && JPluginHelper::isEnabled('content', 'wordcensor'))
		{
			JPluginHelper::importPlugin('content', 'wordcensor');
			$tresults = $dispatcher->trigger('badword_replace', [$string, '', 0]);
			$string   = $tresults[0];
		}
		elseif ($this->pcConfig['config_use_wordfilter'] == 3 && JPluginHelper::isEnabled('content', 'badwordfilter'))
		{
			JPluginHelper::importPlugin('content', 'badwordfilter');
			$params        = new JObject;
			$content       = new stdClass;
			$content->text = $string;
			$tresults      = $dispatcher->trigger('onContentPrepare', ['text', &$content, &$params, 0]);
			$string        = $content->text;
		}
		elseif ($this->pcConfig['config_use_wordfilter'] == 4 && JPluginHelper::isEnabled('content', 'JBehave'))
		{
			JPluginHelper::importPlugin('content', 'JBehave');
			$content       = new stdClass;
			$content->text = $string;
			$tresults      = $dispatcher->trigger('onPrepareContent', ['text', &$content, '', 0]);
			$string        = $content->text;
		}

		return $string;
	}

	/**
	 * Auto Purge
	 *
	 * @param   string $config_request_retention  ?
	 * @param   stirng $config_archive_retention  ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function PCautoPurge($config_request_retention, $config_archive_retention)
	{
		$jcomments = JPATH_SITE . '/components/com_jcomments/jcomments.php';

		if (file_exists($jcomments))
		{
			require_once($jcomments);
		}

		$now                      = time();
		$config_request_retention = (86400 * $config_request_retention);
		$config_archive_retention = (86400 * $config_archive_retention);
		$db                       = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__prayer WHERE archivestate='0'");
		$purgeresult = $db->loadObjectList();

		if (count($purgeresult) > 0)
		{
			foreach ($purgeresult as $purgeresults)
			{
				if (($now - strtotime($purgeresults->date)) >= $config_request_retention)
				{
					$db->setQuery("DELETE FROM #__prayer WHERE id='" . (int) ($purgeresults->id) . "'");

					if (!$db->execute())
					{
						JError::raiseError(500, $db->stderr());
					}
				}

				if (file_exists($jcomments))
				{
					JComments::deleteComments($purgeresults->id, 'com_prayer');
				}
			}
		}

		$db->setQuery("SELECT * FROM #__prayer WHERE archivestate='1'");
		$archivepurgeresult = $db->loadObjectList();

		if (count($archivepurgeresult) > 0)
		{
			foreach ($archivepurgeresult as $archivepurgeresults)
			{
				if (($now - strtotime($archivepurgeresults->date)) >= $config_archive_retention)
				{
					$db->setQuery("DELETE FROM #__prayer WHERE id='" . (int) ($archivepurgeresults->id) . "'");

					if (!$db->execute())
					{
						JError::raiseError(500, $db->stderr());
					}
				}

				if (file_exists($jcomments))
				{
					JComments::deleteComments($archivepurgeresults->id, 'com_prayer');
				}
			}
		}
	}

	/**
	 * Send Privet Message
	 *
	 * @param   int     $newrequesterid  ?
	 * @param   object  $newrequester    ?
	 * @param   string  $newrequest      ?
	 * @param   string  $newemail        ?
	 * @param   string  $sendpriv        ?
	 * @param   int     $lastId          ?
	 * @param   int     $sessionid       ?
	 * @param   bool    $admin           ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function PCsendPM($newrequesterid, $newrequester, $newrequest, $newemail, $sendpriv, $lastId = 0, $sessionid = 0, $admin = false)
	{
		$pcpmsclassname = 'PC' . ucfirst($this->pcConfig['config_pms_plugin']) . 'PMSPlugin';

		if (!empty($this->pcConfig['config_pms_plugin'])
			&& file_exists(JPATH_ROOT . '/administrator/components/com_prayer/pms/plg.pms.' . $this->pcConfig['config_pms_plugin'] . '.php'))
		{
			require_once JPATH_ROOT . '/administrator/components/com_prayer/helpers/pluginhelper.php';
			$PrayerPluginHelper = new PrayerPluginHelper;
			$pluginfile         = 'plg.pms.' . $this->pcConfig['config_pms_plugin'] . '.php';
			$PrayerPluginHelper->importPlugin('pms', $pluginfile);
			$PrayerPMSPlugin = new $pcpmsclassname;
		}
		else
		{
			return;
		}

		if ($admin)
		{
			$PrayerPMSPlugin->admin_private_messaging($newrequesterid, $newrequester, $newrequest, $newemail, $lastId, $sessionid, $sendpriv);
		}
		elseif (!$sendpriv)
		{
			$PrayerPMSPlugin->send_private_messaging($newrequester, $newrequest, $newemail, $sendpriv, $lastId, $sessionid);
		}
	}

	/**
	 * Clean Text String
	 *
	 * @param   string  $text  ?
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	public function PCcleanText(&$text)
	{
		$text = preg_replace("'<script[^>]*>.*?</script>'si", '', $text);
		$text = preg_replace('/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text);
		$text = preg_replace('/<!--.+?-->/', '', $text);
		$text = preg_replace('/&nbsp;/', ' ', $text);
		$text = preg_replace('/&amp;/', ' ', $text);
		$text = preg_replace('/&quot;/', ' ', $text);
		$text = preg_replace('/<(\w[^>]*) class=([^ |>]*)([^>]*)/i', "<$1$3", $text);
		$text = preg_replace('/<(\w[^>]*) style="([^\"]*)"([^>]*)/i', "<$1$3", $text);
		$text = preg_replace('/\s*style="\s*"/i', '', $text);
		$text = preg_replace('/<(\w[^>]*) lang=([^ |>]*)([^>]*)/i', "<$1$3", $text);
		$text = preg_replace('/<STYLE\s*>(.*?)<\/STYLE>/i', '', $text);
		$text = strip_tags($text, '<u><i><em><strong><strike><ul><ol><li><br />');

		return $text;
	}

	/**
	 * Bild Prayer Menu
	 *
	 * @param   bool                      $mod        ?
	 * @param   Joomla\Registry\Registry  $modparams  ?
	 *
	 * @return void;
	 *
	 * @since version
	 */
	public function buildPCMenu($mod = false, $modparams = null)
	{
		$itemid = $this->PCgetItemid();

		if (!is_null($modparams))
		{
			$menu_style = $modparams->get('menu_style');

			if ($menu_style == 'vert_indent')
			{
				$menu_style = 0;
			}

			if ($menu_style == 'horiz_flat')
			{
				$menu_style = 1;
			}

			$menuclass       = 'mainlevel' . $modparams->get('modulesclass_sfx');
			$moduleclass_sfx = $modparams->get('moduleclass_sfx');
			$show_submit     = $modparams->get('show_submit', 1);
			$show_view       = $modparams->get('show_view', 1);
			$show_subscribe  = $modparams->get('show_subscribe', 1);
			$show_links      = $modparams->get('show_links', 1);
			$show_devotion   = $modparams->get('show_devotion', 1);
			$show_moderator  = $modparams->get('show_moderator', 1);
			$menu_css        = "";
		}
		elseif ($this->pcConfig['config_show_menu'])
		{
			$menu_style      = 0;
			$menuclass       = "";
			$moduleclass_sfx = "";

			if ($this->pcConfig['config_moduleclass_sfx'] == 1)
			{
				$moduleclass_sfx = 'alt';
			}

			$show_submit    = 1;
			$show_view      = 1;
			$show_subscribe = $this->pcConfig['config_show_subscribe'];
			$show_links     = $this->pcConfig['config_show_links'];
			$show_devotion  = $this->pcConfig['config_show_devotion'];
			$show_moderator = 1;
			$menu_css       = ' id="pc-menu"';
		}

		if (!$mod)
		{
			echo '<br />';
		}

		echo $menu_style == 0 ? '<div' . $menu_css . '>' : '<ul class="pc-modmenu" id="' . $menuclass . '">';

		if ($show_view && $this->pc_rights->get('pc.view'))
		{
			echo $menu_style == 0 ? '<div align="left">' : '<li class="pc-modmenu">';
			echo '<a class="' . $menuclass . $moduleclass_sfx . '" title="' . htmlentities(JText::_('PRAYERVIEWLIST')) . '" href="' .
				JRoute::_("index.php?option=com_prayer&task=view&Itemid=$itemid") . '">
             ' . htmlentities(JText::_('PRAYERVIEWLIST')) . '</a>';
			echo $menu_style == 0 ? '</div>' : '</li>';
		}

		if ($show_submit && $this->pc_rights->get('pc.post'))
		{
			echo $menu_style == 0 ? '<div align="left">' : '<li class="pc-modmenu">';
			echo '<a class="' . $menuclass . $moduleclass_sfx . '" title="' .
				htmlentities(JText::_('PRAYERSUBMITREQUEST')) . '" href="' .
				JRoute::_("index.php?option=com_prayer&task=newreq&Itemid=$itemid") . '">
            ' . htmlentities(JText::_('PRAYERSUBMITREQUEST')) . '</a>';
			echo $menu_style == 0 ? '</div>' : '</li>';
		}

		if ($show_subscribe && $this->pc_rights->get('pc.subscribe'))
		{
			echo $menu_style == 0 ? '<div align="left">' : '<li class="pc-modmenu">';
			echo '<a class="' . $menuclass . $moduleclass_sfx . '" title="' . htmlentities(JText::_('PRAYERSUBSCRIBE')) .
				'" href="' . JRoute::_("index.php?option=com_prayer&task=subscribe&Itemid=$itemid") . '">
            ' . htmlentities(JText::_('PRAYERSUBSCRIBE')) . '</a>';
			echo $menu_style == 0 ? '</div>' : '</li>';
		}

		if ($show_links && $this->pc_rights->get('pc.view_links'))
		{
			echo $menu_style == 0 ? '<div align="left">' : '<li class="pc-modmenu">';
			echo '<a class="' . $menuclass . $moduleclass_sfx . '" title="' . htmlentities(JText::_('PRAYERLINKSLIST')) .
				'" href="' . JRoute::_("index.php?option=com_prayer&task=view_links&Itemid=$itemid") . '">
            ' . htmlentities(JText::_('PRAYERLINKSLIST')) . '</a>';
			echo $menu_style == 0 ? '</div>' : '</li>';
		}

		if ($show_devotion && $this->pc_rights->get('pc.view_devotional'))
		{
			echo $menu_style == 0 ? '<div align="left">' : '<li class="pc-modmenu">';
			echo '<a class="' . $menuclass . $moduleclass_sfx . '" title="' . htmlentities(JText::_('PRAYERDEVOTIONALS')) .
				'" href="' . JRoute::_("index.php?option=com_prayer&task=view_devotion&Itemid=$itemid") . '">
            ' . htmlentities(JText::_('PRAYERDEVOTIONALS')) . '</a>';
			echo $menu_style == 0 ? '</div>' : '</li>';
		}

		if ($show_moderator && $this->pc_rights->get('pc.moderate') && $this->pcConfig['config_use_admin_alert'] > 1)
		{
			echo $menu_style == 0 ? '<div align="left">' : '<li class="pc-modmenu">';
			echo '<a class="' . $menuclass . $moduleclass_sfx . '" title="' . htmlentities(JText::_('PRAYERMODERATORS')) .
				'" href="' . JRoute::_("index.php?option=com_prayer&task=moderate&Itemid=$itemid") . '">
             ' . htmlentities(JText::_('PRAYERMODERATORS')) . '</a>';
			echo $menu_style == 0 ? '</div>' : '</li>';
		}

		echo $menu_style == 0 ? '</div>' : '</ul>';
	}

	/**
	 * Write Prayer Image
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function writePCImage()
	{
		if (!$this->pcConfig['config_show_image'])
		{
			return;
		}

		jimport('joomla.filesystem.folder');
		$livesite = JURI::base();
		$alt_line = "";
		$border   = "1";
		$width    = "130";
		$height   = "130";

		if ($this->pcConfig['config_use_slideshow'])
		{
			$abpath_folder = JPATH_ROOT . 'media/com_prayer/images/slideshow';

			if (JFolder::exists($abpath_folder))
			{
				$timage = JFolder::files($abpath_folder, 'png|jpg');

				if (!$timage)
				{
					echo JText::_('PRAYERNOIMAGES');
				}
				else
				{
					$i         = count($timage);
					$random    = mt_rand(0, $i - 1);
					$timg_name = $timage[$random];
					$i         = $abpath_folder . '/' . $timg_name;
					$size      = getimagesize($i);

					if ($width == '')
					{
						$width = 100;
					}

					if ($height == '')
					{
						$coeff  = $size[0] / $size[1];
						$height = (int) ($width / $coeff);
					}
				}

				$image = $livesite . 'media/com_prayer/images/slideshow/' . $timg_name;
				?>
				<script type="text/javascript">
					var pcslidespeed =;<?php echo $this->pcConfig['config_slideshow_speed']?>*
					1000;
					var pcslideimages = [];
					var pcslidelinks = [];
					var pcnewwindow = 1 //open links in new window? 1=yes, 0=no
				</script>
				<?php
				$i = 0;

				foreach ($timage as $pcimag)
				{
					if (preg_match('/png$/i', $pcimag) || preg_match('/jpg$/i', $pcimag))
					{
						$the_pcimage = $livesite . 'media/com_prayer/images/slideshow/' . $pcimag;
						?>
						<script type="text/javascript">
							pcslideimages[<?php echo $i; ?>] = "<?php echo $the_pcimage; ?>";
						</script>
						<?php
						$i++;
					}
				}
				?>
				<script type="text/javascript">
					var pcimageholder = [];
					var pcie = document.all;
					for (i = 0; i < pcslideimages.length; i++) {
						pcimageholder[i] = new Image();
						pcimageholder[i].src = pcslideimages[i]
					}

					function gotoshow() {
						if (pcnewwindow)
							window.open(pcslidelinks[pcwhichlink]);
						else
							window.location = pcslidelinks[pcwhichlink]
					}
				</script>
				<div class="mosimage" align="center" style="float:right;padding:0">
					<img src="<?php echo $image; ?>" name="pcslide" border="<?php echo $border; ?>"
					     style="filter:blendTrans(duration=<?php echo $this->pcConfig['config_slideshow_duration']; ?>)"
					     width="<?php echo $width; ?>" height="<?php echo $height; ?>" title="<?php echo $alt_line; ?>"
					     alt="<?php echo $alt_line; ?>">
					<script type="text/javascript">
						var pcwhichlink = 0;
						var pcwhichimage = 0;
						var pcblenddelay = (pcie) ? document.images.pcslide.filters[0].duration * 1000 : 0;

						function pcslideit() {
							if (!document.images) return;
							if (pcie) document.images.pcslide.filters[0].apply();
							document.images.pcslide.src = pcimageholder[pcwhichimage].src;
							if (pcie) document.images.pcslide.filters[0].play();
							pcwhichlink = pcwhichimage;
							pcwhichimage = (pcwhichimage < pcslideimages.length - 1) ? pcwhichimage + 1 : 0;
							setTimeout("pcslideit()", pcslidespeed + pcblenddelay)
						}
						pcslideit()
					</script>
				</div>
				<?php
			}
		}
		else
		{
			echo '<div class="mosimage" align="center" style="float:right;padding:0">
         <img class="pc-img" alt="" title="" border="0" src="media/com_prayer/images/' . $this->pcConfig['config_imagefile'] . '" />
        </div>';
		}
	}

	/**
	 * Write Prayer Header
	 *
	 * @param   string  $text      String tring to Wrigte ot header
	 * @param   bool    $override  Overid if needed
	 * @param   string  $subtext   Sub Text to add
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	public function writePCHeader($text, $override = false, $subtext = "")
	{
		if (!$this->pcConfig['config_show_header_text'] && !$override)
		{
			return;
		}

		$return = $this->PCkeephtml(htmlentities($text)) . '<br /><br />';

		if (!empty($subtext))
		{
			$return .= $this->PCkeephtml($subtext);
		}

		return $return;
	}

	/**
	 * Write Prayer Footer
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function PrayerFooter()
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_prayer', JPATH_SITE);
		$user                 = JFactory::getUser();
		$config_bmrss_service = $this->pcConfig['config_bmrss_service'];
		$return = '';

		if ($this->pcConfig['config_show_credit'])
		{
			$return .= '<div class="footer" style="clear:both;text-align:center;font-size:x-small;">' .
				JText::_('PRAYERFOOTER') . ' <a href="https://www.joomlabiblestudy.com/" title="JBSM">Joomla Bible Study</a></div>';
		}

		if ($this->pcConfig['config_show_rss'] && $this->pc_rights->get('pc.view'))
		{
			!$user->guest ? $key = '&key=' . md5($this->pcConfig['config_rss_authkey']) : $key = "";
			$rss_link = JRoute::_('index.php?option=com_prayer&amp;task=rss' . $key);
			$img      = JHTML::_('image', JURI::base() . 'media/system/images/livemarks.png', htmlentities(JText::_('PRAYERFEEDS')), 'style="border:0;"');
			$return .= '<br /><div style="text-align:right;">';

			if ($config_bmrss_service == 1)
			{
				$return .= "<a href=\"http://www.addthis.com/feed.php?username=&amp;h1=" . $rss_link .
					"&amp;t1=\" onclick=\"return addthis_open(this, 'feed', '" .
					$rss_link . "')\" title=\"" . htmlentities(JText::_('PRAYERFEEDS')) . " by AddThis" .
					"\" target=\"_blank\"><img src=\"http://s7.addthis.com/static/btn/sm-rss-en.gif\" width=\"83\" height=\"16\" title=\"" .
					htmlentities(JText::_('USRLPCFEEDS')) . " by AddThis" . "\" style=\"border:0\"/></a><script type=\"text/javascript\"" .
					" src=\"http://s7.addthis.com/js/250/addthis_widget.js#username=\"></script>";
			}
			elseif ($config_bmrss_service == 2)
			{
				$return .= "<a class=\"a2a_dd\" href=\"http://www.addtoany.com/subscribe?linkname=" . htmlentities(JText::_('PRAYERFEEDS')) . "&amp;linkurl=" .
					$rss_link . "\" title=\"" . htmlentities(JText::_('PRAYERFEEDS')) . " by AddToAny" .
					"\"><img src=\"http://static.addtoany.com/buttons/subscribe_16_16.gif\" width=\"16\" height=\"16\" border=\"0\" title=\"" .
					htmlentities(JText::_('USRLPCFEEDS')) . " by AddToAny" . "\"/></a><script type=\"text/javascript\">a2a_linkname=\"" .
					htmlentities(JText::_('USRLPCFEEDS')) . "\";a2a_linkurl=\"" . $rss_link . "\";</script><script type=\"text/javascript\"" .
					" src=\"http://static.addtoany.com/menu/feed.js\"></script>";
			}
			else
			{
				$return .= "<a href=\"" . $rss_link . "\" target=\"_blank\" title=\"" . htmlentities(JText::_('PRAYERFEEDS')) . "\">" . $img . "</a>";
			}

			$return .= '&nbsp;&nbsp;</div><br /><br /><br />';
		}

		return $return;
	}

	/**
	 * Get Admin Data
	 *
	 * @return object
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

	/**
	 * Get Time Zone Date
	 *
	 * @param   object  $data  ?
	 * @param   string  $alt   ?
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function PCgetTimeZoneData($data, $alt = null)
	{
		$user               = JFactory::getUser();
		$userid             = $user->get('id');
		$juser              = new JUser($userid);
		$usertz             = $juser->getParam('timezone');
		$conf               = JFactory::getConfig();
		$config_offset      = $conf->get('offset');
		$dateTime           = [];
		$dateset            = new DateTime($data->date . ' ' . $data->time, new DateTimeZone('UTC'));
		$config_offset_user = $conf->get('offset_user');

		if (isset($usertz))
		{
			$dateset->setTimeZone(new DateTimeZone($usertz));
		}
		elseif (isset($config_offset_user))
		{
			$dateset->setTimeZone(new DateTimeZone($config_offset_user));
		}
		else
		{
			$dateset->setTimeZone(new DateTimeZone($config_offset));
		}

		$dateTime['time'] = $dateset->format(!is_null($alt) ? $alt : $this->pcConfig['config_time_format']);
		$dateTime['date'] = $dateset->format(!is_null($alt) ? $alt : $this->pcConfig['config_date_format']);
		$dateTime['tz']   = $dateset->format('T');
		$tzid             = $dateset->format('e');

		if ($tzid == 'UTC')
		{
			$dateTime['tzid'] = 'Coordinated Universal Time';
		}
		elseif ($tzid == 'GMT')
		{
			$dateTime['tzid'] = 'Greenwich Mean Time';
		}
		else
		{
			$dateTime['tzid'] = $tzid;
		}

		return $dateTime;
	}

	/**
	 * Date format to Strf Time
	 *
	 * @param   string  $dateFormat  Date & Time
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function dateFormatToStrftime($dateFormat)
	{
		$strarray = [
			'd' => '%d', 'D' => '%a', 'j' => '%e', 'l' => '%A', 'N' => '%u', 'w' => '%w', 'z' => '%j',
			'W' => '%V', 'F' => '%B', 'm' => '%m', 'M' => '%b', 'o' => '%G', 'Y' => '%Y', 'y' => '%y',
			'a' => '%P', 'A' => '%p', 'g' => '%l', 'h' => '%I', 'H' => '%H', 'i' => '%M', 's' => '%S',
			'O' => '%z', 'T' => '%Z', 'U' => '%s', 'r' => '%c'
		];

		return strtr((string) $dateFormat, $strarray);
	}

	/**
	 * Numerig Etrities
	 *
	 * @param   string  $string  String
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	public function Numeric_Entities($string)
	{
		$mapping = [];

		foreach (get_html_translation_table(HTML_ENTITIES, ENT_QUOTES) as $char => $entity)
		{
			$mapping[$entity] = '&#' . ord($char) . ';';
		}

		return str_replace(array_keys($mapping), $mapping, $string);
	}
}
