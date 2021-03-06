<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */

defined('_JEXEC') or die;

/**
 * CWM Prayer Model Requests Class
 *
 * @package  CWMPrayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerModelReq extends JModelAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_CWMPRAYER';

	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  3.2
	 */
	public $typeAlias = 'com_cwmprayer.req';

	/**
	 * The context used for the associations table
	 *
	 * @var    string
	 * @since  3.4.4
	 */
	protected $associationsContext = 'com_cwmprayer.req';

	/**
	 * Batch copy/move command. If set to false, the batch copy/move command is not supported
	 *
	 * @var  string
	 * @since  4.0.0
	 */
	protected $batch_copymove = 'category_id';

	protected $prayer;

	protected $pcConfig;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JControllerLegacy
	 * @since   1.6
	 * @throws  Exception
	 */
	public function __construct(array $config = [])
	{
		$this->prayer = new CWMPrayerSitePrayer;
		$this->prayer->intializePCRights();

		$this->pcConfig = $this->prayer->pcConfig;

		parent::__construct($config);
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   4.0
	 */
	public function getTable($name = 'Req', $prefix = 'CWMPrayerTable', $options = [])
	{
		return JTable::getInstance($name, $prefix, $options);
	}

	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm|boolean  A JForm object on success, false on failure
	 *
	 * @since   4.0
	 */
	public function getForm($data = [], $loadData = true)
	{
		JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_users/models/fields');

		$form = $this->loadForm('com_cwmprayer.req', 'req', ['control' => 'jform', 'load_data' => $loadData]);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 * @since   4.0
	 * @throws  \Exception
	 */
	protected function populateState()
	{
		parent::populateState();
		$input = JFactory::getApplication()->input;
		$user = JFactory::getUser();

		$this->setState('user.id', $user->get('id'));

		$edit = $input->get('edit', true);
		$this->setState('edit', $edit);

		$cid = $input->get('cid');

		$this->setState('id', $cid[0]);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  object  The default data is an empty array.
	 *
	 * @since   4.0
	 */
	protected function loadFormData()
	{
		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   CWMPrayerTableReq  $table  A reference to a JTable object.
	 *
	 * @return  void
	 *
	 * @since   4.0
	 */
	protected function prepareTable($table)
	{
		$table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);

		$table->alias = JApplicationHelper::stringURLSafe($table->alias);

		if (empty($table->alias))
		{
			$table->alias = JApplicationHelper::stringURLSafe($table->title);
		}
	}

	/**
	 * Get Record Conditions
	 *
	 * @param   \CWMPrayerTableReq  $table  Table
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'category = ' . (int) $table->category;

		return $condition;
	}

	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   1.6
	 * @throws \Exception
	 */
	public function delete(&$pks)
	{
		$delete = parent::delete($pks);

		if ($delete)
		{
			if ($this->pcConfig['config_comments'] == 1)
			{
				$jcomments = JPATH_SITE . '/components/com_jcomments/jcomments.php';

				if (file_exists($jcomments))
				{
					require_once $jcomments;

					foreach ($pks as $delid)
					{
						JCommentsModel::deleteComments($delid, 'com_cwmprayer');
					}
				}
			}
			elseif ($this->pcConfig['config_comments'] == 2)
			{
				$jsc = JPATH_SITE . '/components/com_jsitecomments/helpers/jsc_class.php';

				if (file_exists($jsc))
				{
					require_once $jsc;

					foreach ($pks as $delid)
					{
						jsitecomments::JSCdelComment('com_cwmprayer', $delid);
					}
				}
			}
		}

		return $delete;
	}

	/**
	 * Purge
	 *
	 * @param   string  $option  Component
	 *
	 * @return string|\JException
	 *
	 * @since 4.0
	 */
	public function purge($option = 'com_cwmprayer')
	{
		if ($this->pcConfig['config_comments'] == 1)
		{
			$jcomments = JPATH_SITE . '/components/com_jcomments/jcomments.php';

			if (file_exists($jcomments))
			{
				require_once $jcomments;
			}
		}
		elseif ($this->pcConfig['config_comments'] == 2)
		{
			$jsc = JPATH_SITE . '/components/com_jsitecomments/helpers/jsc_class.php';

			if (file_exists($jsc))
			{
				require_once $jsc;
			}
		}

		$config_request_retention = $this->pcConfig['config_request_retention'];

		$config_archive_retention = $this->pcConfig['config_archive_retention'];

		$count = 0;

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__cwmprayer')
			->where('DATEDIFF(CURDATE(),date) >= ' . $db->q($config_request_retention))
			->where('publishstate = 0');
		$db->setQuery($query);

		$result = $db->loadObjectList();
		$query->clear();
		$query->select('*')
			->from('#__cwmprayer')
			->where('DATEDIFF(CURDATE(),date) >= ' . $db->q($config_request_retention))
			->where('publishstate = 2');
		$db->setQuery($query);

		$archiveresult = $db->loadObjectList();

		$db->setQuery("SELECT count(*) FROM #__cwmprayer");

		$totalreqcount = $db->loadResult();

		$requestcount = count($result);

		$archivecount = count($archiveresult);

		$totalcount = ($requestcount + $archivecount);

		if ($totalcount < 1)
		{
			$msg = 'There are no requests to purge';
		}
		else
		{
			foreach ($result as $results)
			{
				$db->setQuery("DELETE FROM #__cwmprayer WHERE id='" . (int) $results->id . "'");

				$db->execute();

				$count++;

				if ($this->pcConfig['config_comments'] > 0)
				{
					if (file_exists($jcomments))
					{
						JCommentsModel::deleteComments($results->id, 'com_cwmprayer');
					}
					elseif (file_exists($jsc))
					{
						jsitecomments::JSCdelComment('com_cwmprayer', $results->id);
					}
				}
			}

			foreach ($archiveresult as $archiveresults)
			{
				$db = JFactory::getDBO();

				$db->setQuery("DELETE FROM #__cwmprayer WHERE id='" . (int) $archiveresults->id . "'");

				$db->execute();

				$count++;

				if ($this->pcConfig['config_comments'] > 0)
				{
					if (file_exists($jcomments))
					{
						JCommentsModel::deleteComments($archiveresults->id, 'com_cwmprayer');
					}
					elseif (file_exists($jsc))
					{
						jsitecomments::JSCdelComment('com_cwmprayer', $archiveresults->id);
					}
				}
			}

			// @todo need to correct Language strings.
			if ($count > 0)
			{
				$msg = $count . ' of ' . $totalreqcount . ' Requests Purged';
			}
			else
			{
				$msg = 'No Requests Purged';
			}
		}

		return $msg;
	}
}
