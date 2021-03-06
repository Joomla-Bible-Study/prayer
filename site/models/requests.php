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
 * Model Requests
 *
 * @package  Prayer.Site
 *
 * @since    4.0
 */
class CWMPrayerModelRequests extends JModelLegacy
{
	protected $data;

	protected $total;

	protected $codata;

	/**
	 * Get Data
	 *
	 * @param   string  $sort               ?
	 * @param   string  $searchword         ?
	 * @param   string  $searchrequester    ?
	 * @param   int     $searchrequesterid  ?
	 *
	 * @return \object[]
	 *
	 * @since 4.0
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
	 * Build Query
	 *
	 * @param   string  $sort               ?
	 * @param   string  $searchword         ?
	 * @param   string  $searchrequester    ?
	 * @param   string  $searchrequesterid  ?
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function _buildQuery($sort, $searchword, $searchrequester, $searchrequesterid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*,request AS text');
		$query->from('#__cwmprayer');
		$query->where('archivestate=' . 0);
		$query->where('publishstate=' . 1);
		$query->where('displaystate=' . 1);

		if ($searchword && $searchword != JText::_('CWMSEARCH...'))
		{
			$query->where("(request REGEXP " . $db->q($searchword) . " OR requester REGEXP " . $db->q($searchword) . ")");
		}
		elseif ($searchrequester)
		{
			$query->where("requester REGEXP " . $db->q($searchrequester));

			if ($searchrequesterid)
			{
				$query->where("requesterid REGEXP " . $db->q($searchrequesterid));
			}
		}

		if ($sort == "99")
		{
			$query->order("DATE_FORMAT(CONCAT_WS(' ',date),'%Y-%m-%d %T') DESC");
		}
		else
		{
			$query->where("topic=" . $db->q($sort));
			$query->order("DATE_FORMAT(CONCAT_WS(' ',date),'%Y-%m-%d %T') DESC");
		}

		return $query;
	}

	/**
	 * Get New Data
	 *
	 * @return \object[]
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
	 * Build Query New Req
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function _buildQueryNewReq()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__cwmprayer')
			->where('publishstate=' . 0)
			->order('id DESC');

		return $query;
	}

	/**
	 * Get Total Data
	 *
	 * @return int
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
	 * Build Query Total Req
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function _buildQueryTotalReq()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__cwmprayer')
			->where('publishstate=' . 1)
			->where('displaystate=' . 1);

		return $query;
	}

	/**
	 * Get Total
	 *
	 * @param   string  $sort               ?
	 * @param   string  $searchword         ?
	 * @param   string  $searchrequester    ?
	 * @param   string  $searchrequesterid  ?
	 *
	 * @return int
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
	 * @return string
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
	 * @param   int  $eid  ?
	 *
	 * @return \object[]
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
	 * @param   int  $eid  ?
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function _buildEditQuery($eid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*,request AS text')
			->from('#__cwmprayer')
			->where('id = ' . (int) $db->q($eid));

		return $query;
	}

	/**
	 * Cod Data
	 *
	 * @param   int  $cou  ?
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
	 * Build Co Query
	 *
	 * @param   int  $cou  ?
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function _buildCOQuery($cou)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('name')
			->from('#__users')
			->where('id = ' . (int) $db->q($cou));

		return $query;
	}
}