<?php

/**
 * prayer Component
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *

 */

defined('_JEXEC') or die('Restricted access');

class CWMPrayerControllerSubs extends JControllerAdmin
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
	 * @since version
	 */
	public function unapprove()
	{
		/** @var \CWMPrayerModelSubs $model */
		$model = $this->getModel('Subs', 'CWMPrayerModel');
		$model->unapprove();

		$this->setRedirect(JRoute::_(JUri::base() . 'index.php?option=com_cwmprayer&view=subs'));
	}
}