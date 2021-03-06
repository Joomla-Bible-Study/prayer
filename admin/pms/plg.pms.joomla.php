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
 * CWM Prayer Joomla PMS Plugin
 *
 * @package  CWMPrayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerJoomlaPMSPlugin extends CWMPrayerPluginHelper
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

		$JVersion = new JVersion;

		$app = JFactory::getApplication();

		$senderid = '130';

		if ($newrequesterid)
		{
			$senderid = $newrequesterid;
		}

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
		$user = JFactory::getUser($recipid->id);

		if ($user->authorise('core', 'manage'))
		{
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_messages/models', 'MessagesModel');
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_messages/tables');

			$message = str_replace("<br />", "\n", $message);

			$PMMessage = [
				'user_id_from' => $senderid,
				'user_id_to' => $recipid->id,
				'subject' => $subject,
				'message' => $message
			];

			/** @var \MessagesModelMessage $model_message */
			$model_message = JModelLegacy::getInstance('Message', 'MessagesModel');
			$model_message->save($PMMessage);
		}

		return true;
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
		return true;
	}
}
