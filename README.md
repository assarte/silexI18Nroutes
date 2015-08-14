# silexI18Nroutes
A simple Silex service provider for declaring multi-lingual (I18N) routes

## Usage
First: register the provider
```php
...
$app->register(new \Sir\SirServiceProvider($app));
...
```

You can define multiple route configurations loader. For example:
```php
...
// SIR registered before
...
$routesData = json_decode(file_get_contents('routes.json'), true);
$app['sir']->add(new \Sir\ArrayLoader($routesData));
$app['sir']->add(new \Sir\CallbackLoader(function($language) use ($app) {
	if (!isset($app['i18nroutes'][$language])) throw new \Exception();
	return $app['i18nroutes'][$language];
});
...
$app['sir']->loadRoutes('en');
$app->mount($app['sir']->getNodeRoute('blog'), \Acme\Controllers\BlogActions::init($app));
$app->mount($app['sir']->getNodeRoute('pagecontent'), \Acme\Controllers\PageActions::init($app));
...
```
A possible code for blog controller collection is (`<...>/Controllers/BlogActions.php`):
```php
namespace Acme\Controllers

use Silex\Application;
use Silex\ControllerCollection;

class BlogActions {
	/**
	 * @param Application $app
	 * @return ControllerCollection
	 */
	public static function init(Application $app) {
		// loads all routes defined for "blog" node, used classes and defined actions must be exists
		$controllers = $app['sir']->registerRoutes('blog');
		return $controllers;
	}
}
```

Example contents of `routes.json` file:
```js
{
	'en': {
		'blog': {
			'path': '/blog',
			'controller': 'Acme\\Controllers\\Blog\\',
			'routes': [{
				'path': '/post/{id}',
				'action': 'PostsAction::get',
				'assert': {'id': '\\d+'}
			}, {
				'method': 'POST',
				'path': '/post/{id}',
				'action': 'PostsAction::post',
				'assert': {'id': '\\d+'}
			}, {
				'path': '/',
				'action': 'PostsAction::list'
			}]
		},
		'pagecontent': {
			'path': '/page',
			'controller': 'Acme\\Controllers\\Page\\',
			'default_resolver': 'PagesAction::get',
			'routes': [{
				'path': '/{path}'
			}, {
				'path': '/{path}',
				'method': 'POST',
				'action': 'PagesAction::post'
			}
		}
	},
	'hu': {
		'blog': {
			'path': '/naplóm',
			'controller': 'Acme\\Controllers\\Blog\\',
			'routes': [{
				'path': '/bejegyzés/{id}',
				'action': 'PostsAction::get',
				'assert': {'id': '\\d+'}
			}, {
				'method': 'POST',
				'path': '/bejegyzés/{id}',
				'action': 'PostsAction::post',
				'assert': {'id': '\\d+'}
			}, {
				'path': '/',
				'action': 'GetList'
			}]
		},
		'pagecontent': {
			'path': '/oldal',
			'controller': 'Acme\\Controllers\\Page\\',
			'default_resolver': 'PagesAction::get',
			'routes': [{
				'path': '/{path}'
			}, {
				'path': '/{path}',
				'method': 'POST',
				'action': 'PagesAction::post'
			}
		}
	}
}
```
## Suggestions for usage
`\Sir\ArrayLoader` could be used best for loading static routes from configuration files, while `\Sir\CallbackLoader` could be better for use as dynamic contents route provider eg. from database storage using any ORM or pure PHP to access to it.

There is an easy way for defining custom loader classes by implementing `\Sir\LoaderInterface`.

# Disclaimer
This is a flexible but very light-weight solution for handling multi-lingual routes, not a space-ship. Please, consider use it as is.

Any constructive feedbacks, suggestions/contributing kindly welcomed by Assarte (the author)!