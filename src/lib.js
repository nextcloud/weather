import { translate as t } from '@nextcloud/l10n'

export function windMapper(windDegrees) {
	if ((windDegrees > 0 && windDegrees < 23)
		|| (windDegrees > 333)) {
		return t('weather', 'North')
	} else if (windDegrees > 22 && windDegrees < 67) {
		return t('weather', 'North-East')
	} else if (windDegrees > 66 && windDegrees < 113) {
		return t('weather', 'East')
	} else if (windDegrees > 112 && windDegrees < 157) {
		return t('weather', 'South-East')
	} else if (windDegrees > 156 && windDegrees < 201) {
		return t('weather', 'South')
	} else if (windDegrees > 200 && windDegrees < 245) {
		return t('weather', 'South-West')
	} else if (windDegrees > 244 && windDegrees < 289) {
		return t('weather', 'West')
	} else if (windDegrees > 288) {
		return t('weather', 'North-West')
	}

	return ''
}

export function imageMapper(weatherDescription) {
	return {
		Clear: 'sun.jpg',
		Clouds: 'clouds.png',
		Drizzle: 'drizzle.jpg',
		Smoke: 'todo.png',
		Dust: 'todo.png',
		Sand: 'sand.jpg',
		Ash: 'todo.png',
		Squall: 'todo.png',
		Tornado: 'tornado.jpg',
		Haze: 'mist.jpg',
		Mist: 'mist.jpg',
		Rain: 'rain.jpg',
		Snow: 'snow.png',
		Thunderstorm: 'thunderstorm.jpg',
		Fog: 'fog.jpg',
	}[weatherDescription] || 'todo.png'
}

export function mapMetric(metric) {
	if (metric === 'kelvin') return '°K'
	if (metric === 'imperial') return '°F'
	return '°C'
}
