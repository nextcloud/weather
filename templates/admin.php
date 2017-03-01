<div id="weather" class="section">
	<h2><?php p($l->t('Weather')) ?></h2>
	<p>
		<label>
			<span><?php p($l->t('OpenWeatherMap API Key')) ?></span>
			<input id="openweathermap-api-key" type="text" value="<?php p($_['openweathermap_api_key']) ?>" />
		</label>
		<input type="submit" id="submitOWMKey" value="<?php p($l->t('Save')); ?>"/>
	</p>
</div>
