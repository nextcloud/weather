<template>
	<div v-if="!!currentCity.name" class="contentPanel cityWeatherPanel">
		<div class="cityName">
			{{ currentCity.name }}, {{ currentCity.sys.country }}
		</div>
		<CurrentWeatherValue
			:name="t('weather', 'Current Temperature')"
			:value="currentCity.main.temp"
			:unit="metricRepresentation" />
		<CurrentWeatherValue
			:name="t('weather', 'Feelslike Temperature')"
			:value="currentCity.main.feels_like"
			:unit="metricRepresentation" />
		<CurrentWeatherValue
			:name="t('weather', 'Minimum Temperature')"
			:value="currentCity.main.temp_min"
			:unit="metricRepresentation" />
		<CurrentWeatherValue
			:name="t('weather', 'Maximum Temperature')"
			:value="currentCity.main.temp_max"
			:unit="metricRepresentation" />
		<CurrentWeatherValue
			:name="t('weather', 'Pressure')"
			:value="currentCity.main.pressure"
			unit="hpa" />
		<CurrentWeatherValue
			:name="t('weather', 'Humidity')"
			:value="currentCity.main.humidity"
			unit="%" />
		<CurrentWeatherValue
			:name="t('weather', 'Cloudiness')"
			:value="currentCity.weather[0].description" />
		<CurrentWeatherValue
			:name="t('weather', 'Wind')"
			:value="`${currentCity.wind.speed}m/s - ${ currentCity.wind.desc }`" />
		<CurrentWeatherValue
			:name="t('weather', 'Sunrise')"
			:value="sunrise" />
		<CurrentWeatherValue
			:name="t('weather', 'Sunset')"
			:value="sunset" />
	</div>
</template>

<script>
import { mapState } from 'vuex'

import CurrentWeatherValue from './CurrentWeatherValue'

const formatDate = function formatDate(rawValue, formatString) {
	if (formatString !== 'HH:mm') {
		throw new Error('Unrecognized format: ' + formatString)
	}
	const date = new Date(rawValue)
	return (date.getHours() > 9 ? '' : '0')
		+ date.getHours() + ':'
		+ (date.getMinutes() > 9 ? '' : '0')
		+ date.getMinutes()
}

export default {
	name: 'CurrentWeather',
	components: {
		CurrentWeatherValue,
	},
	filters: {
		date: formatDate,
	},
	computed: {
		...mapState(['currentCity', 'homeCity', 'selectedCityId', 'metricRepresentation']),
		...mapState({
			sunrise(state) { return formatDate(state.currentCity.sys.sunrise * 1000, 'HH:mm') },
			sunset(state) { return formatDate(state.currentCity.sys.sunset * 1000, 'HH:mm') },
		}),
	},
	methods: {
		setHomeCity(cityId) {
			this.$store.dispatch('setHome', cityId)
		},
	},
}
</script>
