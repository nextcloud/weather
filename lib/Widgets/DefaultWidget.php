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
use \OCP\AppFramework\Http;

use \OCP\IContainer;

use OCP\Dashboard\Model\WidgetSetup;
use OCP\Dashboard\Model\WidgetTemplate;
use OCP\Dashboard\IDashboardWidget;
use OCP\Dashboard\Model\IWidgetRequest;
use OCP\Dashboard\Model\IWidgetConfig;

use \OCA\Weather\AppInfo\Application;
use \OCA\Weather\Controller\WeatherController;

use \OCP\IL10N;
use \OCP\ILogger;

class DefaultWidget implements IDashboardWidget {
	
	const WIDGET_ID = 'weather';


	/** @var IL19N */
	private $l10n;
	private $logger;


	/**
	 * DefaultWidget constructor
	 * @param IL10N $l10n
	 */
	public function __construct(ILogger $logger, IL10N $l10n) {
		$this->l10n = $l10n;
		$this->logger = $logger;
	}
	
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
		return $this->l10n->t('Weather');
	}

	/**
	 * @return string
	 */
	public function getDescription(): string {
		return $this->l10n->t('Watch the weather directly on your Nextcloud.');
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

			if (count($allCities) == 0) {
				$request->addResult('error',  $this->l10n->t('Please make sure you select cities in the Weather app.'));
				return;
			}

			$homeCityId = $allCities['home'];
			$homeCityArray = array_filter(
				$allCities['cities'],
				function($city) use ($homeCityId) { 
					return $city['id'] === $homeCityId; 
				}
			);

			if (count($homeCityArray) != 1) {
				$request->addResult('error',  $this->l10n->t('Please make sure you select a home city in the Weather app.'));
				return;
			}

			$homeCity = $homeCityArray[0]['name'];

			$resultJSONResponse = $weatherController->get($homeCity);
			if ($resultJSONResponse->getStatus() != Http::STATUS_OK) {
				$request->addResult('error',  $this->l10n->t('Failed to get city weather informations. Please contact your administrator'));
				return;
			}

			$result = json_decode($resultJSONResponse->render(), true);
			$metric = json_decode($settingsController->metricGet()->render(), true)['metric'];

			$request->addResult('location', $homeCity);
			$request->addResult('temperature', $result['main']['temp']);
			$request->addResult('metric', $metric);
			$request->addResult('weather', $result['weather'][0]['description']);
			$request->addResult('humidity', $result['main']['humidity']);
			$request->addResult('wind', $result['wind']['speed']);
		}
	}


}
?>
