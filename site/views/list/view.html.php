<?php
/**
 * Core Site CWMPrayer file
 *
 * @package    CWMPrayer.Site
 * @copyright  2007 - 2015 (C) CWM Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.christianwebministries.org/
 * */
defined('_JEXEC') or die;

/**
 * CWM Prayer List View Class
 *
 * @package  CWMPrayer.Site
 *
 * @since    4.0
 */
class CWMPrayerViewList extends JViewLegacy
{
	/** @var  CWMPrayerSitePrayer
	 * @since 4.0
	 */
	protected $prayer;

	/** @var  string
	 * @since 4.0
	 */
	protected $title;

	/** @var  string
	 * @since 4.0
	 */
	protected $intro;

	/** @var  array
	 * @since 4.0
	 */
	protected $results;

	protected $sort;

	protected $limit;

	protected $limitstart;

	protected $moderate;

	protected $total;

	protected $totalresults;

	protected $action;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     JViewLegacy::loadTemplate()
	 * @since   3.0
	 */
	public function display($tpl = null)
	{
		$this->prayer = new CWMPrayerSitePrayer;

		$app = JFactory::getApplication();

		$uri = new JUri();

		$user = JFactory::getUser();

		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $this->prayer->pcConfig['config_rows'], 'int');

		if (empty($limitstart))
		{
			$limitstart = 0;
		}

		if (isset($_REQUEST['limitstart']))
		{
			$limitstart = $app->input->getInt('limitstart', null);
		}

		if (isset($_POST['sort']))
		{
			$sort = $app->input->getInt('sort', null);
		}
		else
		{
			$sort = 99;
		}

		if (isset($_POST['searchword']))
		{
			$searchword = $app->input->getString('searchword', null);
		}
		else
		{
			$searchword = "";
		}

		if (isset($_REQUEST['searchrequester']))
		{
			$searchrequester = $app->input->getString('searchrequester', null);
		}
		else
		{
			$searchrequester = "";
		}

		if (isset($_REQUEST['searchrequesterid']))
		{
			$searchrequesterid = $app->input->getInt('searchrequesterid', null);
		}
		else
		{
			$searchrequesterid = "";
		}

		/** @var \CWMPrayerModelPrayer $model */
		$model = JModelLegacy::getInstance('Prayer', 'CWMPrayerModel');

		$results = $model->getData($sort, $searchword, $searchrequester, $searchrequesterid);

		$total = $model->getTotal($sort, $searchword, $searchrequester, $searchrequesterid);

		$totalresults = $model->getTotalData();

		$this->prayer->pcConfig['config_allowed_plugins'] = preg_split('/[,]/', $this->prayer->pcConfig['config_allowed_plugins'], -1, PREG_SPLIT_NO_EMPTY);

		// Set pathway information
		$this->action = $uri->toString();

		$this->title = JText::_('CWMPRAYERTITLE');

		$this->intro = nl2br(JText::_('CWMPRAYERLISTINTRO'));

		$this->results = $results;

		$this->totalresults = $totalresults;

		$this->total = $total;

		$this->sort = $sort;

		$this->limit = $limit;

		$this->limitstart = $limitstart;

		$this->moderate = $this->prayer->pc_rights->get('pc.moderate');

		return parent::display($tpl);
	}
}
