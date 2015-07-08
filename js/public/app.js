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

var g_error500 = 'Fatal Error: please check your owncloud.log and sent a bug report here: https://github.com/nerzhul/weather/issues';

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
		$scope.selectedCityId = 0;
		$scope.showAddCity = false;
		$scope.addCityError = '';

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

		$scope.addCity = function(city) {
			if (undef(city) || emptyStr(city.name)) {
				$scope.addCityError = 'Empty city name !';
				return;
			}

			$http.post(OC.generateUrl('/apps/weather/city/add'), {'name': city.name}).
			success(function (data, status, headers, config) {
				if (data != null && !undef(data['id'])) {
					$scope.boards.push({"name": city.name, "id": data['id']})
					$scope.showAddCity = false;
				}
				else {
					$scope.addCityError = 'Failed to add city. Please contact your administrator';
				}
			}).
			fail(function (data, status, headers, config) {
				$scope.addCityError = g_error500;
			});
		};
	}
]);
