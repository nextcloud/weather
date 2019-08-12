<?php

?>

<div id="widget-weather" class="weatherWidgetContents">
	<h3 class="locationValue"></h3>
	<div class="info">Updating widget…</div>
	<dl class="weatherWidgetList">
		<dt class="measurement"><?php p($l->t('Temperature')); ?></dt>
		<dd class="value"><span class="temperatureValue"></span>&nbsp;<span class="temperatureRepresentation"></span></dd>

		<dt class="measurement"><?php p($l->t('Cloudiness')); ?></dt>
		<dd class="value"><span class="weatherValue"></span></dd>
		
		<dt class="measurement"><?php p($l->t('Humidity')); ?></dt>
		<dd class="value"><span class="humidityValue"></span>&nbsp;%</dd>
		
		<dt class="measurement"><?php p($l->t('Wind')); ?></dt>
		<dd class="value"><span class="windValue"></span>&nbsp;m/s</dd>
	</dl>
</div>
