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

import { translate, translatePlural } from '@nextcloud/l10n'
import App from './App'

Vue.use(Vuex)

const store = new Vuex.Store({
	state: {
		currentCity: {
			name: '',
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
			forecast: [
				{
					date: 0,
					temperature: 0,
					temperature_feelslike: 0,
					temperature_min: 0,
					temperature_max: 0,
					weather: '',
					pressure: 0,
					humidity: 0,
					wind: {
						speed: 0,
						desc: '',
					},
				},
			],
		},
		metricRepresentation: 'C',
		homeCity: undefined,
		useBackgroundImages: true,
	},
	mutations: {},
	actions: {},
})

Vue.prototype.t = translate
Vue.prototype.n = translatePlural

export default new Vue({
	el: '#content',
	render: h => h(App),
	store,
})
