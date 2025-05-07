<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_form2content
 *
 * @author      https://www.brainforge.co.uk
 * @copyright   Copyright (C) 2025 Jonathan Brain. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace com_form2content\Traits;
defined('JPATH_PLATFORM') or die('Restricted acccess');

/**
 */
trait F2cBrainforgeukSiteControllerTrait
{
	protected $errors = [];

	/*
	 */
	protected function setError($msg)
	{
		$this->errors[] = $msg;
	}

	/*
	 */
	protected function getError()
	{
		return empty($this->errors) ? '' : end($this->errors);
	}
}
