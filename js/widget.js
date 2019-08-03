
/** global: OCA */
/** global: net */

(function () {
console.log("OCA", OCA, "net", net)	
	/**
	 * @constructs Weather
	 */
	var Weather = function() {
	}

	Weather.prototype.divWeather = null;
	Weather.prototype.init = function() {
		this.getWeather();

	}
	Weather.prototype.getWeather = function() {
		console.log("Requesting update...");

		var request = {
			widget: 'weather',
			request: 'getWeather'
		};

		net.requestWidget(request, this.updateWeather);

	}

	Weather.prototype.updateWeather = function(result) {
		console.info("updateWeather result", result);
		var divWeather = document.querySelector("#widget-weather");

		divWeather.querySelector(".locationValue").innerHTML = result.value.location;
		divWeather.querySelector(".temperatureValue").innerHTML = result.value.temperature;
		divWeather.querySelector(".weatherValue").innerHTML = result.value.weather;
		divWeather.querySelector(".humidityValue").innerHTML = result.value.humidity;
		divWeather.querySelector(".windValue").innerHTML = result.value.wind;
	}

	// TODO rewrite the above with JQuery


	OCA.DashBoard.Weather = Weather;
	OCA.DashBoard.weather = new Weather();

})()
