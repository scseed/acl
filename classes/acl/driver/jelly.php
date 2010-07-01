<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Acl Driver of Jelly ORM
 *
 * @package ACL
 * @author avis <smgladkovskiy@gmail.com>
 */
class Acl_Driver_Jelly extends Acl implements Acl_Driver_Interface {

	/**
	 * Queries DB for acl actions
	 *
	 * @param  array  $roles
	 * @param  string $resource
	 * @param  string $regulation
	 * @return object Jelly_Collection
	 */
	private function _get_acl_actions($roles, $resource, $regulation)
	{
		return Jelly::select('rules')
			->where('role.name', 'IN', $roles)
			->where('resource.name', '=', $resource)
			->where('regulation', '=', $regulation)
			->execute();;
	}

	/**
	 * Queries DB for ACL resources
	 *
	 * @param  array  $roles
	 * @param  string $regulation
	 * @return object Jelly_Collection
	 */
	private function _get_acl_resources($roles, $regulation)
	{
		return Jelly::select('rules')
			->distinct(TRUE)
			->where('role.name', 'IN', $roles)
			->where('regulation', '=', $regulation)
			->execute();
	}

} // End Acl_Driver_Jelly