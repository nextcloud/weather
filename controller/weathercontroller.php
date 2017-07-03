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

namespace OCA\Weather\Controller;

use \OCP\IConfig;
use \OCP\IRequest;
use \OCP\AppFramework\Http\TemplateResponse;
use \OCP\AppFramework\Controller;
use \OCP\AppFramework\Http\JSONResponse;
use \OCP\AppFramework\Http;

use \OCA\Weather\Db\CityMapper;
use \OCA\Weather\Db\SettingsMapper;

use \OCA\Weather\Controller\IntermediateController;

class WeatherController extends IntermediateController {

	private $userId;
	private $mapper;
	private $settingsMapper;
	private $metric;
	private static $apiWeatherURL = "http://api.openweathermap.org/data/2.5/weather?mode=json&q=";
	private static $apiForecastURL = "http://api.openweathermap.org/data/2.5/forecast?mode=json&q=";

	public function __construct ($appName, IConfig $config, IRequest $request, $userId, CityMapper $mapper, SettingsMapper $settingsMapper) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->mapper = $mapper;
		$this->settingsMapper = $settingsMapper;
		$this->metric = $settingsMapper->getMetric($this->userId);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function get($name) {
		$cityInfos = $this->getCityInformations($name);
		if (!$cityInfos) {
			return new JSONResponse(array(), $this->errorCode);
		}
		return new JSONResponse($cityInfos);
	}

	private function getCityInformations ($name) {
		$apiKey = $this->config->getAppValue($this->appName, 'openweathermap_api_key');
		$name = preg_replace("[ ]",'%20',$name);
		$reqContent = $this->curlGET(WeatherController::$apiWeatherURL.$name."&APPID=".$apiKey."&units=".$this->metric);
		if ($reqContent[0] != Http::STATUS_OK) {
			$this->errorCode = $reqContent[0];
			return null;
		}

		$cityDatas = json_decode($reqContent[1], true);
		$cityDatas["forecast"] = array(); 
		$forecast = json_decode(file_get_contents(WeatherController::$apiForecastURL.$name."&APPID=".$apiKey."&units=".$this->metric), true);
		if ($forecast['cod'] == '200' && isset($forecast['cnt']) && is_numeric($forecast['cnt'])) {
			// Show only 8 values max
			// @TODO: setting ?
			$maxFC = $forecast['cnt'] > 8 ? 8 : $forecast['cnt'];
			for ($i = 0; $i < $maxFC; $i++) {
				$cityDatas['forecast'][] = array(
					'hour' => $forecast['list'][$i]['dt'],
					'weather' => $forecast['list'][$i]['weather'][0]['description'],
					'temperature' => $forecast['list'][$i]['main']['temp'],
					'pressure' => $forecast['list'][$i]['main']['pressure'],
					'wind' => array(
						'speed' => $forecast['list'][$i]['wind']['speed'],
						'desc' => $this->windDegToString($forecast['list'][$i]['wind']['deg'])
					)
				);
			}
		}


		return $cityDatas;
	}

	private static function windDegToString($deg) {
		if ($deg > 0 && $deg < 23 ||
			$deg > 333) {
			return "North";
		}
		else if ($deg > 22 && $deg < 67) {
			return "North-East";
		}
		else if ($deg > 66 && $deg < 113) {
			return "East";
		}
		else if ($deg > 112 && $deg < 157) {
			return "South-East";
		}
		else if ($deg > 156 && $deg < 201) {
			return "South";
		}
		else if ($deg > 200 && $deg < 245) {
			return "South-West";
		}
		else if ($deg > 244 && $deg < 289) {
			return "West";
		}
		else if ($deg > 288 && $deg < 334) {
			return "North-West";
		}
	}
};
?>
