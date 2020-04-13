<?php
\OCP\Util::addScript('weather', 'angular.min');
\OCP\Util::addScript('weather', 'app');
\OCP\Util::addStyle('weather', 'style');
?>

<div class="ng-scope" id="app" ng-app="Weather" ng-controller="WeatherController">
	<div id="city-list-left">
		<ul class="city-list">
			<li ng-repeat="city in cities" class="city-list-item {{ city.id == selectedCityId ? 'selected' : ''}}">
				<a href="#" ng-click="loadCity(city);">{{ city.name }}</a>
				<div class="icon-delete svn delete action" ng-click="deleteCity(city);"></div>
			</li>
			<li>
				<a href="#" ng-click="city.name = ''; addCityError = ''; showAddCity = true;"><?php p($l->t('Add a city')); ?>...</a>
				<div ng-show="showAddCity == true" id="create-city">
					<h1><?php p($l->t('Add city')); ?></h1>
					<hr>
					<h2><?php p($l->t('City name')); ?></h2>
					<span class="city-form-error" ng-show="addCityError != ''">{{ addCityError }}</span>
					<form novalidate>
						<input type="textbox" ng-model="city.name"/>
						<input type="submit" value="<?php p($l->t('Add')); ?>" ng-click="addCity(city);"/>
						<input type="button" value="<?php p($l->t('Cancel')); ?>" ng-click="showAddCity = false;"/>
					</form>
				</div>
			</li>
		</ul>
		<div id="app-settings" class="ng-scope">
			<div id="app-settings-header">
				<button name="app settings" class="settings-button" data-apps-slide-toggle="#app-settings-content"><?php p($l->t('Settings')); ?></button>
			</div>
			<div style="display: none;" id="app-settings-content">
				<h2><?php p($l->t('Metric')); ?></h2>
				<select name="metric" ng-change="modifyMetric()" ng-model="metric">
					<option value="metric">°C</option>
					<option value="kelvin">°K</option>
					<option value="imperial">°F</option>
				</select>
			</div>
		</div>
	</div>
	<div id="city-right" ng-show="cityLoadError != ''">
		<span class="city-load-error">
			{{ cityLoadError }}<br /><br />
			<a href="http://home.openweathermap.org/users/sign_in" ng-show="cityLoadNeedsAPIKey == true"><?php p($l->t('Click here to get an API key')); ?></a>
		</span>
	</div>
	<div id="city-right" ng-show="cityLoadError == '' && currentCity != null" style="background-image: url('{{ owncloudAppImgPath }}{{ currentCity.image }}');">
		<div id="city-weather-panel">
			<div class="city-name">
				{{ currentCity.name }}, {{ currentCity.sys.country }}
				<img ng-show="selectedCityId == homeCity" src="{{ owncloudAppImgPath }}home-pick.png" />
				<img class="home-icon" ng-click="setHome(selectedCityId);" ng-show="selectedCityId != homeCity" src="{{ owncloudAppImgPath }}home-nopick.png" />
			</div>
			<div class="city-current-temp"><?php p($l->t('Current Temperature')); ?>: {{ currentCity.main.temp }}{{ metricRepresentation }}</div>
			<div class="city-current-temp_feelslike"><?php p($l->t('Feelslike Temperature')); ?>: {{ currentCity.main.feels_like }}{{ metricRepresentation }}</div>
			<div class="city-current-temp_min"><?php p($l->t('Minimum Temperature')); ?>: {{ currentCity.main.temp_min }}{{ metricRepresentation }}</div>
			<div class="city-current-temp_max"><?php p($l->t('Maximum Temperature')); ?>: {{ currentCity.main.temp_max }}{{ metricRepresentation }}</div>
			<div class="city-current-pressure"><?php p($l->t('Pressure')); ?>: {{ currentCity.main.pressure }} hpa</div>
			<div class="city-current-humidity"><?php p($l->t('Humidity')); ?>: {{ currentCity.main.humidity}}%</div>
			<div class="city-current-weather"><?php p($l->t('Cloudiness')); ?>: {{ currentCity.weather[0].description }}</div>
			<div class="city-current-wind"><?php p($l->t('Wind')); ?>: {{ currentCity.wind.speed }} m/s - {{ currentCity.wind.desc }}</div>
			<div class="city-current-sunrise"><?php p($l->t('Sunrise')); ?>: {{ currentCity.sys.sunrise * 1000 | date:'HH:mm' }}</div>
			<div class="city-current-sunset"><?php p($l->t('Sunset')); ?>: {{ currentCity.sys.sunset * 1000 | date:'HH:mm' }}</div>
		</div>
		<div id="city-forecast-panel">
			<table>
				<tr>
					<th><?php p($l->t('Date')); ?></th>
					<th><?php p($l->t('Current Temperature')); ?></th>
					<th><?php p($l->t('Perceptible Temperature')); ?></th>
					<th><?php p($l->t('Minimum Temperature')); ?></th>
					<th><?php p($l->t('Maximum Temperature')); ?></th>
					<th><?php p($l->t('Weather')); ?></th>
					<th><?php p($l->t('Pressure')); ?></th>
					<th><?php p($l->t('Humidity')); ?></th>
					<th><?php p($l->t('Wind')); ?></th>
				</tr>
				<tr ng-repeat="forecast in currentCity.forecast">
					<td>{{ forecast.date }}</td>
					<td>{{ forecast.temperature }}{{ metricRepresentation }}</td>
					<td>{{ forecast.temperature_feelslike }}{{ metricRepresentation }}</td>
					<td>{{ forecast.temperature_min }}{{ metricRepresentation }}</td>
					<td>{{ forecast.temperature_max }}{{ metricRepresentation }}</td>
					<td>{{ forecast.weather }}</td>
					<td>{{ forecast.pressure }} hpa</td>
					<td>{{ forecast.humidity }} %</td>
					<td>{{ forecast.wind.speed }} m/s - {{ forecast.wind.desc }}</td>
				</tr>
			</table>
		</div>
	</div>
</div>
