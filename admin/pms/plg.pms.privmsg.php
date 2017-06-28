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
 * CWM Prayer PMS Plugin
 *
 * @package  CWMPrayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerPrivmsgPMSPlugin extends CWMPrayerPluginHelper
{
	/**
	 * CWM Prayer Load Vars
	 *
	 * @param   int  $newrequesterid  ID
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function pcpmsloadvars($newrequesterid)
	{
		jimport('joomla.date.date');

		$lang = Jfactory::getLanguage();

		$lang->load('com_pms', JPATH_SITE);

		$app = JFactory::getApplication();

		$senderid = null;

		$sender = JText::_('PMSEMAILSENDER');

		$dateset = new JDate;

		$now = $dateset->format('Y-m-d H:i:s');

		return true;
	}

	/**
	 * CWM Prayer Load DB
	 *
	 * @param   string  $senderid       ?
	 * @param   object  $recipid        ?
	 * @param   string  $message        ?
	 * @param   string  $now            ?
	 * @param   object  $config         ?
	 * @param   string  $prayerrequest  ?
	 * @param   string  $subject        ?
	 * @param   string  $time           ?
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 * @throws \Exception
	 */
	public function pcpmsloaddb($senderid, $recipid, $message, $now, $config, $prayerrequest = null, $subject = null, $time = null)
	{
		$db = JFactory::getDBO();
		$message = addslashes(nl2br($message));
		$sql = "INSERT INTO #__pms (id,username,whofrom,datetime,readstate,subject,message,archivestate,deletestate,systemmsg) VALUES (''," .
			$db->quote($recipid->username) . "," .
			$db->quote($prayerrequest) . "," . $db->quote($now) .
			",0," . $db->quote($subject) . "," . $db->quote($message) .
			",0,0,0)";
		$db->setQuery($sql);

		if (!$db->execute())
		{
			throw new Exception("SQL error", 500);
		}

		$sql = "SELECT a.emailnotification AS emailnotification, c.time AS online"
			. "\n FROM #__pms_emailnotify AS a"
			. "\n LEFT JOIN #__users AS b ON (b.username = a.username)"
			. "\n LEFT JOIN #__session AS c ON b.id = c.userid AND c.time=(SELECT MAX(time) FROM #__session)"
			. "\n WHERE a.username='" . $recipid->username . "'";

		$db->setQuery($sql);

		$emailnotifyresult = $db->loadObject();

		return $emailnotifyresult;
	}

	/**
	 * CWM Prayer PMS Loads Mail
	 *
	 * @param   object  $insID        ?
	 * @param   int     $var_fromid   ?
	 * @param   object  $var_toid     ?
	 * @param   string  $var_message  ?
	 * @param   object  $emn_option   ?
	 * @param   object  $config       ?
	 *
	 * @return bool
	 *
	 * @since version
	 */
	public function pcpmsloadsmail($insID, $var_fromid, $var_toid, $var_message, $emn_option, $config)
	{
		$mailer = JFactory::getMailer();

		$app = JFactory::getApplication();

		$sitename = $app->get('sitename');

		$livesite = $app->get('live_site');

		$mailfrom = $app->get('mailfrom');

		$emailnotify = $insID->emailnotification;

		$online = $insID->online;

		$body = null;

		if ($emailnotify == 1 || $emailnotify == 3)
		{
			$body = JText::_('PMSEMAILBODYWITHMESSAGE');
		}
		elseif ($emailnotify == 2 || $emailnotify == 4)
		{
			$body = JText::_('PMSEMAILBODYNOMESSAGE');
		}

		$pms_subject = sprintf(JText::_('PMSEMAILSUBJECT'), $sitename);

		$mailer->setSubject($pms_subject);

		$mailer->setSender(array($mailfrom, $sender));

		$mailer->IsHTML(0);

		if ($emailnotify == 1 && $online > 0 || $emailnotify == 2 && $online > 0)
		{
			$body = sprintf($body, $var_toid->username, $var_fromid, $sitename, $var_message);

			$mailer->setBody($body);

			$mail_to = $var_toid->email;

			$mailer->addRecipient($mail_to);

			$mailer->Send();
		}
		elseif ($emailnotify == 3 && $online == 0 || $emailnotify == 4 && $online == 0)
		{
			$body = sprintf($body, $var_toid->username, $var_fromid, $sitename);

			$mailer->setBody($body);

			$mail_to = $var_toid->email;

			$mailer->addRecipient($mail_to);

			$mailer->Send();
		}

		return true;
	}
}
