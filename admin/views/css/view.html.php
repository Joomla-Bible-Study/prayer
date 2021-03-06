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
 * Class view for CSS
 *
 * @package  Prayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerViewCss extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

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
		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 500);

			return false;
		}

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();

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
		JToolBarHelper::title(JText::_('COM_CWMPRAYER_TITLE_CSS'));
		JToolBarHelper::apply('prayer.savecss');
		$cb = JToolBar::getInstance('toolbar');
		$cb->appendButton('confirm', 'Do you wish to reset the prayer CSS file to default settings?', 'undo', 'Reset', 'prayer.resetcss', false);
		JToolBarHelper::cancel('prayer.cancelsettings');
		JHtmlSidebar::setAction('index.php?option=com_cwmprayer');
	}
}
