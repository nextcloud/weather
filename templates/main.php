<?php
\OCP\Util::addScript('weather', 'angular/angular.min');
\OCP\Util::addScript('weather', 'public/app');
\OCP\Util::addStyle('weather', 'style');
?>

<div class="ng-scope" id="app" ng-app="Weather" ng-controller="WeatherController">
	<div id="city-list-left">
		<ul class="city-list">
			<li class=city-list-item" ng-repeat="city in citys" class="{{ city.id == selectedCityId ? 'selected' : ''}}">
				<a href="#" ng-click="loadCity(city.id);">{{ city.name }}</a>
				<div class="icon-delete svn delete action" ng-click="deleteCity(city);"></div>
			</li>
			<li>
				<a href="#" ng-click="showCreateCity = true;">Add a city...</a>
				<div ng-show="showCreateCity == true" id="create-city">
					<h1>Create city</h1>
					<hr>
					<h2>Title</h2>
					<span class="city-form-error" ng-show="createCityError != ''">{{ createCityError }}</span>
					<form novalidate>
						<input type="textbox" ng-model="city.name"/>
						<input type="submit" value="Add" ng-click="createCity(city);"/>
						<input type="button" value="Cancel" ng-click="showCreateCity = false;"/>
					</form>
				</div>
			</li>
		</ul>
	</div>
	<div id="city-right" ng-show="selectedCityId != 0">
	</div>
</div>
