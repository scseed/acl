<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Class ACL Core
 *
 * @package ACL
 * @author avis <smgladkovskiy@gmail.com>
 */
abstract class Acl_Core {

	// ACL Instances array
	protected static $instances = array();

	// ACL container
	protected $_acl = array();

	// resources container
	protected $_resources = array();

	// Supported CRUD action names and it scores to count and compare
	protected $_actions   = array();

	/**
	 * ACL instance initiation
	 *
	 * @param  string $auth Auth supplier
	 * @return object ACL
	 */
	public static function instance($type = 'default')
	{
		// if $auth is not defined, gets default auth supplier from the config file
		$config = Kohana::config('acl');

		$acl_driver = 'Acl_Driver_' . ucfirst($config->ORM_driver);
		self::$instances[$type] = new $acl_driver;

		return self::$instances[$type];
	}

	/**
	 * Fullfill ACL container on init
	 */
	public function __construct()
	{
		$this->_grab_resources();
		$this->_grab_acl_rules();
		$this->_grab_actions();
	}

	/**
	 * Inspects resources allowed to current $roles
	 *
	 * @param  array  $roles
	 * @return array
	 */
	public function allowed_resources($roles)
	{
		$resources = array();
		foreach($roles as $role)
		{
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
	public function is_allowed($roles, Request $request, $actions = array())
	{
		$route = $request->route();

		$directory  = ($request->directory() == '')         ? NULL : $request->directory();
		$controller = ($request->controller() == 'welcome') ? NULL : $request->controller();
		$action     = ($request->action() == 'index')       ? NULL : $request->action();
		$param      = NULL; // @todo: think how it could be used...

		$resource_path = array(
			$route->name($request->route()),
			$directory,
			$controller,
			$action,
			$param,
		);

		$resource_path = implode('.', $resource_path);

		// checking existance of a route in acl. If not => user is not alowed to do anything there
		$allowed_resources = $this->allowed_resources($roles);

		if( ! array_key_exists($resource_path, $allowed_resources))
		{
			return FALSE;
		}

		// Checking resource existance
		if( ! Arr::get($this->_resources, $resource_path, FALSE))
		{
//			$this->_add_resource($_resource);
		}

		// counting minimal route score, based on acl
		$route_action = 0;
		foreach($actions as $action)
		{
			$route_action += $this->_actions[$action];
		}

		$route_regulations = Arr::get($allowed_resources, $resource_path);

		// counting user score for the route, based on acl
		$user_action = 0;
		foreach($route_regulations as $route_regulation)
		{
			$user_action += $this->_actions[$route_regulation];
		}


		// comparing scores
		if($route_action > $user_action)
		{
			return FALSE;
		}

		return TRUE;
	}

} // End Acl_Core