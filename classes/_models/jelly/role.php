<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Role Model for Jelly ORM
 *
 * @author avis <smgladkovskiy@gmail.com>
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
				'id' => Jelly::field('Primary'),
				'parent' => Jelly::field('BelongsTo', array(
					'null' => TRUE,
					'editable' => FALSE,
					'foreign' => 'role',
					'column' => 'parent_id',
					'default' => NULL,
					'rules' => array(
						'numeric' => NULL,
					),
					'label' => 'Parent role',
				)),
				'name' => Jelly::field('String', array(
					'empty' => FALSE,
					'default' => '',
					'rules' => array(
						'not_empty' => NULL,
					),
					'label' => 'Role name',
				)),
				'description' => Jelly::field('String'),
			))
			->load_with(array('parent'));
	}
} // End Model_Role