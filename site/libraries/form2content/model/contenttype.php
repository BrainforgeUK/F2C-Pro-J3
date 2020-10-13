<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cModelContenttype extends JObject
{
	var $id;
	var $asset_id;
	var $title;
	var $created_by;
	var $created;
	var $modified;
	var $version;
	var $published;
	var $settings;
	var $attribs;
	var $metadata;
	var $metakey;
	var $metadesc;
	var $images;
	var $urls;
	var $fields;
	
	function __construct(JObject $object)
	{
		$this->id = $object->id;
		$this->asset_id = $object->asset_id;
		$this->title = $object->title;
		$this->created_by = $object->created_by;
		$this->created = $object->created;
		$this->modified = $object->modified;
		$this->version = $object->version;
		$this->published = $object->published;
		$this->settings = $object->settings;
		$this->attribs = $object->attribs;
		$this->metadata = $object->metadata;
		$this->metakey = $object->metakey;
		$this->metadesc = $object->metadesc;
		$this->images = $object->images;
		$this->urls = $object->urls;
		$this->fields = $object->fields;
	}
	
	public function getFieldByName($name)
	{
		$name = strtolower($name);
		
		foreach($this->fields as $field)
		{
			if(strtolower($field->fieldname) == $name)
			{
				return $field;
			}
		}
		
		return null;
	}
	
	public function getFieldByType($fieldType)
	{
		foreach($this->fields as $field)
		{
			if(get_class($field) == 'F2cField'.$fieldType)
			{
				return $field;
			}
		}
		
		return null;
	}
	
	public function getFieldsByType($fieldType)
	{
		$fields = array();
		
		foreach($this->fields as $field)
		{
			if(get_class($field) == 'F2cField'.$fieldType)
			{
				$fields[$field->fieldname] = $field;
			}
		}
		
		return $fields;
	}	
}
?>