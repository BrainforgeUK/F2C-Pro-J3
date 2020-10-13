<?php
defined('_JEXEC') or die;

/**
 * Form2Content Component HTML Helper
 */
abstract class JHtmlF2cIcon
{
	/**
	 * Display a Form2Content edit icon for the article.
	 *
	 * @param   object     $article  The article information
	 * @param   Registry  $params   The item parameters
	 * @param   array      $attribs  Optional attributes for the link
	 * @param   boolean    $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string	The HTML for the article edit icon.
	 * @since   4.6.5
	 */
	public static function edit($article, $params, $attribs = array(), $legacy = false)
	{
		$jIcon = JHtml::_('icon.edit', $article, $params);
		
		// Test if this is a F2C Article
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('*');
		$query->from('#__f2c_form');
		$query->where('reference_id = ' . (int)$article->id);
		
		$db->setQuery($query);
		$form = $db->loadObject();
		
		if($form)
		{
			$link = JRoute::_('index.php?option=com_form2content&task=form.edit&id='.$form->id.'&return='.base64_encode(JUri::getInstance()));			
			// use a regular expression to replace the link
			$jIcon = preg_replace('/href="(.*?)"/i', 'href="'.$link.'"', $jIcon);
		}
		
		return $jIcon;		
	}	
}
