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

	protected $_resources = array();

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
//			if(array_key_exists($role, $this->_acl))
//			{
//				$resources = $this->_acl[$role];
//			}
			foreach($this->_acl as $acl_line)
			{
				if($acl_line['role'] == $role)
				{
					$resources[ $acl_line['route'] . '.' . $acl_line['resource']] = array($acl_line['action'] => $acl_line['regulation']);
				}
			}
		}

		return $resources;
	}

	/**
	 * Inspects if current $roles allowed to act as poined in $actions array
	 * in current $resource
	 *
	 * @param  array  $roles
	 * @param  array $resource as array('route_name' => '...', 'resource' => '...')
	 * @param  array  $actions
	 * @return boolean
	 */
	public function is_allowed($roles, $_resource, $actions)
	{
		$route_defaults = Request::instance()->route->get_defaults();
		$route_name = arr::get($_resource, 'route_name', 'default');
		$resource = arr::get($_resource, 'resource', $route_defaults['controller']);

		$resource_path = $route_name . '.' . $resource;

		if( ! Arr::path($this->_resources, $resource_path, FALSE))
		{
			$this->_add_resource($resource, $route_name);
		}

		$allowed_resources = $this->resources($roles, $resource);

		$allowed_actions = Arr::get($allowed_resources, $resource_path, NULL);

		if($allowed_actions === NULL)
			return FALSE;


		foreach($allowed_actions as $action_name => $regulation)
		{
			if( ! in_array($resource_path, $allowed_actions) AND $regulation != 'allow')
			{
				return FALSE;
			}
		}

		return TRUE;
	}

} // End Acl_Core