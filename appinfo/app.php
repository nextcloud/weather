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

namespace OCA\Weather\AppInfo;

if (class_exists('\OCP\AppFramework\App')) {
	\OCP\App::addNavigationEntry(array(
	    // the string under which your app will be referenced in owncloud
	    'id' => 'weather',

	    // sorting weight for the navigation. The higher the number, the higher
	    // will it be listed in the navigation
	    'order' => 10,

	    // the route that will be shown on startup
	    'href' => \OCP\Util::linkToRoute('weather.city.index'),

	    // the icon that will be shown in the navigation
	    // this file needs to exist in img/
	    'icon' => \OCP\Util::imagePath('weather', 'app-icon.png'),

	    // the title of your application. This will be used in the
	    // navigation or on the settings page of your app
	    'name' => \OCP\Util::getL10N('weather')->t('Weather')
	));
} else {
	$msg = 'Can not enable the OwnBoard app because the App Framework App is disabled';
	\OCP\Util::writeLog('weather', $msg, \OCP\Util::ERROR);
}
