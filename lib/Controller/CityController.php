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
use \OCP\AppFramework\Http\StrictContentSecurityPolicy;

use \OCA\Weather\Db\CityEntity;
use \OCA\Weather\Db\CityMapper;
use \OCA\Weather\Db\SettingsMapper;
use \OCA\Weather\Controller\IntermediateController;

class CityController extends IntermediateController {

	private $userId;
	private $mapper;
	private $settingsMapper;
	private $config;

	public function __construct ($appName, IConfig $config, IRequest $request, $userId, CityMapper $mapper, SettingsMapper $settingsMapper) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->mapper = $mapper;
		$this->settingsMapper = $settingsMapper;
		$this->config = $config;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index () {
		$response = new TemplateResponse($this->appName, 'main');  // templates/main.php

		$csp = new StrictContentSecurityPolicy();
		$csp->allowEvalScript();
		$csp->allowInlineStyle();

		$response->setContentSecurityPolicy($csp);

		return $response;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function getAll() {
		$cities = $this->mapper->getAll($this->userId);
		$home = $this->settingsMapper->getHome($this->userId);
		return new JSONResponse(array(
			"cities" => $cities,
			"userid" => $this->userId,
			"home" => $home
		));
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function add ($name) {
		if (!$name) {
			return new JSONResponse(array(), Http::STATUS_BAD_REQUEST);
		}

		// Trim city name to remove unneeded spaces
		$name = trim($name);

		$cities = $this->mapper->getAll($this->userId);
		for ($i = 0; $i < count($cities); $i++) {
			if (strtolower($cities[$i]['name']) == strtolower($name)) {
				return new JSONResponse(array(), 409);
			}
		}

		$cityInfos = $this->getCityInformations($name);

		if (!$cityInfos["response"]) {
			return new JSONResponse($cityInfos, $cityInfos["code"]);
		}

		if ($id = $this->mapper->create($this->userId, $name)) {
			// Load parameter is set to true if we don't found previous cities.
			// This permit to trigger loading of the first city in UI
			return new JSONResponse(array(
				"id" => $id,
				"load" => count($cities) == 0)
			);
		}

		return new JSONResponse(array());
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function delete ($id) {
		if (!$id || !is_numeric($id)) {
			return new JSONResponse(array(), Http::STATUS_BAD_REQUEST);
		}

		$city = $this->mapper->load($id);
		if ($city['user_id'] != $this->userId) {
			return new JSONResponse(array(), 403);
		}

		$entity = new CityEntity();
		$entity->setId($id);
		$entity->setUser_id($this->userId);

		$this->mapper->delete($entity);

		return new JSONResponse(array("deleted" => true));
	}

	private function getCityInformations ($name) {
		$apiKey = $this->config->getAppValue($this->appName, 'openweathermap_api_key');
		$cityDatas = json_decode($this->curlGET(
			"http://api.openweathermap.org/data/2.5/forecast?q=".urlencode($name)."&mode=json&APPID=".urlencode($apiKey))[1],
			true);

		// If no cod we just return a 502 as the API is not responding properly
		if (!array_key_exists("cod", $cityDatas)) {
			return array("code" => 502, "response" => null);
		}
		
		if ($cityDatas['cod'] != '200') {
			return array("code" => $cityDatas['cod'], "response" =>  null, "apikey" => $apiKey);
		}

		return array("code" => 200, "response" => $cityDatas);
	}
};
?>
