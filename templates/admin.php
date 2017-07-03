<?php

\OCP\Util::addScript('weather', 'admin');

/** @var $l \OCP\IL10N */
/** @var $_ array */

?>

<div id="weather" class="section">
	<h2><?php p($l->t('Weather')) ?></h2>
	<p>
		<label for="openweathermap-api-key"><?php p($l->t('OpenWeatherMap API Key')) ?></label>
		<br />
		<input id="openweathermap-api-key" type="text" value="<?php p($_['openweathermap_api_key']) ?>" />
		<input type="submit" id="submitOWMApiKey" value="<?php p($l->t('Save')); ?>"/>
	</p>
</div>

