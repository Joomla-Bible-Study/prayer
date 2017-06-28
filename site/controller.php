<?php

defined('_JEXEC') or die;

/**
 * CWM Prayer Controller
 *
 * @package  CWMPrayer.Site
 *
 * @since    4.0
 */
class CWMPrayerController extends JControllerLegacy
{
	/**
	 * CWM Prayer Site Prayer
	 *
	 * @var \CWMPrayerSitePrayer
	 * @since 4.0
	 */
	public $prayer;

	public $pcConfig;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *                          Recognized key values include 'name', 'default_task', 'model_path', and
	 *                          'view_path' (this list is not meant to be comprehensive).
	 *
	 * @since   4.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->prayer = new CWMPrayerSitePrayer;
		$this->prayer->intializePCRights();

		$this->pcConfig = $this->prayer->pcConfig;

	}

	/**
	 * Confirm ??
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function confirm()
	{
		$db                         = JFactory::getDBO();
		$subject = (object) ['com_cwmprayer'];
		$plgSystemPrayerEmail = new plgSystemCWMPrayerEmail($subject);

		$id = JFilterOutput::cleanText($this->input->getInt('id', null));

		$sessionid = JFilterOutput::cleanText($this->input->getString('sessionid', null));

		$itemid = $this->prayer->PCgetItemid();

		if (is_numeric($id) && $this->prayer->PCSIDvalidate($sessionid))
		{
			$db->setQuery("SELECT request,requester,email,displaystate FROM #__cwmprayer WHERE id='" . $id .
				"' AND sessionid='" . $sessionid . "' AND state='0'");

			$cresults = $db->loadObjectList();

			if (count($cresults) > 0)
			{
				$db->setQuery("UPDATE #__cwmprayer SET state='1' WHERE id='" . $id . "' AND sessionid='" . $sessionid . "'");

				if (!$db->execute())
				{
					die("SQL error" . $db->stderr(true));
				}

				$sendpriv = $cresults[0]->displaystate;

				if ($sendpriv)
				{
					if (JPluginHelper::isEnabled('system', 'cwmprayeremail'))
					{
						$plgSystemPrayerEmail->EmailTask('PCemail_notification', array('0' => $id));
						$plgSystemPrayerEmail->EmailTask('PCemail_prayer_chain', array('0' => $id));
					}

					if ($this->pcConfig['config_distrib_type'] > 1 && $this->pcConfig['config_pms_plugin'])
					{
						$this->prayer->PCsendPM($cresults[0]->requester, $cresults[0]->request, $cresults[0]->request, $cresults[0]->email, $sendpriv);
					}
				}
				elseif (!$sendpriv)
				{
					if (JPluginHelper::isEnabled('system', 'cwmprayeremail'))
					{
						$plgSystemPrayerEmail->EmailTask('PCemail_notification', array('0' => $id));
					}

					if ($this->pcConfig['config_distrib_type'] > 1 && $this->pcConfig['config_pms_plugin'])
					{
						$this->prayer->PCsendPM($cresults[0]->requester, $cresults[0]->request, $cresults[0]->request, $cresults[0]->email, $sendpriv);
					}
				}

				$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=view&Itemid=' . $itemid . '&return_msg=' .
					htmlentities(JText::_('PCREQSUBMIT'), ENT_COMPAT, 'UTF-8')
				);

				$this->setRedirect(JRoute::_($returnurl, false));
			}
		}

		$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=view&Itemid=' . $itemid);

		$this->setRedirect(JRoute::_($returnurl, false));

	}

	/**
	 * Confirm adm ??
	 *
	 * @return  void
	 *
	 * @since 4.0
	 */
	public function confirm_adm()
	{
		$db = JFactory::getDBO();

		$prayeremail = New plgSystemCWMPrayerEmail((object) ['com_cwmprayer']);

		$id = JFilterOutput::cleanText(JInput::getInt('id'));

		$sessionid = JFilterOutput::cleanText(JInput::getString('sessionid'));

		$itemid = $this->prayer->PCgetItemid();

		if (is_numeric($id) && $this->prayer->PCSIDvalidate($sessionid))
		{
			$db->setQuery("SELECT request,requester,email,displaystate FROM #__cwmprayer WHERE id='" . $id .
				"' AND sessionid='" . $sessionid . "' AND state='0'");

			$cresults = $db->loadObjectList();

			if (count($cresults) > 0)
			{
				$db->setQuery("UPDATE #__cwmprayer SET state='1' WHERE id='" . $id . "' AND sessionid='" . $sessionid . "'");

				if (!$db->execute())
				{
					die("SQL error" . $db->stderr(true));
				}

				$sendpriv = $cresults[0]->displaystate;

				if ($sendpriv)
				{
					if (JPluginHelper::isEnabled('system', 'cwmprayeremail'))
					{
						$prayeremail->EmailTask('PCemail_notification', array('0' => $id));

						$prayeremail->EmailTask('PCemail_prayer_chain', array('0' => $id));

					}

					if ($this->pcConfig['config_distrib_type'] > 1 && $this->pcConfig['config_pms_plugin'])
					{
						$this->prayer->PCsendPM($cresults[0]->requester, $cresults[0]->request, $cresults[0]->email, $sendpriv);
					}
				}
				elseif (!$sendpriv)
				{
					if (JPluginHelper::isEnabled('system', 'prayercenteremail'))
					{
						$prayeremail->EmailTask('PCemail_notification', array('0' => $id));
					}

					if ($this->pcConfig['config_distrib_type'] > 1 && $this->pcConfig['config_pms_plugin'])
					{
						$this->prayer->PCsendPM($cresults[0]->requester, $cresults[0]->request, $cresults[0]->email, $sendpriv);
					}
				}

				$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=view&Itemid=' . $itemid . '&return_msg=' .
					htmlentities(JText::_('PCREQAPPROVE'), ENT_COMPAT, 'UTF-8')
				);

				$this->setRedirect(JRoute::_($returnurl, false));
			}
		}

		$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=view&Itemid=' . $itemid);

		$this->setRedirect(JRoute::_($returnurl, false));

	}

	/**
	 * Delreq adm ???
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function delreq_adm()
	{
		$db = JFactory::getDBO();
		$id = JFilterOutput::cleanText(JInput::getInt('id'));

		$sessionid = JFilterOutput::cleanText(JInput::getString('sessionid'));

		$itemid = $this->prayer->PCgetItemid();

		if (is_numeric($id) && $this->prayer->PCSIDvalidate($sessionid))
		{
			$db->setQuery("SELECT COUNT(id) FROM #__cwmprayer WHERE id='" . $id . "' AND sessionid='" . $sessionid . "' AND state='0'");

			$cresults = $db->loadResult();

			if ($cresults > 0)
			{
				$db->setQuery("DELETE FROM #__cwmprayer WHERE id='" . $id . "' AND sessionid='" . $sessionid . "'");

				if (!$db->execute())
				{
					JError::raiseError(500, $db->stderr());
				}
			}
		}

		$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=view&Itemid=' . $itemid);

		$this->setRedirect(JRoute::_($returnurl, false));
	}

	/**
	 * ??
	 *
	 * @return void
	 *
	 * @since version
	 */
	public function confirm_sub()
	{
		$db = JFactory::getDBO();

		$prayeremail = New plgSystemCWMPrayerEmail((object) ['com_cwmprayer']);

		$id = JFilterOutput::cleanText(JInput::getInt('id'));

		$sessionid = JFilterOutput::cleanText(JInput::getString('sessionid'));

		$itemid = $this->prayer->PCgetItemid();

		if (is_numeric($id) && $this->prayer->PCSIDvalidate($sessionid))
		{
			$db->setQuery("SELECT email FROM #__cwmprayer_subscribe WHERE id='" . $id . "' AND sessionid='" . $sessionid . "' AND approved='0'");

			$subresults = $db->loadObjectList();

			if (count($subresults) > 0)
			{
				$db->setQuery("UPDATE #__cwmprayer_subscribe SET approved='1' WHERE id='" . $id . "' AND sessionid='" . $sessionid . "'");

				if (!$db->execute())
				{
					die("SQL error" . $db->stderr(true));

				}

				if (JPluginHelper::isEnabled('system', 'cwmprayeremail'))
				{
					$prayeremail->EmailTask('PCemail_subscribe', array('0' => $subresults[0]->email));

					if (($this->pcConfig['config_email_subscribe']) && ($this->pcConfig['config_admin_approve_subscribe'] == 2))
					{
						$prayeremail->EmailTask('PCadmin_email_subscribe_notification', array('0' => $subresults[0]->email));

					}
				}

				$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=subscribe&Itemid=' . $itemid . '&return_msg=' .
					htmlentities(JText::_('PCENTRYACCEPTED'), ENT_COMPAT, 'UTF-8')
				);

				$this->setRedirect(JRoute::_($returnurl, false));
			}
		}

		$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=subscribe&Itemid=' . $itemid);

		$this->setRedirect(JRoute::_($returnurl, false));
	}

	/**
	 *??
	 *
	 * @return void
	 *
	 * @since version
	 */
	public function confirm_unsub()
	{
		$db = JFactory::getDBO();

		$prayeremail = New plgSystemCWMPrayerEmail((object) ['com_cwmprayer']);

		$id = JFilterOutput::cleanText(JInput::getInt('id'));

		$sessionid = JFilterOutput::cleanText(JInput::getString('sessionid'));

		$itemid = $this->prayer->PCgetItemid();

		if (is_numeric($id) && $this->prayer->PCSIDvalidate($sessionid))
		{
			$db->setQuery("SELECT email FROM #__cwmprayer_subscribe WHERE id='" . $id . "' AND sessionid='" . $sessionid . "' AND approved='1'");

			$unsubresults = $db->loadObjectList();

			if (count($unsubresults) > 0)
			{
				$db->setQuery("DELETE FROM #__cwmprayer_subscribe WHERE id='" . $id . "' AND sessionid='" . $sessionid . "'");

				if (!$db->execute())
				{
					die("SQL error" . $db->stderr(true));

				}

				if (JPluginHelper::isEnabled('system', 'cwmprayeremail'))
				{
					$prayeremail->EmailTask('PCemail_unsubscribe', array('0' => $unsubresults[0]->email));
				}

				$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=subscribe&Itemid=' . $itemid . '&return_msg=' .
					htmlentities(JText::_('PCENTRYREMOVED'), ENT_COMPAT, 'UTF-8')
				);

				$this->setRedirect(JRoute::_($returnurl, false));
			}
		}

		$returnurl = JRoute::_('index.php?option=com_cwmprayer&task=subscribe&Itemid=' . $itemid);

		$this->setRedirect(JRoute::_($returnurl, false));
	}

	/**
	 * ??
	 *
	 * @param   bool  $cachable  ?
	 *
	 * @return JControllerLegacy
	 *
	 * @since 4.0
	 */
	public function newreq($cachable = false)
	{
		$input = JFactory::getApplication()->input;
		$input->set('view', $input->getCmd('view', 'newreq'));

		return parent::display($cachable);
	}

	/**
	 * Subscribe
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function subscribe()
	{
		JFactory::getApplication()->input->set('view', 'subscribe');

		parent::display();
	}

	/**
	 * Unsubscribe
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function unsubscribe()
	{
		JFactory::getApplication()->input->set('view', 'subscribe');

		parent::display();
	}

	/**
	 * View
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function view()
	{
		$view = $this->getView('list', 'html');

		$comp = JComponentHelper::getParams('com_cwmprayer');
		$this->pcConfig = $comp->toArray()['params'];

		if ($this->pcConfig['config_view_template'] == 1)
		{
			$view->setLayout('rounded');
		}
		elseif ($this->pcConfig['config_view_template'] == 2)
		{
			$view->setLayout('basic');
		}

		$view->display();
	}

	/**
	 * ??
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function pdf()
	{
		if ($this->prayer->pc_rights->get('pc.view') && $this->pcConfig['config_show_pdf'])
		{
			$lang = Jfactory::getLanguage();

			$lang->load('com_cwmprayer', JPATH_SITE);

			$headerarr = array(utf8_encode(JText::_('PCMODREQ')), utf8_encode(JText::_('PCMODREQR')));

			$listtype = $this->input->getInt('listtype', null);

			require_once 'components/com_cwmprayer/helpers/pc_pdf_class.php';

			$pdf = new PDF;

			$pdf->listtype = $listtype;

			$pdf->AddPage();

			$pdf->Ln(7);

			$pdf->SetFont('helvetica', '', 10);

			if ($listtype == 0)
			{
				$id = $this->input->getInt('id', null);

				$pdf->Table($headerarr, "SELECT * FROM #__cwmprayer WHERE id='$id' AND state='1' AND displaystate='1'");

			}
			elseif ($listtype == 1)
			{
				$pdf->Table(
					$headerarr, "SELECT * FROM #__cwmprayer WHERE state='1' AND displaystate='1' AND date=CURDATE()" .
					" ORDER BY topic,DATE_FORMAT(CONCAT_WS(' ',date,time),'%Y-%m-%d %T') DESC");

			}
			elseif ($listtype == 2)
			{
				$pdf->Table(
					$headerarr, "SELECT topic,request,requester FROM #__cwmprayer WHERE state='1' AND displaystate='1'" .
					" AND WEEKOFYEAR(date)=WEEKOFYEAR(CURDATE()) AND YEAR(date)=YEAR(CURDATE()) ORDER BY topic," .
						"DATE_FORMAT(CONCAT_WS(' ',date,time),'%Y-%m-%d %T') DESC");

			}

			$pdf->Output();

			exit(0);
		}
		else
		{
			echo '<div class="componentheading">' . utf8_encode(JText::_('CWMPRAYERTITLE')) . '</div>';
			echo '<h5><center>' . JText::_('JERROR_ALERTNOAUTHOR') . '<br />' . utf8_encode(JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST')) . '</center></h5>';
			echo '<br /><br /><br /><br />';
		}
	}

	/**
	 * View Links
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function view_links()
	{
		JFactory::getApplication()->input->set('view', 'links');

		parent::display();

	}

	/**
	 * View Devotion
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function view_devotion()
	{
		JFactory::getApplication()->input->set('view', 'devotions');

		parent::display();
	}

	/**
	 * Moderate Prayer Request
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function moderate()
	{
		if ($this->pcConfig['config_use_admin_alert'] > 1 && $this->prayer->pc_rights->get('pc.moderate'))
		{
			JFactory::getApplication()->input->set('view', 'moderate');

			parent::display();
		}
		else
		{
			echo '<div class="componentheading">' . htmlentities(JText::_('CWMPRAYERTITLE')) . '</div>';

			echo '<h5 style="text-align: center;">' . JText::_('JERROR_ALERTNOAUTHOR') . '<br />' . JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST') . '</h5>';

			echo '<br /><br /><br /><br />';

		}
	}

	/**
	 * Edit Prayer Request
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function edit()
	{
		$eid = JFactory::getApplication()->input->getInt('id', null);

		$eid = JFilterOutput::cleanText($eid);

		JFactory::getApplication()->input->set('view', 'edit');

		parent::display();
	}

	/**
	 * View Request
	 *
	 * @return void
	 *
	 * @since version
	 */
	public function view_request()
	{
		$eid = JFactory::getApplication()->input->getInt('id', null);

		JFactory::getApplication()->input->set('view', 'showreq');

		parent::display();

	}

	/**
	 * RSS Prepare
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function rss()
	{
		$app = JFactory::getApplication();

		if ($this->prayer->pc_rights->get('pc.view') && $this->pcConfig['config_show_pdf'])
		{
			$lang = Jfactory::getLanguage();

			$lang->load('com_cwmprayer', JPATH_SITE);

			$livesite = JURI::base();

			$sitename = $app->get('sitename');

			$itemid = $this->prayer->PCgetItemid();

			$config_rss_num = $this->pcConfig['config_rss_num'];

			$db = JFactory::getDBO();

			while (@ob_end_clean())
			{
				// ?
			};

			require_once 'media/com_cwmprayer/rss/feedcreator.php';

			$feed_type = 'RSS2.0';

			$filename = 'pc_feed.xml';

			$cacheDir = JPATH_BASE . '/cache';

			$cachefile = $cacheDir . '/' . $filename;

			$rss = new UniversalFeedCreator;

			$image = new FeedImage;

			if ($this->pcConfig['config_enable_rss_cache'])
			{
				$rss->useCached($feed_type, $cachefile, $this->pcConfig['config_rss_update_time']);
			}

			$rss->title = $sitename . ' - ' . utf8_encode(JText::_('CWMPRAYERTITLE'));

			$rss->description = utf8_encode(JText::_('PCRSSFEEDMSG')) . ' ' . $sitename;

			$rss->link = htmlspecialchars($livesite) . 'index.php?option=com_cwmprayer&amp;Itemid=' . $itemid;

			$rss->syndicationURL = htmlspecialchars($livesite) . 'index.php?option=com_cwmprayer&amp;Itemid=' . $itemid;

			$rss->cssStyleSheet = null;

			$feed_image = $livesite . 'media/com_cwmprayer/images/prayer.png';

			if ($feed_image)
			{
				$image->url = $feed_image;

				$image->link = $rss->link;

				$image->title = 'Powered by Joomla! & prayer';

				$image->description = $rss->description;

				$rss->image = $image;
			}

			$db->setQuery("SELECT * FROM #__cwmprayer "
				. "\n WHERE state = 1 "
				. "\n AND displaystate = 1 "
				. "\n ORDER BY id DESC "
				. "\n LIMIT " . $config_rss_num
			);

			$rows = $db->loadObjectList();

			foreach ($rows as $row)
			{
				$item = new FeedItem;

				$item->title = utf8_encode(html_entity_decode($row->requester));

				$item->link = JRoute::_("index.php?option=com_cwmprayer&amp;Itemid=" . $itemid . "&amp;task=view_request&amp;type=rss&amp;id=" . $row->id);

				$words = $row->request;

				if ($this->pcConfig['config_rss_limit_text'])
				{
					$words = substr($words, 0, $this->pcConfig['config_rss_text_length']);
				}

				$item->description = $words;

				$seconds = date_offset_get(new DateTime);

				$offset = $seconds / 3600;

				$itemdate = date("r", strtotime($row->date . ' ' . $row->time . ' ' . $offset));

				$item->date = $itemdate;

				$rss->addItem($item);
			}

			$rss->saveFeed($feed_type, $cachefile);
		}
		else
		{
			$this->setRedirect('index.php?option=com_cwmprayer', JText::_('JERROR_ALERTNOAUTHOR'));
		}
	}

	/**
	 * Cap Valid
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function PCCapValid()
	{
		$session = JFactory::getSession();

		$plugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));

		$captcha = JCaptcha::getInstance($plugin, array('namespace' => 'adminForm'));

		$captcha_code = "";

		$resp = $captcha->checkAnswer($captcha_code);

		if ($resp == false)
		{
			$message = htmlentities(JText::_('PCINVALIDCODE'));

			echo $message;
		}
		else
		{
			$session->set('pc_respchk', true);

			echo true;
		}
	}
}
