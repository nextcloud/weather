<template>
	<div v-if="!!currentCity.name" class="cityWeatherPanel">
		<div class="cityName">
			{{ currentCity.name }}, {{ currentCity.sys.country }}
		</div>
		<div class="cityCurrentTemp">
			{{ t("weather", "Current Temperature") }} : {{ currentCity.main.temp
			}}{{ metricRepresentation }} {{ this.$store.unitOfMeasure }}
		</div>
		<div class="cityCurrentTemp_feelslike">
			{{ t("weather", "Feelslike Temperature") }} : {{ currentCity.main.feels_like
			}}{{ metricRepresentation }}
		</div>
		<div class="cityCurrentTemp_min">
			{{ t("weather", "Minimum Temperature") }} : {{ currentCity.main.temp_min
			}}{{ metricRepresentation }}
		</div>
		<div class="cityCurrentTemp_max">
			{{ t("weather", "Maximum Temperature") }} : {{ currentCity.main.temp_max
			}}{{ metricRepresentation }}
		</div>
		<div class="cityCurrentPressure">
			{{ t("weather", "Pressure") }} : {{ currentCity.main.pressure }} hpa
		</div>
		<div class="cityCurrentHumidity">
			{{ t("weather", "Humidity") }} : {{ currentCity.main.humidity }}%
		</div>
		<div class="cityCurrentWeather">
			{{ t("weather", "Cloudiness") }} :
			{{ currentCity.weather[0].description }}
		</div>
		<div class="cityCurrentWind">
			{{ t("weather", "Wind") }} : {{ currentCity.wind.speed }} m/s -
			{{ currentCity.wind.desc }}
		</div>
		<div class="cityCurrentSunrise">
			{{ t("weather", "Sunrise") }} :
			{{ currentCity.sys.sunrise * 1000 | date('HH:mm') }}
		</div>
		<div class="cityCurrentSunset">
			{{ t("weather", "Sunset") }} :
			{{ currentCity.sys.sunset * 1000 | date('HH:mm') }}
		</div>
	</div>
</template>

<script>
import { mapState } from 'vuex'

import { imagePath } from '@nextcloud/router'

export default {
	name: 'CurrentWeather',
	filters: {
		date: function formatDate(rawValue, formatString) {
			if (formatString !== 'HH:mm') {
				throw new Error('Unrecognized format: ' + formatString)
			}
			const date = new Date(rawValue)
			return (date.getHours() > 9 ? '' : '0')
				+ date.getHours() + ':'
				+ (date.getMinutes() > 9 ? '' : '0')
				+ date.getMinutes()
		},
	},
	data() {
		return {
			homePick: imagePath('weather', 'home-pick.png'),
			homeNoPick: imagePath('weather', 'home-nopick.png'),
		}
	},
	computed: mapState(['currentCity', 'homeCity', 'selectedCityId', 'metricRepresentation']),
	methods: {
		setHomeCity(cityId) {
			this.$store.dispatch('setHome', cityId)
		},
	},
}

</script>
