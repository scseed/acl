<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Acl Driver of Jelly ORM
 *
 * @package ACL
 * @author avis <smgladkovskiy@gmail.com>
 */
class Acl_Driver_Jelly_Unstable extends Acl implements Acl_Driver_Interface {

	public function _grab_resources()
	{
		$resources       = Jelly::query('resource')->select();
		$resources_paths = array();
		$resources_assoc = array();

		foreach($resources as $resource)
		{
			$resource_path = array(
				$resource->route_name,
				$resource->directory,
				$resource->controller,
				$resource->action,
				($resource->params) ? serialize($resource->params) : NULL
			);

			$resource_path = implode('.', $resource_path);

			$resources_paths[$resource->id]  = $resource_path;
			$resources_assoc[$resource_path] = $resource;
		}

		$this->_resources_paths = $resources_paths;
		$this->_resources       = $resources_assoc;
	}

	/**
	 * Loads all acl rules in $_acl container
	 */
	public function _grab_acl_rules()
	{
		$acl        = Jelly::query('acl')->select();

		foreach($acl as $acl_line)
		{
			$this->_acl[] = $this->_form_acl($acl_line, $acl_line->resource->id);

			$subresources = $acl_line->resource->childs;

			if(count($subresources))
			{
				foreach($acl_line->resource->childs as $subresource)
				{
					$this->_acl[] = $this->_form_acl($acl_line, $subresource->id);
				}
			}
		}

		if(empty($this->_acl))
		{
			throw new Http_Exception_500('ACL is empty. Fill it first!');
		}
	}

	public function _form_acl(Model_Acl $acl_line, $resource_id = NULL)
	{
		return array(
			'role'          => $acl_line->role->name,
			'resource_path' => $this->_resources_paths[$resource_id],
			'action'        => $acl_line->action->name,
			'regulation'    => $acl_line->regulation
		);
	}

	public function _grab_actions()
	{
		$actions = Jelly::query('action')->select();

		$all_score = 0;
		foreach($actions as $action)
		{
			if($action->score)
				$this->_actions[$action->name] = $action->score;

			$all_score += $action->score;
		}

		$this->_actions['all'] = $all_score;
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
		$new_resource       = Jelly::factory('resource');
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

} // End Acl_Driver_Jelly_Unstable