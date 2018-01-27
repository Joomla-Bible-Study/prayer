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
 * View Edit Class
 *
 * @package  Prayer.Site
 *
 * @since    4.0
 */
class CWMPrayerViewEdit extends JViewLegacy
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
		$pcConfig     = $this->prayer->pcConfig;

		$uri  = new JUri;
		$user = JFactory::getUser();

		$editblock = 0;

		$eid = $app->input->getInt('id');
		$eid = JFilterOutput::cleanText($eid);

		// Model
		/** @var \CWMPrayerModelPrayer $model */
		$model   = JModelLegacy::getInstance('Prayer', 'CWMPrayerModel');
		$edit    = $model->getEditData($eid);
		$moduser = $model->getCOData($edit[0]->checked_out);

		if ($model->isCheckedOut($user->get('id')))
		{
			$coerror = JText::_('CWMPRAYERCHECKEDOUT') . ' ' . $moduser[0]->name;

			JError::raiseWarning(0, $coerror);

			$editblock = 1;
		}
		else
		{
			if ($eid)
			{
				$model->checkout($user->get('id'));
			}
		}

		// Set pathway information
		$this->action = $uri->toString();
		$title        = JText::_('CWMPRAYERTITLE');
		$this->title  = $title;
		$intro                          = nl2br(JText::_('CWMPRAYERLISTINTRO'));
		$this->intro                    = $intro;
		$this->config_show_page_headers = $pcConfig['config_show_page_headers'];
		$this->config_show_xtd_buttons  = $pcConfig['config_show_xtd_buttons'];
		$this->config_cols              = $pcConfig['config_cols'];
		$this->config_show_tz           = $pcConfig['config_show_tz'];
		$this->config_editor            = $pcConfig['config_editor'];
		$this->config_use_gb            = $pcConfig['config_use_gb'];
		$this->editreq                  = $edit[0];
		$this->editblock                = $editblock;

		return parent::display($tpl);
	}
}
