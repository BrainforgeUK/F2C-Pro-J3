<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldF2cCalendar extends JFormField
{
	public $type = 'F2cCalendar';

	/**
	 * The allowable maxlength of calendar field.
	 *
	 * @var    integer
	 * @since  3.2
	 */
	protected $maxlength;

	/**
	 * The format of date and time.
	 *
	 * @var    integer
	 * @since  3.2
	 */
	protected $format;

	/**
	 * The filter.
	 *
	 * @var    integer
	 * @since  3.2
	 */
	protected $filter;

	/**
	 * The minimum year number to subtract/add from the current year
	 *
	 * @var    integer
	 * @since  3.7.0
	 */
	protected $minyear;

	/**
	 * The maximum year number to subtract/add from the current year
	 *
	 * @var    integer
	 * @since  3.7.0
	 */
	protected $maxyear;

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 * @since  3.7.0
	 */
	protected $layout = 'joomla.form.field.calendar';

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   3.2
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'maxlength':
			case 'format':
			case 'filter':
			case 'timeformat':
			case 'todaybutton':
			case 'singleheader':
			case 'weeknumbers':
			case 'showtime':
			case 'filltable':
			case 'minyear':
			case 'maxyear':
				return $this->$name;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'maxlength':
			case 'timeformat':
				$this->$name = (int) $value;
				break;
			case 'todaybutton':
			case 'singleheader':
			case 'weeknumbers':
			case 'showtime':
			case 'filltable':
			case 'format':
			case 'filter':
			case 'minyear':
			case 'maxyear':
				$this->$name = (string) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 * @since   3.2
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return)
		{
			$this->maxlength    = (int) $this->element['maxlength'] ? (int) $this->element['maxlength'] : 45;
			$this->format       = (string) $this->element['format'] ? (string) $this->element['format'] : '%Y-%m-%d';
			$this->filter       = (string) $this->element['filter'] ? (string) $this->element['filter'] : 'USER_UTC';
			$this->todaybutton  = (string) $this->element['todaybutton'] ? (string) $this->element['todaybutton'] : 'true';
			$this->weeknumbers  = (string) $this->element['weeknumbers'] ? (string) $this->element['weeknumbers'] : 'false';
			$this->showtime     = (string) $this->element['showtime'] ? (string) $this->element['showtime'] : 'true';
			$this->filltable    = (string) $this->element['filltable'] ? (string) $this->element['filltable'] : 'true';
			$this->timeformat   = (int) $this->element['timeformat'] ? (int) $this->element['timeformat'] : 24;
			$this->singleheader = (string) $this->element['singleheader'] ? (string) $this->element['singleheader'] : 'false';
			$this->minyear      = (string) $this->element['minyear'] ? (string) $this->element['minyear'] : null;
			$this->maxyear      = (string) $this->element['maxyear'] ? (string) $this->element['maxyear'] : null;
		}

		return $return;
	}
	
	public function getInput()
	{
		// Initialize some field attributes.
		$format = $this->element['format'] ? (string) $this->element['format'] : '%Y-%m-%d';
		
		/*
		if (strtolower($this->element['classiclayout']) == 'false' || empty($this->element['classiclayout']))
		{
			$classicLayout = false;
		}
		else 
		{
			$classicLayout = true;
		}
		
		// Build the attributes array.
		$attributes = array();
		if ($this->element['size']) {
			$attributes['size'] = (int) $this->element['size'];
		}
		if ($this->element['maxlength']) {
			$attributes['maxlength'] = (int) $this->element['maxlength'];
		}
		if ($this->element['class']) {
			$attributes['class'] = (string) $this->element['class'];
		}
		if ((string) $this->element['readonly'] == 'true') {
			$attributes['readonly'] = 'readonly';
		}
		if ((string) $this->element['disabled'] == 'true') {
			$attributes['disabled'] = 'disabled';
		}
		if ($this->element['onchange']) {
			$attributes['onchange'] = (string) $this->element['onchange'];
		}
		*/
		
		// Handle the special case for "now".
		if (strtoupper($this->value) == 'NOW') {
			$this->value = strftime($format);
		}

		// Get some system objects.
		$config 	= JFactory::getConfig();
		$user		= JFactory::getUser();
		$date		= JFactory::getDate($this->value);
		$nullDate 	= JFactory::getDbo()->getNullDate();
		$valueRaw	= $this->value;
		
		if($this->value != $nullDate)
		{
			// If a known filter is given use it.
			switch (strtoupper((string) $this->element['filter']))
			{
				case 'SERVER_UTC':
					// Convert a date to UTC based on the server timezone.
					if (intval($this->value)) {
						// Get a date object based on the correct timezone.
						$date = JFactory::getDate($this->value, 'UTC');
						$date->setTimezone(new DateTimeZone($config->get('offset')));
					}
					break;
	
				case 'USER_UTC':
				case 'FORM2CONTENTHELPER::FILTERUSERUTCWITHFORMAT':
					// Convert a date to UTC based on the user timezone.			
					if (intval($this->value)) 
					{
						// Get a date object based on the correct timezone.
						$date = JFactory::getDate($this->value, 'UTC');
						$date->setTimezone(new DateTimeZone($user->getParam('timezone', $config->get('offset'))));
						
						$dateFormat = str_replace('%','', $format) . ' H:i:s';
						$this->value = $date->format($dateFormat, true);			
					}
					break;
			}
			
			// Apply the correct formatting to the value
//			$dateFormat = str_replace('%','', $format) . ' H:i:s';
//			$this->value = $date->format($dateFormat, true);
		}
		else 
		{
			// nulldate
			$this->value = '';
		}
				
		$showTime = (string)$this->element['showtime'];
		
		if($showTime && $showTime != 'false')
		{
			$this->format .= ' %H:%M:%S';
		}
		
		return $this->getRenderer($this->layout)->render($this->getLayoutData());
	}
	
	/**
	 * Method to get the data to be passed to the layout for rendering.
	 *
	 * @return  array
	 *
	 * @since  3.7.0
	 */
	protected function getLayoutData()
	{
		$data      = parent::getLayoutData();
		$tag       = JFactory::getLanguage()->getTag();
		$calendar  = JFactory::getLanguage()->getCalendar();
		$direction = strtolower(JFactory::getDocument()->getDirection());

		// Get the appropriate file for the current language date helper
		$helperPath = 'system/fields/calendar-locales/date/gregorian/date-helper.min.js';

		if (!empty($calendar) && is_dir(JPATH_ROOT . '/media/system/js/fields/calendar-locales/date/' . strtolower($calendar)))
		{
			$helperPath = 'system/fields/calendar-locales/date/' . strtolower($calendar) . '/date-helper.min.js';
		}

		// Get the appropriate locale file for the current language
		$localesPath = 'system/fields/calendar-locales/en.js';

		if (is_file(JPATH_ROOT . '/media/system/js/fields/calendar-locales/' . strtolower($tag) . '.js'))
		{
			$localesPath = 'system/fields/calendar-locales/' . strtolower($tag) . '.js';
		}
		elseif (is_file(JPATH_ROOT . '/media/system/js/fields/calendar-locales/' . strtolower(substr($tag, 0, -3)) . '.js'))
		{
			$localesPath = 'system/fields/calendar-locales/' . strtolower(substr($tag, 0, -3)) . '.js';
		}

		$extraData = array(
			'value'        => $this->value,
			'maxLength'    => $this->maxlength,
			'format'       => $this->format,
			'filter'       => $this->filter,
			'todaybutton'  => ($this->todaybutton === 'true') ? 1 : 0,
			'weeknumbers'  => ($this->weeknumbers === 'true') ? 1 : 0,
			'showtime'     => ($this->showtime === 'true') ? 1 : 0,
			'filltable'    => ($this->filltable === 'true') ? 1 : 0,
			'timeformat'   => $this->timeformat,
			'singleheader' => ($this->singleheader === 'true') ? 1 : 0,
			'helperPath'   => $helperPath,
			'localesPath'  => $localesPath,
			'minYear'      => $this->minyear,
			'maxYear'      => $this->maxyear,
			'direction'    => $direction,
		);

		return array_merge($data, $extraData);
	}
}