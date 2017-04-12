<?php/** * prayer Component * * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL * */// Check to ensure this file is included in Joomla!defined('_JEXEC') or die('Restricted access');class PrayerModelSubs extends JModelList{	/**	 * Constructor	 *	 * @since 1.5	 */	public function __construct()	{		if (empty($config['filter_fields']))		{			$config['filter_fields'] = array(				'id', 'a.id',				'email', 'email',				'date', 'a.date',				'approved', 'a.approved'			);		}		parent::__construct($config);	}	protected function populateState($ordering = null, $direction = null)	{		$app = JFactory::getApplication('administrator');		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');		$this->setState('filter.search', $search);		$approved = $this->getUserStateFromRequest($this->context . '.filter.email', 'filter_approved', '');		$this->setState('filter.approved', $approved);		// List state information.		parent::populateState('id', 'asc');	}	protected function getListQuery()	{		// Create a new query object.		$db = $this->getDbo();		$query = $db->getQuery(true);		// Select the required fields from the table.		$query->select(			$this->getState(				'list.select',				'a.id, a.email, a.date, a.approved'			)		);		$query->from('#__prayer_subscribe AS a');		// Filter by published state		$approved = $this->getState('filter.approved');		if (is_numeric($approved))		{			$query->where('a.approved = ' . (int) $approved);		}		elseif ($approved === '')		{			$query->where('(a.approved = 0 OR a.approved = 1)');		}		// Filter by search in name.		$search = $this->getState('filter.search');		if (!empty($search))		{			if (stripos($search, 'id:') === 0)			{				$query->where('a.id = ' . (int) substr($search, 3));			}			elseif (stripos($search, 'email:') === 0)			{				$search = $db->Quote('%' . $db->escape(substr($search, 7), true) . '%');				$query->where('(a.email LIKE ' . $search . ')');			}			else			{				$search = $db->Quote('%' . $db->escape($search, true) . '%');				$query->where('(a.date LIKE ' . $search . ')');			}		}		// Add the list ordering clause.		$orderCol = $this->state->get('list.ordering');		$orderDirn = $this->state->get('list.direction');		$query->order($db->escape($orderCol . ' ' . $orderDirn));		return $query;	}	/**	 * Approve the Subscriber	 *	 * @return void	 *	 * @since version	 */	public function approve()	{		$app = JFactory::getApplication();		$db = JFactory::getDBO();		$cid = $app->input->get('cid', array(0), 'post', 'array');		$count = count($cid);		if (!is_array($cid) || $count < 1 || $cid[0] == 0)		{			$app->enqueueMessage('Select an item to approve', 'message');		}		for ($i = 0; $i < $count; $i++)		{			$db->setQuery("UPDATE #__prayer_subscribe SET approved='" . (int) 1 . "'"				. "\nWHERE id='" . (int) $cid[$i] . "'");			if (!$db->execute())			{				throw JError::raiseWarning(500, $db->stderr());			}			$db->setQuery("SELECT email FROM #__prayer_subscribe" . " WHERE id=" . (int) $db->q($cid[$i]));			$email = $db->loadObjectList();			if (JPluginHelper::isEnabled('system', 'prayeremail'))			{				$object = new stdClass;				$object->				$prayeremail = new plgSystemPrayerEmail('subs');				$prayeremail->emailTask('Prayeremail_subscribe', array($email[0]->email));			}		}		return;	}	/**	 * UnApprove the Subscriber	 *	 * @return void	 *	 * @since version	 */	public function unapprove()	{		$app = JFactory::getApplication();		$db = JFactory::getDBO();		$cid = $app->input->get('cid', array(0), 'post', 'array');		$count = count($cid);d		if (!is_array($cid) || $count < 1 || $cid[0] == 0)		{			$app->enqueueMessage('Select an item to unapprove', 'message');		}		for ($i = 0; $i < $count; $i++)		{			$db->setQuery("UPDATE #__prayer_subscribe SET approved='" . (int) 0 . "'"				. "\nWHERE id='" . (int) $cid[$i] . "'");			if (!$db->execute())			{				throw JError::raiseWarning(500, $db->stderr());			}			$db->setQuery("SELECT email FROM #__prayer_subscribe" . " WHERE id=" . (int) $db->q($cid[$i]));			$email = $db->loadObjectList();			if (JPluginHelper::isEnabled('system', 'prayeremail'))			{				$prayeremail = new plgSystemPrayerEmail('subs');				$prayeremail->emailTask('Prayeremail_subscribe', array($email[0]->email));			}		}		return;	}}