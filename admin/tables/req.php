<?phpdefined('_JEXEC') or die('Restricted Access');/** * Provides access to the #__cwmprayer table * * @since  4.0.0 */class CWMPrayerTableEditReq extends JTable{	/** @var int Unique id */	public $id = null;	/** @var int */	public $checked_out = null;	/** @var datetime */	public $checked_out_time = null;	/** @var int */	public $ordering = null;	/**	 * Object constructor to set table and key fields.  In most cases this will	 * be overridden by child classes to explicitly set the table and key fields	 * for a particular database table.	 *	 * @param   string           $table  Name of the table to model.	 * @param   mixed            $key    Name of the primary key field in the table or array of field names that compose the primary key.	 * @param   JDatabaseDriver  $db     JDatabaseDriver object.	 *	 * @since   11.1	 */	public function __construct(&$db)	{		parent::__construct('#__cwmprayer', 'id', $db);	}	/**	 * Method to perform sanity checks on the JTableInterface instance properties to ensure they are safe to store in the database.	 *	 * Implementations of this interface should use this method to make sure the data they are storing in the database is safe and	 * as expected before storage.	 *	 * @return  boolean  True if the instance is sane and able to be stored in the database.	 *	 * @since   3.2	 */	public function check()	{		if (empty($this->created))		{			$this->created = date('Y-m-d H:i:s');		}		return true;	}	/**	 * Method to set the publishing state for a row or list of rows in the database table.	 *	 * The method respects checked out rows by other users and will attempt to checkin rows that it can after adjustments are made.	 *	 * @param   mixed    $pks     An optional array of primary key values to update. If not set the instance property value is used.	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]	 * @param   integer  $userId  The user ID of the user performing the operation.	 *	 * @return  boolean  True on success; false if $pks is empty.	 *	 * @since   11.1	 */	public function publish($pks = null, $state = 1, $userId = 0)	{		$k = $this->_tbl_key;		// Sanitize input.		Joomla\Utilities\ArrayHelper::toInteger($pks);		$userId = (int) $userId;		$state = (int) $state;		// If there are no primary keys set check to see if the instance key is set.		if (empty($pks))		{			if ($this->$k)			{				$pks = array($this->$k);			}			else			{				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));				return false;			}		}		// Build the WHERE clause for the primary keys.		$where = $k . '=' . implode(' OR ' . $k . '=', $pks);		// Determine if there is checkin support for the table.		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))		{			$checkin = '';		}		else		{			$checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';		}		// Update the publishing state for rows with the given primary keys.		$this->_db->setQuery(			'UPDATE ' . $this->_db->quoteName($this->_tbl) .			' SET ' . $this->_db->quoteName('published') . ' = ' . (int) $state .			' WHERE (' . $where . ')' . $checkin		);		try		{			$this->_db->execute();		}		catch (RuntimeException $e)		{			$this->setError($e->getMessage());			return false;		}		// If checkin is supported and all rows were adjusted, check them in.		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))		{			// Checkin the rows.			foreach ($pks as $pk)			{				$this->checkin($pk);			}		}		// If the JTable instance value is in the list of primary keys that were set, set the instance.		if (in_array($this->$k, $pks))		{			$this->state = $state;		}		$this->setError('');		return true;	}}