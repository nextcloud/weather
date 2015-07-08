/**
 * ownCloud - ownBoard
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Loic Blot <loic.blot@unix-experience.fr>
 * @copyright Loic Blot 2014-2015
 */


var app = angular.module('Weather', []);

var g_error500 = 'Fatal Error: please check your owncloud.log and sent a bug report here: https://github.com/nerzhul/ownboard/issues';

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
		$scope.showCreateCity = false;

		$timeout(function () {
			$scope.loadCities();
		});

		$scope.loadCities = function () {
			$http.get(OC.generateUrl('/apps/ownboard/city/getall')).
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
]);
