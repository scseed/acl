<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Role Model for Jelly ORM
 *
 * @package ACL
 * @author Sergei Gladkovskiy <smgladkovskiy@gmail.com>
 */
class Model_Role extends Jelly_Model {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('roles')
			->fields(array(
				'id'     => Jelly::field('Primary'),
				'parent' => Jelly::field('BelongsTo', array(
					'foreign'  => 'role',
					'column'   => 'parent_id',
					'default'  => NULL,
					'rules'    => array(
						'numeric' => array(TRUE),
					)
				)),
				'name' => Jelly::field('String', array(
					'rules'   => array(
						'not_empty' => array(TRUE),
					)
				)),
				'description' => Jelly::field('String'),
			))
			->load_with(array('parent'));
	}

	/**
	 * Returns the ids of available roles.
	 *
	 * @param   array  $role
	 * @return  array
	 */
	public function get_role_ids(array $role)
	{
		return Jelly::query('role')
			->where('name', 'IN', $role)
			->select()
			->as_array(NULL, 'id');
	}

	/**
	 * Loads a role based on name.
	 *
	 * @param   string  $role
	 * @return  Jelly_Model
	 */
	public function get_role($role)
	{
		return Jelly::query('role')->where('name', '=', $role)->limit(1)->select();
	}
} // End Model_Role