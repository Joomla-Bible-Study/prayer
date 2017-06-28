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
 * Provides access to the #__cwmprayer table
 *
 * @since  4.0.0
 */
class CWMPrayerTableReq extends JTable
{
	/** @var int Unique id
	 * @since 4.0
	 */
	public $id = null;

	/** @var int
	 * @since 4.0
	 */
	public $checked_out = null;

	/** @var datetime
	 * @since 4.0
	 */
	public $checked_out_time = null;

	/** @var int
	 * @since 4.0
	 */
	public $ordering = null;

	public $title;

	public $created;

	public $state;

	public $version;

	public $category;

	public $alias;

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
		$this->_columnAlias = array('published' => 'state');

		parent::__construct('#__cwmprayer', 'id', $db);
	}

	/**
	 * Method to perform sanity checks on the JTableInterface instance properties to ensure they are safe to store in the database.
	 *
	 * Implementations of this interface should use this method to make sure the data they are storing in the database is safe and
	 * as expected before storage.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @since   3.2
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
