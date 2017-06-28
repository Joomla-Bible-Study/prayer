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
 * Class view for Prayer
 *
 * @package  Prayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerViewPrayer extends JViewLegacy
{
	protected $sidebar;

	protected $state;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     JViewLegacy::loadTemplate()
	 * @since   4.0
	 */
	public function display($tpl = null)
	{
		$this->state      = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');

			return false;
		}

		if ($this->getLayout() !== 'modal')
		{
			CWMPrayerHelper::addSubmenu('prayer');
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}

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
		JToolbarHelper::title(JText::_('COM_CWMPRAYER_TITLE_CP'));

		JToolbarHelper::preferences('com_cwmprayer');
		JToolbarHelper::help('CWMPRAYER_HELP_MANAGER');
	}
}
