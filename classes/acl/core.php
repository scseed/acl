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
		'all'    => 10,
		'read'   => 1,
		'create' => 2,
		'update' => 3,
		'delete' => 4,
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
		$resources = array();
		foreach($roles as $role)
		{
//			if(array_key_exists($role, $this->_acl))
//			{
//				$resources = $this->_acl[$role];
//			}
			foreach($this->_acl as $acl_line)
			{
				if($acl_line['role'] == $role AND $acl_line['regulation'] == 'allow')
				{
					$resources[$acl_line['resource_path']][$acl_line['role']] = $acl_line['action'];
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
	 * @param  array $resource as array('route_name' => '...', 'directory' => '...', 'controller' => '...', 'action' => '...', 'object_id' => '...')
	 * @param  array  $actions
	 * @return boolean
	 */
	public function is_allowed($roles, $_resource, $actions)
	{
		$route_defaults = Request::instance()->route->get_defaults();
		$resource_path = implode('.', $_resource);

		if( ! Arr::get($this->_resources, $resource_path, FALSE))
		{
			$this->_add_resource($_resource);
		}

		$route_action = 0;
		foreach($actions as $action)
		{
			$route_action += $this->_actions[$action];
		}

		$allowed_resources = $this->resources($roles, $_resource);

		if( ! array_key_exists($resource_path, $allowed_resources))
		{
			return FALSE;
		}

		$route_regulations = Arr::get($allowed_resources, $resource_path);

		$user_action = 0;
		foreach($route_regulations as $route_regulation)
		{
			$user_action += $this->_actions[$route_regulation];
		}

		if($route_action > $user_action)
		{
			return FALSE;
		}

		return TRUE;
	}

} // End Acl_Core