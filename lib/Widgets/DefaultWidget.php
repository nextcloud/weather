<?php

// TODO add license 

namespace OCA\Weather\Widgets;


use \OCP\AppFramework\App;
use \OCP\IContainer;

use \OCA\Weather\AppInfo\Application;
use \OCA\Weather\Controller\WeatherController;

class DefaultWidget implements IDashboardWidget {
/*
	widgetSetup() returns optional information like size of the widget, additional menu entries and background jobs:
	loadWidget($config) is called on external request (cf. requestWidget()). $config is an array that contains the current setup of the widget
	requestWidget(WidgetRequest $request) is called after the loadWidget() after a new.requestWidget(object, callback) from JavaScript
*/
	
	const WIDGET_ID = 'defaultweatherwidget';

	/**
	 * @return string
	 */
	public function getId() {
		return self::WIDGET_ID;
	}
	
	/**
	 * @return string
	 */
	public function getName() {
		return 'Weather Widget';
	}

	/**
	 * @return string
	 */
	public function getDescription() {
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
				 ->setInitFunction('OCA.Weather.widget.init');
		return $template;
	}

	/**
	 * @return WidgetTemplate
	 */
	public function getWidgetSetup(): WidgetSetup {
		$setup = new WidgetSetup();
		$setup->addSize(WidgetSetup::SIZE_TYPE_DEFAULT, 3, 2);
		$setup->addDelayedJob('OCA.Weather.widget.getWeather', 600);
		$setup->setPush('OCA.Weather.widget.push');
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

			$request->addResult('location', $this->weatherService->getWeather());
			$request->addResult('temperature', $this->weatherService->getWeather());
			$request->addResult('weather', $this->weatherService->getWeather());
			$request->addResult('humidity', $this->weatherService->getWeather());
			$request->addResult('wind', $this->weatherService->getWeather());
		}
	}


}
?>
