<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Resource Model for Jelly ORM
 *
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
				'id' => Jelly::field('Primary'),
				'parent' => Jelly::field('BelongsTo', array(
					'foreign' => 'resource',
					'column' => 'parent_id',
					'model' => 'resource'
				)),
				'childs' => Jelly::field('HasMany', array(
					'in_bd' => FALSE,
					'foreign' => 'resource.parent_id',
					'column' => 'parent_id',
					'model' => 'resource'
				)),
				'name' => Jelly::field('String')
			));
	}
} // End Model_Resource