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
		$acl = Kohana::cache('acl');

		if( ! $acl)
		{
			$acl = Jelly::select('acl')
				->order_by('role', 'ASC')
				->execute();
		    Kohana::cache('acl', $acl, 36000);
		}

		foreach($acl as $acl_line)
		{
			$parent_resource = Kohana::cache($acl_line->resource->id . '_parent');

			if( ! $parent_resource)
			{
				$parent_resource = $acl_line->resource->parent->load_values(array('id', 'name'));
			    Kohana::cache($acl_line->resource->id . '_parent', $parent_resource, 36000);
			}

			if($parent_resource->id === NULL)
			{
				$route_name = $acl_line->resource->name;
			}
			else
			{
				$route_name = $parent_resource->name;
			}

			$child_resources = Kohana::cache($acl_line->resource->id . '_childs');

			if( ! $child_resources)
			{
				$child_resources =  $acl_line->resource->childs;
				Kohana::cache($acl_line->resource->id . '_childs', $child_resources, 36000);
			}

			if(count($child_resources))
			{
				foreach($child_resources as $resource)
				{
					$this->_acl[] = array(
						'role' => $acl_line->role->name,
						'route' => $route_name,
						'resource' => $resource->name,
						'action' => $acl_line->action->name,
						'regulation' => $acl_line->regulation
					);
				}
			}
			else
			{
				$this->_acl[] = array(
					'role' => $acl_line->role->name,
					'route' => $route_name,
					'resource' => $acl_line->resource->name,
					'action' => $acl_line->action->name,
					'regulation' => $acl_line->regulation
				);
			}
		}

		$resources = Jelly::select('resource')->where('parent.id', '!=', NULL)->execute();

		foreach($resources as $resource)
		{
			$parent_resource_name = Kohana::cache($resource->id . '_parent_name');

			if( ! $parent_resource_name)
			{
				$parent_resource_name = $resource->parent->name;
			    Kohana::cache($resource->id . '_parent_name', $parent_resource_name, 36000);
			}

			$this->_resources[$parent_resource_name][$resource->name] = TRUE;
		}

		if(empty($this->_acl))
		{
			die('ACL is empty. Fill it first!');
		}
	}

	/**
	 * Adds missing resource to a resources table
	 *
	 * @param string $resournce_name
	 */
	public function _add_resource($resournce_name, $route_name)
	{
		$route = Jelly::select('resource')
			->where('parent.id', '=', NULL)
			->where('name', '=', $route_name)
			->limit(1)
			->execute();

		if( ! $route->loaded())
		{
			$route = Jelly::factory('resource', array('name' => $route_name));

			try
			{
				$route->save();
			}
			catch(Validate_Exception $e)
			{
				die('There is no resources table in your database!');
			}
		}

		$resource = Jelly::factory('resource',
			array(
				'name' => $resournce_name,
				'parent' => $route->id,
			)
		);

		try
		{
			$resource->save();
		}
		catch(Validate_Exception $e)
		{
			die('There is no resources table in your database!');
		}

		array_push($this->_resources, array($resource->name => $resource->id));
	}

} // End Acl_Driver_Jelly