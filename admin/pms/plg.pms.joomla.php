<?phpdefined('_JEXEC') or die('Restricted access');class PCJoomlaPMSPlugin extends PrayerPluginHelper{	public function pcpmsloadvars($newrequesterid)	{		jimport('joomla.date.date');		$JVersion = new JVersion();		$app = JFactory::getApplication();		$senderid = '130';		if ($newrequesterid)		{			$senderid = $newrequesterid;		}		$dateset = new JDate();		$now = $dateset->format('Y-m-d H:i:s');		return true;	}	public function pcpmsloaddb($senderid, $recipid, $message, $now, $config, $prayerrequest = null, $subject = null, $time = null)	{		$user = JFactory::getUser($recipid->id);		if ($user->authorise('core', 'manage'))		{			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_messages/models', 'MessagesModel');			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_messages/tables');			$message = str_replace("<br />", "\n", $message);			$PMMessage = [				'user_id_from' => $senderid,				'user_id_to' => $recipid->id,				'subject' => $subject,				'message' => $message			];			/** @var \MessagesModelMessage $model_message */			$model_message = JModelLegacy::getInstance('Message', 'MessagesModel');			$model_message->save($PMMessage);		}		return true;	}	public function pcpmsloadsmail($insID, $var_fromid, $var_toid, $var_message, $emn_option, $config)	{		return true;	}}