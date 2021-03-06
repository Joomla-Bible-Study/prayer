<?php

/**
 * JComments plugin for CWMPrayer
 *
 * @package    JComments
 * @author     Christian Web Ministries <info@christianwebministries.org>
 * @copyright  (C) 2006-2009 by Sergey M. Litvinov (http://www.joomlatune.ru)
 * @license    GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 * @since      4.0
 **/
class jc_com_cwmprayercenter extends JCommentsPlugin
{
	public function getTitles($ids)
	{
		$db = JCommentsFactory::getDBO();

		$db->setQuery('SELECT id, title FROM #__cwmprayer WHERE id IN (' . implode(',', $ids) . ')');

		return $db->loadObjectList('id');
	}

	public function getObjectTitle($id)
	{
		$db = JCommentsFactory::getDBO();

		$db->setQuery("SELECT title, id FROM #__cwmprayer WHERE id = $id");

		return $db->loadResult();
	}

	public function getObjectLink($id)
	{
		$_Itemid = JCommentsPlugin::getItemid('com_cwmprayer');

		$link = JoomlaTuneRoute::_("index.php?option=com_cwmprayer&amp;task=req&amp;id=" . $id . "&amp;Itemid=" . $_Itemid);

		return $link;
	}

	public function getObjectOwner($id)
	{
		$db = JCommentsFactory::getDBO();

		$db->setQuery('SELECT requester FROM #__cwmprayer WHERE id = ' . $id);

		$userid = $db->loadResult();

		return intval($userid);
	}
}