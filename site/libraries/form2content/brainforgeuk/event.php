<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_form2content
 *
 * @author    https://www.brainforge.co.uk
 * @copyright Copyright (C) 2025 Jonathan Brain. All rights reserved.
 * @license   GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

use Joomla\CMS\Factory;

defined('JPATH_PLATFORM') or die('Restricted acccess');

/**
 */
class F2cBrainforgeukEvent extends JObject
{
	/*
	 */
	public static function getInstance()
	{
		if (version_compare(JVERSION,'4') < 1)
		{
			return JEventDispatcher::getInstance();
		}

		static $instance;
		if (!isset($instance))
		{
			$instance = new F2cBrainforgeukEvent();
		}

		return $instance;
	}

	/*
	 */
	public function trigger($name, $e = array())
	{
		return Factory::getApplication()->triggerEvent($name, $e);
	}
}
?>