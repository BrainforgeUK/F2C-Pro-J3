<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

use Joomla\String\StringHelper;

class F2cFieldGeocoder extends F2cFieldBase
{	
	function __construct($field)
	{
		$this->reset();
		parent::__construct($field);
	}
	
	public function getPrefix()
	{
		return 'gcd';
	}
	
	public function reset()
	{
		$this->values['ADDRESS']		= '';
		$this->values['LAT']			= '';
		$this->values['LON']			= '';						
		$this->internal['addressid']	= null;
		$this->internal['latid']		= null;
		$this->internal['lonid']		= null;
	}
	
	// Modified Brainforge.uk 20250509
	public function render($translatedFields, $contentTypeSettings, $parms, $form, $formId)
	{
		$displayData						= array();
		$displayData['latLonDisplay'] 		= ($this->values['LAT'] && $this->values['LON']) ? '('.$this->values['LAT'].', '.$this->values['LON'].')' : '';
		$displayData['latOnMap'] 			= $displayData['latLonDisplay'] ? $this->values['LAT'] : $this->settings->get('gcd_map_lat', '55.166085');
		$displayData['lonOnMap'] 			= $displayData['latLonDisplay'] ? $this->values['LON'] : $this->settings->get('gcd_map_lon', '10.712890');
		$displayData['attributesLookup']	= $this->settings->get('gcd_attributes_lookup_lat_lon', 'class="btn"');
		$displayData['attributesClear']		= $this->settings->get('gcd_attributes_clear_results', 'class="btn"');
		$displayData['attributesAddress']	= $this->settings->get('gcd_attributes_address', 'class="inputbox"');		
		
		return $this->renderLayout('geocoder', $displayData, $translatedFields, $contentTypeSettings);
	}
	
	public function prepareSubmittedData($formId)
	{
		$jinput = JFactory::getApplication()->input;
		
		$this->internal['addressid'] 	= $jinput->getInt('hid'.$this->elementId.'_address');
		$this->internal['latid'] 		= $jinput->getInt('hid'.$this->elementId.'_lat');
		$this->internal['lonid'] 		= $jinput->getInt('hid'.$this->elementId.'_lon');
		$this->values['ADDRESS']		= $jinput->getString($this->elementId.'_address');
		$this->values['LAT']			= $jinput->getString($this->elementId.'_hid_lat');
		$this->values['LON']			= $jinput->getString($this->elementId.'_hid_lon');
		
		return $this;
	}
	
	public function store($formid)
	{
		$addressId		= $this->internal['addressid'];
		$addressValue 	= $this->values['ADDRESS'];		
		$latId			= $this->internal['latid'];
		$latValue		= $this->values['LAT'];
		$lonId			= $this->internal['lonid'];
		$lonValue 		= $this->values['LON'];		
				
		if($addressId)
		{
			// existing record
			$action = (!$addressValue && !$latValue && !$lonValue) ? 'DELETE' : 'UPDATE';
		}
		else
		{
			// new record
			$action = ($addressValue || $latValue || $lonValue) ? 'INSERT' : '';
		}
		
		$content 	= array();					
		$content[] 	= new F2cFieldHelperContent($addressId, 'ADDRESS', $addressValue, $action);
		$content[] 	= new F2cFieldHelperContent($latId, 'LAT', $latValue, $action);
		$content[] 	= new F2cFieldHelperContent($lonId, 'LON', $lonValue, $action);
		
		return $content;					
	}
	
	public function validate(&$data, $item)
	{
		if($this->settings->get('requiredfield'))
		{
			if(!(trim($this->values['ADDRESS']) && $this->values['LAT'] && $this->values['LON']))		
			{
				throw new Exception($this->getRequiredFieldErrorMessage());
			}
		}
	}
	
	public function getClientSideInitializationScript()
	{
		static $initialized = false;
		
		$script = '';
		
		if(!$initialized)
		{
			$script .= 'var geocoderInit = new Array();';
			
			$key = $this->settings->get('api_key');
			
			if($key)
			{
				$key = '&key=' . $key;
			}
			
			$script .= parent::getClientSideInitializationScript();
			// Add &libraries=places for autocomplete
			JHtml::script('//maps.googleapis.com/maps/api/js?callback=Form2Content.Fields.Geocoder.Init'.$key, array(), array('async' => 'async', 'defer' => 'defer'));
			$initialized = true;
			
			$script .= "var geocoder;\n";
		}
		
		$script .= "var t".$this->id."_map=null;\n";	
		$script .= "var t".$this->id."_marker=null;\n";
		
		$lat 		= $this->values['LAT'] ? $this->values['LAT'] : 0;
		$lon 		= $this->values['LON'] ? $this->values['LON'] : 0;
		$fieldId 	= 't'.$this->id;
		$showMap 	= $this->settings->get('gcd_show_map');
		$showMarker = (empty($this->values['LAT']) || empty($this->values['LON'])) ? 'false' : 'true';
		$zoom 		= $this->settings->get('gcd_map_zoom');
		$mapTypeId 	= strtolower($this->settings->get('gcd_map_type'));
		
		$script .= 'geocoderInit.push({id:"'.$fieldId.'", lat:'.$lat.', lon:'.$lon.', showMap: '.$showMap.', showMarker:'.$showMarker.', zoom:'.$zoom.', mapTypeId:"'.$mapTypeId.'"});';
		
		return $script;
	}
	
	public function copy($formId)
	{
		$this->internal['addressid'] = null;
		$this->internal['latid'] = null;
		$this->internal['lonid'] = null;
	}
	
	public function export($xmlFields, $form)
	{
      	$xmlField = $xmlFields->addChild('field');
      	$xmlField->fieldname = $this->fieldname;
      	$xmlFieldContent = $xmlField->addChild('contentGeocoder');
      	$xmlFieldContent->address = $this->values['ADDRESS'];
      	$xmlFieldContent->lat = $this->values['LAT'];
      	$xmlFieldContent->lon= $this->values['LON'];
    }
    
	public function import($xmlField, $existingInternalData, &$data)
	{
		$this->values['ADDRESS'] = (string)$xmlField->contentGeocoder->address;
		$this->values['LAT'] = (string)$xmlField->contentGeocoder->lat;
		$this->values['LON'] = (string)$xmlField->contentGeocoder->lon;
		$this->internal['addressid'] = $data['id'] ? $existingInternalData['addressid'] : 0;
		$this->internal['latid'] = $data['id'] ? $existingInternalData['latid'] : 0;
		$this->internal['lonid'] = $data['id'] ? $existingInternalData['lonid'] : 0;
	}
	
	public function addTemplateVar($templateEngine, $form)
	{
		if($this->values)
		{
			$templateEngine->addVar($this->fieldname.'_ADDRESS', $this->stringHTMLSafe($this->values['ADDRESS']));
			$templateEngine->addVar($this->fieldname.'_LAT', $this->values['LAT']);
			$templateEngine->addVar($this->fieldname.'_LON', $this->values['LON']);
		}
		else
		{
			$templateEngine->addVar($this->fieldname.'_ADDRESS', '');
			$templateEngine->addVar($this->fieldname.'_LAT', '');
			$templateEngine->addVar($this->fieldname.'_LON', '');
		}
	}
	
	public function getTemplateParameterNames()
	{
		$names = array(	StringHelper::strtoupper($this->fieldname).'_ADDRESS',
						StringHelper::strtoupper($this->fieldname).'_LAT', 
						StringHelper::strtoupper($this->fieldname).'_LON');
		
		return $names;
	}
	
	public function setData($data)
	{
		$this->values[$data->attribute] = $data->content;
		
		switch($data->attribute)
		{
			case 'ADDRESS':
				$this->internal['addressid'] = $data->fieldcontentid;
				break;
			case 'LAT':
				$this->internal['latid'] = $data->fieldcontentid;
				break;
			case 'LON':
				$this->internal['lonid'] = $data->fieldcontentid;
				break;
		}						
	}
}
?>