<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * Model for Subscribers
 *
 * @package  Prayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerModelSubs extends JModelList
{
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	public function __construct()
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'email', 'email',
				'date', 'a.date',
				'approved', 'a.approved'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$approved = $this->getUserStateFromRequest($this->context . '.filter.email', 'filter_approved', '');
		$this->setState('filter.approved', $approved);

		// List state information.
		parent::populateState('id', 'asc');
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery  A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();

		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.email, a.date, a.approved'
			)
		);

		$query->from('#__cwmprayer_subscribe AS a');

		// Filter by published state
		$approved = $this->getState('filter.approved');

		if (is_numeric($approved))
		{
			$query->where('a.approved = ' . (int) $approved);
		}
		elseif ($approved === '')
		{
			$query->where('(a.approved = 0 OR a.approved = 1)');
		}

		// Filter by search in name.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			elseif (stripos($search, 'email:') === 0)
			{
				$search = $db->Quote('%' . $db->escape(substr($search, 7), true) . '%');
				$query->where('(a.email LIKE ' . $search . ')');
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.date LIKE ' . $search . ')');
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.fullordering', 'a.id');
		$orderDirn = '';

		if (empty($orderCol))
		{
			$orderCol  = $this->state->get('list.ordering', 'a.id');
			$orderDirn = $this->state->get('list.direction', 'DESC');
		}

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * Approve the Subscriber
	 *
	 * @return void
	 *
	 * @throws \Exception
	 *
	 * @since 4.0
	 */
	public function approve()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$cid = $app->input->get('cid', array(0), 'post', 'array');

		$count = count($cid);

		if (!is_array($cid) || $count < 1 || $cid[0] == 0)
		{
			$app->enqueueMessage('Select an item to approve', 'message');
		}

		for ($i = 0; $i < $count; $i++)
		{
			$query = $db->getQuery(true);
			$query->update('#__cwmprayer_subscribe')
				->set('approved = 1')
				->where('id = ' . (int) $cid[$i]);
			$db->setQuery($query);

			if (!$db->execute())
			{
				throw new Exception($db->stderr(), 500);
			}

			$query = $db->getQuery(true);
			$query->select('email')
				->from('#__cwmprayer_subscribe')
				->where('id = ' . (int) $db->q($cid[$i]));
			$db->setQuery($query);

			$email = $db->loadObjectList();

			if (JPluginHelper::isEnabled('system', 'cwmprayeremail'))
			{
				$object = new stdClass;
				$object->name = "com_cwmprayer";
				$prayeremail = new plgSystemCWMPrayerEmail($object);
				$prayeremail->emailTask('Prayeremail_subscribe', array($email[0]->email));
			}
		}

		return;
	}

	/**
	 * UnApprove the Subscriber
	 *
	 * @return void
	 *
	 * @throws \Exception
	 *
	 * @since 4.0
	 */
	public function unapprove()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$cid = $app->input->get('cid', array(0), 'post', 'array');

		$count = count($cid);

		if (!is_array($cid) || $count < 1 || $cid[0] == 0)
		{
			$app->enqueueMessage('Select an item to unapprove', 'message');
		}

		for ($i = 0; $i < $count; $i++)
		{
			$query = $db->getQuery(true);
			$query->update('#__cwmprayer_subscribe')
				->set('approved = 0')
				->where('id = ' . (int) $cid[$i]);
			$db->setQuery($query);

			if (!$db->execute())
			{
				throw new Exception($db->stderr(), 500);
			}

			$query = $db->getQuery(true);
			$query->select('email')
				->from('#__cwmprayer_subscribe')
				->where('id = ' . (int) $db->q($cid[$i]));
			$db->setQuery($query);

			$email = $db->loadObjectList();

			if (JPluginHelper::isEnabled('system', 'prayeremail'))
			{
				$object = new stdClass;
				$object->name = "com_cwmprayer";
				$prayeremail = new plgSystemCWMPrayerEmail($object);
				$prayeremail->emailTask('Prayeremail_subscribe', array($email[0]->email));
			}
		}

		return;
	}
}
