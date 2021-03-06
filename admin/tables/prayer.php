<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */

use Joomla\Registry\Registry;

defined('_JEXEC') or die('Restricted Access');

/**
 * Provides access to the #__cwmprayer table
 *
 * @since  4.0.0
 */
class CWMPrayerTablePrayer extends JTable
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

	public $created;

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 *
	 * @since 4.0
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__cwmprayer', 'id', $db);
	}

	/**
	 * Method to bind an associative array or object to the JTableInterface instance.
	 *
	 * This method only binds properties that are publicly accessible and optionally takes an array of properties to ignore when binding.
	 *
	 * @param   mixed  $src     An associative array or object to bind to the JTableInterface instance.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.2
	 * @throws  UnexpectedValueException
	 */
	public function bind($src, $ignore = '')
	{
		if (isset($src['params']) && is_array($src['params']))
		{
			$registry = new Registry;
			$registry->loadArray($src['params']);

			$src['params'] = (string) $registry;
		}

		return parent::bind($src, $ignore);
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
