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

class CityMapper extends Mapper {
	public function __construct (IDb $db) {
		parent::__construct($db, 'weather_city');
	}

	public function load ($id) {
		$sql = 'SELECT id, name, user_id FROM ' .
			'*PREFIX*weather_city WHERE id = ?';
		$query = \OCP\DB::prepare($sql);
		$result = $query->execute(array($id));

		if ($row = $result->fetchRow()) {
			return $row;
		}
		return null;
	}

	public function exists ($id) {
		return ($this->load($id));
	}

	public function getAll ($userId) {
		$sql = 'SELECT id, name FROM ' .
			'*PREFIX*weather_city WHERE user_id = ?';
		$query = \OCP\DB::prepare($sql);
		$result = $query->execute(array($userId));

		$cities = array();
		while ($row = $result->fetchRow()) {
			$cities[] = $row;
		}
		return $cities;
	}

	public function create ($userId, $name) {
		\OCP\DB::beginTransaction();
		$query = \OCP\DB::prepare('INSERT INTO *PREFIX*weather_city ' .
			'(user_id, name) VALUES (?,?)');
		$query->execute(array($userId, $name));
		\OCP\DB::commit();

		$sql = 'SELECT max(id) as maxid FROM ' .
			'*PREFIX*weather_city WHERE user_id = ? and name = ?';
		$query = \OCP\DB::prepare($sql);
		$result = $query->execute(array($userId, $name));

		if ($row = $result->fetchRow()) {
			return $row['maxid'];
		}
		return null;
	}
};
?>
