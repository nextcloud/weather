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

use \OCA\Weather\Db\SettingsMapper;
use \OCA\Weather\Db\CityMapper;

class SettingsController extends Controller {

	private $userId;
	private $mapper;

	public function __construct ($appName, IRequest $request, $userId, SettingsMapper $mapper, CityMapper $cityMapper) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->mapper = $mapper;
		$this->cityMapper = $cityMapper;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function homeSet ($city) {
		if (!$city || !is_numeric($city)) {
			return new JSONResponse(array(), Http::STATUS_BAD_REQUEST);
		}

		if (!$this->cityMapper->exists($city)) {
			return new JSONResponse(array(), Http::STATUS_NOT_FOUND);
		}

		$this->mapper->setHome($this->userId, $city);
		return new JSONResponse(array("set" => true));
	}
};
?>
