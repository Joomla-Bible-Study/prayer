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
 * Class view for Devotion
 *
 * @package  Prayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerViewDevotion extends JViewLegacy
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
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');
		$this->edit  = $this->get('edit');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add Tool Bar
	 *
	 * @return void
	 *
	 * @since  4.0.0
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user = JFactory::getUser();

		$userId = $user->get('id');

		$isNew = ($this->item->id == 0);

		$canDo = CWMPrayerHelper::getActions('com_cwmprayer', '', $this->item->id);

		JToolBarHelper::title(JText::_('COM_CWMPRAYER_TITLE_DEV'));

		JToolbarHelper::apply('devotion.apply');

		JToolbarHelper::save('devotion.save');

		if (!$isNew)
		{
			JToolBarHelper::cancel('devotion.cancel');
		}
		else
		{
			JToolBarHelper::cancel('devotion.cancel', 'Close');
		}
	}
}
