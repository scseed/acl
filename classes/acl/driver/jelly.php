<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Acl Driver of Jelly ORM
 *
 * @package ACL
 * @author avis <smgladkovskiy@gmail.com>
 */
class Acl_Driver_Jelly extends Acl implements Acl_Driver_Interface {

	public function _grab_acl_rules()
	{
		$acl = Jelly::select('acl')
			->with('role')
			->with('resource')
			->with('action')
			->order_by('role', 'ASC')
			->execute();

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

		if(empty($this->_acl))
		{
			die('ACL is empty. Fill it first!');
		}
	}

} // End Acl_Driver_Jelly