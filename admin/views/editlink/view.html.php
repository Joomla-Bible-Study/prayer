<?php/** * prayer Component * * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL * */// No direct accessdefined('_JEXEC') or die('Restricted access');class PrayerViewEditLink extends JViewLegacy{	protected $state;	protected $item;	protected $form;	public function display($tpl = null)	{		$this->state = $this->get('State');		$this->item = $this->get('Item');		$this->form = $this->get('Form');		$this->edit = $this->get('edit');		// Check for errors.		if (count($errors = $this->get('Errors')))		{			JError::raiseError(500, implode("\n", $errors));			return false;		}		$lists = array();		$edit = JRequest::getVar('edit', true);		$plan = JRequest::getVar('plan', true);		if (!isset($plan)) $plan = "";//  	$lists['ordering'] 			= JHTML::_('list.specificordering',  $row, $row->id, $oquery );		$this->assignRef('edit', $edit);		$this->assignRef('lists', $lists);		$this->assignRef('option', $option);		$this->assignRef('plan', $plan);		$this->addToolbar();		parent::display($tpl);	}	protected function addToolbar()	{		JFactory::getApplication()->input->set('hidemainmenu', true);		$user = JFactory::getUser();		$userId = $user->get('id');		$isNew = ($this->item->id == 0);		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));		$canDo = PrayerHelper::getActions($this->item->id, 0);		JToolBarHelper::title('prayer - Edit Link');		JToolbarHelper::apply('prayer.apply');		JToolbarHelper::save('prayer.savelink');		if (!$this->edit)		{			JToolBarHelper::cancel('prayer.canceleditlink');		}		else		{			JToolBarHelper::cancel('prayer.canceleditlink', 'Close');		}	}}?>