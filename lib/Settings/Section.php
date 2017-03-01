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

use OCP\IL10N;
use OCP\Settings\ISection;

class Section implements ISection {
	/** @var IL10N */
	private $l;

	public function __construct(IL10N $l) {
		$this->l = $l;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getID() {
		return 'saml';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return $this->l->t('Weather');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPriority() {
		return 75;
	}
}

?>
