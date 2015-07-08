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
};
?>
