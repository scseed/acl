<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Class ACL Core
 *
 * @package ACL
 * @author Sergei Gladkovskiy <smgladkovskiy@gmail.com>
 */
abstract class Acl_Core {

	/**
	 * Instances container
	 * @var array
	 */
	protected static $instances;

	/**
	 * ACL container
	 * @var array
	 */
	protected $_acl;

	/**
	 * Resources container
	 * @var array
	 */
	protected $_resources;

	/**
	 * Resources paths container
	 * @var array
	 */
	protected $_resources_paths;

	/**
	 * Supported CRUD action names and it scores to count and compare
	 * @var array
	 */
	protected $_actions;

	/**
	 * ACL instance initiation
	 *
	 * @static
	 * @param  string $type
	 * @return Acl
	 */
	public static function instance($type = 'default')
	{
		// if $auth is not defined, gets default auth supplier from the config file
		$config = Kohana::config('acl');

		$acl_driver = 'Acl_Driver_' . ucfirst($config->ORM_driver);
		self::$instances[$type] = new $acl_driver;

		return self::$instances[$type];
	}

	public function __construct()
	{
		$this->grab_actions()->grab_resources()->grab_acl_rules();
	}

	/**
	 * Inspects resources allowed to current $roles
	 *
	 * @param  array $roles
	 * @return array
	 */
	public function allowed_resources(array $roles)
	{
		$resources = array();
		foreach($roles as $role)
		{
			foreach($this->_acl as $acl_line)
			{
				if($acl_line['role'] == $role AND $acl_line['regulation'] == 'allow')
				{
					if( ! isset($resources[$acl_line['resource_path']][$acl_line['role']]))
					{
						$resources[$acl_line['resource_path']][$acl_line['role']] = array();
					}

					array_push($resources[$acl_line['resource_path']][$acl_line['role']], $acl_line['action']);
				}
			}
		}

		return $resources;
	}

	/**
	 * Inspects if current $roles allowed to act as poined in $actions array of current $resource
	 *
	 * @todo   Implement ACL Assertions
	 *
	 * @throws Http_Exception_401
	 * @param  array        $roles
	 * @param  array        $actions
	 * @param  null|Request $request
	 * @return bool
	 */
	public function is_allowed(array $roles, array $actions = array(), Request $request = NULL)
	{
		if($request === NULL)
			$request = Request::current();

		$route = $request->route();

		// Forms resource path
		$directory  = ($request->directory() == '')         ? NULL : $request->directory();
		$controller = ($request->controller() == 'welcome') ? NULL : $request->controller();
		$action     = ($request->action() == 'index')       ? NULL : $request->action();

		$resource_path = array(
			$route->name($request->route()),
			$directory,
			$controller,
			$action,
		);

		$resource_path = implode('.', $resource_path);

		// Checks resource existance in resources map
		if( ! Arr::get($this->_resources, $resource_path, FALSE))
		{
			throw new Http_Exception_401('Unauthorized access');
		}

		// Checks existance of a route in alowed resources list
		$allowed_resources = $this->allowed_resources($roles);
		if( ! array_key_exists($resource_path, $allowed_resources))
		{
			throw new Http_Exception_401('Unauthorized access');
		}

		// Counts minimal route score, based on acl
		$route_score = 0;
		foreach($actions as $action)
		{
			$route_score += $this->_actions[$action];
		}

		$route_regulations = Arr::get($allowed_resources, $resource_path);

		// Counts user score for the route, based on acl
		$user_actions = array();
		foreach($route_regulations as $role => $actions)
		{
			$user_actions[$role] = 0;
			foreach($actions as $action)
			{
				$user_actions[$role] += $this->_actions[$action];
			}
		}

		foreach($user_actions as $user_score)
		{
			// comparing scores
			if($route_score < $user_score)
			{
				return TRUE;
			}
		}

		throw new Http_Exception_401('Access denied');
	}

} // End Acl_Core