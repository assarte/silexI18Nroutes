<?php
/**
 * Created by PhpStorm.
 * User: assarte
 * Date: 2015.08.14.
 * Time: 18:57
 */

namespace Sir;

use Symfony\Component\HttpFoundation\Request;

trait SirTrait {
	/**
	 * This must be registered using $app->before()!
	 * @param Request $request
	 */
	public function sirBeforeMiddleware(Request $request) {
		$routeName = $request->get('_route');
		$routeArgs = $request->get('_route_params');
		$getParams = $request->query->all();
		$this['session']->set('sir_route', array(
			'name'	=> $routeName,
			'args'	=> $routeArgs,
			'get'	=> $getParams
		));
	}

	/**
	 * Initializes the redirect controller used by redirectSir()
	 */
	public function initRedirector() {
		$app = $this;
		$this->get('/_sir_redirect/{locale}/{name}', function(Request $request, $locale, $name) use ($app) {
			$args = json_decode($request->get('args'), true);
			$get = json_decode($request->get('get'), true);
			$queryString = is_array($get) && count($get) > 0? '?'.http_build_query($get) : '';

			$app['translator']->setLocale($locale);

			$response = $app->redirect($app['url_generator']->generate($name, $args).$queryString);
			$response->headers->set('X-Sir-Redirection', '1');

			return $response;
		});
	}

	/**
	 * Returns currently used route name and arguments data
	 * @return array Keys are: name, args, get (listing HTTP GET parameters)
	 */
	public function getSirRoute() {
		$sirRoute = $this['session']->get('sir_route');

		return $sirRoute;
	}

	/**
	 * Returns redirection response to changed locale for current route. URLGeneratorServiceProvider must be registered!
	 * @param string $toLocale The locale which will be used for content redirection
	 * @param string|null $routeName Route name or leave as NULL to use current route
	 * @param array $args Arguments of route or leave to use current route's arguments if any
	 * @param array $get The HTTP GET parameters used when redirecting - leave to use current route's
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function redirectSir($toLocale, $routeName=null, array $args=array(), array $get=array()) {
		$sirRoute = $this->getSirRoute();
		if ($routeName === null) {
			if ($sirRoute === null) throw new \LogicException('SIR redirect must be initialized by Application::sirBeforeMiddleware().');
			$routeName = $sirRoute['name'];
			$args = $sirRoute['args'];
			$get = $sirRoute['get'];
		} else if (count($args) == 0) {
			if ($sirRoute === null) throw new \LogicException('SIR redirect must be initialized by Application::sirBeforeMiddleware().');
			$args = $sirRoute['args'];
			$get = $sirRoute['get'];
		} else if (count($get) == 0) {
			if ($sirRoute === null) throw new \LogicException('SIR redirect must be initialized by Application::sirBeforeMiddleware().');
			$get = $sirRoute['get'];
		}

		return $this->redirect(
			'/_sir_redirect/'.$toLocale.'/'.$routeName.
			'?args='.json_encode($args).
			'&get='.json_encode($get)
		);
	}
}