<template>
	<div v-if="currentCity.forecast.length > 0" class="contentPanel cityForecastPanel">
		<table>
			<thead>
				<tr>
					<th class="date">
						{{ t('weather', 'Date') }}
					</th>
					<th class="temp">
						{{ t('weather', 'Temperature') }}
					</th>
					<th class="tempPerception">
						{{ t('weather', 'Perceptible Temperature') }}
					</th>
					<th class="tempMin">
						{{ t('weather', 'Minimum Temperature') }}
					</th>
					<th class="tempMax">
						{{ t('weather', 'Maximum Temperature') }}
					</th>
					<th class="weather">
						{{ t('weather', 'Weather') }}
					</th>
					<th class="pressure">
						{{ t('weather', 'Pressure') }}
					</th>
					<th class="humidity">
						{{ t('weather', 'Humidity') }}
					</th>
					<th class="wind">
						{{ t('weather', 'Wind') }}
					</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="forecast in currentCity.forecast" :key="forecast.date">
					<td>{{ forecast.date }}</td>
					<td class="numeric temp">
						{{ forecast.temperature }}&nbsp;{{ metricRepresentation }}
					</td>
					<td class="numeric tempPerception">
						{{ forecast.temperature_feelslike }}&nbsp;{{ metricRepresentation }}
					</td>
					<td class="numeric tempMin">
						{{ forecast.temperature_min }}&nbsp;{{ metricRepresentation }}
					</td>
					<td class="numeric tempMax">
						{{ forecast.temperature_max }}&nbsp;{{ metricRepresentation }}
					</td>
					<td class="weather">
						{{ forecast.weather }}
					</td>
					<td class="numeric pressure">
						{{ forecast.pressure }} hpa
					</td>
					<td class="numeric humidity">
						{{ forecast.humidity }} %
					</td>
					<td class="wind">
						{{ forecast.wind.speed }}&nbsp;m/s - {{ forecast.wind.desc }}
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
import { mapState } from 'vuex'

export default {
	name: 'Forecast',
	computed: mapState(['currentCity', 'metricRepresentation']),
}
</script>
