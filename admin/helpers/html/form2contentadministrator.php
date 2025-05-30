<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_form2content
 */

defined('_JEXEC') or die;

JLoader::register('ContentHelper', JPATH_ADMINISTRATOR . '/components/com_content/helpers/content.php');

/**
 * Content HTML helper
 *
 * @package     Joomla.Administrator
 * @subpackage  com_form2content
 *
 * @since       6.8.0
 */
abstract class JHtmlForm2ContentAdministrator
{
	/**
	 * Render the list of associated items
	 *
	 * @param   int  $articleid  The article item id
	 *
	 * @return  string  The language HTML
	 */
	public static function association($articleid)
	{
		// Defaults
		$html = '';

		// Get the associations
		if ($associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $articleid))
		{
			foreach ($associations as $tag => $associated)
			{
				$associations[$tag] = (int) $associated->id;
			}

			// Get the associated menu items
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('c.*')
				->select('l.sef as lang_sef')
				->from('#__content as c')
				->select('cat.title as category_title')
				->join('LEFT', '#__categories as cat ON cat.id=c.catid')
				->where('c.id IN (' . implode(',', array_values($associations)) . ')')
				->join('LEFT', '#__languages as l ON c.language=l.lang_code')
				->select('l.image')
				->select('l.title as language_title')
				->join('LEFT', '#__f2c_form as f ON c.id = f.reference_id')
				->select('f.id as f2cId');
			$db->setQuery($query);

			try
			{
				$items = $db->loadObjectList('id');
			}
			catch (RuntimeException $e)
			{
				throw new Exception($e->getMessage(), 500);
			}

			if ($items)
			{
				foreach ($items as &$item)
				{
					$text = strtoupper($item->lang_sef);
					
					if($item->f2cId)
					{
						// Form2content article
						$url = JRoute::_('index.php?option=com_form2content&task=form.edit&id=' . (int) $item->f2cId);
					}
					else
					{
						// Joomla article
						$url = JRoute::_('index.php?option=com_content&task=article.edit&id=' . (int) $item->id);
					}
					
					$tooltipParts = array(
						JHtml::_('image', 'mod_languages/' . $item->image . '.gif',
							$item->language_title,
							array('title' => $item->language_title),
							true
						),
						$item->title,
						'(' . $item->category_title . ')'
					);
					$item->link = JHtml::_('tooltip', implode(' ', $tooltipParts), null, null, $text, $url, null, 'hasTooltip label label-association label-' . $item->lang_sef);
				}
			}

			$html = JLayoutHelper::render('joomla.content.associations', $items);
		}

		return $html;
	}

	/**
	 * Show the feature/unfeature links
	 *
	 * @param   int      $value      The state value
	 * @param   int      $i          Row number
	 * @param   boolean  $canChange  Is user allowed to change?
	 *
	 * @return  string       HTML code
	 */
	// Modified Brainforge.uk 20250509
	public static function featured($value, $i, $canChange = true)
	{
		JHtml::_('bootstrap.tooltip');

		// Array of image, task, title, action
		$states	= array(
			// Modified Brainforge.uk 20250511
			0	=> array('unfeatured',	'forms.featured',	'COM_FORM2CONTENT_UNFEATURED',	'COM_FORM2CONTENT_TOGGLE_TO_FEATURE'),
			// Modified Brainforge.uk 20250511
			1	=> array('featured',	'forms.unfeatured',	'COM_FORM2CONTENT_FEATURED',	'COM_FORM2CONTENT_TOGGLE_TO_UNFEATURE'),
		);
		$state	= F2cBrainforgeukArrayhelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];

		if ($canChange)
		{
			// Modified Brainforge.uk 2025/04/29
			$html	= '<a href="#" onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" class="btn btn-micro hasTooltip' . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[3]) . '"><i class="icon-'
					. $icon . '"></i></a>';
		}
		else
		{
			$html	= '<a class="btn btn-micro hasTooltip disabled' . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[2]) . '"><i class="icon-'
					. $icon . '"></i></a>';
		}

		return $html;
	}
}