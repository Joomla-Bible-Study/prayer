<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */
defined('JPATH_BASE') or die;

/**
 * PC Plugin class
 *
 * @since  4.0.0
 */
class CWMPrayerPluginHelper
{
	private $pcConfig;

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
	 * ?
	 *
	 * @param   string  $type    Type of plugin
	 * @param   string  $plugin  Plugin file name
	 *
	 * @return bool
	 *
	 * @since version
	 */
	public function importPlugin($type, $plugin = null)
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_cwmprayer/' . $type . '/' . $plugin;

		$checkpath = self::isEnabled($type, $plugin);

		if ($checkpath)
		{
			require_once $path;

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * ?
	 *
	 * @param   string  $type    Type of plugin
	 * @param   string  $plugin  Plugin file name
	 *
	 * @return bool
	 *
	 * @since version
	 */
	public function isEnabled($type, $plugin = null)
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_cwmprayer/' . $type . '/' . $plugin;

		$result = file_exists($path) ? true : false;

		return $result;
	}

	/**
	 * Admin Private Massaging Function
	 *
	 * @param   string  $newrequester  ?
	 * @param   string  $newrequest    ?
	 * @param   string  $newemail      ?
	 * @param   int     $lastId        ?
	 * @param   int     $sessionid     ?
	 * @param   int     $sendpriv      ?
	 *
	 * @return void
	 *
	 * @since 4.0.0
	 */
	public function admin_private_messaging($newrequester, $newrequest, $newemail, $lastId, $sessionid, $sendpriv)
	{
		$senderid = null;
		$time = null;
		$now = new JDate('now');
		$config = array();

		$lang = JFactory::getLanguage();
		$lang->load('com_cwmprayer', JPATH_SITE);

		$db = JFactory::getDBO();
		$livesite = JURI::base();
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');

		$prayeradmin = new CWMPrayerSitePrayer;
		$prayeradmin->intializePCRights();
		$pcConfig = $prayeradmin->pcConfig;

		$sender = JText::_('PCTITLE');

		$config_use_admin_alert = $pcConfig['config_use_admin_alert'];
		$config_email_inc_req = $pcConfig['config_email_inc_req'];
		$config_email_request = $pcConfig['config_email_request'];

		$config_moderator_list = trim($pcConfig['config_moderator_user_list']);

		$config_moderator_list = strip_tags($config_moderator_list);

		$moderatorArray = preg_split('/[,]/', $config_moderator_list, -1, PREG_SPLIT_NO_EMPTY);

		$link = $livesite . 'index.php?option=com_cwmprayer&task=confirm_adm&id=' . $lastId . '&sessionid=' . $sessionid;

		$slink = '<a href="' . $livesite . '" target="_blank">' . $sitename . '</a>';

		$clink = '<a href="' . $link . '" target="_blank">' . $link . '</a>';

		$prayerrequest = htmlentities(JText::_('PCTITLE'), ENT_COMPAT, 'UTF-8');

		if ($sendpriv)
		{
			$config_email_message = $this->PCkeephtml(JText::_('APPROVEEMAILMSG'));
		}
		else
		{
			$config_email_message = $this->PCkeephtml(JText::_('APPROVEEMAILNOMSG'));
		}

		$approver_name = htmlentities(JText::_('PCAPPROVERNAME'), ENT_COMPAT, 'UTF-8');

		$config_email_subject = $this->PCkeephtml(JText::_('APPROVEEMAILSUBJECT'));

		$subject = sprintf($config_email_subject, $sitename);

		if ($newrequester == JText::_('USRLANONUSER'))
		{
			$newrequester = strtolower($newrequester);
		}

		if ($newemail)
		{
			$newrequester = $newrequester . ' (' . $newemail . ')';
		}

		if (!$sendpriv)
		{
			$private = JText::_('PCPRIVATE') . ' ';
		}
		else
		{
			$private = "";
		}

		if ($config_use_admin_alert == 2)
		{
			$showrecips = $prayeradmin->PCgetAdminData();
		}

		elseif ($config_use_admin_alert == 3)
		{
			$showrecips = [];

			foreach ($moderatorArray as $mod)
			{
				$mod = strtolower(trim($mod));

				preg_match('#(\d+)[-]#', $mod, $matches);

				$query = $db->getQuery(true);
				$query->select('id,name,username,email')
					->from('#__users')
					->where('id=' . $matches[1]);

				$db->setQuery($query);

				$showrecipsq = $db->loadObjectList();

				if (is_array($showrecipsq) && !empty($showrecipsq))
				{
					$showrecips[] = $showrecipsq[0];
				}
				elseif (!empty($showrecipsq))
				{
					$showrecips[] = $showrecipsq;
				}
			}
		}
		elseif ($config_use_admin_alert == 4)
		{
			$showrecips1 = $prayeradmin->PCgetAdminData();

			$showrecips2 = [];

			foreach ($moderatorArray as $mod)
			{
				$mod = strtolower(trim($mod));

				preg_match('#(\d+)[-]#', $mod, $matches);

				$query = $db->getQuery(true);
				$query->select('id,name,username,email')
					->from('#__users')
					->where('id=' . (int) $matches[1]);

				$db->setQuery($query);

				$showrecipsq = $db->loadObjectList();

				if (is_array($showrecipsq) && !empty($showrecipsq))
				{
					$showrecips2[] = $showrecipsq[0];
				}
				elseif (!empty($showrecipsq))
				{
					$showrecips2[] = $showrecipsq;
				}
			}

			$showrecipsmerge = array_merge_recursive($showrecips1, $showrecips2);

			$showrecips = array_values($this->array_unique($showrecipsmerge));
		}

		$count = count($showrecips);

		if ($count > 0)
		{
			foreach ($showrecips as $recip)
			{
				$newrequest = wordwrap($newrequest, 60, "\t\r\n");

				if ($pcConfig['config_pms_plugin'] == 'privmsg')
				{
					$newrequest = addslashes(nl2br(JText::_($newrequest)));
				}

				if ($config_email_inc_req == true)
				{
					$message = sprintf($config_email_message, $approver_name, $private, $newrequester, $sitename, $newrequest, $link, $sitename);
				}
				else
				{
					$message = sprintf($config_email_message, $approver_name, $private, $newrequester, $sitename, $sitename);
				}

				$message = str_replace("\n", "<br />", $message);

				$insID = $this->pcpmsloaddb($senderid, $recip, $message, $now, $config, $prayerrequest, $subject, $time);

				$this->pcpmsloadsmail($insID, $prayerrequest, $recip, $message, 0, $config);

				$message = "";
			}
		}
	}

	/**
	 * CWM Pryaer HTML to xml
	 *
	 * @param   string  $string  HTML to convert
	 *
	 * @return mixed|string
	 *
	 * @since version
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
	 * Send Private Messageing
	 *
	 * @param   string  $newrequester  ?
	 * @param   string  $newrequest    ?
	 * @param   string  $newemail      Email Address
	 * @param   string  $sendpriv      Privint True / False
	 * @param   int     $lastid        Last ID
	 * @param   int     $sessionid     Sessiong ID
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function send_private_messaging($newrequester, $newrequest, $newemail, $sendpriv, $lastid, $sessionid)
	{
		$time = null;
		$senderid = null;
		$now = new JDate('now');
		$config = array();

		$cwmprayeradmin = New CWMPrayerAdmin;

		$this->pcpmsloadvars();

		$lang = Jfactory::getLanguage();
		$lang->load('com_cwmprayer', JPATH_SITE);

		$db = JFactory::getDBO();

		$livesite = JURI::root();

		$app = JFactory::getApplication();

		$sitename = $app->get('sitename');

		$sender = JText::_('PCTITLE');

		$config_use_admin_alert = intval($this->pcConfig['config_use_admin_alert']);

		$config_email_request = intval($this->pcConfig['config_email_request']);

		$config_email_inc_req = $this->pcConfig['config_email_inc_req'];

		$config_pms_list = trim($this->pcConfig['config_pms_list']);

		$config_pms_list = strip_tags($config_pms_list);

		$pmsArray = preg_split('/[,]/', $config_pms_list, -1, PREG_SPLIT_NO_EMPTY);

		$slink = '<a href="' . $livesite . '" target="_blank">' . $sitename . '</a>';

		$prayerrequest = JText::_('PCTITLE');

		$config_email_message = $this->PCkeephtml(JText::_('PCEMAILMSG'));

		if ($sendpriv)
		{
			$config_email_nomessage = $this->PCkeephtml(JText::_('PCEMAILNOMSG'));
		}
		else
		{
			$config_email_nomessage = $this->PCkeephtml(JText::_('PCEMAILNOMSGPRIV'));
		}

		if ($sendpriv)
		{
			$link = "";
		}
		else
		{
			$link = 'index.php?option=com_cwmprayer&task=view_request&id=' . $lastid . '&prv=1&pop=1&tmpl=component&sessionid=' . $sessionid;
		}

		$config_email_subject = $this->PCkeephtml(JText::_('PCEMAILSUBJECT'));

		$viewer_name = htmlentities(JText::_('PCVIEWERNAME'), ENT_COMPAT, 'UTF-8');

		if ($newemail)
		{
			$newrequester = $newrequester . ' (' . $newemail . ')';
		}

		if ($config_email_request == 0)
		{
			$showrecips = $cwmprayeradmin->PCgetAdminData();
		}
		elseif ($config_email_request == 1)
		{
			$db->setQuery("SELECT id,name,username,email FROM #__users");

			$showrecips = $db->loadObjectList();
		}
		elseif ($config_email_request == 2)
		{
			$showrecips = [];

			foreach ($pmsArray as $pms)
			{
				$pms = strtolower(trim($pms));

				preg_match('#(\d+)[-]#', $pms, $matches);

				$query = $db->getQuery(true);
				$query->select('id,name,username,email')
					->from('#__users')
					->where('id=' . (int) $matches[1]);

				$db->setQuery($query);

				$pmrecip = $db->loadObjectList();

				if (is_array($pmrecip) && !empty($pmrecip))
				{
					$showrecips[] = $pmrecip[0];
				}
			}
		}

		$count = count($showrecips);

		$subject = sprintf($config_email_subject, $newrequester);

		if ($count > 0)
		{
			foreach ($showrecips as $recip)
			{
				if ($config_email_inc_req == true)
				{
					$message = sprintf($config_email_message, $viewer_name, $newrequester, $sitename, $newrequest);
				}
				else
				{
					$message = sprintf($config_email_nomessage, $viewer_name, $newrequester, $sitename, $livesite, $link);
				}

				$insID = $this->pcpmsloaddb((int) $senderid, $recip, $message, $now, $config, $prayerrequest, $subject, $time);

				$this->pcpmsloadsmail($insID, $prayerrequest, $recip, $message, 0, $config);
			}
		}
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
	private function array_unique(&$old)
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
}
