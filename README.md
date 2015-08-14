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
```
Example contents of `routes.json` file:
```js
{
	'en': {
		'blog': {
			'path': '/blog',
			'classname': 'Acme\\Controllers\\Blog',
			'routes': [{
				'path': '/post/{id}',
				'action': 'GetPost',
				'assert': {'id': '\\d+'}
			}, {
				'method': 'POST',
				'path': '/post/{id}',
				'action': 'PostPost',
				'assert': {'id': '\\d+'}
			}, {
				'path': '/',
				'action': 'GetList'
			}]
		},
		'pagecontent': {
			'path': '/page',
			'classname': 'Acme\\Controller\\Page',
			'default_resolver': 'GetPage',
			'routes': [{
				'path': '/{path}'
			}, {
				'path': '/{path}',
				'method': 'POST',
				'action': 'PostPage'
			}
		}
	},
	'hu': {
		'blog': {
			'path': '/naplóm',
			'classname': 'Acme\\Controllers\\Blog',
			'routes': [{
				'path': '/bejegyzés/{id}',
				'action': 'GetPost',
				'assert': {'id': '\\d+'}
			}, {
				'method': 'POST',
				'path': '/bejegyzés/{id}',
				'action': 'PostPost',
				'assert': {'id': '\\d+'}
			}, {
				'path': '/',
				'action': 'GetList'
			}]
		},
		'pagecontent': {
			'path': '/oldal',
			'classname': 'Acme\\Controller\\Page',
			'default_resolver': 'GetPage',
			'routes': [{
				'path': '/{path}'
			}, {
				'path': '/{path}',
				'method': 'POST',
				'action': 'PostPage'
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