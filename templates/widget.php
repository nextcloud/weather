<?php

?>

<div id="widget-weather">
	<div class="locationValue"></div>
	<dl>
		<dt><?php p($l->t('Temperature')); ?></dt>
		<dd><span class="temperatureValue"></span>&nbsp;<span class="temperatureRepresentation"></span></dd>

		<dt><?php p($l->t('Cloudiness')); ?></dt>
		<dd><span class="weatherValue"></span></dd>
		
		<dt><?php p($l->t('Humidity')); ?></dt>
		<dd><span class="humidityValue"></span>&nbsp;%</dd>
		
		<dt><?php p($l->t('Wind')); ?></dt>
		<dd><span class="windValue"></span>&nbsp;m/s</dd>
	</dl>
</div>
