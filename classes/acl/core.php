<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Class ACL Core
 * 
 * @todo make it work with hierarchical roles structure (is it needed?) and to add assertions
 * @package ACL
 * @author avis <smgladkovskiy@gmail.com>
 */
abstract class Acl_Core {

	// ACL Instances array
	protected static $instances = array();

	// ACL container
	protected $_acl = array();

	// Supported CRUD action names
	protected $_actions   = array(
		'create', 'read', 'update', 'delete', 'all'
	);

	/**
	 * ACL instance initiation
	 *
	 * @param  string $auth Auth supplier
	 * @return object ALC
	 */
	public static function instance($auth = 'default')
	{
		// if $auth is not defined, gets default
		// auth supplier from the config file
		if($auth === 'default')
		{
			$config = Kohana::config('acl');
			$auth = $config['default_auth_supplier'];
		}
		
		if( ! in_array($auth, self::$instances))
		{
			$auth_config = Kohana::config($auth);

			$acl_driver = 'Acl_Driver_' . ucfirst($auth_config['driver']);
			
			self::$instances[$auth] = new $acl_driver;
		}
		
		return self::$instances[$auth];
	}

	/**
	 * Fullfill ACL container on init
	 */
	public function __construct()
	{
		$this->_grab_acl_rules();
	}

	/**
	 * Inspects resources allowed to current $roles
	 * 
	 * @param  array  $roles
	 * @return array
	 */
	public function resources($roles)
	{
		foreach($roles as $role)
		{
			if(array_key_exists($role, $this->_acl))
			{
				$resources = $this->_acl[$role];
			}
		}

		return $resources;
	}

	/**
	 * Inspects if current $roles allowed to act as poined in $actions array 
	 * in current $resource
	 *
	 * @param  array  $roles
	 * @param  string $resorce
	 * @param  array  $actions
	 * @return boolean
	 */
	public function is_allowed($roles, $resorce, $actions)
	{
		$allowed_resources = $this->resources($roles);

		$allowed_actions = Arr::get($allowed_resources, $resorce, NULL);

		if($allowed_actions === NULL)
			return FALSE;

		foreach($allowed_actions as $action_name => $regulation)
		{
			if( ! in_array($action_name, $allowed_actions) AND $regulation != 'allow')
			{
				return FALSE;
			}
		}

		return TRUE;
	}

} // End Acl_Core