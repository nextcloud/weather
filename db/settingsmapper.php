<?php
/**
 * ownCloud - weather
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Loic Blot <loic.blot@unix-experience.fr>
 * @copyright Loic Blot 2015
 * @copyright e-alfred 2018 
 */

namespace OCA\Weather\Db;

use \OCP\IDBConnection;

use \OCP\AppFramework\Db\Mapper;

class SettingsMapper extends Mapper {
        public function __construct (IDBConnection $db) {
                parent::__construct($db, 'weather_config');
        }

        public function setHome ($userId, $cityId) {
                $this->setSetting("home", $userId, $cityId);
        }

        public function getHome ($userId) {
                return $this->getSetting($userId, "home");
        }

        public function setMetric ($userId, $metric) {
                $this->setSetting("metric", $userId, $metric);
        }

        public function getMetric ($userId) {
                return $this->getSetting($userId, "metric");
        }

        public function setSetting ($settingName, $userId, $settingValue) {
                $sql = "DELETE FROM *PREFIX*weather_config  WHERE `user` = '" . $userId . "' and `key` = '" . $settingName . "'";
                $this->db->executequery($sql);

                $sql = "INSERT INTO *PREFIX*weather_config (`user`,`key`,`value`) VALUES ('" . $userId . "','" . $settingName . "','" . $settingValue . "')";
                $this->db->executequery($sql);
        }

        public function getSetting ($userId, $settingName) {
                $sql = "SELECT value FROM *PREFIX*weather_config WHERE `user` ='" . $userId . "' and `key` ='" . $settingName . "'";
                $result = $this->db->executeQuery($sql);

                if ($row = $result->fetch()) {
                        return $row["value"];
                }
                return 0;
        }

};
?>
