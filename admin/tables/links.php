<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */

defined('_JEXEC') or die('Restricted Access');

/**
 * Provides access to the #__cwmprayer_links table
 *
 * @since  4.0.0
 */
class CWMPrayerTableLinks extends JTable
{
	public $id = null;

	public $checked_out = null;

	public $checked_out_time = null;

	public $ordering = null;

	public $name;

	public $alias;

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 *
	 * @since 4.0
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__cwmprayer_links', 'id', $db);
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database table.
	 *
	 * The method respects checked out rows by other users and will attempt to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update. If not set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user ID of the user performing the operation.
	 *
	 * @return  boolean  True on success; false if $pks is empty.
	 *
	 * @since   11.1
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		$prayer = new CWMPrayerSitePrayer;
		$prayer->intializePCRights();

		$pcConfig = $prayer->pcConfig;
		$k = $this->_tbl_key;

		// Sanitize input.
		Joomla\Utilities\ArrayHelper::toInteger($pks);

		$userId = (int) $userId;

		$state = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			else
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

				return false;
			}
		}

		// Build the WHERE clause for the primary keys.
		$where = $k . '=' . implode(' OR ' . $k . '=', $pks);

		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			$checkin = '';
		}
		else
		{
			$checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
		}

		// Update the publishing state for rows with the given primary keys.
		$this->_db->setQuery(
			'UPDATE ' . $this->_db->qn($this->_tbl) .
			' SET ' . $this->_db->qn('published') . ' = ' . (int) $state .
			' WHERE (' . $where . ')' . $checkin
		);

		try
		{
			$this->_db->execute();
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
		{
			// Checkin the rows.
			foreach ($pks as $pk)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks))
		{
			$this->state = $state;
		}

		foreach ($pks as $pk)
		{
			if ($state == 1)
			{
				$lang = Jfactory::getLanguage();

				$lang->load('com_cwmprayer', JPATH_SITE);

				$this->_db->setQuery("SELECT * FROM #__cwmprayer WHERE id='" . (int) $pk . "'");

				$publishedq = $this->_db->loadObjectList();

				$published = $publishedq[0];

				$newrequest = $published->request;

				$newrequester = $published->requester;

				$newrequesterid = null;

				$newemail = $published->email;

				$sendpriv = $published->displaystate;

				$sessionid = $published->sessionid;

				if ($sendpriv)
				{
					if ($pcConfig['config_distrib_type'] > 1 && !empty($pcConfig['config_pms_plugin']))
					{
						$prayer->PCAsendPM($newrequester, $newrequest, $newemail, $sendpriv);
					}
				}
				elseif (!$sendpriv)
				{
					if ($pcConfig['config_distrib_type'] > 1 && !empty($pcConfig['config_pms_plugin']))
					{
						$prayer->PCAsendPM($newrequester, $newrequest, $newemail, $sendpriv, $pk, $sessionid);
					}
				}
			}
		}

		return true;
	}
}
