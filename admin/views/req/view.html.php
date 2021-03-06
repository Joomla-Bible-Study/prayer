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
 * Class view for Req
 *
 * @package  Prayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerViewReq extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     JViewLegacy::loadTemplate()
	 * @since   3.0
	 * @throws  \Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		$this->edit = $this->get('edit');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		$lists = array();

		$plan = JFactory::getApplication()->input->getBool('plan', true);

		if (!isset($plan))
		{
			$plan = "";
		}

		$this->lists = $lists;
		$this->plan = $plan;

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add Toolbar
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		JToolBarHelper::title('CWM Prayer - Edit Request');

		JToolbarHelper::apply('req.apply');

		JToolbarHelper::save('req.save');

		if (!$isNew)
		{
			JToolBarHelper::cancel('req.cancel');
		}
		else
		{
			JToolBarHelper::cancel('req.cancel', 'Close');
		}
	}
}
