<?php/** * prayer Component * * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL * */defined('_JEXEC') or die('Restricted access');class CWMPrayerControllerPrayer extends JControllerLegacy{	/**	 * @param string $option	 *	 *	 * @since version	 */	public function uploadfile($option = 'com_cwmprayer')	{		if ((!empty($_FILES['uploadedfile'])) && ($_FILES['uploadedfile']['error'] == 0))		{			$filename = basename($_FILES['uploadedfile']['name']);			$ext = substr($filename, strrpos($filename, '.') + 1);			if (($ext == "php") && ($_FILES['uploadedfile']['size'] < 350000))			{				$newname = JPATH_ADMINISTRATOR . '/components/com_cwmprayer/plugins/' . $filename;				if (!file_exists($newname))				{					if ((move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $newname)))					{						$this->setMessage("The file has been saved as: " . $newname, 'message');						$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));					}					else					{						$this->setMessage("Error: A problem occurred during file upload!", 'error');						$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));					}				}				else				{					$this->setMessage("Error: File " . $_FILES['uploadedfile']['name'] . " already exists.", 'error');					$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));				}			}			else			{				$this->setMessage("Error: Only .php files under 350Kb are accepted for upload.", 'error');				$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));			}		}		else		{			$this->setMessage("Error: No file uploaded.", 'error');			$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));		}	}	/**	 * @param string $option	 *	 *	 * @since version	 */	public function deletefile($option = 'com_cwmprayer')	{		$file = JRequest::getVar('file', null, 'method', '');		$plugin_path = JPATH_ADMINISTRATOR . '/components/com_cwmprayer/plugins/';		if (count($file))		{			unlink($plugin_path . $file);		}		$this->setMessage("File has been deleted.", 'message');		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));	}	public function uploadLangfile($option = 'com_cwmprayer')	{		if ((!empty($_FILES['uploadedlangfile'])) && ($_FILES['uploadedlangfile']['error'] == 0))		{			$filename = basename($_FILES['uploadedlangfile']['name']);			$foldername = substr($filename, 0, -21);			$ext = substr($filename, strrpos($filename, '.') + 1);			if (($ext == "ini") && ($_FILES['uploadedlangfile']['size'] < 350000))			{				$newname = JPATH_ROOT . DS . 'language' . DS . $foldername . DS . $filename;				if (!file_exists($newname))				{					if ((move_uploaded_file($_FILES['uploadedlangfile']['tmp_name'], $newname)))					{						$this->setMessage("The file has been saved as: " . $newname, 'message');						$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));					}					else					{						$this->setMessage("Error: A problem occurred during file upload!", 'error');						$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));					}				}				else				{					$this->setMessage("Error: File " . $_FILES['uploadedlangfile']['name'] . " already exists.", 'error');					$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));				}			}			else			{				$this->setMessage("Error: Only .ini files under 350Kb are accepted for upload.", 'error');				$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));			}		}		else		{			$this->setMessage("Error: No file uploaded.", 'error');			$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));		}	}	/**	 * @param string $option	 *	 *	 * @since version	 */	public function deleteLangfile($option = 'com_cwmprayer')	{		$file = JRequest::getVar('file', null, 'method', '');		$lang_folder = substr($file, 0, -21);		$lang_path = JPATH_ROOT . '/language/' . $lang_folder . '/';		if (count($file))		{			unlink($lang_path . $file);		}		$this->setMessage("File has been deleted.", 'message');		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));	}	/**	 * @param string $option	 *	 *	 * @since version	 */	public function uploadimage($option = 'com_cwmprayer')	{		if ((!empty($_FILES['uploadedimage'])) && ($_FILES['uploadedimage']['error'] == 0))		{			$imagename = basename($_FILES['uploadedimage']['name']);			$ext = substr($imagename, strrpos($imagename, '.') + 1);			if (($ext == "jpg" | $ext == "png" | $ext == "gif") && ($_FILES['uploadedimage']['size'] < 350000))			{				$newname = JPATH_ROOT . '/components/com_cwmprayer/assets/images/' . $imagename;				if (!file_exists($newname))				{					if ((move_uploaded_file($_FILES['uploadedimage']['tmp_name'], $newname)))					{						$this->setMessage("The image has been saved as: " . $newname, 'message');						$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));					}					else					{						$this->setMessage("Error: A problem occurred during image upload!", 'error');						$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));					}				}				else				{					$this->setMessage("Error: Image " . $_FILES['uploadedimage']['name'] . " already exists.", 'error');					$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));				}			}			else			{				$this->setMessage("Error: Only image files under 350Kb are accepted for upload.", 'error');				$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));			}		}		else		{			$this->setMessage("Error: No image uploaded.", 'error');			$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));		}	}	function deleteimage($option = 'com_cwmprayer')	{		$image = JRequest::getVar('image', null, 'method', 'string');		$image_path = JPATH_ROOT . '/components/com_cwmprayer/assets/images/';		if (count($image))		{			unlink($image_path . $image);		}		$this->setMessage("Image file has been deleted.", 'message');		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));	}	function uploadssimage($option = 'com_cwmprayer')	{		if ((!empty($_FILES['uploadedssimage'])) && ($_FILES['uploadedssimage']['error'] == 0))		{			$imagename = basename($_FILES['uploadedssimage']['name']);			$ext = substr($imagename, strrpos($imagename, '.') + 1);			if (($ext == "jpg" | $ext == "png" | $ext == "gif") && ($_FILES['uploadedssimage']['size'] < 350000))			{				$newname = JPATH_ROOT . '/components/com_cwmprayer/assets/images/slideshow/' . $imagename;				if (!file_exists($newname))				{					if ((move_uploaded_file($_FILES['uploadedssimage']['tmp_name'], $newname)))					{						$this->setMessage("The image has been saved as: " . $newname, 'message');						$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));					}					else					{						$this->setMessage("Error: A problem occurred during image upload!", 'error');						$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));					}				}				else				{					$this->setMessage("Error: Image " . $_FILES['uploadedssimage']['name'] . " already exists.", 'error');					$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));				}			}			else			{				$this->setMessage("Error: Only image files under 350Kb are accepted for upload.", 'error');				$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));			}		}		else		{			$this->setMessage("Error: No image uploaded.", 'error');			$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));		}	}	function deletessimage($option = 'com_cwmprayer')	{		$image = JRequest::getVar('image', null, 'method', 'string');		$image_path = JPATH_ROOT . '/components/com_cwmprayer/assets/images/slideshow/';		if (count($image))		{			unlink($image_path . $image);		}		$this->setMessage("Image file has been deleted.", 'message');		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_files", false));	}	function doPRUpgrade($option = 'com_cwmprayer')	{		$db = JFactory::getDBO();		$filePath = JPATH_COMPONENT;		$checkfileName = 'prayerrequest_copied_checkfile';		touch($filePath . DS . $checkfileName);		$db->setQuery("SELECT * FROM #__cwmprayerrequests");		$prdata = $db->loadObjectList();		foreach ($prdata as $prrequests)		{			$date = date('Y-m-d', strtotime($prrequests->date));			$time = date('h:i:s', strtotime($prrequests->date));			$db->setQuery("INSERT INTO #__cwmprayer (id,requester,date,time,request,publishstate,archivestate,displaystate,sendto,email,adminsendto) VALUES (''," . $db->quote($db->getEscaped($prrequests->requester), false) . "," . $db->quote($db->getEscaped($date), false) . "," . $db->quote($db->getEscaped($time), false) . "," . $db->quote($db->getEscaped($prrequests->request), false) . ",'" . (int) $prrequests->approved . "','" . (int) $prrequests->archived . "','" . (int) $prrequests->display . "','" . (int) $prrequests->distribute . "'," . $db->quote($db->getEscaped($prrequests->email), false) . ",'" . (int) $prrequests->distribute . "')");			$db->query();		}		if (!$db->query())		{			return JError::raiseWarning(500, $db->stderr());		}		else		{			$this->setMessage("Prayer Requests Data Migrated Into prayer.", 'message');			$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=support", false));		}	}	function syncsub($option = 'com_cwmprayer')	{		$app = JFactory::getApplication();		jimport('joomla.date.date');		$db = JFactory::getDBO();		$db->setQuery("SELECT email FROM #__users WHERE block='0'");		$uemail = $db->loadObjectList();		$db->setQuery("SELECT email FROM #__cwmprayer_subscribe");		$pcemail = $db->loadObjectList();		$dateset = new JDate();		$date = $dateset->format('Y-m-d');		$count = 0;		foreach ($uemail as $ue)		{			if (!$this->pca_recursive_in_array($ue->email, $pcemail))			{				$db->setQuery("INSERT INTO #__cwmprayer_subscribe (id,email,date,approved) VALUES (''," . $db->quote($db->getEscaped($ue->email), false) . "," . $db->quote($db->getEscaped($date), false) . ",'0')");				if (!$db->query())				{					return JError::raiseWarning(500, $db->stderr());				}				$count++;			}		}		$this->setMessage($count . " Joomla registered users email addresses syncronized with prayer subscriber list.", 'message');		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_sub", false));	}	function pca_recursive_in_array($needle, $haystack)	{		foreach ($haystack as $stalk)		{			if ($needle == $stalk->email || (is_array($stalk) && $this->pca_recursive_in_array($needle, $stalk)))			{				return true;			}		}		return false;	}	function remove_req($option = 'com_cwmprayer')	{		$cid = JRequest::getVar('cid', array(0), 'post', 'array');		$db = JFactory::getDBO();		if (count($cid))		{			$cids = implode(',', $cid);			$db->setQuery("DELETE FROM #__cwmprayer WHERE id IN ($cids)");			if (!$db->query())			{				return JError::raiseWarning(500, $db->getError());			}		}		if ($pcConfig['config_comments'] == 1)		{			$jcomments = JPATH_SITE . '/components/com_jcomments/jcomments.php';			if (file_exists($jcomments))			{				require_once($jcomments);				foreach ($cid as $delid)				{					JCommentsModel::deleteComments($delid, 'com_cwmprayer');				}			}		}		elseif ($pcConfig['config_comments'] == 2)		{			$jsc = JPATH_SITE . '/components/com_jsitecomments/helpers/jsc_class.php';			if (file_exists($jsc))			{				require_once($jsc);				foreach ($cid as $delid)				{					jsitecomments::JSCdelComment('com_cwmprayer', $delid);				}			}		}		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_req", false));	}	function remove_link($option = 'com_cwmprayer')	{		$cid = JRequest::getVar('cid', array(0), 'post', 'array');		$db = JFactory::getDBO();		if (count($cid))		{			$cids = implode(',', $cid);			$db->setQuery("DELETE FROM #__cwmprayer_links WHERE id IN ($cids)");			if (!$db->query())			{				return JError::raiseWarning(500, $db->getError());			}		}		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_link", false));	}	function remove_devotion($option = 'com_cwmprayer')	{		$cid = JRequest::getVar('cid', array(0), 'post', 'array');		$db = JFactory::getDBO();		if (count($cid))		{			$cids = implode(',', $cid);			$db->setQuery("DELETE FROM #__cwmprayer_devotions WHERE id IN ($cids)");			if (!$db->query())			{				return JError::raiseWarning(500, $db->getError());			}		}		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_dev", false));	}	function remove_sub($option = 'com_cwmprayer')	{		$cid = JRequest::getVar('cid', array(0), 'post', 'array');		$db = JFactory::getDBO();		if (count($cid))		{			$cids = implode(',', $cid);			$db->setQuery("DELETE FROM #__cwmprayer_subscribe WHERE id IN ($cids)");			if (!$db->query())			{				return JError::raiseWarning(500, $db->getError());			}		}		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_sub", false));	}	function cancelSettings($option = 'com_cwmprayer')	{		$this->setRedirect(JRoute::_("index.php?option=" . $option, false));	}	function canceledit($option = 'com_cwmprayer')	{		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_req", false));	}	function canceleditlink($option = 'com_cwmprayer')	{		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_link", false));	}	function canceleditlang($option = 'com_cwmprayer')	{		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_lang", false));	}	function canceleditdevotion($option = 'com_cwmprayer')	{		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_dev", false));	}	function publish($option = 'com_cwmprayer')	{		$db = JFactory::getDBO();		$cid = JRequest::getVar('cid', array(0), 'post', 'array');		preg_match('/\.(\w+)$/', JRequest::getVar('task', null, 'method'), $action);		if ($action[1] == 'publish')		{			$publish = true;		}		else		{			$publish = false;		}		$count = count($cid);		if (!is_array($cid) || $count < 1 || $cid[0] == 0)		{			$this->setMessage('Select an item to ' . $action[1], 'message');			$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_req", false));		}		for ($i = 0; $i < $count; $i++)		{			$db->setQuery("UPDATE #__cwmprayer SET publishstate='" . (int) $publish . "'"				. "\nWHERE id='" . (int) $cid[$i] . "'");			if (!$db->query())			{				return JError::raiseWarning(500, $db->stderr());			}			if ($action[1] == 'publish')			{				$lang = Jfactory::getLanguage();				$lang->load('com_cwmprayer', JPATH_SITE);				$db->setQuery("SELECT * FROM #__cwmprayer WHERE id='" . (int) $cid[$i] . "'");				$publishedq = $db->loadObjectList();				$published = $publishedq[0];				$newrequest = $published->request;				$newrequester = $published->requester;				$newemail = $published->email;				$sendpriv = $published->displaystate;				$sessionid = $published->sessionid;				if ($sendpriv)				{					if ($pcConfig['config_distrib_type'] > 1 && !empty($pcConfig['config_pms_plugin']))					{						$prayeradmin->PCAsendPM($newrequesterid, $newrequester, $newrequest, $newemail, $sendpriv);					}				}				elseif (!$sendpriv)				{					if ($pcConfig['config_distrib_type'] > 1 && !empty($pcConfig['config_pms_plugin']))					{						$prayeradmin->PCAsendPM($newrequesterid, $newrequester, $newrequest, $newemail, $sendpriv, $cid[$i], $sessionid);					}				}			}		}		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_req", false));	}	function publishlink($option = 'com_cwmprayer')	{		$db = JFactory::getDBO();		$cid = JRequest::getVar('cid', array(0), 'post', 'array');		preg_match('/\.(\w+)$/', JRequest::getVar('task', null, 'method'), $action);		if ($action[1] == 'publishlink')		{			$publish = true;		}		else		{			$publish = false;		}		$count = count($cid);		if (!is_array($cid) || $count < 1 || $cid[0] == 0)		{			$this->setMessage('Select an item to ' . $action[1], 'message');			$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_link", false));		}		for ($i = 0; $i < $count; $i++)		{			$db->setQuery("UPDATE #__cwmprayer_links SET published='" . (int) $publish . "'"				. "\nWHERE id='" . (int) $cid[$i] . "'");			if (!$db->query())			{				return JError::raiseWarning(500, $db->stderr());			}		}		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_link", false));	}	function publishdevotion($option = 'com_cwmprayer')	{		$db = JFactory::getDBO();		$cid = JRequest::getVar('cid', array(0), 'post', 'array');		preg_match('/\.(\w+)$/', JRequest::getVar('task', null, 'method'), $action);		if ($action[1] == 'publishdevotion')		{			$publish = true;		}		else		{			$publish = false;		}		$count = count($cid);		if (!is_array($cid) || $count < 1 || $cid[0] == 0)		{			$this->setMessage('Select an item to ' . $action[1], 'message');			$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_dev", false));		}		for ($i = 0; $i < $count; $i++)		{			$db->setQuery("UPDATE #__cwmprayer_devotions SET published='" . (int) $publish . "'"				. "\nWHERE id='" . (int) $cid[$i] . "'");			if (!$db->query())			{				return JError::raiseWarning(500, $db->stderr());			}		}		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_dev", false));	}	function displayreq($option = 'com_cwmprayer')	{		$cid = JRequest::getVar('cid', array(0), 'post', 'array');		preg_match('/\.(\w+)$/', JRequest::getVar('task', null, 'method'), $action);		if ($action[1] == 'displayreq')		{			$display = true;		}		else		{			$display = false;		}		$count = count($cid);		if (!is_array($cid) || $count < 1 || $cid[0] == 0)		{			$this->setMessage('Select an item to ' . rtrim("req", $action[1]), 'message');			$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_req", false));		}		for ($i = 0; $i < $count; $i++)		{			$db = JFactory::getDBO();			$db->setQuery("UPDATE #__cwmprayer SET displaystate='" . (int) $display . "'"				. "\nWHERE id='" . (int) $cid[$i] . "'");			if (!$db->query())			{				return JError::raiseWarning(500, $db->stderr());			}		}		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_req", false));	}	function archive($option = 'com_cwmprayer')	{		$cid = JRequest::getVar('cid', array(0), 'post', 'array');		preg_match('/\.(\w+)$/', JRequest::getVar('task', null, 'method'), $action);		if ($action[1] == 'archive')		{			$archive = true;		}		else		{			$archive = false;		}		$count = count($cid);		if (!is_array($cid) || $count < 1 || $cid[0] == 0)		{			$this->setMessage('Select an item to ' . $action[1], 'message');			$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_req", false));		}		for ($i = 0; $i < $count; $i++)		{			$db = JFactory::getDBO();			$db->setQuery("UPDATE #__cwmprayer SET archivestate='" . (int) $archive . "'"				. "\nWHERE id='" . (int) $cid[$i] . "'");			if (!$db->query())			{				return JError::raiseWarning(500, $db->stderr());			}		}		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_req", false));	}	function purge($option = 'com_cwmprayer')	{		if ($pcConfig['config_comments'] == 1)		{			$jcomments = JPATH_SITE . '/components/com_jcomments/jcomments.php';			if (file_exists($jcomments))			{				require_once($jcomments);			}		}		elseif ($pcConfig['config_comments'] == 2)		{			$jsc = JPATH_SITE . '/components/com_jsitecomments/helpers/jsc_class.php';			if (file_exists($jsc))			{				require_once($jsc);			}		}		$config_request_retention = $pcConfig['config_request_retention'];		$config_archive_retention = $pcConfig['config_archive_retention'];		$count = 0;		$totalcount = 0;		$db = JFactory::getDBO();		$db->setQuery("SELECT * FROM #__cwmprayer WHERE DATEDIFF(CURDATE(),date) >= " . $config_request_retention . " AND archivestate='0'");		$result = $db->loadObjectList();		$db->setQuery("SELECT * FROM #__cwmprayer WHERE DATEDIFF(CURDATE(),date) >= " . $config_archive_retention . " AND archivestate='1'");		$archiveresult = $db->loadObjectList();		$db->setQuery("SELECT count(*) FROM #__cwmprayer");		$totalreqcount = $db->loadResult();		$requestcount = count($result);		$archivecount = count($archiveresult);		$totalcount = ($requestcount + $archivecount);		if ($totalcount < 1)		{			$msg = 'There are no requests to purge';		}		else		{			foreach ($result as $results)			{				$db->setQuery("DELETE FROM #__cwmprayer WHERE id='" . (int) $results->id . "'");				$db->Query();				$count++;				if ($pcConfig['config_comments'] > 0)				{					if (file_exists($jcomments))					{						JCommentsModel::deleteComments($results->id, 'com_cwmprayer');					}					elseif (file_exists($jsc))					{						jsitecomments::JSCdelComment('com_cwmprayer', $results->id);					}				}			}			foreach ($archiveresult as $archiveresults)			{				$db = JFactory::getDBO();				$db->setQuery("DELETE FROM #__cwmprayer WHERE id='" . (int) $archiveresults->id . "'");				$db->Query();				$count++;				if ($pcConfig['config_comments'] > 0)				{					if (file_exists($jcomments))					{						JCommentsModel::deleteComments($archiveresults->id, 'com_cwmprayer');					}					elseif (file_exists($jsc))					{						jsitecomments::JSCdelComment('com_cwmprayer', $archiveresults->id);					}				}			}			if ($count > 0)			{				$msg = $count . ' of ' . $totalreqcount . ' Requests Purged';			}			else			{				$msg = 'No Requests Purged';			}		}		$this->setMessage($msg, 'message');		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_req", false));	}	function saveCss($option = 'com_cwmprayer')	{		JRequest::checkToken() or jexit('Invalid Token');		$config_css = JRequest::getVar('config_css', null, 'post', 'string');		$configcss = str_replace("[CR][NL]", "\n", $config_css);		$configcss = str_replace("[ES][SQ]", "'", $configcss);		$configcss = nl2br($configcss);		$configcss = str_replace("<br />", " ", $configcss);		$filename = JPATH_ROOT . '/components/com_cwmprayer/assets/css/prayer.css';		file_put_contents($filename, $configcss);		$this->setMessage("Changes in CSS have been saved.", 'message');		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_css", false));	}	function saveLang($option = 'com_cwmprayer')	{		JRequest::checkToken() or jexit('Invalid Token');		$foldername = JRequest::getVar('config_langfolder', null, 'post', 'string');		$filename = JRequest::getVar('config_langfile', null, 'post', 'string');		$config_lang = JRequest::getVar('config_lang', null, 'post', 'string');		$configlang = str_replace("[CR][NL]", "\n", $config_lang);		$configlang = str_replace("[ES][SQ]", "'", $configlang);		$configlang = nl2br($configlang);		$configlang = str_replace("<br />", " ", $configlang);		$langfilepath = JPATH_ROOT . '/language/' . $foldername;		if (!is_dir($langfilepath) || !file_exists($langfilepath . '/' . $foldername . '.xml'))		{			$this->setMessage("Please install the corresponding Joomla language extension for " . $foldername . ".COM_CWMPRAYER.ini.", 'message');			$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_lang", false));		}		$ldata = JApplicationHelper::parseXMLLangMetaFile($langfilepath . '/' . $foldername . '.xml');		if (!is_array($ldata))		{			$this->setMessage("Please install the valid Joomla language extension for " . $foldername . ".COM_CWMPRAYER.ini.", 'message');			$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_lang", false));		}		$langfilename = $langfilepath . '/' . $foldername . ".COM_CWMPRAYER.ini";		file_put_contents($langfilename, stripslashes($configlang));		$this->setMessage("Changes in the language file have been saved.", 'message');		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_lang", false));	}	function resetCss($option = 'com_cwmprayer')	{		$savfilename = JPATH_ROOT . '/components/com_cwmprayer/assets/css/prayer.sav';		$savfilecontent = file_get_contents($savfilename);		$replacecss = str_replace("[CR][NL]", "\n", $savfilecontent);		$replacecss = str_replace("[ES][SQ]", "'", $replacecss);		$replacecss = nl2br($replacecss);		$replacecss = str_replace("<br />", " ", $replacecss);		$filename = JPATH_ROOT . '/components/com_cwmprayer/assets/css/prayer.css';		file_put_contents($filename, $replacecss);		$this->setMessage("CSS has been reset to default settings.", 'message');		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_css", false));	}	function resetLang($option = 'com_cwmprayer')	{		$savfilename = JPATH_ROOT . '/administrator/components/com_cwmprayer/langsource/j30/en-GB.COM_CWMPRAYER.ini';		$savfilecontent = file_get_contents($savfilename);		$replacelang = str_replace("[CR][NL]", "\n", $savfilecontent);		$replacelang = str_replace("[ES][SQ]", "'", $replacelang);		$replacelang = nl2br($replacelang);		$replacelang = str_replace("<br />", " ", $replacelang);		$filename = JPATH_ROOT . '/language/en-GB/en-GB.COM_CWMPRAYER.ini';		file_put_contents($filename, stripslashes($replacelang));		$this->setMessage("The prayer English language file has been reset to default settings.", 'message');		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_lang", false));	}	function savereq($option = 'com_cwmprayer')	{		JRequest::checkToken() or jexit('Invalid Token');		$db = JFactory::getDBO();		$app = JFactory::getApplication();		$postarray = JRequest::get('post');		$post = $postarray['jform'];		$save = "UPDATE #__cwmprayer SET request=" . $db->quote($post['request']) . ",title=" . $db->quote($post['title']) . ",topic=" . (int) $post['topic'] . ", date=" . $db->quote($post['date']) . ", time=" . $db->quote($post['time']) . " WHERE id='" . (int) $post['id'] . "'";		$db->setQuery($save);		if (!$db->query())		{			return JError::raiseWarning(500, $db->getError());		}		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_req", false));	}	function savelink($option = 'com_cwmprayer')	{		JRequest::checkToken() or jexit('Invalid Token');		$db = JFactory::getDBO();		$postarray = JRequest::get('post');		$post = $postarray['jform'];		$id = JRequest::getVar('cid', null, 'post', 'array');		if ($id[0] > 0)		{			$save = "UPDATE #__cwmprayer_links SET name='" . addslashes(JText::_($post['name'])) . "', url='" . $post['url'] . "', alias='" . addslashes(JText::_($post['alias'])) . "', published='" . $post['published'] . "', ordering='" . $post['ordering'] . "', catid='" . $post['catid'] . "' WHERE id='" . (int) $id[0] . "'";		}		else		{			$save = "INSERT INTO #__cwmprayer_links (id,name,url,alias,published,checked_out,checked_out_time,ordering,catid) VALUES ('','" . addslashes(JText::_($post['name'])) . "','" . $post['url'] . "','" . addslashes(JText::_($post['alias'])) . "','" . $post['published'] . "','','','" . $post['ordering'] . "','" . $post['catid'] . "')";		}		$db->setQuery($save);		if (!$db->query())		{			return JError::raiseWarning(500, $db->getError());		}		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_link", false));	}	function savedevotion($option = 'com_cwmprayer')	{		JRequest::checkToken() or jexit('Invalid Token');		$db = JFactory::getDBO();		$postarray = JRequest::get('post');		$post = $postarray['jform'];		$id = JRequest::getVar('cid', null, 'post', 'array');		if ($id > 0)		{			$save = "UPDATE #__cwmprayer_devotions SET name='" . addslashes(JText::_($post['name'])) . "', feed='" . $post['feed'] . "', published='" . $post['published'] . "', ordering='" . $post['ordering'] . "', catid='" . $post['catid'] . "' WHERE id='" . (int) $id[0] . "'";		}		else		{			$save = "INSERT INTO #__cwmprayer_devotions (id,name,feed,published,checked_out,checked_out_time,ordering,catid) VALUES ('','" . addslashes(JText::_($post['title'])) . "','" . $post['feed'] . "','" . $post['published'] . "','','','" . $post['ordering'] . "','" . $post['catid'] . "')";		}		$db->setQuery($save);		if (!$db->query())		{			return JError::raiseWarning(500, $db->getError());		}		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=manage_dev", false));	}}