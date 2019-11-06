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
use \OCP\AppFramework\Http\StrictContentSecurityPolicy;

use \OCA\Weather\Controller\CityController;
use \OCA\Weather\Controller\SettingsController;
use \OCA\Weather\Controller\WeatherController;

use \OCA\Weather\Db\CityMapper;
use \OCA\Weather\Db\SettingsMapper;

class Application extends App {

	public function __construct (array $urlParams=array()) {
		parent::__construct('weather', $urlParams);

		$container = $this->getContainer();

		/**
		 * Core
		 */
		$container->registerService('UserId', function(IContainer $c) {
			$user = $c->getServer()->getUserSession()->getUser();
			return $user ? $user->getUID() : null;
    });

		$container->registerService('Config', function($c) {
			return $c->query('ServerContainer')->getConfig();
		});

		$container->registerService('L10N', function($c) {
		return $c->query('ServerContainer')->getL10N($c->query('AppName'));
		});

		/**
		 * Database Layer
		 */
		$container->registerService('CityMapper', function(IContainer $c) {
			return new CityMapper($c->query('ServerContainer')->getDatabaseConnection());
		});

		$container->registerService('SettingsMapper', function(IContainer $c) {
			return new SettingsMapper($c->query('ServerContainer')->getDatabaseConnection());
		});

		/**
		 * Controllers
		 */
		$container->registerService('CityController', function(IContainer $c) {
			return new CityController(
				$c->query('AppName'),
				$c->query('Config'),
				$c->query('Request'),
				$c->query('UserId'),
				$c->query('CityMapper'),
				$c->query('SettingsMapper')
			);
		});

		$container->registerService('SettingsController', function(IContainer $c) {
			return new SettingsController(
				$c->query('AppName'),
				$c->query('Config'),
				$c->query('Request'),
				$c->query('UserId'),
				$c->query('SettingsMapper'),
				$c->query('CityMapper')
			);
		});

		$container->registerService('WeatherController', function(IContainer $c) {
			return new WeatherController(
				$c->query('AppName'),
				$c->query('Config'),
				$c->query('Request'),
				$c->query('UserId'),
				$c->query('CityMapper'),
				$c->query('SettingsMapper'),
				$c->query('L10N')
			);
		});
	}
}
