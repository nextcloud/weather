<?php
/**
 * ownCloud - weather
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Loic Blot <loic.blot@unix-experience.fr>
 * @copyright Loic Blot 2015
 */

namespace OCA\Weather\Db;

use \OCP\IDb;

use \OCP\AppFramework\Db\Mapper;

class SettingsMapper extends Mapper {
	public function __construct (IDb $db) {
		parent::__construct($db, 'weather_city');
	}

	public function setHome ($userId, $cityId) {
		$this->setSetting("home", $userId, $cityId);
	}

	public function getHome ($userId) {
		return $this->getSetting($userId, "home");
	}

	public function setApiKey ($userId, $apiKey) {
		$this->setSetting("apikey", $userId, $apiKey);
	}

	public function getApiKey ($userId) {
		return $this->getSetting($userId, "apikey");
	}

	public function setMetric ($userId, $metric) {
		$this->setSetting("metric", $userId, $metric);
	}

	public function getMetric ($userId) {
		return $this->getSetting($userId, "metric");
	}

	public function setSetting ($settingName, $userId, $settingValue) {
		\OCP\DB::beginTransaction();
		$query = \OCP\DB::prepare('DELETE FROM *PREFIX*weather_config ' .
			'WHERE `user` = ? and `key` = ?');
		$query->execute(array($userId, $settingName));

		$query = \OCP\DB::prepare('INSERT INTO *PREFIX*weather_config ' .
			'(`user`,`key`,`value`) VALUES (?,?,?)');
		$query->execute(array($userId, $settingName, $settingValue));
		\OCP\DB::commit();
	}

	public function getSetting ($userId, $settingName) {
		$sql = 'SELECT value FROM ' .
			'*PREFIX*weather_config WHERE `user` = ? and `key` = ?';
		$query = \OCP\DB::prepare($sql);
		$result = $query->execute(array($userId, $settingName));

		if ($row = $result->fetchRow()) {
			return $row["value"];
		}
		return 0;
	}

};
?>
