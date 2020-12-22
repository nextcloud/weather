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

use \OCP\AppFramework\Db\Entity;

class CityEntity extends Entity {
	public $id;
	public $name;
	public $user_id;

	public function __construct() {
		$this->addType('id', 'integer');
		$this->addType('user_id', 'integer');
	}
}

?>
