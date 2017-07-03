<?php
/**
 * ownCloud - Weather
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Loic Blot <loic.blot@unix-experience.fr>
 * @copyright Loic Blot 2017
 */

namespace OCA\Weather\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IL10N;
use OCP\IConfig;
use OCP\Settings\ISettings;

class AdminSettings implements ISettings {
	/** @var IConfig */
	private $config;

	/** @var IL10N */
	private $l;

	/**
	 * @param IL10N $l10n
	 */
	public function __construct(IConfig $config, IL10N $l10n) {
		$this->l = $l10n;
		$this->config = $config;
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm() {
		/*$params = [
			"openweathermap_api_key" => $this->config->getAppValue('weather', 'openweathermap_api_key', ''),
		];

		return new TemplateResponse('weather', 'admin', $params);
	*/	return new TemplateResponse('weather', 'admin', []);
	}

	/**
	 * @return string the section ID, e.g. 'sharing'
	 */
	public function getSection() {
		return 'additional';
	}

	/**
	 * @return int whether the form should be rather on the top or bottom of
	 * the admin section. The forms are arranged in ascending order of the
	 * priority values. It is required to return a value between 0 and 100.
	 *
	 * keep the server setting at the top, right after "server settings"
	 */
	public function getPriority() {
		return 50;
	}
};
?>
