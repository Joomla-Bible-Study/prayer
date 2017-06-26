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

class CWMPrayerControllerPrayer extends CWMPrayerController
{
	private $prayer;

	public function __construct($config = array())
	{
		$this->prayer = new CWMPrayerSitePrayer;

		parent::__construct($config);
	}

	public function newreqsubmit()
	{
		JSession::checkToken() or jexit('Invalid Token');
		$app           = JFactory::getApplication();
		$pc_rights     = $this->prayer->pc_rights;
		$mod           = $app->input->getString('mod');
		$modtype       = $app->input->getString('modtype');
		$returntoarray = preg_split('/\&return/', $_SERVER['HTTP_REFERER'], -1, PREG_SPLIT_NO_EMPTY);
		$returnto      = $returntoarray[0];
		jimport('joomla.date.date');
		jimport('joomla.mail.helper');
		jimport('joomla.filter.output');
		$user           = JFactory::getUser();
		$db             = JFactory::getDBO();
		$itemid         = $this->prayer->PCgetItemid();
		$dateset        = new JDate;
		$time           = $dateset->format('H:i:s');
		$date           = $dateset->format('Y-m-d');
		$session        = JFactory::getSession();
		$sessionid      = $session->get('session.token');
		$newtitle       = $app->input->getString('newtitle');
		$newrequest     = $app->input->getString('newrequest');
		$newrequester   = $app->input->getInt('requesterid');
		$newemail       = $app->input->getString('newemail');
		$newtopic       = $app->input->getInt('newtopic');
		$newrequesterid = $app->input->getInt('newrequesterid');

		if (!empty($newemail) && JMailHelper::isEmailAddress($newemail))
		{
			if (!$this->prayer->PCcheckEmail($newemail))
			{
				if (isset($_GET['modtype']))
				{
					$this->setMessage(htmlentities(JText::_('CWMPRAYERINVALIDDOMAIN')), 'message');
					$this->setRedirect(JRoute::_($returnto, false));
				}
				else
				{
					$returnurl = JRoute::_("index.php?option=com_cwmprayer&task=newreq&Itemid=" . (int) $itemid . '&return_msg=' .
						htmlentities(JText::_('CWMPRAYERINVALIDDOMAIN'))
					);
					$this->setRedirect(JRoute::_($returnurl, false));
				}
			}

			if (!$this->prayer->PCcheckBlockedEmail($newemail))
			{
				if (isset($_GET['modtype']))
				{
					$this->setMessage(JText::_('CWMPRAYERINVALIDEMAIL'), 'message');
					$this->setRedirect(JRoute::_($returnto, false));
				}
				else
				{
					$returnurl = JRoute::_("index.php?option=com_cwmprayer&task=newreq&Itemid=" . (int) $itemid . '&return_msg=' .
						htmlentities(JText::_('CWMPRAYERINVALIDEMAIL'))
					);
					$this->setRedirect(JRoute::_($returnurl, false));
				}
			}
		}

		if (!empty($newrequest))
		{
			if (!$this->prayer->pcConfig['config_captcha_bypass_4member'] || $this->prayer->pcConfig['config_captcha_bypass_4member'] && $user->guest)
			{
				$this->pcCaptchaValidate($returnto, $itemid, $modtype, 'newreq');
			}

			if ($this->prayer->PCspamcheck($newrequest) && $this->prayer->PCspamcheck($newrequester))
			{
				$newtitle     = $this->prayer->PCcleanText($newtitle);
				$newrequest   = $this->prayer->PCcleanText($newrequest);
				$newrequest   = addslashes($newrequest);
				$newrequester = JFilterOutput::cleanText($newrequester);
				$newemail     = JMailHelper::cleanAddress($newemail);

				if (!JMailHelper::isEmailAddress($newemail) && $this->prayer->pcConfig['config_use_admin_alert'] == 1)
				{
					if (isset($_GET['modtype']))
					{
						$this->setMessage(htmlentities(JText::_('CWMPRAYERINVALIDEMAIL')), 'message');
						$this->setRedirect(JRoute::_($returnto, false));
					}
					else
					{
						$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=newreq&Itemid=' . (int) $itemid . '&return_msg=' .
							htmlentities(JText::_('CWMPRAYERINVALIDEMAIL'))
						);
						$this->setRedirect(JRoute::_($returnurl, false));
					}
				}

				$sendpriv = $app->input->getBool('sendpriv');

				if ($newrequester == '')
				{
					$newrequester = htmlentities(JText::_('CWMPRAYERANONUSER'));
				}

				if ($this->prayer->pcConfig['config_use_wordfilter'] > 0)
				{
					$newtitle     = $this->prayer->PCbadword_replace($newtitle);
					$newrequest   = $this->prayer->PCbadword_replace($newrequest);
					$newemail     = $this->prayer->PCbadword_replace($newemail);
					$newrequester = $this->prayer->PCbadword_replace($newrequester);
				}

				if ($this->prayer->pcConfig['config_use_admin_alert'] == 0 && $pc_rights->get('pc.publish'))
				{
					$sql = "INSERT INTO #__cwmprayer (id,requesterid,requester,date,request,state,displaystate," .
					"sendto,email,adminsendto,sessionid,title,topic) VALUES (''," .
						(int) $newrequesterid . "," . $db->q($newrequester) . "," . $db->q($date . ' ' . $time) . "," . $db->q($newrequest) .
						",'1'," . (int) $sendpriv . ",'0000-00-00 00:00:00'," . $db->q($newemail) .
						",'0000-00-00 00:00:00'," . (int) $db->q($sessionid) . "," .
						$db->q($db->escape($newtitle), false) . "," . (int) $newtopic . ")";

					$db->setQuery($sql);

					if (!$db->execute())
					{
						JError::raiseError(500, $db->stderr());
					}

					$lastId = $db->insertid();
				}
				elseif ($this->prayer->pcConfig['config_use_admin_alert'] > 0)
				{
					$sql = "INSERT INTO #__cwmprayer (id,requesterid,requester,date,request,state,displaystate,sendto" .
						",email,adminsendto,sessionid,title,topic) VALUES (''," . (int) $newrequesterid . "," .
						$db->q($newrequester) . "," . $db->q($date . ' ' . $time) . "," . $db->q($newrequest) .
						",'0'," . (int) $sendpriv . ",'0000-00-00 00:00:00'," . $db->q($newemail) .
						",'0000-00-00 00:00:00'," . (int) $db->q($sessionid) . "," .
						$db->q($newtitle) . "," . (int) $newtopic . ")";
					$db->setQuery($sql);

					if (!$db->execute())
					{
						JError::raiseError(500, $db->stderr());
					}

					$lastId = $db->insertid();
				}

				// Notify Site Admin(s) and/or moderators on event of new request
				if ($this->prayer->pcConfig['config_use_admin_alert'] > 1 && !$pc_rights->get('pc.publish'))
				{
					if ($this->prayer->pcConfig['config_admin_distrib_type'] > 1 && $this->prayer->pcConfig['config_pms_plugin'])
					{
						$this->prayer->PCsendPM($newrequesterid, $newrequester, $newrequest, $newemail, $sendpriv, $lastId, $sessionid, true);
					}
				}
				elseif ($this->prayer->pcConfig['config_use_admin_alert'] < 2)
				{
					if ($this->prayer->pcConfig['config_use_admin_alert'] == 1 && !empty($newemail))
					{
						if (JPluginHelper::isEnabled('system', 'prayercenteremail'))
						{
							$results = plgSystemPrayerEmail::pcEmailTask('PCconfirm_notification', array('0' => $lastId));
						}

						if (isset($_GET['modtype']))
						{
							$this->setMessage(htmlentities(JText::_('CWMPRAYERREQSUBMITCONFIRM')), 'message');
							$this->setRedirect(JRoute::_($returnto, false));
						}
						else
						{
							$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=view&Itemid=' . (int) $itemid . '&return_msg=' .
								htmlentities(JText::_('CWMPRAYERREQSUBMITCONFIRM'))
							);
							$this->setRedirect(JRoute::_($returnurl, false));
						}
					}

					if ($this->prayer->pcConfig['config_use_admin_alert'] == 0)
					{
						if ($sendpriv)
						{
							if ($this->prayer->pcConfig['config_distrib_type'] > 1 && $this->prayer->pcConfig['config_pms_plugin'])
							{
								$this->prayer->PCsendPM($newrequesterid, $newrequester, $newrequest, $newemail, $sendpriv);
							}
						}
						elseif (!$sendpriv)
						{
							if ($this->prayer->pcConfig['config_distrib_type'] > 1 && $this->prayer->pcConfig['config_pms_plugin'])
							{
								$this->prayer->PCsendPM($newrequesterid, $newrequester, $newrequest, $newemail, $sendpriv, $lastId, $sessionid);
							}
						}
					}
				}

				if (isset($_GET['modtype']))
				{
					$this->setMessage(htmlentities(JText::_('CWMPRAYERREQSUBMIT')), 'message');
					$this->setRedirect(JRoute::_($returnto, false));
				}
				else
				{
					$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=view&Itemid=' . (int) $itemid . '&return_msg=' .
						htmlentities(JText::_('CWMPRAYERREQSUBMIT')), false
					);
					$this->setRedirect($returnurl);
				}
			}
			else
			{
				if (stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']))
				{
					if (isset($_GET['modtype']))
					{
						$this->setMessage(JText::_('CWMPRAYERSPAMMSG'), 'message');
						$this->setRedirect(JRoute::_($returnto, false));
					}
					else
					{
						$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=newreq&Itemid=' . (int) $itemid . '&return_msg=' .
							htmlentities(JText::_('CWMPRAYERSPAMMSG'))
						);
						$this->setRedirect(JRoute::_($returnurl, false));
					}
				}
				else
				{
					header('HTTP/1.0 403 Forbidden');

					// Delay spammers a bit
					sleep(rand(2, 5));
					JError::raiseError(403, 'No Aurth');
				}
			}
		}
		else
		{
			if (isset($_GET['modtype']))
			{
				$this->setMessage(htmlentities(JText::_('CWMPRAYERFORMNC')), 'message');
				$this->setRedirect(JRoute::_($returnto, false));
			}
			else
			{
				$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=view&Itemid=' . (int) $itemid . '&return_msg=' .
					htmlentities(JText::_('CWMPRAYERFORMNC'))
				);
				$this->setRedirect(JRoute::_($returnurl, false));
			}
		}
	}

	public function pcCaptchaValidate($returnto, $itemid, $modtype, $task)
	{
		$JVersion = new JVersion();

		if ($this->prayer->pcConfig['config_captcha'] == '1')
		{
			$scode = JRequest::getVar('security_code', null, 'post');

			if (!$this->prayer->PCCaptchaValidate($scode, 'newreq'))
			{
				if (isset($_GET['modtype']))
				{
					$this->setRedirect(JRoute::_($returnto . '&' . $modtype . '=' . htmlentities(JText::_('CWMPRAYERINVALIDCODE')), false));
				}
				else
				{
					$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=' . $task . '&Itemid=' . (int) $itemid . '&return_msg=' .
						htmlentities(JText::_('CWMPRAYERINVALIDCODE'))
					);
					$this->setRedirect(JRoute::_($returnurl, false));
				}
			}
		}
		elseif ($this->prayer->pcConfig['config_captcha'] == '3' && JPluginHelper::isEnabled('system', 'crosscheck'))
		{
			$results = plgSystemCrossCheck::checkCrossChk(JRequest::getVar('user_code', null, 'method'));

			if ($results !== true)
			{
				if (isset($_GET['modtype']))
				{
					$this->setRedirect(JRoute::_($returnto . '&' . $modtype . '=' . $results, false));
				}
				else
				{
					$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=' . $task . '&Itemid=' . (int) $itemid . '&return_msg=' . $results);

					$this->setRedirect(JRoute::_($returnurl, false));
				}
			}
		}
		elseif ($this->prayer->pcConfig['config_captcha'] == '6'
			&& $this->prayer->pcConfig['config_recap_pubkey'] != ""
			&& $this->prayer->pcConfig['config_recap_privkey'] != ""
		)
		{
			require_once JPATH_ROOT . '/madia/com_cwmprayer/captcha/recaptchalib.php';
			$privatekey = $this->prayer->pcConfig['config_recap_privkey'];

			$resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

			if (!$resp->is_valid)
			{
				if (isset($_GET['modtype']))
				{
					$this->setRedirect(JRoute::_($returnto . '&' . $modtype . '=' . htmlentities(JText::_('CWMPRAYERINVALIDCODE')), false));
				}
				else
				{
					$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=' . $task . '&Itemid=' . (int) $itemid . '&return_msg=' .
						htmlentities(JText::_('CWMPRAYERINVALIDCODE'))
					);

					$this->setRedirect(JRoute::_($returnurl, false));
				}
			}
		}
		elseif ($this->prayer->pcConfig['config_captcha'] == '7' && (real) $JVersion->RELEASE >= 2.5)
		{
			$session      = JFactory::getSession();
			$respchk      = $session->has('pc_respchk');
			$plugin       = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
			$captcha      = JCaptcha::getInstance($plugin, array('namespace' => 'adminForm'));
			$captcha_code = "";
			$resp         = $captcha->checkAnswer($captcha_code);

			if ($resp == false && !$respchk)
			{
				if (isset($_GET['modtype']))
				{
					$this->setRedirect(JRoute::_($returnto . '&' . $modtype . '=' . htmlentities(JText::_('CWMPRAYERINVALIDCODE')), false));
				}
				else
				{
					$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=' . $task . '&Itemid=' . (int) $itemid . '&return_msg=' .
						htmlentities(JText::_('CWMPRAYERINVALIDCODE'))
					);
					$this->setRedirect(JRoute::_($returnurl, false));
				}
			}
			elseif ($respchk)
			{
				$session->clear('pc_respchk');
			}
		}

		return true;
	}

	public function subscribesubmit()
	{
		JSession::checkToken() or jexit('Invalid Token');
		$app           = JFactory::getApplication();
		$pc_rights     = $this->prayer->pc_rights;
		$mod           = $app->input->getString('mod');
		$modtype       = $app->input->getString('modtype');
		$returntoarray = preg_split('/\&return/', $_SERVER['HTTP_REFERER'], -1, PREG_SPLIT_NO_EMPTY);
		$returnto      = $returntoarray[0];
		jimport('joomla.date.date');
		jimport('joomla.mail.helper');
		jimport('joomla.filter.output');
		$itemid = $this->prayer->PCgetItemid();
		$user   = JFactory::getUser();

		$plugin = new plgSystemCWMPrayerEmail((object) 'com_cwmprayer');

		if (!$this->prayer->pcConfig['config_captcha_bypass_4member'] || $this->prayer->pcConfig['config_captcha_bypass_4member'] && $user->guest)
		{
			$this->pcCaptchaValidate($returnto, $itemid, $modtype, 'subscribe');
		}

		$session      = JFactory::getSession();
		$sessionid    = $session->get('session.token');
		$newsubscribe = $app->input->getString('newsubscribe', null);

		if (!empty($newsubscribe) && JMailHelper::isEmailAddress($newsubscribe))
		{
			if (!$this->prayer->PCcheckEmail($newsubscribe))
			{
				if (isset($_GET['modtype']))
				{
					$this->setMessage(htmlentities(JText::_('CWMPRAYERINVALIDDOMAIN')), 'message');
					$this->setRedirect(JRoute::_($returnto, false));
				}
				else
				{
					$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=subscribe&Itemid=' . (int) $itemid . '&return_msg=' .
						htmlentities(JText::_('CWMPRAYERINVALIDDOMAIN'))
					);
					$this->setRedirect(JRoute::_($returnurl, false));
				}
			}

			if (!$this->prayer->PCcheckBlockedEmail($newsubscribe))
			{
				if (isset($_GET['modtype']))
				{
					$this->setMessage(htmlentities(JText::_('CWMPRAYERINVALIDEMAIL')), 'message');
					$this->setRedirect(JRoute::_($returnto, false));
				}
				else
				{
					$returnurl = JRoute::_("index.php?option=com_cwmprayer&task=subscribe&Itemid=" . (int) $itemid . '&return_msg=' .
						htmlentities(JText::_('CWMPRAYERINVALIDEMAIL'))
					);
					$this->setRedirect(JRoute::_($returnurl, false));
				}
			}
		}

		if ($this->prayer->pcConfig['config_use_wordfilter'] > 0)
		{
			$newsubscribe = $this->prayer->PCbadword_replace($newsubscribe);
		}

		$newsubscribe = JMailHelper::cleanAddress($newsubscribe);

		if (JMailHelper::isEmailAddress($newsubscribe))
		{
			$dateset = new JDate;
			$date    = $dateset->format('Y-m-d T');
			$db      = JFactory::getDBO();
			$db->setQuery("SELECT email FROM #__cwmprayer_subscribe");
			$readq     = $db->loadObjectList();
			$duplicate = '0';

			foreach ($readq as $dup)
			{
				if ($newsubscribe == $dup->email)
				{
					$duplicate = '1';
				}
			}

			if ($duplicate != '1')
			{
				if ($this->prayer->pcConfig['config_admin_approve_subscribe'] == 0)
				{
					$sql = "INSERT INTO #__cwmprayer_subscribe (id,email,date,approved,sessionid) VALUES (''," .
						$db->q($newsubscribe) . "," . $db->q($date) . ",'1'," . (int) $db->q($sessionid) . ")";
					$db->setQuery($sql);

					if (!$db->execute())
					{
						JError::raiseError(500, $db->stderr());
					}

					$lastId = $db->insertid();
				}
				elseif ($this->prayer->pcConfig['config_admin_approve_subscribe'] > 0)
				{
					$sql = "INSERT INTO #__cwmprayer_subscribe (id,email,date,approved,sessionid) VALUES (''," .
						$db->q($newsubscribe) . "," . $db->q($date) . ",'0'," . (int) $db->q($sessionid) . ")";
					$db->setQuery($sql);

					if (!$db->execute())
					{
						JError::raiseError(500, $db->stderr());
					}

					$lastId = $db->insertid();
				}

				if ($this->prayer->pcConfig['config_admin_approve_subscribe'] == 2)
				{
					if (JPluginHelper::isEnabled('system', 'prayercenteremail'))
					{
						$plugin->EmailTask('CWMPRAYERconfirm_sub_notification',
							array('0' => $newsubscribe, '1' => $lastId, '2' => $sessionid)
						);
					}

					if (isset($_GET['modtype']))
					{
						$this->setMessage(htmlentities(JText::_('CWMPRAYERREQSUBMITCONFIRM')), 'message');
						$this->setRedirect(JRoute::_($returnto, false));
					}
					else
					{
						$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=subscribe&Itemid=' . (int) $itemid . '&return_msg=' .
							htmlentities(JText::_('CWMPRAYERREQSUBMITCONFIRM'))
						);
						$this->setRedirect(JRoute::_($returnurl, false));
					}
				}

				if ($this->prayer->pcConfig['config_admin_approve_subscribe'] == 1)
				{
					if (JPluginHelper::isEnabled('system', 'prayercenteremail'))
					{
						$plugin->EmailTask('CWMPRAYERconfirm_sub_notification', array('0' => $newsubscribe, '1' => $lastId, '2' => $sessionid));
					}

					if (isset($_GET['modtype']))
					{
						$this->setMessage(htmlentities(JText::_('CWMPRAYERENTRYACCEPTED')), 'message');
						$this->setRedirect(JRoute::_($returnto, false));
					}
					else
					{
						$returnurl = JRoute::_("index.php?option=com_cwmprayer&task=subscribe&Itemid=" . (int) $itemid . "&return_msg=" .
							htmlentities(JText::_('CWMPRAYERREQSUBMITAUTH'))
						);
						$this->setRedirect(JRoute::_($returnurl, false));
					}
				}

				if ($pc_rights->get('pc.subscribe') && $this->prayer->pcConfig['config_admin_approve_subscribe'] == 0)
				{
					if (JPluginHelper::isEnabled('system', 'prayercenteremail'))
					{
						$plugin->EmailTask('PCemail_subscribe', array('0' => $newsubscribe));

						if ($this->prayer->pcConfig['config_email_subscribe'])
						{
							$plugin->pcEmailTask('PCadmin_email_subscribe_notification', array('0' => $newsubscribe));
						}
					}

					if (isset($_GET['modtype']))
					{
						$this->setMessage(htmlentities(JText::_('CWMPRAYERENTRYACCEPTED')), 'message');
						$this->setRedirect(JRoute::_($returnto, false));
					}
					else
					{
						$returnurl = JRoute::_("index.php?option=com_cwmprayer&task=subscribe&Itemid=" . (int) $itemid . "&return_msg=" .
							htmlentities(JText::_('CWMPRAYERENTRYACCEPTED'))
						);

						$this->setRedirect(JRoute::_($returnurl, false));
					}
				}
			}
			else
			{
				if (isset($_GET['modtype']))
				{
					$this->setMessage(htmlentities(JText::_('CWMPRAYERDUPLICATEDENTRY')), 'message');
					$this->setRedirect(JRoute::_($returnto, false));
				}
				else
				{
					$returnurl = JRoute::_("index.php?option=com_cwmprayer&task=subscribe&Itemid=" . (int) $itemid . "&return_msg=" .
						htmlentities(JText::_('CWMPRAYERDUPLICATEDENTRY'))
					);
					$this->setRedirect(JRoute::_($returnurl, false));
				}
			}
		}
		else
		{
			if (isset($_GET['modtype']))
			{
				$this->setMessage(htmlentities(JText::_('CWMPRAYERINVALIDEMAIL')), 'message');
				$this->setRedirect(JRoute::_($returnto, false));
			}
			else
			{
				$returnurl = JRoute::_("index.php?option=com_cwmprayer&task=subscribe&Itemid=" . (int) $itemid . "&return_msg=" .
					htmlentities(JText::_('CWMPRAYERINVALIDEMAIL'))
				);
				$this->setRedirect(JRoute::_($returnurl, false));
			}
		}
	}

	public function unsubscribesubmit()
	{
		$app = JFactory::getApplication();
		JSession::checkToken() or jexit('Invalid Token');
		$mod           = $app->input->getString('mod');
		$modtype       = $app->input->getString('modtype');
		$returntoarray = preg_split('/\&return/', $_SERVER['HTTP_REFERER'], -1, PREG_SPLIT_NO_EMPTY);
		$returnto      = $returntoarray[0];
		jimport('joomla.date.date');
		jimport('joomla.mail.helper');
		jimport('joomla.filter.output');
		$itemid = $this->prayer->PCgetItemid();
		$user   = JFactory::getUser();

		if (!$this->prayer->pcConfig['config_captcha_bypass_4member'] || $this->prayer->pcConfig['config_captcha_bypass_4member'] && $user->guest)
		{
			$this->pcCaptchaValidate($returnto, $itemid, $modtype, 'subscribe');
		}

		$newsubscribe = $app->input->getString('newsubscribe');

		if (!empty($newsubscribe) && JMailHelper::isEmailAddress($newsubscribe))
		{
			if (!$this->prayer->PCcheckEmail($newsubscribe))
			{
				if (isset($_GET['modtype']))
				{
					$this->setMessage(htmlentities(JText::_('CWMPRAYERINVALIDDOMAIN')), 'message');
					$this->setRedirect(JRoute::_($returnto, false));
				}
				else
				{
					$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=subscribe&Itemid=' . (int) $itemid . '&return_msg=' .
						htmlentities(JText::_('CWMPRAYERINVALIDDOMAIN'))
					);
					$this->setRedirect(JRoute::_($returnurl, false));
				}
			}

			if (!$this->prayer->PCcheckBlockedEmail($newsubscribe))
			{
				if (isset($_GET['modtype']))
				{
					$this->setMessage(htmlentities(JText::_('CWMPRAYERINVALIDEMAIL')), 'message');
					$this->setRedirect(JRoute::_($returnto, false));
				}
				else
				{
					$returnurl = JRoute::_("index.php?option=com_cwmprayer&task=subscribe&Itemid=" . (int) $itemid . '&return_msg=' .
						htmlentities(JText::_('CWMPRAYERINVALIDEMAIL'))
					);
					$this->setRedirect(JRoute::_($returnurl, false));
				}
			}
		}

		$newsubscribe = JMailHelper::cleanAddress($newsubscribe);

		if (JMailHelper::isEmailAddress($newsubscribe))
		{
			$db = JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__cwmprayer_subscribe WHERE email=" . $db->q($newsubscribe));
			$readq = $db->loadObjectList();

			if ($this->prayer->pcConfig['config_admin_approve_subscribe'] == 2)
			{
				if (JPluginHelper::isEnabled('system', 'prayercenteremail'))
				{
					$plugin->pcEmailTask(
						'PCconfirm_unsub_notification',
						array(
							'0' => $newsubscribe,
							'1' => $readq[0]->id,
							'2' => $readq[0]->sessionid
						)
					);
				}

				if (isset($_GET['modtype']))
				{
					$this->setMessage(htmlentities(JText::_('CWMPRAYERREQSUBMITCONFIRM')), 'message');
					$this->setRedirect(JRoute::_($returnto, false));
				}
				else
				{
					$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=subscribe&Itemid=' . (int) $itemid . '&return_msg=' .
						htmlentities(JText::_('CWMPRAYERREQSUBMITCONFIRM'))
					);
					$this->setRedirect(JRoute::_($returnurl, false));
				}
			}
			else
			{
				if ($newsubscribe == $readq[0]->email)
				{
					$db->setQuery("DELETE FROM #__cwmprayer_subscribe WHERE email=" . $db->q($newsubscribe));

					if (!$db->execute())
					{
						JError::raiseError(500, $db->stderr());
					}

					if (JPluginHelper::isEnabled('system', 'prayercenteremail'))
					{
						$results = plgSystemPrayerEmail::pcEmailTask('PCemail_unsubscribe', array('0' => $newsubscribe));
					}

					if (isset($_GET['modtype']))
					{
						$this->setMessage(htmlentities(JText::_('CWMPRAYERENTRYREMOVED')), 'message');
						$this->setRedirect(JRoute::_($returnto, false));
					}
					else
					{
						$returnurl = JRoute::_("index.php?option=com_cwmprayer&task=subscribe&Itemid=" . (int) $itemid . "&return_msg=" .
							htmlentities(JText::_('CWMPRAYERENTRYREMOVED'))
						);
						$this->setRedirect(JRoute::_($returnurl, false));
					}
				}
				else
				{
					if (isset($_GET['modtype']))
					{
						$this->setMessage(htmlentities(JText::_('CWMPRAYERNOTSUBSCRIBED')), 'message');
						$this->setRedirect(JRoute::_($returnto, false));
					}
					else
					{
						$returnurl = JRoute::_("index.php?option=com_cwmprayer&task=subscribe&Itemid=" . (int) $itemid . "&return_msg=" .
							htmlentities(JText::_('CWMPRAYERNOTSUBSCRIBED'))
						);
						$this->setRedirect(JRoute::_($returnurl, false));
					}
				}
			}
		}
		else
		{
			if (isset($_GET['modtype']))
			{
				$this->setMessage(htmlentities(JText::_('CWMPRAYERINVALIDEMAIL')), 'message');
				$this->setRedirect(JRoute::_($returnto, false));
			}
			else
			{
				$returnurl = JRoute::_("index.php?option=com_cwmprayer&task=subscribe&Itemid=" . (int) $itemid . "&return_msg=" .
					htmlentities(JText::_('CWMPRAYERINVALIDEMAIL'))
				);
				$this->setRedirect(JRoute::_($returnurl, false));
			}
		}
	}

	public function editrequest()
	{
		$itemid = $this->prayer->PCgetItemid();
		$db     = JFactory::getDBO();
		$app    = JFactory::getApplication();
		jimport('joomla.date.date');
		$dateset = new JDate;
		$time    = $dateset->format('H:i:s');
		$date    = $dateset->format('Y-m-d');
		$id      = $app->input->getInt('id');
		$request = $app->input->getString('newrequest');
		$db->setQuery("UPDATE #__cwmprayer SET request=" . $db->q($request) . ", date=" . $db->q($date . ' ' . $time) . " WHERE id=" . (int) $id
		);

		if (!$db->execute())
		{
			JError::raiseError(500, $db->stderr());
		}

		$db->setQuery("SELECT * FROM #__cwmprayer WHERE id=" . (int) ($id));
		$readresult = $db->loadObjectList();
		$model      = $this->getModel('prayer');
		$model->checkin();
		$returnurl = JRoute::_("index.php?option=com_cwmprayer&task=" . $_POST['last'] . "&Itemid=" . (int) $itemid);
		$this->setRedirect(JRoute::_($returnurl, false));
	}

	public function closeedit()
	{
		$app = JFactory::getApplication();
		$itemid = $this->prayer->PCgetItemid();
		$last   = $app->input->getString('last');
		$id     = $app->input->getInt('id');
		$model  = $this->getModel('prayer');
		$model->checkin();
		$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=' . $last . '&Itemid=' . (int) $itemid);
		$this->setRedirect(JRoute::_($returnurl, false));
	}

	public function delrequest()
	{
		$app = JFactory::getApplication();
		$id  = null;

		if ($this->prayer->pcConfig['config_comments'] == 1)
		{
			$jcomments = JPATH_SITE . '/components/com_jcomments/jcomments.php';

			if (file_exists($jcomments))
			{
				require_once $jcomments;
			}
		}
		elseif ($this->prayer->pcConfig['config_comments'] == 2)
		{
			$jsc = JPATH_SITE . '/components/com_jsitecomments/helpers/jsc_class.php';

			if (file_exists($jsc))
			{
				require_once $jsc;
			}
		}

		$itemid = $this->prayer->PCgetItemid();
		$db     = JFactory::getDBO();
		$cid    = ($app->input->getArray('delete'));

		while (list($key, $val) = each($cid))
		{
			$delreq = "DELETE FROM #__cwmprayer WHERE id=" . (int) $key;
			$db->setQuery($delreq);

			if (!$db->execute())
			{
				JError::raiseError(500, $db->stderr());
			}

			if ($this->prayer->pcConfig['config_comments'] > 0)
			{
				if (file_exists($jcomments))
				{
					JComments::deleteComments($id, 'com_cwmprayer');
				}
				elseif (file_exists($jsc))
				{
					jsitecomments::JSCdelComment('com_cwmprayer', $id);
				}
			}
		}

		$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=moderate&Itemid=' . (int) $itemid);
		$this->setRedirect(JRoute::_($returnurl, false));
	}

	public function editdelrequest()
	{
		$app = JFactory::getApplication();

		if ($this->prayer->pcConfig['config_comments'] == 1)
		{
			$jcomments = JPATH_SITE . '/components/com_jcomments/jcomments.php';

			if (file_exists($jcomments))
			{
				require_once $jcomments;
			}
		}
		elseif ($this->prayer->pcConfig['config_comments'] == 2)
		{
			$jsc = JPATH_SITE . '/components/com_jsitecomments/helpers/jsc_class.php';

			if (file_exists($jsc))
			{
				require_once $jsc;
			}
		}

		$itemid = $this->prayer->PCgetItemid();
		$db     = JFactory::getDBO();
		$id     = $app->input->getInt('id');
		$delreq = "DELETE FROM #__cwmprayer WHERE id=" . (int) $id;
		$db->setQuery($delreq);

		if (!$db->execute())
		{
			JError::raiseError(500, $db->stderr());
		}

		if ($this->prayer->pcConfig['config_comments'] > 0)
		{
			if (file_exists($jcomments))
			{
				JComments::deleteComments($id, 'com_cwmprayer');
			}
			elseif (file_exists($jsc))
			{
				jsitecomments::JSCdelComment('com_cwmprayer', $id);
			}
		}

		$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=view&Itemid=' . (int) $itemid);
		$this->setRedirect(JRoute::_($returnurl, false));
	}

	public function pubrequest()
	{
		jimport('joomla.plugin.helper');
		$app = JFactory::getApplication();
		$itemid  = $this->prayer->PCgetItemid();
		$newrequesterid = null;
		$db      = JFactory::getDBO();
		$cid     = $app->input->getArray('delete');
		$idarray = array_keys($cid);

		while (list($key, $val) = each($cid))
		{
			$pubreq = "UPDATE #__cwmprayer SET state='1' WHERE id=" . (int) $key;
			$db->setQuery($pubreq);

			if (!$db->execute())
			{
				JError::raiseError(500, $db->stderr());
			}

			$model = $this->getModel('prayer');
			$model->checkin();
			$query        = $db->setQuery("SELECT * FROM #__cwmprayer WHERE id=" . (int) $key);
			$result       = $db->loadObjectList();
			$newrequester = $result[0]->requester;
			$newrequest   = stripslashes($result[0]->request);
			$newemail     = $result[0]->email;
			$sendpriv     = $result[0]->displaystate;
			$sessionid    = $result[0]->sessionid;

			if ($sendpriv)
			{
				if ($this->prayer->pcConfig['config_distrib_type'] > 1 && !empty($this->prayer->pcConfig['config_pms_plugin']))
				{
					$this->prayer->PCsendPM($newrequesterid, $newrequester, $newrequest, $newemail, $sendpriv);
				}
			}
			elseif (!$sendpriv)
			{
				if ($this->prayer->pcConfig['config_distrib_type'] > 1 && !empty($this->prayer->pcConfig['config_pms_plugin']))
				{
					$this->prayer->PCsendPM($newrequesterid, $newrequester, $newrequest, $newemail, $sendpriv, (int) $key, $sessionid);
				}
			}
		}

		$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=moderate&Itemid=' . $itemid . '&return_msg=' . htmlentities(JText::_('CWMPRAYERREQSUBMIT')));
		$this->setRedirect(JRoute::_($returnurl, false));
	}

	public function unpubrequest()
	{
		JSession::checkToken() or jexit('Invalid Token');
		$app      = JFactory::getApplication();
		$db       = JFactory::getDBO();
		$itemid   = $this->prayer->PCgetItemid();
		$id       = $app->input->getInt('id');
		$unpubreq = "UPDATE #__cwmprayer SET state='0' WHERE id=" . (int) $id;
		$db->setQuery($unpubreq);

		if (!$db->execute())
		{
			JError::raiseError(500, $db->stderr());
		}

		$model = $this->getModel('prayer');
		$model->checkin();
		$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=moderate&Itemid=' . $itemid);
		$this->setRedirect(JRoute::_($returnurl, false));
	}
}
