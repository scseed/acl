<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Acl Driver of Jelly ORM
 *
 * @package ACL
 * @author avis <smgladkovskiy@gmail.com>
 *
 * @todo Use caching in Jelly, not by Kohana::cache()
 */
class Acl_Driver_Jelly extends Acl implements Acl_Driver_Interface {

	/**
	 * Loads all acl rules in $_acl container
	 */
	public function _grab_acl_rules()
	{
		$resources_paths = NULL;
		$resources = NULL;
		$_resources = Jelly::query('resource')->select();
		$acl = Jelly::query('acl')->select();

		foreach($_resources as $resource)
		{
			$resource_path = $resource->route_name . '.'
			               . $resource->directory . '.'
			               . $resource->controller . '.'
			               . $resource->action . '.'
			               . $resource->object_id;
			$resources_paths[$resource->id] = $resource_path;
			$this->_resources[$resource_path] = $resource;
			$resources[$resource->parent->id][$resource->id] = $resource;
		}

 		foreach($acl as $acl_line)
		{
			$child_resources = (isset($resources[$acl_line->resource->id]))
			                    ? $resources[$acl_line->resource->id]
			                    : NULL;

			if(count($child_resources))
			{
				foreach($child_resources as $resource)
				{
					$this->_acl[] = array(
						'role' => $acl_line->role->name,
						'resource_path' => $resources_paths[$resource->id],
						'action' => $acl_line->action->name,
						'regulation' => $acl_line->regulation
					);
				}
			}
			else
			{
				$this->_acl[] = array(
					'role' => $acl_line->role->name,
					'resource_path' => $resources_paths[$acl_line->resource->id],
					'action' => $acl_line->action->name,
					'regulation' => $acl_line->regulation
				);
			}
		}

		if(empty($this->_acl))
		{
			throw new Kohana_Exception('ACL is empty. Fill it first!');
		}
	}

	/**
	 * Adds missing resource to a resources table
	 *
	 * @param string $resournce_name
	 */
	public function _add_resource(array $resource)
	{
		$route = Jelly::query('resource')
			->where('resource:parent.id', '=', NULL)
			->where('route_name', '=', $resource['route_name'])
			->limit(1)
			->select();

		if( ! $route->loaded())
		{
			$route = Jelly::factory('resource');
			$route->set(array('route_name' => $resource['route_name']));

			try
			{
				$route->save();
			}
			catch(Validate_Exception $e)
			{
				throw new Kohana_Exception('There is no resources table in your database!');
			}
		}

		$resource['parent'] = $route->id;
		$new_resource = Jelly::factory('resource');
		$new_resource->set($resource);

		try
		{
			$new_resource->save();
			$this->_grab_acl_rules();
			return;
		}
		catch(Validate_Exception $e)
		{
			die('There is no resources table in your database!');
		}
	}

} // End Acl_Driver_Jelly