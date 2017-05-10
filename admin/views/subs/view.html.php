<?php/** * prayer Component * * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL * */// No direct accessdefined('_JEXEC') or die();/** * Prayer Subs * * @package  Prayer.Admin * * @since    4.0 */class PrayerViewSubs extends JViewLegacy{	protected $items;	protected $pagination;	protected $state;	/**	 * Display	 *	 * @param   string  $tpl  Timplate seting	 *	 * @return bool|mixed	 *	 * @since 4.0	 */	public function display($tpl = null)	{		$this->items = $this->get('Items');		$this->state = $this->get('State');		$this->pagination = $this->get('Pagination');		// Check for errors.		if (count($errors = $this->get('Errors')))		{			JError::raiseError(500, implode("\n", $errors));			return false;		}		PrayerHelper::addSubmenu('managesub');		$this->addToolbar();		$this->sidebar = JHtmlSidebar::render();		return parent::display($tpl);	}	/**	 * Address ToolBar	 *	 * @since 4.0	 */	protected function addToolbar()	{		JToolbarHelper::title(JText::_('COM_CWMPRAYER_TITLE_SUBS'));		JToolBarHelper::publishList("subs.approve", 'COM_CWMPRAYER_APPROVE');		JToolBarHelper::unpublishList("subs.unapprove", 'COM_CWMPRAYER_UNAPPROVE');		JToolBarHelper::deleteList("Remove Subscriber(s)?", "subs.del");	}	/**	 * Sort Fields	 *	 * @return array	 *	 * @since version	 */	protected function getSortFields()	{		return array(			'a.email' => JText::_('Email'),			'a.date' => JText::_('Date'),			'a.approved' => JText::_('Approved'),			'a.id' => JText::_('JGRID_HEADING_ID')		);	}}