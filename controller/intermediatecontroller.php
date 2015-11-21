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
use \OCP\AppFramework\Controller;
use \OCA\Weather\Controller\IntermediateController;

class IntermediateController extends Controller {
	private $curl;

	public function __construct ($appName, IRequest $request) {
		parent::__construct($appName, $request);
		$this->curl = curl_init();
	}

	public function __destruct () {
                curl_close($this->curl);
        }

	protected function curlGET ($URL) {
		curl_setopt($this->curl, CURLOPT_URL, $URL);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($this->curl);
		return array(curl_getinfo($this->curl, CURLINFO_HTTP_CODE), $output);
	}

};
?>
