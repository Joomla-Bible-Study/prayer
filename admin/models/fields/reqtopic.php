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

JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of categories
 *
 * @package     Joomla.Administrator
 * @subpackage  com_livingword
 * @since       3.0
 */
class JFormFieldReqTopic extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'ReqTopic';

	/**
	 * Method to get the field input markup.
	 *
	 * @return    array  The field input markup.
	 *
	 * @since    1.6
	 */
	protected function getOptions()
	{
		// Always load JBSM API if it exists.
		$api = JPATH_ADMINISTRATOR . '/components/com_cwmprayer/api.php';

		if (file_exists($api))
		{
			require_once $api;
		}

		$prayeradmin = new CWMPrayerAdmin;

		$newtopicarray = $prayeradmin->PCgetTopics();

		$topicarray = array();

		$topicarray[0]['value'] = "";

		$topicarray[0]['text'] = JText::_('CWMPRAYERSELECTTOPIC');

		for ($i = 1; $i < count($newtopicarray) + 1; $i++)
		{
			$topicarray[$i]['value'] = $newtopicarray[$i]['val'];
			$topicarray[$i]['text'] = $newtopicarray[$i]['text'];
		}

		$options = array_merge(parent::getOptions(), $topicarray);

		return $options;
	}
}
