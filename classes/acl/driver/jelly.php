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
		$acl = Jelly::query('acl')
			->order_by('role', 'ASC')
			->select();

		foreach($acl as $acl_line)
		{
			if(count($acl_line->resource->childs))
			{
				foreach($acl_line->resource->childs as $resource)
				{
					$this->_acl[$acl_line->role->name][$resource->name][$acl_line->action->name] = $acl_line->regulation;
				}
			}
			else
			{
				$this->_acl[$acl_line->role->name][$acl_line->resource->name][$acl_line->action->name] = $acl_line->regulation;
			}
		}

		$this->_resources = Jelly::query('resource')->execute()->as_array('name', 'id');


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
	public function _add_resource($resournce_name)
	{
		$resource = Jelly::factory('resource')->set(array('name' => $resournce_name));

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