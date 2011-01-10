<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Acl Driver of Jelly ORM
 *
 * @package ACL
 * @author avis <smgladkovskiy@gmail.com>
 */
class Acl_Driver_Jelly extends Acl implements Acl_Driver_Interface {

	/**
	 * Loads all acl rules in $_acl container
	 */
	public function _grab_acl_rules()
	{
		$acl = Jelly::select('acl')
			->order_by('role', 'ASC')
			->execute();

		foreach($acl as $acl_line)
		{
			if($acl_line->resource->parent->id === NULL)
			{
				$route_name = $acl_line->resource->name;
			}
			else
			{
				$route_name = $acl_line->resource->parent->name;
			}

			if(count($acl_line->resource->childs))
			{
				foreach($acl_line->resource->childs as $resource)
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

		$resources = Jelly::select('resource')->with('parent')->where('parent.id', '!=', NULL)->execute();

		foreach($resources as $resource)
		{
			$this->_resources[$resource->parent->name][$resource->name] = TRUE;
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
			->with('parent')
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