 <?php

/* *************************************************************************************

Title          prayer Component for Joomla

Author         Mike Leeper

License        This program is free software: you can redistribute it and/or modify

               it under the terms of the GNU General Public License as published by

               the Free Software Foundation, either version 3 of the License, or

               (at your option) any later version.

               This program is distributed in the hope that it will be useful,

               but WITHOUT ANY WARRANTY; without even the implied warranty of

               MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

               GNU General Public License for more details.

               You should have received a copy of the GNU General Public License

               along with this program.  If not, see <http://www.gnu.org/licenses/>.

Copyright      2006-2014 - Mike Leeper (MLWebTechnologies) 

****************************************************************************************

No direct access*/

defined('_JEXEC') or die('Restricted access');

$input = JFactory::getApplication()->input;

$pop = $input->getInt('pop', null);

if (!$pop)
{
	if ($input->getString('return_msg', null))
	{
		$this->prayer->PCReturnMsg($input->getString('return_msg', null));
	}

	echo '<div>';

	if ($this->config_show_page_headers)
	{
		echo '<div class="componentheading"><h2>' . htmlentities($this->title) . '</h2></div>';
	}

	echo '<div>';

	$this->prayer->buildPCMenu();

	echo '</div><div>';

	echo $this->prayer->writePCImage() . '</div>';

	echo $this->prayer->PCkeephtml(htmlentities($this->intro)) . '<br />';

	echo '<span class="article_separator"> </span>';

	echo '<br /></div></div>';

	$this->prayer->PrayerFooter();
}
