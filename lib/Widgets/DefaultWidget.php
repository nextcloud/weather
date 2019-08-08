<?php
/**
 *
 * @copyright Copyright (c) 2019, Balint Erdosi (erdosib@gmail.com)
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Weather\Widgets;

use \OCP\AppFramework\App;
use \OCP\IContainer;

use OCP\Dashboard\Model\WidgetSetup;
use OCP\Dashboard\Model\WidgetTemplate;
use OCP\Dashboard\IDashboardWidget;
use OCP\Dashboard\Model\IWidgetRequest;
use OCP\Dashboard\Model\IWidgetConfig;

use \OCA\Weather\AppInfo\Application;
use \OCA\Weather\Controller\WeatherController;

class DefaultWidget implements IDashboardWidget {
/*
	widgetSetup() returns optional information like size of the widget, additional menu entries and background jobs:
	loadWidget($config) is called on external request (cf. requestWidget()). $config is an array that contains the current setup of the widget
	requestWidget(WidgetRequest $request) is called after the loadWidget() after a new.requestWidget(object, callback) from JavaScript
*/
	
	const WIDGET_ID = 'weather';

	/**
	 * @return string
	 */
	public function getId(): string {
		return self::WIDGET_ID;
	}
	
	/**
	 * @return string
	 */
	public function getName(): string {
		return 'Weather Widget';
	}

	/**
	 * @return string
	 */
	public function getDescription(): string {
		return 'Get current weather conditions';
	}

	/**
	 * @return WidgetTemplate
	 */
	public function getWidgetTemplate(): WidgetTemplate {
		$template = new WidgetTemplate();
		$template->addCss('widget')
				 ->addJs('widget')
				 ->setIcon('icon-weather')
				 ->setContent('widget')
				 ->setInitFunction('OCA.DashBoard.weather.getWeather');
		return $template;
	}

	/**
	 * @return WidgetTemplate
	 */
	public function getWidgetSetup(): WidgetSetup {
		$setup = new WidgetSetup();
		$setup->addSize(WidgetSetup::SIZE_TYPE_MIN, 2, 1);
		$setup->addSize(WidgetSetup::SIZE_TYPE_MAX, 4, 5);
		$setup->addSize(WidgetSetup::SIZE_TYPE_DEFAULT, 2, 3);
		$setup->addDelayedJob('OCA.DashBoard.weather.getWeather', 600);
		return $setup;
	}

	/**
	 * @param IWidgetConfig $settings
	 */
	public function loadWidget(IWidgetConfig $settings) {

	}

	/**
	 * @param IWidgetRequest $request
	 */
	public function requestWidget(IWidgetRequest $request) {
		if ($request->getRequest() === 'getWeather') {

			$app = new Application();
			$container = $app->getContainer();
			$weatherController = $container->query('OCA\Weather\Controller\WeatherController');
			$cityController = $container->query('OCA\Weather\Controller\CityController');
			$settingsController = $container->query('OCA\Weather\Controller\SettingsController');

			$allCities = json_decode($cityController->getAll()->render(), true);

			$homeCityId = $allCities['home'];
			$firstCity = array_filter(
				$allCities['cities'],
				function($city) use ($homeCityId) { 
					return $city['id'] === $homeCityId; 
				}
			)[0]['name'];
	
			$result = json_decode($weatherController->get($firstCity)->render(), true);
			$metric = json_decode($settingsController->metricGet()->render(), true)['metric'];

			$request->addResult('location', $firstCity);
			$request->addResult('temperature', $result['main']['temp']);
			$request->addResult('metric', $metric);
			$request->addResult('weather', $result['weather'][0]['main']);
			$request->addResult('humidity', $result['main']['humidity']);
			$request->addResult('wind', $result['wind']['speed']);
		}
	}


}
?>
