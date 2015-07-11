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

var g_error500 = 'Fatal Error: please check your owncloud.log and send a bug report here: https://github.com/nerzhul/weather/issues';

function undef (obj) {
	return typeof obj === undefined || obj === undefined;
}

function emptyStr (obj) {
	return undef(obj) || obj == '';
}

app.controller('WeatherController', ['$scope', '$interval', '$timeout', '$compile', '$http',
	function ($scope, $interval, $timeout, $compile, $http) {
		$scope.userId = '';
		$scope.cities = [];
		$scope.showAddCity = false;
		$scope.addCityError = '';

		$scope.cityLoadError = '';
		$scope.currentCity = null;

		$scope.imageMapper = {
			"Clear": "sun.png",
			"Clouds": "clouds.png",
			"Mist": "mist.png",
			"Rain": "rain.jpg",
			"Snow": "snow.png",
			"Thunderstorm": "thunderstorm.png",
		}

		$timeout(function () {
			$scope.loadCities();
		});

		$scope.loadCities = function () {
			$http.get(OC.generateUrl('/apps/weather/city/getall')).
			success(function (data, status, headers, config) {
				if (!undef(data['cities'])) {
					$scope.cities = data['cities']
				}

				if (!undef(data['userid'])) {
					$scope.userId = data['userid'];
				}
			}).
			fail(function (data, status, headers, config) {
				$scope.fatalError();
			});
		};

		$scope.loadCity = function(city) {
			if (undef(city) || emptyStr(city.name)) {
				alert(g_error500);
				return;
			}

			$http.get(OC.generateUrl('/apps/weather/weather/get?name=' + city.name)).
			success(function (data, status, headers, config) {
				if (data != null) {
					$scope.currentCity = data;
					$scope.currentCity.image = $scope.imageMapper[$scope.currentCity.weather[0].main];
					$scope.currentCity.wind.desc = "";
					if ($scope.currentCity.wind.deg > 0 && $scope.currentCity.wind.deg < 23 ||
						$scope.currentCity.wind.deg > 333) {
						$scope.currentCity.wind.desc = "North";
					}
					else if ($scope.currentCity.wind.deg > 22 && $scope.currentCity.wind.deg < 67) {
						$scope.currentCity.wind.desc = "North-East";
					}
					else if ($scope.currentCity.wind.deg > 66 && $scope.currentCity.wind.deg < 113) {
						$scope.currentCity.wind.desc = "East";
					}
					else if ($scope.currentCity.wind.deg > 112 && $scope.currentCity.wind.deg < 157) {
						$scope.currentCity.wind.desc = "South-East";
					}
					else if ($scope.currentCity.wind.deg > 156 && $scope.currentCity.wind.deg < 201) {
						$scope.currentCity.wind.desc = "South";
					}
					else if ($scope.currentCity.wind.deg > 200 && $scope.currentCity.wind.deg < 245) {
						$scope.currentCity.wind.desc = "South-West";
					}
					else if ($scope.currentCity.wind.deg > 244 && $scope.currentCity.wind.deg < 289) {
						$scope.currentCity.wind.desc = "West";
					}
					else if ($scope.currentCity.wind.deg > 288 && $scope.currentCity.wind.deg < 334) {
						$scope.currentCity.wind.desc = "North-West";
					}
				}
				else {
					$scope.cityLoadError = 'Failed to get city weather informations. Please contact your administrator';
				}
			}).
			error(function (data, status, headers, config) {
				if (status == 404) {
					$scope.cityLoadError = "No city with this name found.";
				}
				else if (status == 500) {
					$scope.cityLoadError = g_error500;
				}
			}).
			fail(function (data, status, headers, config) {
				$scope.cityLoadError = g_error500;
			});
		}

		$scope.addCity = function(city) {
			if (undef(city) || emptyStr(city.name)) {
				$scope.addCityError = 'Empty city name !';
				return;
			}

			$http.post(OC.generateUrl('/apps/weather/city/add'), {'name': city.name}).
			success(function (data, status, headers, config) {
				if (data != null && !undef(data['id'])) {
					$scope.cities.push({"name": city.name, "id": data['id']})
					$scope.showAddCity = false;
				}
				else {
					$scope.addCityError = 'Failed to add city. Please contact your administrator';
				}
			}).
			error(function (data, status, headers, config) {
				if (status == 404) {
					$scope.addCityError = "No city with this name found.";
				}
				else if (status == 409) {
					$scope.addCityError = "This city is already registered for your account.";
				}
			}).
			fail(function (data, status, headers, config) {
				$scope.addCityError = g_error500;
			});
		};

		$scope.deleteCity = function(city) {
			if (undef(city)) {
				alert(g_error500);
				return;
			}

			$http.post(OC.generateUrl('/apps/weather/city/delete'), {'id': city.id}).
			success(function (data, status, headers, config) {
				if (data != null && !undef(data['deleted'])) {
					for (var i = 0; i < $scope.cities.length; i++) {
                                                if ($scope.cities[i].id === city.id) {
                                                        $scope.cities.splice(i, 1);
                                                        // If current city is the removed city, close it
                                                        if ($scope.selectedCityId === city.id) {
                                                                $scope.selectedCityId = 0;
                                                        }
                                                        return;
                                                }
                                        }
				}
				else {
					alert('Failed to remove city. Please contact your administrator');
				}
			}).
			fail(function (data, status, headers, config) {
				alert(g_error500);
			});
		};
	}
]);
