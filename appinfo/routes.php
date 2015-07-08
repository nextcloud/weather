<?php
/**
 * ownCloud - owncity
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Loic Blot <loic.blot@unix-experience.fr>
 * @copyright Loic Blot 2015
 */

namespace OCA\OwnBoard\AppInfo;

$application = new Application();

$application->registerRoutes($this, array('routes' => array(
	array('name' => 'city#index',		'url' => '/',			'verb' => 'GET'),

	array('name' => 'city#get',		'url' => '/city/get',		'verb' => 'GET'),
	array('name' => 'city#getall',		'url' => '/city/getall',	'verb' => 'GET'),
	array('name' => 'city#create',		'url' => '/city/create',	'verb' => 'POST'),
	array('name' => 'city#delete',		'url' => '/city/delete',	'verb' => 'POST'),
	array('name' => 'city#update',		'url' => '/city/update',	'verb' => 'POST'),
)));
?>
