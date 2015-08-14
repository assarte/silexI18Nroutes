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
				'assert': {'id': '\\d+'},
				'bind': 'blog_get_post'
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
				'assert': {'id': '\\d+'},
				'bind': 'blog_get_post'
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
## Possible route items options
 * `path`: The path used for the route. This is relative to the parent node's path.
 * `method`: One or more HTTP methods, separated by `|`, to register the route for.
 * `action`: Named action method (or in "class::method"-form) which would be executed on this route.
 * `convert`: Array of argument converters - see: http://silex.sensiolabs.org/doc/usage.html#route-variable-converters
 * `assert`: Array of argument assertions - see: http://silex.sensiolabs.org/doc/usage.html#requirements
 * `defaults`: Array of argument default values - see: http://silex.sensiolabs.org/doc/usage.html#default-values
 * `bind`: Name of route - see: http://silex.sensiolabs.org/doc/usage.html#named-routes
 * `https`: Define as `true` if want to require HTTPS to be used, `false` to deny HTTPS

## Getting more help on handling I18N routes
By using `Sir\SirTrait` trait in your `Application` you can get help for multi-lingual content redirection when switching between languages.

### Initialize the trait
You must be use the `before` middleware of Silex for getting use the helper methods:
```php
...
// instancing your Silex application, etc.
...
$app->before(function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
	$app->sirBeforeMiddleware(); // storing important routing data in session
});
$app->initRedirector(); // adding redirect controller used by redirectSir() method
...
```
Now you can get the most important routing information whenever and wherever you want by calling `getSirRoute()`:
```php
...
$routeData = $app->getSirRoute();
/*
	Could contains something like:
	- name: blog_get_post
	- args:
		- id: 42
	- get:
		- highlight: "searched,keywords,list"
*/
...
```
...and can use `redirectSir()` for generating a redirection response to switching locale and redirect to the locale-equivalent of current route:
```php
...
// any controller code
...
return $app->redirectSir('hu'); // This will be changes current locale to "hu" and redirects the user agent
...
```
The method `redirectSir()` has 3 more arguments detailed below:
 * `$routeName`: for defining a different route than the current one
 * `$args`: for defining different route arguments than the currents are
 * `$get`: for defining different HTTP GET parameters than the currents are

On redirection, SIR adding an `X-Sir-Redirection` HTTP header with value of `1` and `_locale_switched_from` GET parameter to the response, this could be checked on redirected target to be getting informed about the reason of redirection.

## Suggestions for usage
`\Sir\ArrayLoader` could be used best for loading static routes from configuration files, while `\Sir\CallbackLoader` could be better for use as dynamic contents route provider eg. from database storage using any ORM or pure PHP to access to it.

There is an easy way for defining custom loader classes by implementing `\Sir\LoaderInterface`.

# Feedback notice
This is a flexible but very light-weight solution for handling multi-lingual routes, not a space-ship. Please, consider use it as is.

Any constructive feedbacks, suggestions/contributing kindly welcomed by Assarte (the author)!