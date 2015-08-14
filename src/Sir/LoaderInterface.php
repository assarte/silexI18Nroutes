<?php
/**
 * Created by PhpStorm.
 * User: assarte
 * Date: 2015.08.13.
 * Time: 17:38
 */

namespace Sir;

/**
 * Interface LoaderInterface
 *
 * Loads multi-lingual database of routes in the structure described below (using JSON for example):
 * {
 *		'en': {
 * 			'blog': {
 * 				'path': '/blog',
 * 				'classname': 'Acme\\Controllers\\Blog',
 * 				'routes': [{
 * 					'path': '/post/{id}',
 * 					'action': 'GetPost',
 * 					'assert': {'id': '\\d+'}
 * 				}, {
 * 					'method': 'POST',
 * 					'path': '/post/{id}',
 * 					'action': 'PostPost',
 * 					'assert': {'id': '\\d+'}
 * 				}, {
 * 					'path': '/',
 * 					'action': 'GetList'
 * 				}]
 * 			},
 * 			'pagecontent': {
 * 				'path': '/page',
 * 				'classname': 'Acme\\Controller\\Page',
 * 				'default_resolver': 'GetPage',
 * 				'routes': [{
 * 					'path': '/{path}'
 * 				}, {
 * 					'path': '/{path}',
 * 					'method': 'POST',
 * 					'action': 'PostPage'
 * 				}
 * 			}
 * 		},
 * 		'hu': {
 * 			'blog': {
 * 				'path': '/naplóm',
 * 				'classname': 'Acme\\Controllers\\Blog',
 * 				'routes': [{
 * 					'path': '/bejegyzés/{id}',
 * 					'action': 'GetPost',
 * 					'assert': {'id': '\\d+'}
 * 				}, {
 * 					'method': 'POST',
 * 					'path': '/bejegyzés/{id}',
 * 					'action': 'PostPost',
 * 					'assert': {'id': '\\d+'}
 * 				}, {
 * 					'path': '/',
 * 					'action': 'GetList'
 * 				}]
 * 			},
 * 			'pagecontent': {
 * 				'path': '/oldal',
 * 				'classname': 'Acme\\Controller\\Page',
 * 				'default_resolver': 'GetPage',
 * 				'routes': [{
 * 					'path': '/{path}'
 * 				}, {
 * 					'path': '/{path}',
 * 					'method': 'POST',
 * 					'action': 'PostPage'
 * 				}
 * 			}
 * 		}
 * }
 *
 * @package Sir
 */
interface LoaderInterface {
	public function load($language);
}