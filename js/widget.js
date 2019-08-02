
/** global: OCA */
/** global: net */

(function () {
	
	/**
	 * @constructs Weather
	 */
	var Weather = function() {
	}

	Weather.prototype.divWeather = null;
	Weather.prototype.init = function() {
		this.divWeather = document.querySelector("#widget-weather");
		this.getWeather();

	}
	Weather.prototype.getWeather = function() {
		console.log("Requesting update...");
		this.divWeather.textContent = "Update requested at " + Date();

		var request = {
			widget: 'weather',
			request: 'getWeather'
		};

		net.requestWidget(request, weather.updateWeather);

	}

	Weather.prototype.updateWeather = function(result) {
		console.info("updateWeather result", result);

		this.divWeather.querySelector(".locationValue").innerHTML = result.value.location;
		this.divWeather.querySelector(".temperatureValue").innerHTML = result.value.temperature;
		this.divWeather.querySelector(".weatherValue").innerHTML = result.value.weather;
		this.divWeather.querySelector(".humidityValue").innerHTML = result.value.humidity;
		this.divWeather.querySelector(".windValue").innerHTML = result.value.wind;
	}

	// TODO rewrite the above with JQuery


	OCA.DashBoard.Weather = Weather;
	OCA.DashBoard.weather = new Weather();

})()