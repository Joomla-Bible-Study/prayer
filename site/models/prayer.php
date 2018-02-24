<?php
/**
 * Core Site CWMPrayer file
 *
 * @package    CWMPrayer.Site
 * @copyright  2007 - 2015 (C) CWM Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.christianwebministries.org/
 * */
defined('_JEXEC') or die;

/**
 * CWM Prayer Site Model Prayer Class
 *
 * @package  CWMPrayer.Site
 *
 * @since    4.0
 */
class CWMPrayerModelPrayer extends JModelLegacy
{
	public $id = null;

	public $data = null;

	public $codata = null;

	public $total = null;

	public $hits = null;

	/**
	 * Constructor
	 *
	 * @since   3.0
	 * @throws  Exception
	 */
	public function __construct()
	{
		parent::__construct();

		$input = new JInput;

		$id = $input->getInt('id', 0);

		$this->setId((int) $id);
	}

	/**
	 * Set ID
	 *
	 * @param   int  $id  ID
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function setId($id)
	{
		$this->id = $id;

		$this->data = null;
	}

	/**
	 * Get Data
	 *
	 * @param   string  $sort               Sort Column
	 * @param   string  $searchword         Search Word
	 * @param   string  $searchrequester    Requester to search for
	 * @param   int     $searchrequesterid  ID of Requester
	 *
	 * @return null|\object[]
	 *
	 * @since version
	 */
	public function getData($sort, $searchword, $searchrequester, $searchrequesterid)
	{
		if (empty($this->data))
		{
			$query = $this->_buildQuery($sort, $searchword, $searchrequester, $searchrequesterid);

			$this->data = $this->_getList($query);
		}

		return $this->data;
	}

	/**
	 * Build Query for return
	 *
	 * @param   string  $sort               Sort Column
	 * @param   string  $searchword         Search Word
	 * @param   string  $searchrequester    Requester to search for
	 * @param   int     $searchrequesterid  ID of Requester
	 *
	 * @return \JDatabaseQuery
	 *
	 * @since 4.0
	 */
	public function _buildQuery($sort, $searchword, $searchrequester, $searchrequesterid)
	{
		$query = $this->_db->getQuery(true);

		if ($searchword && $searchword != JText::_('PRAYERSEARCH...'))
		{
			$query->where("(request REGEXP " . $this->_db->q($searchword) . " OR requester REGEXP " . $this->_db->q($searchword) . ")");
		}
		elseif ($searchrequester)
		{
			$query->where("requester REGEXP " . $this->_db->q($searchrequester));

			if ($searchrequesterid)
			{
				$query->where("requesterid REGEXP " . $this->_db->q($searchrequesterid));
			}
		}

		$query->select('*,request AS text')
			->from('#__cwmprayer')
			->where('publishstate=1')
			->where('displaystate=1');

		if ($sort == "99")
		{
			$query->order('date DESC');
		}
		else
		{
			$query->where("topic=" . $this->_db->q($sort));
			$query->order("date DESC");
		}

		return $query;
	}

	/**
	 * Get New Data
	 *
	 * @return null|\object[]
	 *
	 * @since 4.0
	 */
	public function getNewData()
	{
		if (empty($this->data))
		{
			$query = $this->_buildQueryNewReq();

			$this->data = $this->_getList($query);
		}

		return $this->data;
	}

	/**
	 * Build Query New Request
	 *
	 * @return \JDatabaseQuery
	 *
	 * @since 4.0
	 */
	public function _buildQueryNewReq()
	{
		$query = $this->_db->getQuery(true);
		$query->select('*')
			->from('#__cwmprayer')
			->where('publishstate=' . $this->_db->q('0'))
			->order('id DESC');

		return $query;
	}

	/**
	 * Get Total Data Count
	 *
	 * @return int|null
	 *
	 * @since 4.0
	 */
	public function getTotalData()
	{
		if (empty($this->total))
		{
			$query = $this->_buildQueryTotalReq();

			$this->total = $this->_getListCount($query);
		}

		return $this->total;
	}

	/**
	 * Build Query Total Requests
	 *
	 * @return \JDatabaseQuery
	 *
	 * @since 4.0
	 */
	public function _buildQueryTotalReq()
	{
		$query = $this->_db->getQuery(true);
		$query->select('id')
			->from('#__cwmprayer')
			->where('publishstate=' . $this->_db->q('1'))
			->where('displaystate=' . $this->_db->q('1'));

		return $query;
	}

	/**
	 * Get Total
	 *
	 * @param   string  $sort               Sort Column
	 * @param   string  $searchword         Search Word
	 * @param   string  $searchrequester    Requester to search for
	 * @param   int     $searchrequesterid  ID of Requester
	 *
	 * @return int|null
	 *
	 * @since 4.0
	 */
	public function getTotal($sort, $searchword, $searchrequester, $searchrequesterid)
	{
		if (empty($this->total))
		{
			$query = $this->_buildQuery($sort, $searchword, $searchrequester, $searchrequesterid);

			$this->total = $this->_getListCount($query);
		}

		return $this->total;
	}

	/**
	 * Get New Total
	 *
	 * @return int|null
	 *
	 * @since 4.0
	 */
	public function getNewTotal()
	{
		if (empty($this->total))
		{
			$query = $this->_buildQueryNewReq();

			$this->total = $this->_getListCount($query);
		}

		return $this->total;
	}

	/**
	 * Get Edit Data
	 *
	 * @param   int  $eid  Requester ID
	 *
	 * @return null|\object[]
	 *
	 * @since 4.0
	 */
	public function getEditData($eid)
	{
		if (empty($this->data))
		{
			$query = $this->_buildEditQuery($eid);

			$this->data = $this->_getList($query);
		}

		return $this->data;
	}

	/**
	 * Build Edit Query
	 *
	 * @param   int  $eid  Requester ID
	 *
	 * @return \JDatabaseQuery
	 *
	 * @since version
	 */
	public function _buildEditQuery($eid)
	{
		$query = $this->_db->getQuery(true);
		$query->select('*,request AS text')
			->from('#__cwmprayer')
			->where('id=' . $this->_db->q((int) $eid));

		return $query;
	}

	/**
	 * Get User Name
	 *
	 * @param   int  $cou  User ID
	 *
	 * @return \object[]
	 *
	 * @since 4.0
	 */
	public function getCOData($cou)
	{
		if (empty($this->codata))
		{
			$query = $this->_buildCOQuery($cou);

			$this->codata = $this->_getList($query);
		}

		return $this->codata;
	}

	/**
	 * Build User Query
	 *
	 * @param   int  $cou  User ID
	 *
	 * @return \JDatabaseQuery
	 *
	 * @since 4.0
	 */
	public function _buildCOQuery($cou)
	{
		$query = $this->_db->getQuery(true);
		$query->select('name')
			->from('#__users')
			->where('id=' . $this->_db->q((int) $cou));

		return $query;
	}

	/**
	 * Is Checked out
	 * 
	 * @param   int  $uid  ID
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	public function isCheckedOut($uid = 0)
	{
		if ($this->_loadData())
		{
			if ($uid)
			{
				return ($this->data[0]->checked_out && $this->data[0]->checked_out != $uid);
			}

			return $this->data[0]->checked_out;
		}

		return false;
	}

	/**
	 * Load Data
	 *
	 * @return bool|mixed|null
	 *
	 * @since 4.0
	 */
	public function _loadData()
	{
		if (empty($this->data))
		{
			$query = $this->_db->getQuery(true);
			$query->select('checked_out')
				->from('#__cwmprayer')
				->where('id=' . $this->_db->q((int) $this->id));

			$this->_db->setQuery($query);

			$this->data = $this->_db->loadObject();

			return $this->data;
		}

		return true;
	}

	/**
	 * Check IN Table Record
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function checkin()
	{
		if ($this->id)
		{
			$prayer = $this->getTable();

			if (!$prayer->checkin($this->id))
			{
				$this->setError($this->_db->getErrorMsg());

				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * Checkout Record
	 *
	 * @param   int  $uid  ID of User
	 *
	 * @return bool
	 *
	 * @since version
	 */
	public function checkout($uid = null)
	{
		if ($this->id)
		{
			if (is_null($uid))
			{
				$user = JFactory::getUser();

				$uid = $user->get('id');
			}

			$prayer = $this->getTable();

			if (!$prayer->checkout($uid, $this->id))
			{
				$this->setError($this->_db->getErrorMsg());

				return false;
			}

			return true;
		}

		return false;
	}
}
