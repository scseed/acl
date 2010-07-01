<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Acl Driver Interface
 *
 * @package ACL
 * @author avis <smgladkovskiy@gmail.com>
 */
interface Acl_Driver_Interface {

	/* methods for classes that extend ACL as database drivers */
	private function _get_acl_actions($roles, $resource, $regulation);
	private function _get_acl_resources($roles, $regulation);

}