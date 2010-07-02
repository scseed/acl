<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Class ACL Core
 * 
 * @todo make it work with hierarchical roles structure
 * @package ACL
 * @author avis <smgladkovskiy@gmail.com>
 */
class Acl_Core {

	// ACL Instances array
	protected static $instances = array();

	// ACL container
	protected $_acl = array();

	// Supported CRUD action names
	protected $_actions   = array(
		'create', 'read', 'update', 'delete'
	);

	/**
	 * ACL instance initiation
	 *
	 * @param  string $auth Auth supplier
	 * @return object ALC
	 */
	public static function instance($auth = 'default')
	{
		if( ! in_array($auth, self::$instances) AND ! is_object(self::$instances[$auth]))
		{
			// if $auth is not defined, gets default
			// auth supplier from the config file
			if($auth === 'default')
			{
				$config = Kohana::config('acl');
				$auth = $config['default_auth_supplier'];
			}

			$auth_config = Kohana::config($auth);

			$acl_driver = 'Acl_Driver_' . ucfirst($auth_config['driver']);

			self::$instances[$auth] = new $acl_driver();
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
	 * @param  string $regulation
	 * @return array
	 */
	public function resources($roles, $regulation = 'allow')
	{
		foreach($roles as $role)
		{
			$resources[] = $this->_acl[$role];
		}

		$resources = Arr::flatten($resources);

		return $resources;

	}

	/**
	 * Inspects actions allowed to current $roles and $resource
	 *
	 * @param  array  $roles
	 * @param  string $resource
	 * @param  string $regulation
	 * @return array
	 */
	public function actions($roles, $resource, $regulation = 'allow')
	{
		foreach ($roles as $role)
		{
			$actions[] = $this->_acl[$role][$resource];
		}

		return $actions;
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
		
		$allowed_actions   = $this->actions($roles, $allowed_resources);

		$actions = Arr::extract($this->_actions, $actions, array());

		foreach($actions as $action)
		{
			if( ! in_array($action, $allowed_actions))
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}

} // End Acl_core