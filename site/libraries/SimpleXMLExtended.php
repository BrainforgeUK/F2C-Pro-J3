<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class SimpleXMLExtended extends SimpleXMLElement
{
	/**
	 * Add a CDATA section as a value to the current node
	 * Found on http://coffeerings.posterous.com/php-simplexml-and-cdata
	 *
	 * @param   string  $cdata   The text to be put in the CDATA
	 *
	 * @return 	void
	 *
	 * @since   6.17.0
	 */
	public function addCData(string $cdata)
	{
		$node 	= dom_import_simplexml($this);
		$no 	= $node->ownerDocument;
		
		$node->appendChild($no->createCDATASection($cdata));
	}
	
	/**
	 * Add an array as a child to the current node
	 *
	 * @param   array  	$array   		Array of data to be added
	 * @param	bool	$keyIsElement	Defines whether the key is the name for the element
	 *
	 * @return 	void
	 *
	 * @since   6.17.0
	 */
	public function addArray($array, $keyIsElement = true)
	{
		if(count($array))
		{
			foreach($array as $key => $value)
			{
				if($keyIsElement)
				{
					if(is_array($value))
					{
						// The array key is the element name
						$xmlElement = $this->addChild($key);
						$xmlElement->addArray($value, false);
					}
					else
					{
						$this->$key = $value;
					}
				}
				else
				{
					// 'key' is the element name. Use this when $key might
					// not be a valid XML element name
					$xmlArrayElement = $this->addChild('arrayelement');
					
					$xmlArrayElement->key = $key;
					$xmlArrayElement->value = $value;
				}
			}
		}
	}
	
	/**
	 * Add a registry as a child to the current node
	 *
	 * @param   Registry	$registry   Registry to be added
	 *
	 * @return 	void
	 *
	 * @since   6.17.0
	 */
	public function addRegistry($registry)
	{
		$this->addArray($registry->toArray());
	}
	
	/**
	 * Add a date field to the current element.
	 * Format the date according to the ISO8601 specs
	 *
	 * @param   string	$date   		The date value
	 * 
	 * @return 	void
	 *
	 * @since   6.17.0
	 */
	public function addDate($date)
	{
		$this[0] = '';
		
		if($date != JFactory::getDbo()->getNullDate())
		{
			$formattedDate = new JDate($date);
			$this[0] = $formattedDate->toISO8601();
		}
	}
	
	/**
	 * Append a SimpleXML fragment as a child to a  SimpleXML node.
	 *
	 * @param   object	$xml	SimpleXML element node
	 *
	 * @return 	void
	 *
	 * @since   6.17.0
	 */
	public function appendXml($xml)
	{
		$toDom = dom_import_simplexml($this);
		$fromDom = dom_import_simplexml($xml);
		$toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
	}
	
	/**
	 * Export the contents of a node to an array.
	 *
	 * @return 	array	the transformed contents of the node
	 *
	 * @since   6.17.0
	 */
	public function toArray()
	{
		$array = array();
		
		if(count($this->children()))
		{
			foreach($this->children() as $elementName => $child)
			{
				if($child->children())
				{
					if($elementName != 'arrayelement')
					{
						$array[$elementName] = $child->toArray();
					}
					else
					{
						$array[(string)$child->key] = (string)$child->value;
					}
				}
				else
				{
					$array[$elementName] = (string)$child;
				}
			}
		}
		
		return $array;
	}
}
?>