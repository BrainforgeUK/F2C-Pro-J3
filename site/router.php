<?php
defined('JPATH_PLATFORM') or die();

/**
 * Build the route for the com_form2content component
 *
 * @param	array	An array of URL arguments
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 * @since	4.0.0
 */
function Form2ContentBuildRoute(&$query)
{
	// get a menu item based on Itemid or currently active
	$app			= JFactory::getApplication();
	$menu			= $app->getMenu();
	$segments		= array();

	// we need a menu item.  Either the one specified in the query, or the current active one if none specified
	$menuItem = empty($query['Itemid']) ? $menu->getActive() : $menu->getItem($query['Itemid']);

	$queryView = ($menuItem->component == 'com_form2content') ? $menuItem->query['view'] : 'form';

	$view = isset($query['view']) ? $query['view'] : $queryView;

	switch($view)
	{
		case 'form':
			$segments[] = 'article';
			if(empty($query['id']))
			{
				// new article
				$segments[] = 'new';
				$segments[] = isset($query['projectid']) ? $query['projectid'] : '';
			}
			else
			{
				// existing article
				$segments[] = 'edit';
				$segments[] = $query['id'];
			}
			unset($query['view']);
			unset($query['layout']);
			unset($query['id']);
			break;
		case 'forms':
			switch($query['task'] ?? null)
			{
				case 'form.edit':
					$segments[] = 'article';
					$segments[] = 'edit';
					$segments[] = $query['id'];
					unset($query['view']);
					unset($query['task']);
					unset($query['id']);
					$query['Itemid'] = $menuItem->id;
					break;
				case 'form.new':
					$segments[] = $menuItem->route;
					$segments[] = 'article';
					$segments[] = 'new';
					unset($query['view']);
					unset($query['task']);
					unset($query['id']);
					break;
				default:
					unset($query['view']);
					break;
			}
			break;
	}

	return $segments;
}

		/*
		switch($view)
		{
			case 'templates':
				$segments[] = 'selecttemplate';
				unset($query['tmpl']);
				break;

			case 'users':
				$segments[] = 'selectuser';
				unset($query['tmpl']);
				break;

			// We come from the F2C Article Manager
			case 'forms':
				if(isset($query['task']))
				{
					list($controller, $task) = explode('.', $query['task']);

					switch($controller)
					{
						case 'forms':
							// Removed Brainforge.uk 2025/04/29
							//$segments[] = 'articlemanager';
							break;
						case 'form':
							$segments[] = 'article';
							$segments[] = $task;
							$segments[] = $query['id'];
							break;
					}
				}
				else
				{
					// Removed Brainforge.uk 2025/04/29
					//$segments[] = 'articlemanager';
				}
				break;

			case 'form':
				$segments[] = 'article';

				if(isset($menuItem))
				{
					// Modified Brainforge.uk 2025/04/29
					$params = $menuItem->getParams();
					switch($params->get('editmode'))
					{
						case '':
							$editmode = 'edit';
							break;
						case 0;
							$editmode = 'new';
							break;
						case 1;
							$editmode = 'edit';
							break;
					}
				}
				else
				{
					$editmode = 'edit';
				}

				if(empty($query['id']))
				{
					// new article
					$segments[] = 'new';
					$segments[] = isset($query['projectid']) ? $query['projectid'] : '';
				}
				else
				{
					// existing article
					$segments[] = $editmode;
					$segments[] = $query['id'];
				}
				break;
		}

		unset($query['view']);
		unset($query['task']);
		unset($query['id']);
		unset($query['projectid']);
		unset($query['layout']);

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param	array	The segments of the URL to parse.
	 *
	 * @return	array	The URL attributes to be used by the application.
	 * @since	4.0.0
	 */
function Form2ContentParseRoute(&$segments)
{
	$vars = array();
	
	//Get the active menu item.
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();

	// Modified Brainforge.uk 2025/04/30
	switch($segments[0])
	{
		case 'article':
			$vars['view'] = 'form';
			$vars['layout'] = 'edit';
			$vars['task'] = 'form.edit';

			switch($segments[1])
			{
				case 'add':
					$vars['task'] = 'form.add';
					unset($segments[0]);
					unset($segments[1]);
					break;
				case 'edit':
					$vars['id'] = $segments[2];
					unset($segments[0]);
					unset($segments[1]);
					unset($segments[2]);
					break;
				case 'new':
					unset($segments[0]);
					unset($segments[1]);
					if(empty($segments[2]))
					{
						if (empty($item))
						{
							$vars['option'] = 'com_form2content';
							$vars['view'] = 'forms';
							unset($vars['layout']);
							unset($vars['task']);
							break;
						}

						// get the Content Type Id from the menu setting
						$vars['projectid'] = $item->getParams()->get('contenttypeid');
						$vars['task'] = '';
						break;
					}

					$vars['projectid'] = $segments[2];
					unset($segments[2]);
					break;
			}
	}

	return $vars;

	/*
	switch($segments[0])
	{
		case 'articlemanager':
			// Added Brainforge.uk 2025/04/29
			unset($segments[0]);
			$vars['view'] = 'forms';
			break;

		case 'selecttemplate':
			// Added Brainforge.uk 2025/04/29
			unset($segments[0]);
			$vars['view'] = 'templates';
			$vars['layout'] = 'modal';
			$vars['task'] = 'templates.select';
			$vars['tmpl'] = 'component';
			break;
			
		case 'selectuser':
			// Added Brainforge.uk 2025/04/29
			unset($segments[0]);
			$vars['view'] = 'users';
			$vars['layout'] = 'modal';
			$vars['task'] = 'users.display';
			$vars['tmpl'] = 'component';
			break;
			
		case 'article':
			// Added Brainforge.uk 2025/04/29
			unset($segments[0]);
			$vars['view'] = 'form';
			$vars['layout'] = 'edit';
			$vars['task'] = 'form.edit';
			
			if($segments[1] == 'add')
			{
				$vars['task'] = 'form.add';
			}
			else 
			{
				if($segments[1] == 'new')
				{
					if(isset($segments[2]))
					{
						$vars['projectid'] = $segments[2];
					}
					else 
					{
						// get the Content Type Id from the menu setting
						$vars['projectid'] = $item->params->get('contenttypeid');
						$vars['task'] = '';
					}
				}
				else 
				{
					$vars['id'] = $segments[2];
				}

				// Added Brainforge.uk 2025/04/29
				unset($segments[1]);
				// Added Brainforge.uk 2025/04/29
				unset($segments[2]);
			}
			break;
	}

	return $vars;
	*/
}