<template>
	<div v-if="currentCity.forecast.length > 0" class="cityForecastPanel">
		<table>
			<thead>
				<tr>
					<th>{{ t('weather', 'Date') }}</th>
					<th>{{ t('weather', 'Temperature') }}</th>
					<th>{{ t('weather', 'Perceptible Temperature') }}</th>
					<th>{{ t('weather', 'Minimum Temperature') }}</th>
					<th>{{ t('weather', 'Maximum Temperature') }}</th>
					<th>{{ t('weather', 'Weather') }}</th>
					<th>{{ t('weather', 'Pressure') }}</th>
					<th>{{ t('weather', 'Humidity') }}</th>
					<th>{{ t('weather', 'Wind') }}</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="forecast in currentCity.forecast" :key="forecast.date">
					<td>{{ forecast.date }}</td>
					<td>{{ forecast.temperature }}{{ metricRepresentation }}</td>
					<td>{{ forecast.temperature_feelslike }}{{ metricRepresentation }}</td>
					<td>{{ forecast.temperature_min }}{{ metricRepresentation }}</td>
					<td>{{ forecast.temperature_max }}{{ metricRepresentation }}</td>
					<td>{{ forecast.weather }}</td>
					<td>{{ forecast.pressure }} hpa</td>
					<td>{{ forecast.humidity }} %</td>
					<td>{{ forecast.wind.speed }} m/s - {{ forecast.wind.desc }}</td>
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

<style scoped>
th {
	font-weight: bold;
}

.cityForecastPanel {
	color: #ddd;
	border-radius: 3px;
	padding: 20px;
	background-color: rgba(50, 50, 50, 0.5);
	display: block;
	backdrop-filter: blur(3px);
	-webkit-backdrop-filter: blur(3px);
	font-size: 1.3em;
	margin-top: 10px;
}

td, th {
	padding: 10px;
}

tr:hover {
	background-color: var(--color-background-light);
}
</style>
