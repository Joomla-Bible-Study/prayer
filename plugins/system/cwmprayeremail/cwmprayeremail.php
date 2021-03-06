<?php
/**
 * @Component  Prayer
 * @Plugin     Prayer RequestEemail
 * @copyright  2017 (C) joomlabiblestudy.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Plugin for CWMPrayerEmail
 *
 * @package  CWMPrayerEmail
 *
 * @since    4.0
 */
class PlgSystemCWMPrayerEmail extends JPlugin
{
	public $pcConfig;

	/** @var \JDatabaseDriver */
	private $db;

	/**
	 * Constructor
	 *
	 * @param   object  $subject  The object to observe
	 * @param   array   $config   An optional associative array of configuration settings.
	 *                            Recognized key values include 'name', 'group', 'params', 'language'
	 *                            (this list is not meant to be comprehensive).
	 *
	 * @since   1.5
	 */
	public function __construct($subject, $config = array())
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();

		$this->db = JFactory::getDbo();

		// Always load Prayer API if it exists.
		$api = JPATH_ADMINISTRATOR . '/components/com_cwmprayer/api.php';

		if (file_exists($api))
		{
			require_once $api;
		}

		$prayer = new CWMPrayerSitePrayer;
		$this->pcConfig     = $prayer->pcConfig;
	}

	/**
	 * On After Route
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function onAfterRoute()
	{
		$pcParams = JComponentHelper::getParams('com_cwmprayer');

		$pcParamsArray = $pcParams->toArray();

		foreach ($pcParamsArray['params'] as $name => $value)
		{
			$this->pcConfig[(string) $name] = (string) $value;
		}

		self::EmailTask('admin_email_notification');
		self::EmailTask('email_notification');
	}

	/**
	 * Email Tasks
	 *
	 * @param   string  $pctask  ?
	 * @param   array   $cid     Id's
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function EmailTask($pctask, $cid = array())
	{
		if ($pctask == 'confirm_sub_notification')
		{
			self::$pctask($cid);
		}

		if ($pctask == 'confirm_unsub_notification')
		{
			self::$pctask($cid);
		}

		if ($pctask == 'email_subscribe')
		{
			self::$pctask($cid);
		}

		if ($pctask == 'email_unsubscribe')
		{
			self::$pctask($cid);
		}

		if ($pctask == 'admin_email_subscribe_notification')
		{
			self::$pctask($cid);
		}

		$send = self::checkSend($pctask);

		if ($pctask == 'admin_email_notification')
		{
			$results = self::getUnsent($cid, true);
		}
		else
		{
			$results = self::getUnsent($cid, false);
		}

		if ($send && (count($results) > 0))
		{
			if ($pctask == 'admin_email_notification')
			{
				self::setSendto($results, true);
			}
			else
			{
				self::setSendto($results, false);
			}

			self::$pctask($results);
		}
	}

	/**
	 * Check Send
	 *
	 * @param   string  $pctask  ?
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function checkSend($pctask)
	{
		jimport('joomla.filesystem.folder');

		if ($pctask == 'confirm_notification')
		{
			return true;
		}

		if ($pctask == 'confirm_sub_notification')
		{
			return true;
		}

		if ($pctask == 'confirm_unsub_notification')
		{
			return true;
		}

		if ($pctask == 'email_subscribe')
		{
			return true;
		}

		if ($pctask == 'email_unsubscribe')
		{
			return true;
		}

		$mediaPath = JPATH_ROOT . '/media';

		$checkfileName = 'plg_pcemail_checkfile';

		$dateCheckFile = $checkfileName;

		$okToContinue = true;

		$filearray = JFolder::files($mediaPath, $checkfileName . '*.*');

		if (count($filearray) > 0)
		{
			$lastsent = filemtime($mediaPath . '/' . $filearray[0]);
		}
		else
		{
			$lastsent = '';
		}

		$timenow = date('Y-m-d H:i:s');

		$freq = $this->pcConfig['config_sendfreq'];

		if ($freq == 0)
		{
			return true;
		}

		if ($freq == 1)
		{
			$timeadd = 60 * 60;

			if (isset($lastsent))
			{
				$sendstring = $lastsent + $timeadd;
			}
			else
			{
				$sendstring = time();
			}

			$newdate = date('Y-m-d H:i:s', $sendstring);

			foreach ($filearray as $matchfile)
			{
				if ($timenow > $newdate)
				{
					@unlink($mediaPath . '/' . $matchfile);
				}
			}
		}

		if ($freq == 2)
		{
			$time = $this->pcConfig['config_sendtime'];

			$time = date('H:i:s', strtotime($time));

			$nowday = JHTML::Date($timenow, 'w');

			$nowtime = JHTML::Date($timenow, 'H:i:s');

			$nowdate = JHTML::Date($timenow, 'Y-m-d');

			$sentdate = JHTML::Date($lastsent, 'Y-m-d');

			foreach ($filearray as $matchfile)
			{
				if ($nowdate != $sentdate && $nowtime > $time)
				{
					@unlink($mediaPath . '/' . $matchfile);
				}
			}
		}

		if ($freq == 3)
		{
			$day = $this->pcConfig['config_sendday'];

			$time = $this->pcConfig['config_sendtime'];

			$time = date('H:i:s', strtotime($time));

			$nowday = JHTML::Date($timenow, 'w');

			$nowtime = JHTML::Date($timenow, 'H:i:s');

			$nowdate = JHTML::Date($timenow, 'Y-m-d');

			$sentdate = JHTML::Date($lastsent, 'Y-m-d');

			foreach ($filearray as $matchfile)
			{
				if ($nowdate != $sentdate && $nowday == $day && $nowtime > $time)
				{
					@unlink($mediaPath . '/' . $matchfile);
				}
			}
		}

		if (is_writable($mediaPath))
		{
			if (is_file($mediaPath . '/' . $dateCheckFile))
			{
				$okToContinue = false;
			}
			elseif (!touch($mediaPath . '/' . $dateCheckFile))
			{
				$okToContinue = false;
			}
		}
		else
		{
			$okToContinue = false;
		}

		return $okToContinue;
	}

	/**
	 * Get Unsent mail
	 *
	 * @param   array  $cid    id
	 * @param   bool   $admin  ?
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	public function getUnsent($cid, $admin)
	{
		count($cid) > 0 ? $idstr = ' AND id IN ("' . $cid[0] . '")' : $idstr = "";

		if ($admin)
		{
			$this->db->setQuery("SELECT * FROM #__cwmprayer WHERE adminsendto='0000-00-00 00:00:00' AND publishstate=1" . $idstr);
		}
		else
		{
			$this->db->setQuery(
				"SELECT * FROM #__cwmprayer WHERE sendto='0000-00-00 00:00:00' AND publishstate=1" .
				$idstr
			);
		}

		return $this->db->loadObjectList();
	}

	/**
	 * Set Send To
	 *
	 * @param   object  $results  ?
	 * @param   bool    $admin    ?
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function setSendto($results, $admin)
	{
		$timenow = gmdate('Y-m-d H:i:s');
		$cids    = [];

		foreach ($results as $result)
		{
			$cids[] = $result->id;
		}

		$cids = implode(',', $cids);

		if ($admin)
		{
			$this->db->setQuery("UPDATE #__cwmprayer SET adminsendto='$timenow' WHERE id IN($cids)");
		}
		else
		{
			$this->db->setQuery("UPDATE #__cwmprayer SET sendto='$timenow' WHERE id IN($cids)");
		}

		$this->db->execute();

		$updateresult = $this->db->getAffectedRows();

		if ($updateresult > 0)
		{
			return true;
		}

		return false;
	}

	/**
	 * Confirm Notifications
	 *
	 * @param   array  $items  ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 * @throws \Exception
	 */
	public function confirm_notification($items)
	{
		jimport('joomla.mail.helper');

		$config_sender_name = htmlentities(JText::_('CWMPRAYEREMAILSENDER'));

		$livesite = JURI::root();

		$conf = JFactory::getConfig();

		$sitename = $conf->get('sitename');

		$config_email_mode = $this->pcConfig['config_email_mode'];

		$mail_from = self::getReturnAddress();

		$email_message = htmlentities(JText::_('CWMPRAYERCONFIRMEMAILMSG'));

		$email_subject = htmlentities(JText::_('CWMPRAYERCONFIRMEMAILSUBJECT'));

		$message = stripslashes(JText::_($email_message));

		$link = $livesite . 'index.php?option=com_cwmprayer&task=confirm&id=' . $items[0]->id . '&sessionid=' . $items[0]->sessionid;

		$clink = '<a href="' . $link . '" target="_blank">' . $link . '</a>';

		$slink = '<a href="' . $livesite . '" target="_blank">' . $sitename . '</a>';

		$toname = stripslashes(JText::_($items[0]->requester));

		$toemail = $items[0]->email;

		if ($config_email_mode == true)
		{
			$body = sprintf($email_message, $toname, $slink, $message, $clink);

			$body = str_replace("\n", "<br />", $body);
		}
		else
		{
			$body = sprintf($email_message, $toname, $livesite, $message, $link);
		}

		$mail_to[] = $toemail;

		self::sendmail($mail_from, $config_sender_name, $mail_to, $email_subject, $body, $config_email_mode);
	}

	/**
	 * Get Return Address
	 *
	 * @return mixed|string
	 *
	 * @since 4.0
	 * @throws \Exception
	 */
	public function getReturnAddress()
	{
		$app = JFactory::getApplication();

		$config_custom_ret_addr = $this->pcConfig['config_custom_ret_addr'];

		$config_return_addr = $this->pcConfig['config_return_addr'];

		$mailfrom = $app->get('mailfrom');

		if ($config_return_addr == 0 || $config_return_addr == 2)
		{
			$pc_mf = $mailfrom;
		}
		elseif ($config_return_addr == 1)
		{
			$pc_mf = $config_custom_ret_addr;
		}
		else
		{
			$pc_mf = '';
		}

		$valid = preg_match('/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}$/', $pc_mf);

		if (!$valid)
		{
			return htmlentities(JText::_('CWMPRAYERMAILFROM'));
		}
		else
		{
			return $pc_mf;
		}
	}

	/**
	 * Send Mail
	 *
	 * @param   string  $mail_from           ?
	 * @param   string  $config_sender_name  ?
	 * @param   array   $toemail             ?
	 * @param   string  $email_subject       ?
	 * @param   string  $body                ?
	 * @param   string  $config_email_mode   ?
	 * @param   string  $email_intro         ?
	 * @param   string  $email_footer        ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 * @throws \Exception
	 */
	public function sendmail(
		$mail_from,
		$config_sender_name,
		$toemail,
		$email_subject,
		$body,
		$config_email_mode,
		$email_intro = null,
		$email_footer = null)
	{
		$config_email_bcc = $this->pcConfig['config_email_bcc'];

		$config_email_mode = $this->pcConfig['config_email_mode'];

		$root = JURI::root();

		$app = JFactory::getApplication();

		$sitename = $app->get('sitename');

		$livesite = JURI::root();

		if ($config_email_mode)
		{
			$image = $root . 'media/com_cwmprayer/images/prayer.jpg';

			$slink = '<a href="' . $livesite . '" target="_blank">' . $sitename . '</a>';

			$filename = JPATH_ROOT . '/media/com_cwmprayer/css/cwmprayeremail.css';

			if (file_exists($filename))
			{
				$csscontents = fopen($filename, "rb");

				$filecontent = fread($csscontents, filesize($filename));

				fclose($csscontents);
			}
			else
			{
				$csscontents = '';

				$filecontent = 0;
			}

			$message = '<style>' . $filecontent . '</style>';

			$message .= '<a href="' . $root . '" title="Visit the website"><img class="emailimage" src="' . $image . '"></a>';

			if (!is_null($email_intro))
			{
				$message .= '<div class="intro">' . $email_intro . '</div><div class="divider">	</div>';
			}

			$message .= '<div>' . $body . '</div><div class="divider">    </div>';

			if (!is_null($email_footer))
			{
				if ($config_email_mode == true)
				{
					$footer = str_replace(array("\n", "\t"), array("<br />", '<span style="padding: 0 10px">&nbsp;</span>'), $email_footer);
				}
				else
				{
					$footer = '';
				}

				$message .= '<div>' . $footer . '</div>';
			}
		}
		else
		{
			$message = str_replace("\t", "", $email_intro);

			$message .= $body;

			$message .= $email_footer;
		}

		$mailer = JFactory::getMailer();

		$mailer->setSender(array($mail_from, html_entity_decode($config_sender_name, ENT_QUOTES, "UTF-8")));

		$mailer->setSubject(html_entity_decode($email_subject, ENT_QUOTES, "UTF-8"));

		$mailer->setBody(html_entity_decode($message, ENT_QUOTES, "UTF-8"));

		$mailer->IsHTML($config_email_mode);

		if ($config_email_bcc && count($toemail) > 1)
		{
			$mailer->addBCC($toemail);

			$mailer->addRecipient($mail_from);
		}
		else
		{
			$mailer->addRecipient($toemail);
		}

		$count = count($toemail);

		$rs = $mailer->Send();

		if ($this->pcConfig['config_error_logging'])
		{
			if (JError::isError($rs) && $this->pcConfig['config_error_logging'] > 0)
			{
				$msg = $rs->getError();
			}
			elseif (!$rs && $this->pcConfig['config_error_logging'] > 0)
			{
				$msg = JText::_('CWMPRAYERMAILNOTSENT');

			}
			elseif ($this->pcConfig['config_error_logging'] == 2)
			{
				$msg = JText::_('CWMPRAYERMAILSENT');

				$msg = sprintf($msg, $count);
			}

			if (isset($msg))
			{
				self::errorLog($msg);
			}
		}
	}

	/**
	 * Error Log
	 *
	 * @param   string  $msg  Message to log
	 *
	 * @return void
	 *
	 * @since v4.0
	 */
	public function errorLog($msg)
	{
		jimport('joomla.error.log');

		$log_file_path = JPATH_ROOT . '/administrator/components/com_cwmprayer/logs';

		JLog::addLogger(
			array(
				'text_file' => 'pcerrorlog.php',
				'text_entry_format' => '{DATE} {TIME} {MESSAGE}',
				'text_file_path' => $log_file_path
			),
			JLog::ALL & ~JLog::DEBUG,
			array('com_cwmprayer')
		);

		JLog::add($msg, JLog::ALL, 'com_cwmprayer');
	}

	/**
	 * Confirm Subscription Notification
	 *
	 * @param   array  $item  ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 * @throws \Exception
	 */
	public function confirm_sub_notification($item)
	{
		jimport('joomla.mail.helper');

		$livesite = JURI::root();

		$conf = JFactory::getConfig();

		$sitename = $conf->get('sitename');

		$config_email_mode = $this->pcConfig['config_email_mode'];

		$config_admin_approve_subscribe = $this->pcConfig['config_admin_approve_subscribe'];

		$mail_from = self::getReturnAddress();

		$config_sender_name = htmlentities(JText::_('CWMPRAYEREMAILSENDER'));

		$email_message = htmlentities(JText::_('CWMPRAYERSUBCONFIRMEMAILMSG'));

		$email_subject = htmlentities(JText::_('CWMPRAYERSUBCONFIRMEMAILSUBJECT'));

		$subemail = $item[0];

		$link = $livesite . 'index.php?option=com_cwmprayer&task=confirm_sub&id=' . $item[1] . '&sessionid=' . $item[2];

		$clink = '<a href="' . $link . '" target="_blank">' . $link . '</a>';

		if ($config_admin_approve_subscribe == 1)
		{
			$email_message = htmlentities(JText::_('CWMPRAYERAPPROVESUBEMAILMSG'));

			$mail_to = self::getAdminModAddress();
		}
		else
		{
			$mail_to[] = $item[0];
		}

		if ($config_email_mode == true)
		{
			$body = sprintf($email_message, $subemail, $sitename, $clink);

			$body = str_replace("\n", "<br />", $body);
		}
		else
		{
			$body = sprintf($email_message, $subemail, $sitename, $link);
		}

		self::sendmail($mail_from, $config_sender_name, $mail_to, $email_subject, $body, $config_email_mode);
	}

	/**
	 * Get Admin Model Address
	 *
	 * @return array
	 *
	 * @since version
	 */
	public function getAdminModAddress()
	{
		$resultusers = array();

		$config_use_admin_alert = $this->pcConfig['config_use_admin_alert'];

		$config_moderator_list = trim($this->pcConfig['config_moderator_user_list']);

		$config_moderator_list = strip_tags($config_moderator_list);

		$moderatorArray = preg_split('/[,]/', $config_moderator_list, -1, PREG_SPLIT_NO_EMPTY);

		$config_email_list = trim($this->pcConfig['config_email_list']);

		$config_email_list = strip_tags($config_email_list);

		$emailArray = preg_split('/[,]/', $config_email_list, -1, PREG_SPLIT_NO_EMPTY);

		$config_email_request = $this->pcConfig['config_email_request'];

		// Get admins or moderators (from admin request approval function)
		if ($config_use_admin_alert == 2)
		{
			$resultusers = self::PlggetAdminData();
		}
		elseif ($config_use_admin_alert == 3)
		{
			foreach ($moderatorArray as $mod)
			{
				preg_match('#(\d+)[-]#', $mod, $matches);

				$modquery = "SELECT name,email FROM #__users WHERE id=" . (int) $matches[1];

				$this->db->setQuery($modquery);

				$showrecipsq = $this->db->loadObjectList();

				if (is_array($showrecipsq) && !empty($showrecipsq))
				{
					$resultusers[] = $showrecipsq[0];
				}
			}
		}
		elseif ($config_use_admin_alert == 4)
		{
			$showrecips1 = self::PlggetAdminData();

			$showrecips2 = array();

			foreach ($moderatorArray as $mod)
			{
				preg_match('#(\d+)[-]#', $mod, $matches);

				$modquery = "SELECT name,email FROM #__users WHERE id=" . (int) $matches[1];

				$this->db->setQuery($modquery);

				$showrecipsq = $this->db->loadObjectList();

				if (is_array($showrecipsq) && !empty($showrecipsq))
				{
					$showrecips2[] = $showrecipsq[0];
				}
			}

			$showrecipsmerge = array_merge_recursive($showrecips1, $showrecips2);

			$resultusers = array_values(self::array_unique($showrecipsmerge));

		}

		$mail_to = array();

		foreach ($resultusers as $results)
		{
			$mail_to[] = $results->email;
		}

		return $mail_to;
	}

	/**
	 * Plgget Admin Data
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	public function PlggetAdminData()
	{
		$adminusers = [];

		$access = new JAccess;

		$this->db->setQuery("SELECT id FROM #__usergroups");

		$groups = $this->db->loadObjectList();

		foreach ($groups as $group)
		{
			if ($access->checkGroup($group->id, 'core.manage') || $access->checkGroup($group->id, 'core.admin'))
			{
				$adminusers[] = $access->getUsersByGroup($group->id);
			}
		}

		$result = self::PlgArray_flatten($adminusers);

		$result = implode(',', $result);

		$this->db->setQuery("SELECT name,email FROM #__users WHERE id IN (" . $result . ")");

		$resultusers = $this->db->loadObjectList();

		return $resultusers;

	}

	/**
	 * Flatten array
	 *
	 * @param   array  $array  Array to flatten
	 *
	 * @return array|bool
	 *
	 * @since version
	 */
	public function PlgArray_flatten($array)
	{
		if (!is_array($array))
		{
			return false;
		}

		$result = array();

		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				$result = array_merge($result, self::PlgArray_flatten($value));
			}
			else
			{
				$result[$key] = $value;
			}
		}

		return $result;
	}

	/**
	 * Make a unique array of items
	 *
	 * @param   array  &$old  ?
	 *
	 * @return array
	 *
	 * @since version
	 */
	public function array_unique(&$old)
	{
		$new = array();

		foreach ($old as $key => $value)
		{
			if (!in_array($value, $new))
			{
				$new[$key] = $value;
			}
		}

		return $new;
	}

	/**
	 * Confirm UnSubscribe Notification
	 *
	 * @param   array  $item  ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 * @throws \Exception
	 */
	public function confirm_unsub_notification($item)
	{
		jimport('joomla.mail.helper');

		$livesite = JURI::root();

		$conf = JFactory::getConfig();

		$sitename = $conf->get('sitename');

		$config_email_mode = $this->pcConfig['config_email_mode'];

		$mail_from = self::getReturnAddress();

		$config_sender_name = htmlentities(JText::_('CWMPRAYEREMAILSENDER'));

		$email_message = htmlentities(JText::_('CWMPRAYERUNSUBCONFIRMEMAILMSG'));

		$email_subject = htmlentities(JText::_('CWMPRAYERUNSUBCONFIRMEMAILSUBJECT'));

		$link = $livesite . 'index.php?option=com_cwmprayer&task=confirm_unsub&id=' . $item[1] . '&sessionid=' . $item[2];

		$clink = '<a href="' . $link . '" target="_blank">' . $link . '</a>';

		if ($config_email_mode == true)
		{
			$body = sprintf($email_message, $sitename, $clink);
			$body = str_replace("\n", "<br />", $body);
		}
		else
		{
			$body = sprintf($email_message, $sitename, $link);
		}

		$mail_to[] = $item[0];

		self::sendmail($mail_from, $config_sender_name, $mail_to, $email_subject, $body, $config_email_mode);
	}

	/**
	 * Email Notification
	 *
	 * @param   array  $items  ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 * @throws \Exception
	 */
	public function email_notification($items)
	{
		jimport('joomla.mail.helper');

		$config_sender_name = htmlentities(JText::_('CWMPRAYEREMAILSENDER'));

		$livesite = JURI::root();

		$conf = JFactory::getConfig();

		$sitename = $conf->get('sitename');

		$config_email_mode = $this->pcConfig['config_email_mode'];

		$config_email_inc_req = $this->pcConfig['config_email_inc_req'];

		$config_use_admin_alert = $this->pcConfig['config_use_admin_alert'];

		$config_distrib_type = $this->pcConfig['config_distrib_type'];

		$config_email_list = $this->pcConfig['config_email_list'];

		$config_email_list = strip_tags($config_email_list);

		$emailArray = preg_split('/[,]/', $config_email_list, -1, PREG_SPLIT_NO_EMPTY);

		$config_email_request = $this->pcConfig['config_email_request'];

		$email_intro = htmlentities(JText::_('CWMPRAYEREMAILINTRO'));

		$email_message = htmlentities(JText::_('CWMPRAYEREMAILMSG'));

		$email_subject = htmlentities(JText::_('CWMPRAYEREMAILSUBJECT'));

		$viewer_name = htmlentities(JText::_('CWMPRAYERVIEWERNAME'));

		$email_footer = JText::_('CWMPRAYEREMAILFOOTER');

		$email_footer = sprintf(str_replace("\t", "", $email_footer), $livesite);

		$mail_from = self::getReturnAddress();

		$slink = '';

		$body = "";

		$mail_to = array();

		if ($config_email_request == '0')
		{
			$resultusers = self::PlggetAdminData();

			if (!empty($resultusers))
			{
				foreach ($resultusers as $results)
				{
					$mail_to[] = $results->email;
				}
			}
		}

		if ($config_email_request == '1')
		{
			$this->db->setQuery("SELECT name,email FROM #__users");

			$resultusers = $this->db->loadObjectList();

			if (!empty($resultusers))
			{
				foreach ($resultusers as $results)
				{
					$mail_to[] = $results->email;
				}
			}
		}

		if ($config_email_request == '2')
		{
			if (!empty($emailArray))
			{
				foreach ($emailArray as $email)
				{
					$mail_to[] = trim($email);
				}
			}
		}

		if ($config_email_mode == true)
		{
			$body .= '<ul>';
		}

		foreach ($items as $item)
		{
			$reqdate = JHtml::date($item->date, 'Y-m-d G:i:s', false);

			$fdate = date("D M j, Y", strtotime($reqdate));

			$ftime = date("g:ia (T)", strtotime($reqdate));

			if ($config_email_mode == true)
			{
				$body .= '<li>';
			}
			else
			{
				$body .= "\t";
			}

			if (!$item->displaystate)
			{
				$private = htmlentities(JText::_('CWMPRAYERPRIVATE')) . " ";
				$email_nomessage = htmlentities(JText::_('CWMPRAYEREMAILNOMSGPRIV'));
			}
			else
			{
				$private = "";
				$email_nomessage = htmlentities(JText::_('CWMPRAYEREMAILNOMSG'));
			}

			$message = stripslashes(JText::_($item->request));

			if ($item->requester == JText::_('CWMPRAYERANONUSER'))
			{
				$item->requester = htmlentities(strtolower($item->requester));
			}

			$item->displaystate ? $link = "" : $link = 'index.php?option=com_cwmprayer&view=request&prv=1&pop=1&tmpl=component&id=' .
				$item->id . '&sessionid=' . $item->sessionid;

			$slink = '<a href="' . $livesite . '" target="_blank">' . $sitename . '</a>';

			if (!empty($item->email) && $config_email_mode == true)
			{
				$from_id = '<a href="mailto:' . $item->email . '">' . $item->requester . '</a>';
			}
			elseif (!empty($item->email) && $config_email_mode == false)
			{
				$from_id = $item->requester . ' (' . $item->email . ')';
			}
			elseif (empty($item->email))
			{
				$from_id = $item->requester;
			}
			else
			{
				$from_id = '';
			}

			if ($config_email_mode == true)
			{
				if ($config_email_inc_req == true)
				{
					$body .= sprintf($email_message, $private, $from_id, $fdate, $ftime, $message, $slink);
				}
				else
				{
					$body .= sprintf($email_nomessage, $private, $from_id, $slink, $livesite, $link);
				}

				$body = str_replace("\n", "<br />", $body);
			}
			else
			{
				if ($config_email_inc_req == true)
				{
					$body .= sprintf($email_message, $private, $from_id, $fdate, $ftime, $message, $sitename);
				}
				else
				{
					$body .= sprintf($email_nomessage, $private, $from_id, $sitename, $livesite, $link);
				}
			}

			$body .= "\n\n";

			if ($config_email_mode == true)
			{
				$body .= '</li>';
			}
		}

		if ($config_email_mode == true)
		{
			$body .= '</ul>';
		}

		if ($config_email_mode == true)
		{
			$email_intro = sprintf($email_intro, $viewer_name, $slink);
			$email_intro = str_replace(array("\n", "\t"), array("<br />", '<span style="padding: 0 10px">&nbsp;</span>'), $email_intro);
			$body = str_replace("\n", "<br />", $body);
		}
		else
		{
			$email_intro = sprintf($email_intro, $viewer_name, $sitename);
		}

		$subject = sprintf($email_subject, $sitename);

		if (count($mail_to) > 0)
		{
			self::sendmail($mail_from, $config_sender_name, $mail_to, $subject, $body, $config_email_mode, $email_intro, $email_footer);
		}

		self::email_prayer_chain($items);
	}

	/**
	 * Email Prayer Chain
	 *
	 * @param   array  $items  ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 * @throws \Exception
	 */
	public function email_prayer_chain($items)
	{
		jimport('joomla.mail.helper');

		$livesite = JURI::root();

		$conf = JFactory::getConfig();

		$sitename = $conf->get('sitename');

		$config_email_mode = $this->pcConfig['config_email_mode'];

		$config_email_inc_req = $this->pcConfig['config_email_inc_req'];

		$config_sender_name = htmlentities(JText::_('CWMPRAYEREMAILSENDER'));

		$email_intro = htmlentities(JText::_('CWMPRAYEREMAILINTRO'));

		$config_email_message = htmlentities(JText::_('CWMPRAYERCHAINEMAILMSG'));

		$config_email_nomessage = htmlentities(JText::_('CWMPRAYERCHAINEMAILNOMSG'));

		$email_footer = JText::_('CWMPRAYEREMAILFOOTER');

		$subject = htmlentities(JText::_('CWMPRAYEREMAILSUBJECT'));

		$this->db->setQuery("SELECT * FROM #__cwmprayer_subscribe WHERE approved='1'");

		$resultsubscribers = $this->db->loadObjectList();

		$subscriber_name = htmlentities(JText::_('CWMPRAYERSUBSCRIBERNAME'));

		$slink = '<a href="' . $livesite . '" target="_blank">' . $sitename . '</a>';

		$mail_from = self::getReturnAddress();

		$mail_to = array();

		$body = "";

		$k = 0;

		foreach ($resultsubscribers as $subscribers)
		{
			$mail_to[] = $subscribers->email;
		}

		if ($config_email_mode == true)
		{
			$body .= '<ul>';
		}

		foreach ($items as $item)
		{
			$reqdate = JHtml::date($item->date, 'Y-m-d G:i:s', false);

			$fdate = date("D M j, Y", strtotime($reqdate));

			$ftime = date("g:ia (T)", strtotime($reqdate));

			if ($item->displaystate)
			{
				if ($config_email_mode == true)
				{
					$body .= '<li>';
				}
				else
				{
					$body .= "\t";
				}

				$message = stripslashes(JText::_($item->request));

				if ($item->requester == JText::_('CWMPRAYERANONUSER'))
				{
					$item->requester = htmlentities(strtolower($item->requester));
				}

				if ($config_email_mode == true)
				{
					if ($config_email_inc_req == true)
					{
						$body .= sprintf($config_email_message, $item->requester, $fdate, $ftime, $message, $slink);
					}
					else
					{
						$body .= sprintf($config_email_nomessage, $item->requester, $slink, $livesite, $slink);
					}

					$body = str_replace("\n", "<br />", $body);

				}
				else
				{
					if ($config_email_inc_req == true)
					{
						$body .= sprintf($config_email_message, $item->requester, $fdate, $ftime, $message, $sitename);
					}
					else
					{
						$body .= sprintf($config_email_nomessage, $item->requester, $sitename, $livesite, '');
					}
				}

				$body .= "\n\n";

				if ($config_email_mode == true)
				{
					$body .= '</li>';
				}

				$k++;
			}
		}

		if ($config_email_mode == true)
		{
			$body .= '</ul>';
		}

		if ($config_email_mode == true)
		{
			$email_intro = sprintf($email_intro, $subscriber_name, $slink);
			$email_intro = str_replace(array("\n", "\t"), array("<br />", '<span style="padding: 0 10px">&nbsp;</span>'), $email_intro);
			$body = str_replace("\n", "<br />", $body);
		}
		else
		{
			$email_intro = sprintf($email_intro, $subscriber_name, $sitename);
		}

		$subject = sprintf($subject, $sitename);

		$email_footer = JText::_('CWMPRAYEREMAILFOOTER');

		$email_footer = sprintf(str_replace("\t", "", $email_footer), $livesite);

		if (count($mail_to) > 0 && $k > 0)
		{
			self::sendmail($mail_from, $config_sender_name, $mail_to, $subject, $body, $config_email_mode, $email_intro, $email_footer);
		}
	}

	/**
	 * Admin Email Notification
	 *
	 * @param   array  $items  ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 * @throws \Exception
	 */
	public function admin_email_notification($items)
	{
		jimport('joomla.mail.helper');

		$config_sender_name = htmlentities(JText::_('CWMPRAYEREMAILSENDER'));

		$livesite = JURI::root();

		$adminsite = $livesite . "administrator";

		$app = JFactory::getApplication();

		$sitename = $app->get('sitename');

		$config_email_mode = $this->pcConfig['config_email_mode'];

		$config_email_inc_req = $this->pcConfig['config_email_inc_req'];

		$config_use_admin_alert = $this->pcConfig['config_use_admin_alert'];

		$config_moderator_list = trim($this->pcConfig['config_moderator_user_list']);

		$config_moderator_list = strip_tags($config_moderator_list);

		$moderatorArray = preg_split('/[,]/', $config_moderator_list, -1, PREG_SPLIT_NO_EMPTY);

		$config_email_list = trim($this->pcConfig['config_email_list']);

		$config_email_list = strip_tags($config_email_list);

		$emailArray = preg_split('/[,]/', $config_email_list, -1, PREG_SPLIT_NO_EMPTY);

		$config_email_request = $this->pcConfig['config_email_request'];

		$mail_from = self::getReturnAddress();

		$email_intro = htmlentities(JText::_('CWMPRAYERAPPROVEEMAILINTRO'));

		$email_subject = htmlentities(JText::_('CWMPRAYERAPPROVEEMAILSUBJECT'));

		$email_message = htmlentities(JText::_('CWMPRAYERAPPROVEEMAILMSG'));

		$email_nomessage = htmlentities(JText::_('CWMPRAYERAPPROVEEMAILNOMSG'));

		$approver_name = htmlentities(JText::_('CWMPRAYERAPPROVERNAME'));

		$slink = '';

		$body = "";

		$resultusers = array();

		if ($config_use_admin_alert == 2)
		{
			$resultusers = self::PlggetAdminData();
		}
		elseif ($config_use_admin_alert == 3)
		{
			foreach ($moderatorArray as $mod)
			{
				preg_match('#(\d+)[-]#', $mod, $matches);

				$modquery = "SELECT name,email FROM #__users WHERE id=" . (int) $matches[1];

				$this->db->setQuery($modquery);

				$showrecipsq = $this->db->loadObjectList();

				if (is_array($showrecipsq) && !empty($showrecipsq))
				{
					$resultusers[] = $showrecipsq[0];
				}
			}
		}
		elseif ($config_use_admin_alert == 4)
		{
			$showrecips1 = self::PlggetAdminData();

			$showrecips2 = array();

			foreach ($moderatorArray as $mod)
			{
				preg_match('#(\d+)[-]#', $mod, $matches);

				$modquery = "SELECT name,email FROM #__users WHERE id=" . (int) $matches[1];

				$this->db->setQuery($modquery);

				$showrecipsq = $this->db->loadObjectList();

				if (is_array($showrecipsq) && !empty($showrecipsq))
				{
					$showrecips2[] = $showrecipsq[0];
				}
			}

			$showrecipsmerge = array_merge_recursive($showrecips1, $showrecips2);

			$resultusers = array_values(self::array_unique($showrecipsmerge));
		}

		$mail_to = array();

		foreach ($resultusers as $results)
		{
			$mail_to[] = $results->email;
		}

		if ($config_email_mode == true)
		{
			$body .= '<ul>';
		}

		foreach ($items as $item)
		{
			if ($config_email_mode == true)
			{
				$body .= '<li>';
			}
			else
			{
				$body .= "\t";
			}

			if (!$item->displaystate)
			{
				$private = htmlentities(JText::_('CWMPRAYERPRIVATE')) . " ";
			}
			else
			{
				$private = "";
			}

			if ($item->requester == JText::_('CWMPRAYERANONUSER'))
			{
				$item->requester = htmlentities(strtolower($item->requester));
			}

			$message = '"' . stripslashes(JText::_($item->request)) . '"';

			$approvelink = $livesite . 'index.php?option=com_cwmprayer&task=confirm_adm&id=' . $item->id . '&sessionid=' . $item->sessionid;

			$dellink = $livesite . 'index.php?option=com_cwmprayer&task=delreq_adm&id=' . $item->id . '&sessionid=' . $item->sessionid;

			$slink = '<a href="' . $livesite . '" target="_blank">' . $sitename . '</a>';

			$clink = '<a href="' . $approvelink . '" target="_blank">' . htmlentities(JText::_('CWMPRAYERAPPROVE')) . '</a> | <a href="' .
				$dellink . '" target="_blank">' . htmlentities(JText::_('CWMPRAYERDELETE')) . '</a>';

			$plink = "\n\n" . htmlentities(JText::_('CWMPRAYERAPPROVE')) . "\n" . $approvelink . "\n\n" .
				htmlentities(JText::_('CWMPRAYERDELETE')) . "\n" . $dellink;

			$reqdate = JHtml::date($item->date, 'Y-m-d G:i:s', false);

			$fdate = date("D M j, Y", strtotime($reqdate));

			$ftime = date("g:ia (T)", strtotime($reqdate));

			if (!empty($item->email) && $config_email_mode == true)
			{
				$from_id = '<a href="mailto:' . $item->email . '">' . $item->requester . '</a>';
			}
			elseif (!empty($item->email) && $config_email_mode == false)
			{
				$from_id = $item->requester . ' (' . $item->email . ')';
			}
			elseif (empty($item->email))
			{
				$from_id = $item->requester;
			}
			else
			{
				$from_id = '';
			}

			if ($config_email_mode == true)
			{
				if ($config_email_inc_req == true)
				{
					$body .= sprintf($email_message, $private, $from_id, $fdate, $ftime, $message, $clink, $slink);
				}
				else
				{
					$body .= sprintf($email_nomessage, $private, $from_id, $slink, $slink);
				}
			}
			else
			{
				if ($config_email_inc_req == true)
				{
					$body .= sprintf($email_message, $private, $from_id, $fdate, $ftime, $message, $plink, $livesite);
				}
				else
				{
					$body .= sprintf($email_nomessage, $private, $from_id, $sitename, $livesite);
				}
			}

			$body .= "\n\n";

			if ($config_email_mode == true)
			{
				$body .= '</li>';
			}
		}

		if ($config_email_mode == true)
		{
			$body .= '</ul>';
		}

		if ($config_email_mode == true)
		{
			$email_intro = sprintf($email_intro, $approver_name, $slink);

			$email_intro = str_replace(array("\n", "\t"), array("<br />", '<span style="padding: 0 10px">&nbsp;</span>'), $email_intro);

			$body = str_replace("\n", "<br />", $body);
		}
		else
		{
			$email_intro = sprintf($email_intro, $approver_name, $sitename);
		}

		$subject = sprintf($email_subject, $sitename);

		$email_footer = JText::_('CWMPRAYERAPPROVEEMAILFOOTER');

		$email_footer = sprintf(str_replace("\t", "", $email_footer), $adminsite);

		if (count($mail_to) > 0)
		{
			self::sendmail($mail_from, $config_sender_name, $mail_to, $subject, $body, $config_email_mode, $email_intro, $email_footer);
		}
	}

	/**
	 * Admin Email Subscribe Notification
	 *
	 * @param   array  $item  ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 * @throws \Exception
	 */
	public function Admin_Email_Subscribe_Notification($item)
	{
		jimport('joomla.mail.helper');

		$livesite = JURI::root();

		$conf = JFactory::getConfig();

		$sitename = $conf->get('sitename');

		$config_email_mode = $this->pcConfig['config_email_mode'];

		$config_sender_name = htmlentities(JText::_('CWMPRAYEREMAILSENDER'));

		$config_subscribe_email_message = htmlentities(JText::_('CWMPRAYERADMINSUBSCRIBEMSG'));

		$subject = htmlentities(JText::_('CWMPRAYERADMINSUBSCRIBESUBJECT'));

		$link = $livesite . 'administrator/';

		$slink = '<a href="' . $livesite . 'administrator/" target="_blank">' . $livesite . 'index.php?option=com_cwmprayer&task=unsubscribe</a>';

		if ($config_email_mode == true)
		{
			$body = sprintf($config_subscribe_email_message, $item[0], $sitename, $slink);

			$body = str_replace("\n", "<br />", $body);
		}
		else
		{
			$body = sprintf($config_subscribe_email_message, $item[0], $sitename, $link);
		}

		$mail_from = self::getReturnAddress();

		$mail_to = self::getAdminModAddress();

		self::sendmail($mail_from, $config_sender_name, $mail_to, $subject, $body, $config_email_mode);
	}

	/**
	 * Email Subscribe
	 *
	 * @param   array  $item  ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 * @throws \Exception
	 */
	public function email_subscribe($item)
	{
		jimport('joomla.mail.helper');

		$livesite = JURI::base();

		$conf = JFactory::getConfig();

		$sitename = $conf->get('sitename');

		$config_email_mode = $this->pcConfig['config_email_mode'];

		$config_sender_name = htmlentities(JText::_('CWMPRAYEREMAILSENDER'));

		$config_subscribe_email_message = htmlentities(JText::_('CWMPRAYERSUBSCRIBEMSG'));

		$subject = htmlentities(JText::_('CWMPRAYERSUBSCRIBESUBJECT'));

		$link = $livesite . 'index.php?option=com_cwmprayer&task=subscribe';

		$slink = '<a href="' . $livesite . 'index.php?option=com_cwmprayer&task=unsubscribe" target="_blank">' . $livesite .
			'index.php?option=com_cwmprayer&task=unsubscribe</a>';

		if ($config_email_mode == true)
		{
			$body = sprintf($config_subscribe_email_message, $item[0], $sitename, $slink);
			$body = str_replace("\n", "<br />", $body);
		}
		else
		{
			$body = sprintf($config_subscribe_email_message, $item[0], $sitename, $link);
		}

		$mail_from = self::getReturnAddress();

		$mail_to[] = $item[0];

		self::sendmail($mail_from, $config_sender_name, $mail_to, $subject, $body, $config_email_mode);
	}

	/**
	 * Email Unsubscribe
	 *
	 * @param   array  $item  ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 * @throws \Exception
	 */
	public function Email_Unsubscribe($item)
	{
		jimport('joomla.mail.helper');

		$livesite = JURI::base();

		$conf = JFactory::getConfig();

		$sitename = $conf->get('sitename');

		$config_email_mode = $this->pcConfig['config_email_mode'];

		$config_sender_name = htmlentities(JText::_('CWMPRAYEREMAILSENDER'));

		$config_unsubscribe_email_message = htmlentities(JText::_('CWMPRAYERUNSUBSCRIBEMSG'));

		$subject = htmlentities(JText::_('CWMPRAYERSUBSCRIBESUBJECT'));

		$link = $livesite . 'index.php?option=com_cwmprayer&task=subscribe';

		$slink = '<a href="' . $livesite . 'index.php?option=com_cwmprayer&task=subscribe" target="_blank">' . $livesite .
			'index.php?option=com_cwmprayer&task=subscribe</a>';

		if ($config_email_mode == true)
		{
			$body = sprintf($config_unsubscribe_email_message, $item[0], $sitename, $slink);
			$body = str_replace("\n", "<br />", $body);
		}
		else
		{
			$body = sprintf($config_unsubscribe_email_message, $item[0], $sitename, $link);
		}

		$mail_from = self::getReturnAddress();

		$mail_to[] = $item[0];

		self::sendmail($mail_from, $config_sender_name, $mail_to, $subject, $body, $config_email_mode);
	}
}
