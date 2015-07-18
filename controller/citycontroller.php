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

use \OCP\IRequest;
use \OCP\AppFramework\Http\TemplateResponse;
use \OCP\AppFramework\Controller;
use \OCP\AppFramework\Http\JSONResponse;
use \OCP\AppFramework\Http;

use \OCA\Weather\Db\CityMapper;

class CityController extends Controller {

	private $userId;
	private $mapper;
	private $settingsMapper;

	public function __construct ($appName, IRequest $request, $userId, CityMapper $mapper, SettingsMapper $settingsMapper) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->mapper = $mapper;
		$this->settingsMapper = $settingsMapper;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index () {
		return new TemplateResponse($this->appName, 'main');
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

		if (!$this->getCityInformations($name)) {
			return new JSONResponse(array(), Http::STATUS_NOT_FOUND);
		}

		if ($id = $this->mapper->create($this->userId, $name)) {
			return new JSONResponse(array("id" => $id));
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

		$this->mapper->delete($id);

		return new JSONResponse(array("deleted" => true));
	}

	private function getCityInformations ($name) {
		$cityDatas = json_decode(file_get_contents("http://api.openweathermap.org/data/2.5/forecast?q=$name&mode=json"), true);
		if ($cityDatas['cod'] != '200') {
			return null;
		}

		return $cityDatas;
	}
};
?>
