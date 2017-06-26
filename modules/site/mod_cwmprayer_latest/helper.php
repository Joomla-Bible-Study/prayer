<?php

defined('_JEXEC') or die;

class ModCWMPrayerLatestHelper
{
	/**
	 * Get CWM Prayer Load Module Data
	 *
	 * @param   int  $count  ?
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	public function getPrayerLModData($count)
	{
		$db    = JFactory::getDBO();
		$query = "SELECT a.id, a.requester, a.request, TIMESTAMP(CONCAT( a.date,' ', a.time)) AS date "
			. "\n FROM #__cwmprayer AS a"
			. "\n WHERE a.state='1' AND a.displaystate='1'"
			. "\n ORDER BY date DESC"
			. "\n LIMIT $count";
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}
}
