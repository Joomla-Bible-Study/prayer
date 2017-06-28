<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */

defined('_JEXEC') or die;

/**
 * CWMPrayer Controller Request
 *
 * @package  CWMPrayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerControllerReq extends JControllerForm
{
	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $view_item = 'req';

	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $view_list = 'reqs';

	protected $prayer;

	protected $pcConfig;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JControllerLegacy
	 * @since   1.6
	 * @throws  Exception
	 */
	public function __construct(array $config = [])
	{
		$app = JFactory::getApplication();

		if ($app->input->get('task') == 'hidereq')
		{
			$app->input->set('task', 'displayreq');
			$app->input->set('action', 'hidereq');
		}

		parent::__construct($config);

		$this->prayer = new CWMPrayerSitePrayer;
		$this->prayer->intializePCRights();

		$this->pcConfig = $this->prayer->pcConfig;
	}

	/**
	 * Display Req
	 *
	 * @param   string  $option  Component
	 *
	 * @return void|\JException
	 *
	 * @throws \Exception
	 * @since 4.0
	 */
	public function displayreq($option = 'com_cwmprayer')
	{
		$cid = $this->input->get('cid', array(), 'array');

		$action = $this->input->get('action', 'displayreq', 'method');

		if ($action == 'displayreq')
		{
			$display = true;
		}
		else
		{
			$display = false;
		}

		$count = count($cid);

		if (!is_array($cid) || $count < 1 || $cid[0] == 0)
		{
			// @todo Need to correct Language Strings
			$this->setMessage('Select an item to ' . rtrim("req", $action[1]), 'message');

			$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_req", false));
		}

		for ($i = 0; $i < $count; $i++)
		{
			$db = JFactory::getDBO();

			$db->setQuery("UPDATE #__cwmprayer SET displaystate='" . (int) $display . "'"

				. "\nWHERE id='" . (int) $cid[$i] . "'");

			if (!$db->execute())
			{
				Throw new Exception('DB Error', 500);
			}
		}

		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&view=reqs", false));
	}

	/**
	 * Purge
	 *
	 * @return void|\JException
	 *
	 * @since 4.0
	 */
	public function purge()
	{
		/** @var \CWMPrayerModelReq $model */
		$model = $this->getModel();
		$msg = $model->purge();
		$this->setMessage($msg, 'message');

		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&view=reqs", false));
	}
}
