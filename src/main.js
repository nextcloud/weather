/**
 * @copyright Copyright (c) 2018 John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @author John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

import 'es6-promise/auto'

import Vue from 'vue'
import Vuex from 'vuex'

import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { translate, translatePlural } from '@nextcloud/l10n'
import App from './App'
import { showSuccess, showError } from '@nextcloud/dialogs'
import '@nextcloud/dialogs/styles/toast.scss'

import { windMapper, imageMapper, mapMetric } from './lib'

Vue.use(Vuex)

const store = new Vuex.Store({
	state: {
		allCities: {
			cities: [], // { id: '29', name: 'Budapest', },
			userid: '',
			home: '',
		},
		currentCity: {
			name: '',
			image: '',
			sys: {
				country: '',
				sunrise: 0,
				sunset: 0,
			},
			wind: {
				speed: 0,
				desc: 'bad one',
			},
			main: {
				feels_like: 0,
				temp: 0,
				temp_min: 0,
				temp_max: 0,
				humidity: 0,
				pressure: 0,
			},
			weather: [{ description: '' }],
			forecast: [], // {date: 0, temperature: 0, temperature_feelslike: 0, temperature_min: 0, temperature_max: 0, weather: '', pressure: 0, humidity: 0, wind: { speed: 0, desc: '',},},
		},
		selectedCityId: undefined,
		metric: 'metric',
		metricRepresentation: '°C',
		useBackgroundImages: true,
		cityLoadNeedsApiKey: false,
		cityLoadError: '',
		loaded: false,
	},
	getters: {
		fatalError: (state) =>
			t('weather', 'Fatal Error: please check your nextcloud.log and send a bug report here: https://github.com/nextcloud/weather/issues'),
	},
	mutations: {
		setCities(state, cities) {
			state.allCities.cities = cities
		},
		setApiKeyNeeded(state, isNeeded) {
			state.cityLoadNeedsApiKey = isNeeded
		},
		setCityLoadError(state, error) {
			state.cityLoadError = error
		},
		setHome(state, cityId) {
			state.allCities.home = cityId
		},
		unsetSelected(state) {
			state.currentCity = null
			state.selectedCityId = 0
		},
		removeCity(state, cityId) {
			state.allCities.cities = state.allCities.cities.filter(city => city.id !== cityId)
		},
		addCity(state, city) {
			state.allCities.cities = state.allCities.cities.concat({ id: city.id, name: city.name })
		},
		setLoaded(state, isLoaded) {
			state.loaded = isLoaded
		},
	},
	actions: {
		addCity(context, newCityName) {
			axios.post(generateUrl('/apps/weather/city/add'), { name: newCityName })
				.then((response) => {
					showSuccess(`City added: ${newCityName}`)
					if (response.data.id) {
						// response contains the id, but not the name
						context.commit('addCity', { name: newCityName, id: response.data.id })
					}
					// otherwise one may send a new API call :
					// context.dispatch('loadCities')
				})
				.catch((reason) => showError(`Cannot add city: ${reason}`))
		},
		loadCities(context) {
			axios.get(generateUrl('/apps/weather/city/getall'))
				.then((response) => {
					const responseData = response.data
					if (responseData.home) {
						context.commit('setHome', responseData.home)
					}
					if (responseData.cities) {
						context.commit('setCities', responseData.cities)
					}
					if (!context.state.loaded) {
						if (responseData.home && !!responseData.cities.find(city => city.id === responseData.home)) {
							// there is a home city set, and it hasnn't been removed before -> load it
							context.dispatch('loadCity', responseData.home)
								.then(() => { context.commit('setLoaded', true) })
						} else if (responseData.cities && responseData.cities.length > 0) {
							// load the first city, if no home city is set
							context.dispatch('loadCity', responseData.cities[0].id)
						}
					}
				})
				.catch(reason => {
					showError(context.getters.fatalError)
				})
		},
		loadCity(context, cityId) {
			const cityToLoad = context.state.allCities.cities.find(city => city.id === cityId)
			if (!cityToLoad) return

			axios.get(
				generateUrl('/apps/weather/weather/get'),
				{ params: { name: cityToLoad.name } })
				.then((response) => {
					context.state.currentCity = response.data
					context.state.currentCity.wind.desc = windMapper(context.state.currentCity.wind.deg)
					context.state.currentCity.image = imageMapper(context.state.currentCity.weather[0].main)
					context.state.selectedCityId = cityToLoad.id
				})
				.catch(reason => {
					showError(`Cannot load city: ${reason}`)
					if (reason.status === 404) {
						const error = t('weather', 'No city with this name found.')

						showError(error)
						context.commit('setCityLoadError', error)
						context.commit('setApiKeyNeeded', false)
					} else if (reason.status === 401) {
						const error = t(
							'weather',
							'Your OpenWeatherMap API key is invalid. Contact your administrator to configure a valid API key in Additional Settings of the Administration')
						showError(error)
						context.commit('setCityLoadError', error)
						context.commit('setApiKeyNeeded', true)
					} else {
						const error = context.getters.fatalError

						showError(error)
						context.commit('setCityLoadError', error)
						context.commit('setApiKeyNeeded', false)
					}

				})
		},
		deleteCity(context, cityId) {
			axios.post(generateUrl('/apps/weather/city/delete'), { id: cityId })
				.then(response => {
					if (response.data != null && !!response.data.deleted) {
						context.commit('removeCity', cityId)
						// If current city is the removed city, close it
						if (context.state.selectedCityId === cityId) {
							context.commit('unsetSelected')
							// if there are still cities on the list, and the selected was deleted, select the home city.
							// if the home city was deleted, select the first one instead
							if (context.state.allCities.cities.length > 0) {
								context.dispatch(
									'loadCity',
									cityId !== context.state.allCities.home
										? context.state.allCities.home
										: context.state.allCities.cities[0].id)
							}
						}
					} else {
						alert(t('weather', 'Failed to remove city. Please contact your administrator'))
					}
				}).catch(function(r) {
					showError(context.getters.fatalError)
				})
		},
		setHome(context, cityId) {
			axios.post(generateUrl('/apps/weather/settings/home/set'), { city: cityId })
				.then(response => {
					if (response.data != null && !!response.data.set) {
						context.commit('setHome', cityId)
					} else {
						showError(t('weather', 'Failed to set home. Please contact your administrator'))
					}
				})
				.catch(reason => {
					showError(context.getters.fatalError)
				})
		},
		selectCity(context, cityId) {
			context.dispatch('loadCity', cityId)
				.then(() => {
					context.state.selectedCityId = cityId
				})
		},
		loadMetric(context) {
			axios.get(generateUrl('/apps/weather/settings/metric/get'))
				.then((r) => {
					if (r.data.metric) {
						context.state.metric = r.data.metric
						context.state.metricRepresentation = mapMetric(r.data.metric)
					}
				}).catch((r) => {
					showError(context.getters.fatalError)
				})
		},
		modifyMetric(context, metric) {
			axios.post(generateUrl('/apps/weather/settings/metric/set'), { metric })
				.then((r) => {
					if (r.data != null && !!(r.data.set)) {
						context.state.metric = metric
						context.state.metricRepresentation = mapMetric(metric)
						context.dispatch('loadCity', context.state.selectedCityId)
					} else {
						showError(t('weather', 'Failed to set metric. Please contact your administrator'))
					}
				}).catch(
					(r) => {
						if (r.status === 404) {
							showError(t('weather', 'This metric is not known.'))
						} else {
							showError(context.getters.fatalError)
						}
					})
		},
	},
})

Vue.prototype.t = translate
Vue.prototype.n = translatePlural

export default new Vue({
	el: '#content',
	created() {
		this.$store.dispatch('loadCities')
	},
	render: h => h(App),
	store,
})
