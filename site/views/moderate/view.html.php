<?php

defined('_JEXEC') or die('Restricted access');


class CWMPrayerViewModerate extends JViewLegacy
{
	protected $prayer;

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
		$app = JFactory::getApplication();

		$this->prayer = new CWMPrayerSitePrayer;
		$pcConfig = $this->prayer->pcConfig;

		$uri = new JUri;

		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $pcConfig['config_rows'], 'int');

		if (empty($limitstart))
		{
			$limitstart = 0;
		}

		if (isset($_GET['limitstart']))
		{
			$limitstart = $app->input->getInt('limitstart');
		}

		if (isset($_POST['sort']))
		{
			$sort = $app->input->getInt('sort');
		}
		else
		{
			$sort = "";
		}

		if ($sort == "0" or $sort == "")
		{
			$achecked = 'checked';
		}

		if ($sort == "1")
		{
			$pchecked = 'checked';
		}
		elseif ($sort == "2")
		{
			$rchecked = 'checked';
		}

		// Model
		/** @var \CWMPrayerModelPrayer $model */
		$model = JModelLegacy::getInstance('Prayer', 'CWMPrayerModel');

		$newresults = $model->getNewData();
		$newtotal = $model->getNewTotal();

		// Set pathway information
		$this->action = $uri->toString();

		$this->config_show_page_headers = $pcConfig['config_show_page_headers'];

		$pctitle = JText::_('CWMPRAYERTITLE');

		$this->title = $pctitle;

		$pcintro = nl2br(JText::_('CWMPRAYERLISTINTRO'));

		$this->intro = $pcintro;


		$this->config_show_tz = $pcConfig['config_show_tz'];

		$this->newresults = $newresults;

		$this->newtotal = $newtotal;

		$this->limit = $limit;

		$this->limitstart = $limitstart;

		return parent::display($tpl);
	}
}
