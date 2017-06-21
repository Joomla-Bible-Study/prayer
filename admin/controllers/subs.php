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
 * CWMPrayer Controller Subscriptions
 *
 * @package  CWMPrayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerControllerSubs extends JControllerAdmin
{
	/**
	 * Approved
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function approve()
	{
		/** @var \CWMPrayerModelSubs $model */
		$model = $this->getModel('Subs', 'CWMPrayerModel');
		$model->approve();

		$this->setRedirect(JRoute::_(JUri::base() . 'index.php?option=com_cwmprayer&view=subs'));
	}

	/**
	 * UnApproved
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function unapprove()
	{
		/** @var \CWMPrayerModelSubs $model */
		$model = $this->getModel('Subs', 'CWMPrayerModel');
		$model->unapprove();

		$this->setRedirect(JRoute::_(JUri::base() . 'index.php?option=com_cwmprayer&view=subs'));
	}
}
