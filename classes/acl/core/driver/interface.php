<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Acl Driver Interface
 *
 * @package ACL
 * @author Sergei Gladkovskiy <smgladkovskiy@gmail.com>
 */
interface Acl_Core_Driver_Interface {

	/* methods for classes that extend ACL as database drivers */
	function grab_actions();
	function grab_resources();
	function grab_acl_rules();
	function _form_acl(Model_Acl $acl_line, $resource_id = NULL);
//	function _add_resource(array $resource);

}