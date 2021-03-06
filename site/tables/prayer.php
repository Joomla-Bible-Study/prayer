<?php
/**
 * Core Site CWMPrayer file
 *
 * @package    CWMPrayer.Site
 * @copyright  2007 - 2015 (C) CWM Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.christianwebministries.org/
 * */
defined('_JEXEC') or die('Restricted Access');


/**
 * Provides access to the #__cwmprayer table
 *
 * @since  4.0.0
 */
class TablePrayer extends JTable
{
	public $id = null;

	public $checked_out = null;

	public $checked_out_time = null;

	public $created;

	/**
	 * Object constructor to set table and key fields.  In most cases this will
	 * be overridden by child classes to explicitly set the table and key fields
	 * for a particular database table.
	 *
	 * @param   JDatabaseDriver  &$db  JDatabaseDriver object.
	 *
	 * @since   11.1
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__cwmprayer', 'id', $db);
	}

	/**
	 * Method to perform sanity checks on the JTable instance properties to ensure they are safe to store in the database.
	 *
	 * Child classes should override this method to make sure the data they are storing in the database is safe and as expected before storage.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @since   11.1
	 */
	public function check()
	{
		if (empty($this->created))
		{
			$this->created = date('Y-m-d H:i:s');
		}

		return true;
	}
}
