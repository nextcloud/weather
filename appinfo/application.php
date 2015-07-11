<?php
/**
 * ownCloud - weather
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Loic Blot <loic.blot@unix-experience.fr>
 * @copyright Loic Blot 2015
 */


namespace OCA\Weather\AppInfo;

use \OCP\AppFramework\App;
use \OCP\IContainer;

use \OCA\Weather\Controller\CityController;
use \OCA\Weather\Controller\WeatherController;

use \OCA\Weather\Db\CityMapper;

class Application extends App {

	public function __construct (array $urlParams=array()) {
		parent::__construct('weather', $urlParams);

		$container = $this->getContainer();

		/**
		 * Core
		 */
		$container->registerService('UserId', function(IContainer $c) {
			return \OCP\User::getUser();
		});

		/**
		 * Database Layer
		 */
		$container->registerService('CityMapper', function(IContainer $c) {
			return new CityMapper($c->query('ServerContainer')->getDb());
		});

		/**
		 * Controllers
		 */
		$container->registerService('CityController', function(IContainer $c) {
			return new CityController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('UserId'),
				$c->query('CityMapper')
			);
		});

		$container->registerService('WeatherController', function(IContainer $c) {
			return new WeatherController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('UserId'),
				$c->query('CityMapper')
			);
		});
	}
}
