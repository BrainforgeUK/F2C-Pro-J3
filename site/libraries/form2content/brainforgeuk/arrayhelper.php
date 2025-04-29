<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_form2content
 *
 * @author    https://www.brainforge.co.uk
 * @copyright Copyright (C) 2025 Jonathan Brain. All rights reserved.
 * @license   GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die('Restricted acccess');

/**
 */
class F2cBrainforgeukArrayhelper
{
	/*
	 */
	public static function toInteger($array, $default = null)
	{
		ArrayHelper::toInteger($array, $default);
	}

	/*
	 */
	public static function getValue($array, $name, $default = null, string $type = '')
	{
		if (empty($array[$name])) return $default;

		return $array[$name];
	}
}
?>