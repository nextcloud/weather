<?php

// TODO add license 

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
				 ->setIcon('app')
				 ->setContent('widget')
				 ->setInitFunction('OCA.DashBoard.weather.getWeather');
		return $template;
	}

	/**
	 * @return WidgetTemplate
	 */
	public function getWidgetSetup(): WidgetSetup {
		$setup = new WidgetSetup();
		$setup->addSize(WidgetSetup::SIZE_TYPE_DEFAULT, 3, 2);
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

			$allCities = json_decode($cityController->getAll()->render(), true);
			$firstCity = $allCities['cities'][$allCities['home']]['name'];
			$result = json_decode($weatherController->get($firstCity)->render(), true);

			$request->addResult('location', $result['name']);
			$request->addResult('temperature', $result['main']['temp']);
			$request->addResult('weather', $result['weather'][0]['main']);
			$request->addResult('humidity', $result['main']['humidity']);
			$request->addResult('wind', $result['wind']['speed']);
		}
	}


}
?>
