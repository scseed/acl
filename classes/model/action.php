<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Action Model for Jelly ORM
 *
 * @package ACL
 * @author Sergei Gladkovskiy <smgladkovskiy@gmail.com>
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
				'id'   => Jelly::field('Primary'),
				'name' => Jelly::field('String'),
				'score' => Jelly::field('Integer'),
			));
	}
} // End Model_Action