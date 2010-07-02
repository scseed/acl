<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Acl Model for Jelly ORM
 *
 * @author avis <smgladkovskiy@gmail.com>
 */
class Model_Acl extends Jelly_Model {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('acls')
			->fields(array(
				'id' => Jelly::field('Primary'),
				'role' => Jelly::field('BelongsTo'),
				'resource' => Jelly::field('BelongsTo'),
				'action' => Jelly::field('BelongsTo'),
				'regulation' => Jelly::field('Enum', array(
					'choices' => array('allow', 'deny'),
				)),
			))
			->load_with(array('role', 'resource', 'action'));
	}
} // End Model_Acl