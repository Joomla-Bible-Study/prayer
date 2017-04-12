<?php

/**
 * prayer Component
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *

 */

defined('_JEXEC') or die('Restricted access');

class PrayerControllerSubs extends JControllerAdmin
{
	/**
	 * Approved
	 *
	 * @return void
	 *
	 * @since version
	 */
	public function approve()
	{
		/** @var \PrayerModelSubs $model */
		$model = $this->getModel('Subs', 'PrayerModel');
		$model->approve();

		$this->setRedirect(JRoute::_(JUri::base() . 'index.php?option=com_prayer&view=subs'));
	}

	/**
	 * UnApproved
	 *
	 * @return void
	 *
	 * @since version
	 */
	public function unapprove()
	{
		/** @var \PrayerModelSubs $model */
		$model = $this->getModel('Subs', 'PrayerModel');
		$model->unapprove();

		$this->setRedirect(JRoute::_(JUri::base() . 'index.php?option=com_prayer&view=subs'));
	}
}