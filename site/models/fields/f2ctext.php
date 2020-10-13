<?php
defined('JPATH_BASE') or die;

class JFormFieldF2cText extends JFormField
{
	protected $type = 'F2cText';

	protected function getInput()
	{
		$attributes = $this->element['attributes'];
		
		return '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $attributes . '/>';
	}
}
