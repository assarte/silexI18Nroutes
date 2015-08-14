<?php
/**
 * Created by PhpStorm.
 * User: assarte
 * Date: 2015.08.13.
 * Time: 17:36
 */

namespace Sir;


class LoaderManager {
	/**
	 * @var array
	 */
	protected $loaders = array();

	/**
	 * @var array
	 */
	protected $routes = array();

	/**
	 * @var Silex\Application
	 */
	protected $app;

	/**
	 * @var string
	 */
	protected $language;

	/**
	 * @param Silex\Application $app
	 */
	public function __constructor(Silex\Application $app) {
		$this->app = $app;
	}

	/**
	 * Adds the route-loader
	 * @param LoaderInterface $loader
	 * @param null $key Specifying a key grants key-based access to loader
	 * @return LoaderInterface
	 */
	public function add(LoaderInterface $loader, $key=null) {
		if ($key === null) {
			$this->loaders[] = $loader;
		} else {
			$this->loaders[$key] = $loader;
		}

		return $loader;
	}

	/**
	 * Returns loader by key or NULL on non-existent loader
	 * @param $key
	 * @return LoaderInterface|null
	 */
	public function get($key) {
		if (isset($this->loaders[$key])) return $this->loaders[$key];
		return null;
	}

	/**
	 * Loads all routes for given language
	 * @param string $language
	 */
	public function loadRoutes($language) {
		$this->language = $language;

		foreach ($this->loaders as $loader) {
			$routes = $loader->load($language);

			$this->routes = array_merge($this->routes, $routes);
		}
	}

	/**
	 * Returns raw routes database
	 * @return array
	 */
	public function getRoutes() {
		return $this->routes;
	}

	/**
	 * Returns a node's route.
	 * @param $node Name of a controller collection
	 * @return mixed
	 * @throws RouteNotExistsException
	 */
	public function getNodeRoute($node) {
		if (!isset($this->routes[$node]) or !isset($this->routes[$node]['path'])) {
			throw new RouteNotExistsException($node);
		}

		return $this->routes[$node]['path'];
	}

	/**
	 * Registers routes for given node (controller collection).
	 * @param string $node
	 * @return Silex\ControllerCollection
	 * @throws \RuntimeException
	 * @throws RouteNotExistsException
	 * @throws \InvalidArgumentException
	 * @throws \LogicException
	 */
	public function registerRoutes($node) {
		if ($this->language === null) {
			throw new \RuntimeException('You must call loadRoutes() before registering routes for a node.');
		}

		if (!isset($this->routes[$node]) or !isset($this->routes[$node]['classname']) or !isset($this->routes[$node]['routes'])) {
			throw new RouteNotExistsException($node);
		}

		$controllers = $this->app['controllers_factory'];
		foreach ($this->routes[$node]['routes'] as $route) {
			$method = isset($route['method'])? $route['method'] : 'GET';
			$resolver = isset($route['action'])? $route['action'] : $this->routes[$node]['default_resolver'];
			if (empty($resolver)) {
				throw new \InvalidArgumentException('Undefined "action" or "default_resolver" on node "'.$node.'" for language "'.$this->language.'" at path: '.$route['path']);
			}

			$ins = $controllers
				->match($route['path'], $this->routes[$node]['classname'].'::'.$resolver)
				->method($method)
			;

			// check for converter callback or settings - see: http://silex.sensiolabs.org/doc/usage.html#route-variable-converters
			if (isset($route['convert'])) {
				if (!is_array($route['convert'])) {
					throw new \LogicException('Key "convert" must be array on node "'.$node.'" for language "'.$this->language.'".', 1);
				}
				foreach ($route['convert'] as $arg=>$setting) {
					$ins->convert($arg, $setting);
				}
			}
			// check for assertion regex - see: http://silex.sensiolabs.org/doc/usage.html#requirements
			if (isset($route['assert'])) {
				if (!is_array($route['assert'])) {
					throw new \LogicException('Key "assert" must be array on node "'.$node.'" for language "'.$this->language.'".', 2);
				}
				foreach ($route['assert'] as $arg=>$setting) {
					$ins->assert($arg, $setting);
				}
			}
			// check for default value - see: http://silex.sensiolabs.org/doc/usage.html#default-values
			if (isset($route['defaults'])) {
				if (!is_array($route['defaults'])) {
					throw new \LogicException('Key "defaults" must be array on node "'.$node.'" for language "'.$this->language.'".', 3);
				}
				foreach ($route['defaults'] as $arg=>$setting) {
					$ins->value($arg, $setting);
				}
			}
			// check for named route setting - see: http://silex.sensiolabs.org/doc/usage.html#named-routes
			if (isset($route['bind'])) {
				$ins->bind($route['bind']);
			}
			// check if required HTTPS
			if (isset($route['https']) and $route['https'] == true) {
				$ins->requireHttps();
			}
		}

		return $controllers;
	}
}