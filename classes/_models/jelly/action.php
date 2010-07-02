<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Action Model for Jelly ORM
 *
 * @author avis <smgladkovskiy@gmail.com>
 */
class Model_Action extends Jelly_Model {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('actions')
			->fields(array(
				'id' => Jelly::field('Primary'),
				'name' => Jelly::field('String'),
			));
	}
} // End Model_Action