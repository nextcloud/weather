/**
 * ownCloud - Weather
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Loic Blot <loic.blot@unix-experience.fr>
 * @copyright Loic Blot 2014-2015
 */


var app = angular.module('Weather', []);

var g_error500 = t('weather', 'Fatal Error: please check your nextcloud.log and send a bug report here: https://github.com/nextcloud/weather/issues');

function undef (obj) {
	return typeof obj === 'undefined' || obj === undefined;
}

function emptyStr (obj) {
	return undef(obj) || obj == '';
}

app.controller('WeatherController', ['$scope', '$interval', '$timeout', '$compile', '$http',
	function ($scope, $interval, $timeout, $compile, $http) {
		$scope.owncloudAppImgPath = '';
		$scope.userId = '';
		$scope.metric = 'metric';
		$scope.metricRepresentation = '°C';
		$scope.cities = [];
		$scope.showAddCity = false;
		$scope.addCityError = '';

		$scope.cityLoadError = '';
		$scope.cityLoadNeedsAPIKey = false;
		$scope.currentCity = null;
		$scope.selectedCityId = 0;
		$scope.domCity = null;
		$scope.homeCity = '';

		$scope.imageMapper = {
			"Clear": "sun.png",
			"Clouds": "clouds.png",
			"Drizzle": "drizzle.jpg",
			"Smoke": "todo.png",
      "Dust": "todo.png",
      "Sand": "sand.jpg",
      "Ash": "todo.png",
      "Squall": "todo.png",
      "Tornado": "tornado.jpg",
			"Haze": "mist.png",
			"Mist": "mist.png",
			"Rain": "rain.jpg",
			"Snow": "snow.png",
			"Thunderstorm": "thunderstorm.png",
      "Fog": "fog.png",
		}

		// Reload weather information each minute
		$interval(function () {
			if ($scope.currentCity != null) {
				$scope.loadCity($scope.domCity);
			}
		}, 60000);

		// timeout functions internal calls cannot be serialized
		$timeout(function () {
			var imgPath = OC.filePath('weather','img','').replace('index.php/','');
			$scope.owncloudAppImgPath = imgPath;
			$scope.loadCities();
		});

		$timeout(function () { $scope.loadMetric(); });

		$scope.mapMetric = function () {
			if ($scope.metric == 'kelvin') {
				$scope.metricRepresentation = '°K';
			}
			else if ($scope.metric == 'imperial') {
				$scope.metricRepresentation = '°F';
			}
			else {
				$scope.metric = 'metric';
				$scope.metricRepresentation = '°C';
			}
		};

		$scope.modifyMetric = function () {
			$http.post(OC.generateUrl('/apps/weather/settings/metric/set'), {'metric': $scope.metric}).
			then(function (r) {
				if (r.data != null && !undef(r.data['set'])) {
					$scope.mapMetric();
					$scope.loadCity($scope.domCity);
				}
				else {
					$scope.settingError = t('weather', 'Failed to set metric. Please contact your administrator');
				}
			},
			function (r) {
				if (r.status == 404) {
					$scope.settingError = t('weather', 'This metric is not known.');
				}
				else {
					$scope.settingError = g_error500;
				}
			});
		}

		$scope.loadMetric = function () {
			$http.get(OC.generateUrl('/apps/weather/settings/metric/get')).
			then(function (r) {
				if (!undef(r.data['metric'])) {
					$scope.metric = r.data['metric'];
					$scope.mapMetric();
				}
			},
			function (r) {
				$scope.fatalError();
			});
		};

		$scope.loadCities = function () {
			$http.get(OC.generateUrl('/apps/weather/city/getall')).
			then(function (r) {
				if (!undef(r.data['cities'])) {
					$scope.cities = r.data['cities'];
				}

				if (!undef(r.data['userid'])) {
					$scope.userId = r.data['userid'];
				}

				if (!undef(r.data['home'])) {
					$scope.homeCity = r.data['home'];
					if ($scope.homeCity) {
						for (var i = 0; i < $scope.cities.length; i++) {
							if ($scope.cities[i].id == $scope.homeCity) {
								$scope.loadCity($scope.cities[i]);
								return;
							}
						}
					}
				}

				// If no home found, load first city found
				if ($scope.cities.length > 0) {
					$scope.loadCity($scope.cities[0]);
				}

			},
			function (r) {
				$scope.fatalError();
			});
		};

		$scope.loadCity = function(city) {
			if (undef(city) || emptyStr(city.name)) {
				alert(g_error500);
				return;
			}

			$http.get(OC.generateUrl('/apps/weather/weather/get?name=' + city.name)).
			then(function (r) {
				if (r.data != null) {
					$scope.domCity = city;
					$scope.currentCity = r.data;
					$scope.selectedCityId = city.id;
					$scope.currentCity.image = $scope.imageMapper[$scope.currentCity.weather[0].main];
					$scope.currentCity.wind.desc = "";
					if ($scope.currentCity.wind.deg > 0 && $scope.currentCity.wind.deg < 23 ||
						$scope.currentCity.wind.deg > 333) {
						$scope.currentCity.wind.desc = t('weather', 'North');
					}
					else if ($scope.currentCity.wind.deg > 22 && $scope.currentCity.wind.deg < 67) {
						$scope.currentCity.wind.desc = t('weather', 'North-East');
					}
					else if ($scope.currentCity.wind.deg > 66 && $scope.currentCity.wind.deg < 113) {
						$scope.currentCity.wind.desc = t('weather', 'East');
					}
					else if ($scope.currentCity.wind.deg > 112 && $scope.currentCity.wind.deg < 157) {
						$scope.currentCity.wind.desc = t('weather', 'South-East');
					}
					else if ($scope.currentCity.wind.deg > 156 && $scope.currentCity.wind.deg < 201) {
						$scope.currentCity.wind.desc = t('weather', 'South');
					}
					else if ($scope.currentCity.wind.deg > 200 && $scope.currentCity.wind.deg < 245) {
						$scope.currentCity.wind.desc = t('weather', 'South-West');
					}
					else if ($scope.currentCity.wind.deg > 244 && $scope.currentCity.wind.deg < 289) {
						$scope.currentCity.wind.desc = t('weather', 'West');
					}
					else if ($scope.currentCity.wind.deg > 288) {
						$scope.currentCity.wind.desc = t('weather', 'North-West');
					}
					$scope.cityLoadError = '';
				}
				else {
					$scope.cityLoadError = t('weather', 'Failed to get city weather informations. Please contact your administrator');
				}
				$scope.cityLoadNeedsAPIKey = false;
			},
			function (r) {
				if (r.status == 404) {
					$scope.cityLoadError = t('weather','No city with this name found.');
					$scope.cityLoadNeedsAPIKey = false;
				}
				else if (r.status == 401) {
					$scope.cityLoadError = t('weather', 'Your OpenWeatherMap API key is invalid. Contact your administrator to configure a valid API key in Additional Settings of the Administration');
					$scope.cityLoadNeedsAPIKey = true;
				}
				else {
					$scope.cityLoadError = g_error500;
					$scope.cityLoadNeedsAPIKey = false;
				}
			});
		}

		$scope.addCity = function(city) {
			if (undef(city) || emptyStr(city.name)) {
				$scope.addCityError = t('weather', 'Empty city name !');
				return;
			}

			$http.post(OC.generateUrl('/apps/weather/city/add'), {'name': city.name}).
			then(function (r) {
				if (r.data != null && !undef(r.data['id'])) {
					$scope.cities.push({"name": city.name, "id": r.data['id']})
					$scope.showAddCity = false;

					if (!undef(r.data['load']) && r.data['load']) {
						loadingCity = angular.copy(city);
						loadingCity.id = r.data['id'];
						$scope.loadCity(loadingCity);
					}
					city.name = "";
				}
				else {
					$scope.addCityError = t('weather', 'Failed to add city. Please contact your administrator');
				}
			},
			function (r) {
				if (r.status == 401) {
					$scope.addCityError = t('weather', 'Your OpenWeatherMap API key is invalid. Contact your administrator to configure a valid API key in Additional Settings of the Administration');
				}
				else if (r.status == 404) {
					$scope.addCityError = t('weather', 'No city with this name found.');
				}
				else if (r.status == 409) {
					$scope.addCityError = t('weather', 'This city is already registered for your account.');
				}
				else {
					$scope.addCityError = g_error500;
				}
			});
		};

		$scope.deleteCity = function(city) {
			if (undef(city)) {
				alert(g_error500);
				return;
			}

			$http.post(OC.generateUrl('/apps/weather/city/delete'), {'id': city.id}).
			then(function (r) {
				if (r.data != null && !undef(r.data['deleted'])) {
					for (var i = 0; i < $scope.cities.length; i++) {
                                                if ($scope.cities[i].id === city.id) {
                                                        $scope.cities.splice(i, 1);
                                                        // If current city is the removed city, close it
                                                        if ($scope.selectedCityId === city.id) {
								                                                $scope.currentCity = null;
                                                                $scope.selectedCityId = 0;
                                                        }
                                                        return;
                                                }
                                        }
				}
				else {
					alert(t('weather', 'Failed to remove city. Please contact your administrator'));
				}
			},
			function (r) {
				alert(g_error500);
			});
		};

		$scope.setHome = function(cityId) {
			if (undef(cityId)) {
				alert(g_error500);
				return;
			}

			$http.post(OC.generateUrl('/apps/weather/settings/home/set'), {'city': cityId}).
			then(function (r) {
				if (r.data != null && !undef(r.data['set'])) {
					$scope.homeCity = cityId;
				}
				else {
					alert(t('weather', 'Failed to set home. Please contact your administrator'));
				}
			},
			function (r) {
				alert(g_error500);
			});
		}
	}
]);
