<?php/** * prayer Component * * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL * */// No direct accessdefined('_JEXEC') or die('Restricted access');class CWMPrayerViewLink extends JViewLegacy{	protected $state;	protected $item;	protected $form;	public function display($tpl = null)	{		$this->state = $this->get('State');		$this->item  = $this->get('Item');		$this->form  = $this->get('Form');		$this->edit  = $this->get('edit');		// Check for errors.		if (count($errors = $this->get('Errors')))		{			JError::raiseError(500, implode("\n", $errors));			return false;		}		$this->addToolbar();		return parent::display($tpl);	}	protected function addToolbar()	{		JFactory::getApplication()->input->set('hidemainmenu', true);		JToolBarHelper::title(JText::_('COM_CWMPRAYER_TITLE_LINK'));		JToolbarHelper::apply('link.apply');		JToolbarHelper::save('link.save');		JToolBarHelper::cancel('link.cancel');	}}