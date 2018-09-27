<?php
/**
 * ownCloud - weather
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Loic Blot <loic.blot@unix-experience.fr>
 * @copyright Loic Blot 2015
 * @copyright Loic Blot 2018
 */

namespace OCA\Weather\Db;

use \OCP\IDBConnection;

use \OCP\AppFramework\Db\Mapper;

class CityMapper extends Mapper {
        public function __construct (IDBConnection $db) {
                parent::__construct($db, 'weather_city');
        }

        public function load ($id) {
                $sql = "SELECT id, name, user_id FROM *PREFIX*weather_city WHERE `id` ='" . $id . "'";
                $result = $this->db->executequery($sql);

                if ($row = $result->fetch()) {
                        return $row;
                }
                return null;
        }

        public function count() {
                $sql = "SELECT count(*) AS ct FROM *PREFIX*weather_city WHERE `user_id` ='" . $userId . "'";
                $result = $this->db->executequery($sql);
                if ($row = $result->fetch()) {
                        return $row['ct'];
                }
                return 0;
        }

        public function exists ($id) {
                return ($this->load($id));
        }

        public function getAll ($userId) {
                $sql = "SELECT id, name FROM *PREFIX*weather_city WHERE `user_id` = '" . $userId . "'";
                $result = $this->db->executequery($sql);

                $cities = array();
                while ($row = $result->fetch()) {
                        $cities[] = $row;
                }
                return $cities;
        }

        public function create ($userId, $name) {
                $this->db->beginTransaction();
                $sql = "INSERT INTO *PREFIX*weather_city(`user_id`, `name`) VALUES ('" . $userId . "','" . $name . "')";
                $this->db->executequery($sql);
                $this->db->commit();

                $sql = "SELECT max(id) as maxid FROM *PREFIX*weather_city WHERE `user_id` = '" . $userId . "' and `name` = '" . $name . "'";
                $result = $this->db->executequery($sql);

                if ($row = $result->fetch()) {
                        return $row['maxid'];
                }
                return null;
        }
};
?>
