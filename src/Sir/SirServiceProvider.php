<?php
/**
 * Created by PhpStorm.
 * User: assarte
 * Date: 2015.08.13.
 * Time: 17:15
 */

namespace Sir;

use Silex\Application;
use Silex\ServiceProviderInterface;

class SirServiceProvider implements ServiceProviderInterface {
	public function register(Application $app)
	{
		$app['sir'] = new LoaderManager($app);
	}

	public function boot(Application $app)
	{
	}
}