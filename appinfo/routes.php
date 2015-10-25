<?php
/**
 * ownCloud - Weather
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Loic Blot <loic.blot@unix-experience.fr>
 * @copyright Loic Blot 2015
 */

namespace OCA\Weather\AppInfo;

$application = new Application();

$application->registerRoutes($this, array('routes' => array(
	array('name' => 'city#index',		'url' => '/',			'verb' => 'GET'),

	array('name' => 'city#getall',		'url' => '/city/getall',	'verb' => 'GET'),
	array('name' => 'city#add',		'url' => '/city/add',		'verb' => 'POST'),
	array('name' => 'city#delete',		'url' => '/city/delete',	'verb' => 'POST'),

	array('name' => 'weather#get',		'url' => '/weather/get',	'verb' => 'GET'),

	array('name' => 'settings#homeset',	'url' => '/settings/home/set',	'verb' => 'POST'),
	array('name' => 'settings#apikeyset',	'url' => '/settings/apikey/set','verb' => 'POST'),
	array('name' => 'settings#apikeyget',	'url' => '/settings/apikey/get','verb' => 'GET'),
	array('name' => 'settings#metricset',	'url' => '/settings/metric/set','verb' => 'POST'),
	array('name' => 'settings#metricget',	'url' => '/settings/metric/get','verb' => 'GET'),
)));
?>
