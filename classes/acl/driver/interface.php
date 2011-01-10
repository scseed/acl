<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Acl Driver Interface
 *
 * @package ACL
 * @author avis <smgladkovskiy@gmail.com>
 */
interface Acl_Driver_Interface {

	/* methods for classes that extend ACL as database drivers */
	public function _grab_acl_rules();
	public function _add_resource($resournce_name, $route_name);

}