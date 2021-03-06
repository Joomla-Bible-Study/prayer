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
 * Prayer Component Controller
 *
 * @since  4.0
 */
class CWMPrayerController extends JControllerLegacy
{
	/**
	 * The default view.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $default_view = 'prayer';

	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types,
	 *                               for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  bool|JControllerLegacy  A JControllerLegacy object to support chaining.
	 *
	 * @since   3.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		JLoader::register('CWMPrayerHelper', JPATH_ADMINISTRATOR . '/components/com_cwmprayer/helpers/cwmprayer.php');

		$view   = $this->input->get('view', 'prayer');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		// Check for edit form.
		if ($view == 'editdev' && $layout == 'edit' && !$this->checkEditId('com_cwmprayer.edit.editdev', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_cwmprayer&view=devotions', false));

			return false;
		}

		return parent::display();
	}

	/**
	 * Optimize P Tables
	 *
	 * @param   string  $option  ?
	 *
	 * @return  JException| void
	 *
	 * @throws  Exception
	 *
	 * @since 4.0
	 *
	 * @todo  Not sure if this is used.
	 */
	public function optimizePCTables($option = 'com_cwmprayer')
	{
		$db = JFactory::getDBO();

		$dbcmds = [$db->name . '_data_seek', $db->name . '_num_rows', $db->name . '_fetch_assoc'];

		$sql = "OPTIMIZE TABLE #__cwmprayer, #__cwmprayer_subscribe, #__cwmprayer_links, #__cwmprayer_devotions";

		$db->setQuery($sql);

		$rs_status = $db->execute();

		if (!$rs_status)
		{
			throw new Exception('Error Optimizing tables');
		}

		$dbcmds[0]($rs_status, $dbcmds[1]($rs_status) - 1);

		$row_status = $dbcmds[2]($rs_status);

		$this->setMessage("prayer database tables have been optimized.  (" . ucfirst($row_status['Msg_type']) . ": " .
			$row_status['Msg_text'] . ")", 'message');

		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=utilities", false));
	}

	/**
	 * Check P Tables
	 *
	 * @param   string  $option  ?
	 *
	 * @return void
	 *
	 * @throws  Exception
	 *
	 * @since 4.0
	 */
	public function checkPCTables($option = 'com_cwmprayer')
	{
		$db = JFactory::getDBO();

		$dbcmds = [$db->name . '_data_seek', $db->name . '_num_rows', $db->name . '_fetch_assoc'];

		$sql = "CHECK TABLE #__cwmprayer, #__cwmprayer_subscribe, #__cwmprayer_links, #__cwmprayer_devotions MEDIUM";

		$db->setQuery($sql);

		$rs_status = $db->execute();

		if (!$rs_status)
		{
			throw new Exception('Error Optimizing tables');
		}

		$dbcmds[0]($rs_status, $dbcmds[1]($rs_status) - 1);

		$row_status = $dbcmds[2]($rs_status);

		$this->setMessage("prayer database tables have been checked.  (" . ucfirst($row_status['Msg_type']) . ": " .
			$row_status['Msg_text'] . ")", 'message');

		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=utilities", false));
	}

	/**
	 * Repair Tables
	 *
	 * @param   string  $option  ?
	 *
	 * @return void
	 *
	 * @throws  Exception
	 *
	 * @since 4.0
	 */
	public function repairPCTables($option = 'com_cwmprayer')
	{
		$db = JFactory::getDBO();

		$dbcmds = [$db->name . '_data_seek', $db->name . '_num_rows', $db->name . '_fetch_assoc'];

		$sql = "REPAIR TABLE #__cwmprayer, #__cwmprayer_subscribe, #__cwmprayer_links, #__cwmprayer_devotions";

		$db->setQuery($sql);

		$rs_status = $db->execute();

		if (!$rs_status)
		{
			throw new Exception('Error Optimizing tables');
		}

		$dbcmds[0]($rs_status, $dbcmds[1]($rs_status) - 1);

		$row_status = $dbcmds[2]($rs_status);

		$this->setMessage("prayer database tables have been repaired.  (" . ucfirst($row_status['Msg_type']) . ": " .
			$row_status['Msg_text'] . ")", 'message');

		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=utilities", false));
	}

	/**
	 * Backup tables
	 *
	 * @param   string  $option       ?
	 * @param   bool    $locks        ?
	 * @param   bool    $compress     ?
	 * @param   bool    $drop_tables  ?
	 * @param   bool    $download     ?
	 *
	 * @return mixed
	 *
	 * @throws  Exception
	 *
	 * @since 4.0
	 */
	public function backupPCTables($option = 'com_cwmprayer', $locks = true, $compress = false, $drop_tables = true, $download = true)
	{
		$db = JFactory::getDBO();
		$insert = null;

		$dbcmds = [$db->name . '_data_seek', $db->name . '_num_rows', $db->name . '_fetch_assoc', $db->name . '_fetch_row'];

		$app = JFactory::getApplication('site');

		$dbprefix = $app->getCfg('dbprefix');

		$fpath = 'components' . DS . 'com_cwmprayer' . DS;

		$filename = ($compress ? 'prayer.sql.gz' : 'prayer.sql');

		$fname = $fpath . $filename;

		$value = "";

		$tablestr = 'prayer,prayer_subscribe,prayer_links,prayer_devotions';

		$tables = preg_split('/[,]/', $tablestr, -1, PREG_SPLIT_NO_EMPTY);

		$null_values = ['0000-00-00', '00:00:00', '0000-00-00 00:00:00'];

		$compress ? $fp = gzopen($fname, 'w9') : $fp = fopen($fname, 'w');

		$sql = "LOCK TABLES #__cwmprayer WRITE, #__cwmprayer_subscribe WRITE, #__cwmprayer_links WRITE, #__cwmprayer_devotions WRITE";

		$db->setQuery($sql);

		$rs_status = $db->execute();

		if (!$rs_status)
		{
			throw new Exception('Error Optimizing tables');
		}

		$value .= '# ' . "\n";

		$value .= '# prayer Database Table Dump' . "\n";

		$value .= '# Host: ' . $app->get('sitename') . "\n";

		$value .= '# Generated: ' . date('M j, Y') . ' at ' . date('H:i:s') . "\n";

		$value .= '# MySQL version: ' . $db->getVersion() . "\n";

		$value .= '# PHP version: ' . phpversion() . "\n";

		$value .= '# ' . "\n";

		$value .= '# Database: `' . $app->get('db') . '`' . "\n";

		$value .= '# Tables: `' . str_replace("p", " P", $tablestr) . '`' . "\n";

		$value .= '# ' . "\n\n\n";

		foreach ($tables as $table)
		{
			if ($drop_tables)
			{
				$value .= 'DROP TABLE IF EXISTS `' . $dbprefix . $table . '`;' . "\n";
			}

			$sql = "SHOW CREATE TABLE #__" . $table;

			$db->setQuery($sql);

			if (!($result = $db->execute()))
			{
				return JError::raiseWarning(500, $db->stderr());
			}

			$row = $dbcmds[2]($result);

			$value .= $row['Create Table'] . ';';

			$value .= "\n\n";

			$sql = "SELECT * FROM #__" . $table;

			$db->setQuery($sql);

			if (!($result = $db->execute()))
			{
				return JError::raiseWarning(500, $db->stderr());
			}

			$num_rows = $dbcmds[1]($result);

			if ($num_rows > 0)
			{
				if ($locks)
				{
					$value .= 'LOCK TABLES #__' . $table . ' WRITE;' . "\n\n";
				}

				$value .= 'INSERT INTO #__' . $table;

				$row = $dbcmds[2]($result);

				$value .= ' (`' . implode('`,`', array_keys($row)) . '`)';

				$value .= ' VALUES ';

				$fields = count($row);

				$dbcmds[0]($result, 0);

				$value .= "\n";

				if ($fp)
				{
					$compress ? gzwrite($fp, $value) : fwrite($fp, $value);
				}

				$j = 0;

				$size = 0;

				while ($row = $dbcmds[3]($result))
				{
					if ($fp)
					{
						$i = 0;

						$compress ? $size += gzwrite($fp, '(') : $size += fwrite($fp, '(');

						for ($x = 0; $x < $fields; $x++)
						{
							if (!isset($row[$x]) || in_array($row[$x], $null_values))
							{
								$row[$x] = 'NULL';
							}
							else
							{
								$row[$x] = '\'' . str_replace("\n", "\\n", addslashes($row[$x])) . '\'';
							}

							if ($i > 0)
							{
								$compress ? $size += gzwrite($fp, ',') : $size += fwrite($fp, ",");
							}

							$compress ? $size += gzwrite($fp, $row[$x]) : $size += fwrite($fp, $row[$x]);

							$i++;
						}

						$compress ? $size += gzwrite($fp, ')') : $size += fwrite($fp, ')');

						if ($j + 1 < $num_rows && $size < 900000)
						{
							$compress ? $size += gzwrite($fp, ",\n") : $size += fwrite($fp, ",\n");
						}
						else
						{
							$size = 0;

							$compress ? gzwrite($fp, ';' . "\n\n\n") : fwrite($fp, ';' . "\n\n\n");

							if ($j + 1 < $num_rows)
							{
								$compress ? gzwrite($fp, $insert) : fwrite($fp, $insert);
							}
							elseif ($locks)
							{
								$compress ? gzwrite($fp, 'UNLOCK TABLES;' . "\n") : fwrite($fp, 'UNLOCK TABLES;' . "\n");
							}
						}

						$j++;
					}
				}

				$value = "";
			}
		}

		$sql = "UNLOCK TABLES";

		$db->setQuery($sql);

		if (!$db->execute())
		{
			return JError::raiseWarning(500, $db->stderr());
		}

		$compress ? gzclose($fp) : fclose($fp);

		$fp = fopen($fname, 'rb');

		if ($fp && $download)
		{
			if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT']))
			{
				header("Content-type: application/octet-stream;");
				header('Content-disposition: attachment; filename=' . $filename . ';');
				header('Pragma: no-cache;');
				header('Expires: 0;');
			}
			else
			{
				header("Refresh:0; URL=index.php?option=com_cwmprayer&task=utilities");
				header("Content-type: application/octet-stream;");
				header('Content-disposition: attachment; filename=' . $filename . ';');
				header('Pragma: no-cache;');
				header('Expires: 0;');
			}

			while ($value = fread($fp, 8192))
			{
				echo $value;

				unset ($value);
			}

			$compress ? gzclose($fp) : fclose($fp);

			@unlink($fname);
		}

		return null;
	}

	/**
	 * Restore Tables
	 *
	 * @param   string  $option  ?
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	public function restorePCTables($option = 'com_cwmprayer')
	{
		$db = JFactory::getDBO();

		jimport('joomla.installer.helper');

		if ((!empty($_FILES['uploadedbkfile'])) && ($_FILES['uploadedbkfile']['error'] == 0))
		{
			$filename = basename($_FILES['uploadedbkfile']['name']);

			$ext = substr($filename, strrpos($filename, '.') + 1);

			if ($ext == "sql")
			{
				$newname = JPATH_ADMINISTRATOR . '/components/com_cwmprayer/' . $filename;

				if (!file_exists($newname))
				{
					if ((move_uploaded_file($_FILES['uploadedbkfile']['tmp_name'], $newname)))
					{
						$buffer = file_get_contents($newname);

						$queries = JInstallerHelper::splitSql($buffer);

						foreach ($queries as $query)
						{
							$query = trim($query);

							if ($query != '' && $query{0} != '#')
							{
								$db->setQuery($query);

								if (!$db->query())
								{
									return JError::raiseWarning(500, $db->stderr());
								}
							}
						}

						@unlink($newname);

						$this->setMessage("CWM Prayer database tables have been restored from path provided.", 'message');

						$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=utilities", false));
					}
					else
					{
						$this->setMessage("Error: A problem occurred during file upload!", 'error');

						$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=utilities", false));
					}
				}
				else
				{
					$this->setMessage("Error: File " . $_FILES['uploadedbkfile']['name'] . " already exists", 'error');

					$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=utilities", false));
				}
			}
			else
			{
				$this->setMessage("Error: Only .sql files are accepted for upload", 'error');

				$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=utilities", false));
			}
		}
		else
		{
			$this->setMessage("Error: No file uploaded", 'error');

			$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=utilities", false));
		}

		return null;
	}

	/**
	 * Manage Req
	 *
	 * @return void
	 *
	 * @since version
	 */
	public function support()
	{
		JRequest::setVar('view', 'support');

		CWMPrayerHelper::addSubmenu(JRequest::getCmd('view', 'prayer'));

		parent::display();
	}

	/**
	 * Manage Req
	 *
	 * @return void
	 *
	 * @since version
	 */
	public function utilities()
	{
		JRequest::setVar('view', 'utilities');

		CWMPrayerHelper::addSubmenu(JRequest::getCmd('view', 'prayer'));

		parent::display();
	}

	/**
	 * Manage Req
	 *
	 * @param   string  $option  ?
	 *
	 * @return void
	 *
	 * @since version
	 */
	public function showmigwiz($option = 'com_cwmprayer')
	{
		jexit('bad usage showmigwiz');
	}

	/**
	 * Purge Error Log
	 *
	 * @param   string  $option  ?
	 *
	 * @return void
	 *
	 * @since version
	 */
	public function purgeErrorLog($option = 'com_cwmprayer')
	{
		jexit('bad usage purgeErrorLog');
		$user = JFactory::getUser();

		@unlink(JPATH_ROOT . '/administrator/components/com_cwmprayer/logs/pcerrorlog.php');

		jimport('joomla.error.log');

		jimport('joomla.utilities.date');

		$dateset = new JDate(gmdate('Y-m-d H:i:s'));

		$options['format'] = "{DATE} {TIME} {MESSAGE}";

		$log = JLog::getInstance('pcerrorlog.php', $options, JPATH_ROOT . '/administrator/components/com_cwmprayer/logs');

		$pcerrorlog = [];

		$pcerrorlog['message'] = JText::_('Log file purged by ') . $user->get('name');

		$pcerrorlog['time'] = $dateset->toFormat("%H:%M:%S(GMT)");

		$log->addEntry($pcerrorlog);

		$this->setMessage("Log file purged.", 'message');

		$this->setRedirect(JRoute::_("index.php?option=" . $option . "&task=utilities", false));
	}

	/**
	 * Manage Req
	 *
	 * @return void
	 *
	 * @since version
	 */
	public function pchelp()
	{
		jexit('bad usage getHelpPCTOC');

		$helpurl = 'http://www.mlwebtechnologies.com';

		$fullhelpurl = $helpurl . '/index.php?option=com_content&amp;tmpl=component&amp;task=findkey&amp;pop=1&amp;keyref=';

		$helpsearch = JRequest::getString('pchelpsearch');

		$helpsearch = str_replace(['=', '<', '"'], '', $helpsearch);

		$page = JRequest::getCmd('page', 'pcnews');

		$toc = $this->getHelpPCToc($helpsearch, $helpurl);

		?>

		<form action="index.php?option=com_cwmprayer&amp;tmpl=component" method="post" name="pchelpForm">

			<fieldset>

				<div style="float: right">

					<button type="button" onclick="window.parent.SqueezeBox.close();">

						<?php echo JText::_('Close'); ?></button>&nbsp;&nbsp;&nbsp;&nbsp;

				</div>

				<div class="configuration">

					<?php echo JText::_('prayer Help') ?>

				</div>

			</fieldset>

			<table class="adminform" border="1">

				<tr>

					<td colspan="2">

						<table width="100%">

							<tr>

								<td>

									<strong><?php echo JText::_('Search'); ?>:</strong>

									<input type="text" name="pchelpsearch" value="" class="inputbox"/>

									<input type="submit" value="<?php echo JText::_('Go'); ?>" class="button"/>

									<input type="button" value="<?php echo JText::_('Reset'); ?>" class="button"
									       onclick="f=document.pchelpForm;f.pchelpsearch.value='';f.submit()"/>

								</td>

								<td class="helpMenu">

									<?php

									if ($helpurl)
									{
										?>

										<?php echo JHTML::_('link', $helpurl . '/index.php?option=com_kunena', JText::_('Support Forum'), ['target' => '_blank']) ?>

									<?php
									}
									?>
									&nbsp;|&nbsp;

									<?php echo JHTML::_('link', 'http://www.gnu.org/licenses/gpl-2.0.html', JText::_('License'), ['target' => 'helpFrame']) ?>

									&nbsp;|&nbsp;

									<?php echo JHTML::_('link', $fullhelpurl . 'change-logs', JText::_('Changelog'), ['target' => 'helpFrame']) ?>

								</td>

							</tr>

						</table>

					</td>

				</tr>

			</table>

			<div id="treecellhelp">

				<fieldset title="<?php echo JText::_('Alphabetical Index'); ?>">

					<legend>

						<?php echo JText::_('Alphabetical Index'); ?>

					</legend>

					<div class="helpIndex">

						<ul class="subext">

							<?php

							if (is_array($toc))
							{
								foreach ($toc as $tocitem)
								{
									if ($helpurl)
									{
										echo '<li>';

										echo JHTML::_('link', $fullhelpurl . $tocitem['keyref'], $tocitem['title'], ['target' => 'helpFrame']);

										echo '</li>';
									}
								}
							}
							else
							{
								echo $toc;
							}

							?>

						</ul>

					</div>

				</fieldset>

			</div>

			<div id="datacellhelp">

				<fieldset title="<?php echo JText::_('View'); ?>">

					<legend>

						<?php echo JText::_('View'); ?>

					</legend>

					<?php
					if ($helpurl)
					{
						?>
						<iframe name="helpFrame" src="<?php echo $fullhelpurl . $page; ?>" class="helpFrame"
						        frameborder="0" width="100% height=" 100%"></iframe>
						<?php
					}
					?>

				</fieldset>

			</div>

			<input type="hidden" name="task" value="pchelp"/>

		</form>

		<?php
	}

	/**
	 * Bad url
	 *
	 * @param   string  $helpsearch  ?
	 * @param   string  $helpurl     ?
	 *
	 * @return array|string
	 *
	 * @since version
	 */
	public function getHelpPCTOC($helpsearch, $helpurl)
	{
		jexit('bad usage getHelpPCTOC');
		$fullhelpurl = 'http://www.mlwebtechnologies.com/index.php?option=com_content&amp;tmpl=component&amp;task=findkey&amp;pop=1&amp;keyref=';

		$docliststr = file_get_contents($fullhelpurl . 'pcdocslist');

		preg_match_all('#<p>(.*?)</p>#', $docliststr, $doclist);

		$toc = [];

		foreach ($doclist[1] as $key => $line)
		{
			$line = strip_tags($line);

			$buffer = file_get_contents($fullhelpurl . $line);

			if (preg_match('#<title>(.*?)</title>#', $buffer, $m))
			{
				$title = trim($m[1]);

				if ($title)
				{
					if ($helpsearch)
					{
						if (JString::strpos(strip_tags($buffer), $helpsearch) !== false)
						{
							$toc[$key] = $title;

							$toc[$key]['keyref'] = $line;
						}
					}
					else
					{
						$toc[$key]['title'] = $title;

						$toc[$key]['keyref'] = $line;
					}
				}
			}
		}

		if (count($toc) < 1)
		{
			return 'Keyword not found';
		}
		else
		{
			asort($toc);

			return $toc;
		}
	}

	/**
	 * Check In
	 *
	 * @since 4.0
	 *
	 * @return void
	 *
	 * @throws \Exception
	 */
	public function checkin()
	{
		JSession::checkToken() or jexit(JText::_('JInvalid_Token'));

		$lang = Jfactory::getLanguage();

		$lang->load('com_checkin', JPATH_ADMINISTRATOR);

		$ids = $this->input->get('cid', [], 'array');

		$ids = $ids[0];

		if (empty($ids))
		{
			Throw new Exception(JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'), 500);
		}
		else
		{
			/** @var  \CWMPrayerModelReq $model */
			$model = $this->getModel('Req', 'CWMPrayerModel');
			$this->setMessage(JText::plural('COM_CHECKIN_N_ITEMS_CHECKED_IN_1', $model->checkin($ids)));
		}

		$this->setRedirect('index.php?option=com_cwmprayer&view=reqs');
	}
}
