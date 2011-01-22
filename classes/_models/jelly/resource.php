<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Resource Model for Jelly ORM
 *
 * @package ACL
 * @author avis <smgladkovskiy@gmail.com>
 */
class Model_Resource extends Jelly_Model {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('resources')
			->fields(array(
				'id'     => Jelly::field('Primary'),
				'parent' => Jelly::field('BelongsTo', array(
					'foreign'    => 'resource',
					'column'     => 'parent_id',
					'model'      => 'resource',
					'allow_null' => TRUE,
					'default'    => NULL,
				)),
				'route_name' => Jelly::field('String'),
				'directory'  => Jelly::field('String', array(
					'allow_null' => TRUE,
					'default'    => NULL,
				)),
				'controller' => Jelly::field('String', array(
					'allow_null' => TRUE,
					'default'    => NULL,
				)),
				'action' => Jelly::field('String', array(
					'allow_null' => TRUE,
					'default'    => NULL,
				)),
				'object_id' => Jelly::field('String', array(
					'allow_null' => TRUE,
					'default'   => NULL,
				)),
				'acl' => Jelly::field('HasOne')
			))
			->load_with(array('parent', 'acl'));
	}
} // End Model_Resource