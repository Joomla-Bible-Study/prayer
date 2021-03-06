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

require 'media/com_cwmprayer/fpdf/fpdf.php';

/**
 * PDF MySQL Table Class
 *
 * @package  Prayer.Site
 *
 * @since    4.0
 */
class PDF_MySQL_Table extends FPDF
{
	public $TempTopic = -1;

	public $TempHeader = 1;

	/**
	 * Table
	 *
	 * @param   string  $header  ?
	 * @param   string  $query   Query String for MySQL
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function Table($header, $query)
	{
		$prayer = new CWMPrayerSitePrayer;
		$prayer->intializePCRights();
		$topicarray = $prayer->PCgetTopics();

		$db = JFactory::getDBO();

		$db->setQuery($query);

		$res = $db->loadObjectList();

		if (count($res) < 1)
		{
			$this->Ln(2);

			$this->Cell(0, 5, JText::_('No requests found'), 0, 0, 'C');

			return;
		}

		$f = 0;

		foreach ($res as $row)
		{
			$fill = ($f % 2) ? true : false;

			if ($this->TempTopic != $row->topic)
			{
				$this->Ln(3);

				$this->SetFont('helvetica', '', 10);

				$topic = $topicarray[$row->topic + 1]['text'];

				$this->Cell(0, 5, $topic, 0, 0, 'L');

				$this->Ln(5);

				$this->TempTopic = $row->topic;

				$this->TempHeader = 0;

				$w = array(135, 55);

				$this->SetFillColor(255, 0, 0);

				$this->SetTextColor(255);

				$this->SetDrawColor(128, 0, 0);

				$this->SetLineWidth(.3);

				$this->SetFont('helvetica', '', 9);

				for ($i = 0; $i < count($header); $i++)
				{
					$this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
				}

				$this->Ln();
			}

			$this->SetFillColor(224, 235, 255);

			$this->SetTextColor(0);

			$this->SetFont('');

			$request = preg_replace('/(\r|\n|\r\n){2,}/', ' ', $row->request);

			$nb1 = $this->NbLines($w[0], $request) * 6;

			$this->MultiCell($w[0], 6, str_replace('&nbsp;', ' ', $request), 'TLRB', 'L', $fill);

			$tempx = $this->GetX();

			$tempy = $this->GetY();

			$this->SetXY($tempx + $w[0], $tempy - $nb1);

			$this->MultiCell($w[1], $nb1, utf8_encode($row->requester), 'TLRB', 'C', $fill);

			$f++;
		}

		$this->TempHeader = 1;
	}

	/**
	 * NB Lines
	 *
	 * @param   string  $w    ?
	 * @param   string  $txt  ?
	 *
	 * @return int
	 *
	 * @since 4.0
	 */
	public function NbLines($w, $txt)
	{
		$cw = $this->CurrentFont['cw'];

		if ($w == 0)
		{
			$w = $this->w - $this->rMargin - $this->x;
		}

		$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;

		$s = str_replace("\r", '', $txt);

		$nb = strlen($s);

		if ($nb > 0 && $s[$nb - 1] == "\n")
		{
			$nb--;
		}

		$sep = -1;
		$i = 0;
		$j = 0;
		$l = 0;
		$nl = 1;

		while ($i < $nb)
		{
			$c = $s[$i];

			if ($c == "\n")
			{
				$i++;
				$sep = -1;
				$j = $i;
				$l = 0;
				$nl++;
				continue;
			}

			if ($c == ' ')
			{
				$sep = $i;
			}

			$l += $cw[$c];

			if ($l > $wmax)
			{
				if ($sep == -1)
				{
					if ($i == $j)
					{
						$i++;
					}
				}
				else
				{
					$i = $sep + 1;
					$sep = -1;
					$j = $i;
					$l = 0;
					$nl++;
				}
			}
			else
			{
				$i++;
			}
		}

		return $nl;
	}
}

/**
 * PDF Class
 *
 * @package  Prayer.Site
 *
 * @since    4.0
 */
class PDF extends PDF_MySQL_Table
{
	/**
	 * Header Create
	 *
	 * @return void
	 *
	 * @since version
	 */
	public function Header()
	{
		jimport('joomla.utilities.date');
		jimport('joomla.filesystem.file');
		$app = JFactory::getApplication();
		$prayer = new CWMPrayerSitePrayer;
		$prayer->intializePCRights();

		$offset = date('T');
		$dateset = new JDate('now', $offset);
		$date = $dateset->format('F d, Y', true);
		$sitename = $app->get('sitename');
		$img = 'media/com_cwmprayer/images/' . $prayer->pcConfig['config_imagefile'];
		$ext = substr($prayer->pcConfig['config_imagefile'], strrpos($prayer->pcConfig['config_imagefile'], '.') + 1);

		if ($ext == 'png' && !JFile::exists('media/com_cwmprayer/fpdf/images/' . basename($prayer->pcConfig['config_imagefile'], '.png') . '.jpg'))
		{
			imagejpeg(imagecreatefrompng($img), 'media/com_cwmprayer/fpdf/images/' . basename($prayer->pcConfig['config_imagefile'], '.png') . '.jpg');
			$img = 'media/com_cwmprayer/fpdf/images/' . basename($prayer->pcConfig['config_imagefile'], '.png') . '.jpg';
		}
		elseif ($ext == 'png' && JFile::exists('media/com_cwmprayer/fpdf/images/' . basename($prayer->pcConfig['config_imagefile'], '.png') . '.jpg'))
		{
			$img = 'media/com_cwmprayer/fpdf/images/' . basename($prayer->pcConfig['config_imagefile'], '.png') . '.jpg';
		}

		$this->Image($img, 160, 8, 20);
		$this->SetFont('helvetica', 'B', 12);
		$this->Cell(0, 5, $sitename . ' - ' . JText::_('CWMPRAYERTITLE') . ' ' . JText::_('CWMPRAYERCWMPRAYERREQUESTS'), 0, 0, '');
		$this->Ln();
		$this->SetFont('helvetica', '', 7);
		$listtype = $this->listtype;

		if ($listtype == 1)
		{
			$this->Cell(0, 5, JText::_('CWMPRAYERPDFDAILY') . ' ' . $date, 0, 0, '');
		}
		elseif ($listtype == 2)
		{
			$this->Cell(0, 5, JText::_('CWMPRAYERPDFWEEKLY') . ' ' . date('F d', strtotime("-7 day")) . ' - ' . date('F d, Y'), 0, 0, '');
		}

		$this->Ln(10);
	}

	/**
	 * Footer Create
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function Footer()
	{
		jimport('joomla.date.date');
		$dateset = new JDate;
		$date = $dateset->format('F d, Y h:i:s A', false, true);
		$this->SetY(-15);
		$this->SetFont('helvetica', '', 6);
		$this->Cell(0, 5, JText::_('CWMPRAYERPDFGEN') . ' ' . $date, 0, 0, 'C');
		$this->Ln(5);
		$this->SetFont('helvetica', 'I', 6);
		$this->Cell(0, 10, JText::_('CWMPRAYERPDFPAGE') . ' ' . $this->PageNo(), 0, 0, 'C');
	}
}
