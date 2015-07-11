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

class WeatherController extends Controller {

	private $userId;
	private $mapper;

	public function __construct ($appName, IRequest $request, $userId, CityMapper $mapper) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->mapper = $mapper;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function get($name) {
		$cityInfos = $this->getCityInformations($name);
		if (!$cityInfos) {
			return new JSONResponse(array(), Http::STATUS_NOT_FOUND);
		}
		return new JSONResponse($cityInfos);
	}

	private function getCityInformations ($name) {
		// @TODO setting for metric
		$cityDatas = json_decode(file_get_contents("http://api.openweathermap.org/data/2.5/weather?q=$name&mode=json&units=metric"), true);
		if ($cityDatas['cod'] != '200') {
			return null;
		}

		return $cityDatas;
	}
};
?>
