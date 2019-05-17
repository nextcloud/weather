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
use \OCP\IL10N;

use \OCA\Weather\Db\CityMapper;
use \OCA\Weather\Db\SettingsMapper;

use \OCA\Weather\Controller\IntermediateController;

class WeatherController extends IntermediateController {

	private $userId;
	private $mapper;
	private $settingsMapper;
	private $metric;
	private $config;
  private $trans;
	private static $apiWeatherURL = "http://api.openweathermap.org/data/2.5/weather?mode=json&q=";
	private static $apiForecastURL = "http://api.openweathermap.org/data/2.5/forecast?mode=json&q=";

	public function __construct ($appName, IConfig $config, IRequest $request, $userId, CityMapper $mapper, SettingsMapper $settingsMapper, IL10N $trans) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->mapper = $mapper;
		$this->settingsMapper = $settingsMapper;
		$this->metric = $settingsMapper->getMetric($this->userId);
		$this->config = $config;
		$this->trans = $trans;
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

  public function getLanguageCode() {
        return $this->trans->getLanguageCode();
  }

	private function getCityInformations ($name) {

		$apiKey = $this->config->getAppValue($this->appName, 'openweathermap_api_key');
		$name = preg_replace("[ ]",'%20',$name);

		$openWeatherMapLang = array("ar", "bg", "ca", "cz", "de", "el", "en", "fa", "fi", "fr", "gl", "hr", "hu", "it", "ja", "kr", "la", "lt", "mk", "nl", "pl", "pt", "ro", "ru", "se", "sk", "sl", "es", "tr", "ua", "vi");
		$currentLang = \OC::$server->getL10N('core')->getLanguageCode();

		if (preg_match("/_/i", $currentLang)) {
			$currentLang = strstr($currentLang, '_', true);
		}

		if (in_array($currentLang, $openWeatherMapLang)) {
			$reqContent = $this->curlGET(WeatherController::$apiWeatherURL.$name."&APPID=".$apiKey."&units=".$this->metric."&lang=".$currentLang);
		}
		else {
			$reqContent = $this->curlGET(WeatherController::$apiWeatherURL.$name."&APPID=".$apiKey."&units=".$this->metric);
		}

		if ($reqContent[0] != Http::STATUS_OK) {
			$this->errorCode = $reqContent[0];
			return null;
		}

		$cityDatas = json_decode($reqContent[1], true);
		$cityDatas["forecast"] = array();

		if (in_array($currentLang, $openWeatherMapLang)) {
			$forecast = json_decode(file_get_contents(WeatherController::$apiForecastURL.$name."&APPID=".$apiKey."&units=".$this->metric."&lang=".$currentLang), true);
		}
		else {
			$forecast = json_decode(file_get_contents(WeatherController::$apiForecastURL.$name."&APPID=".$apiKey."&units=".$this->metric), true);
		}

		if ($forecast['cod'] == '200' && isset($forecast['cnt']) && is_numeric($forecast['cnt'])) {
			// Show only 8 values max
			// @TODO: setting ?
			$maxFC = $forecast['cnt'] > 40 ? 40 : $forecast['cnt'];
			for ($i = 0; $i < $maxFC; $i++) {
				$cityDatas['forecast'][] = array(
					'date' => $this->UnixTimeToString($forecast['list'][$i]['dt']),
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

	private function windDegToString($deg) {

		if ($deg > 0 && $deg < 23 ||
			$deg > 333) {
			return $this->trans->t('North');
		}
		else if ($deg > 22 && $deg < 67) {
			return $this->trans->t('North-East');
		}
		else if ($deg > 66 && $deg < 113) {
			return $this->trans->t('East');
		}
		else if ($deg > 112 && $deg < 157) {
			return $this->trans->t('South-East');
		}
		else if ($deg > 156 && $deg < 201) {
			return $this->trans->t('South');
		}
		else if ($deg > 200 && $deg < 245) {
			return $this->trans->t('South-West');
		}
		else if ($deg > 244 && $deg < 289) {
			return $this->trans->t('West');
		}
		else if ($deg > 288 && $deg < 334) {
			return $this->trans->t('North-West');
		}
	}

	private function UnixTimeToString($unixtime) {

		if (date("l", $unixtime) == "Monday") {
			return $this->trans->t('Monday') . " " . date("H:i",$unixtime);
		}
		else if (date("l", $unixtime) == "Tuesday") {
			return $this->trans->t('Tuesday') . " " . date("H:i",$unixtime);
		}
		else if (date("l", $unixtime) == "Wednesday") {
			return $this->trans->t('Wednesday') . " " . date("H:i",$unixtime);
		}
		else if (date("l", $unixtime) == "Thursday") {
			return $this->trans->t('Thursday') . " " . date("H:i",$unixtime);
		}
		else if (date("l", $unixtime) == "Friday") {
			return $this->trans->t('Friday') . " " . date("H:i",$unixtime);
		}
		else if (date("l", $unixtime) == "Saturday") {
			return $this->trans->t('Saturday') . " " . date("H:i",$unixtime);
		}
		else if (date("l", $unixtime) == "Sunday") {
			return $this->trans->t('Sunday') . " " . date("H:i",$unixtime);
		}
	}
};

?>
