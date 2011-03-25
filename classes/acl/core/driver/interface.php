<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Acl Driver Interface
 *
 * @package ACL
 * @author Sergei Gladkovskiy <smgladkovskiy@gmail.com>
 */
interface Acl_Core_Driver_Interface {

	/**
	 * @abstract
	 * @return ACL
	 */
	function grab_actions();

	/**
	 * @abstract
	 * @return ACL
	 */
	function grab_resources();

	/**
	 * @abstract
	 * @return ACL
	 */
	function grab_acl_rules();

	function _form_acl(Model_Acl $acl_line, $resource_id = NULL);
//	function _add_resource(array $resource);

}