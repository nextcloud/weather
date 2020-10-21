<template>
	<Content app-name="weather">
		<AppNavigation>
			<!-- default : add city -->
			<ActionInput :value.sync="newCityName" icon="icon-address" @submit="addCity">
				{{ t('weather', 'Add City') }}
			</ActionInput>
			<!-- list of cities -->
			<template #list>
				<AppNavigationItem v-for="city in allCities.cities"
					:key="city.id"
					:title="city.name"
					:icon="allCities.home === city.id ? 'icon-home' : 'icon-address'"
					@click="selectCity(city.id)">
					<template slot="actions">
						<ActionButton v-if="allCities.home !== city.id" icon="icon-home" @click="setHomeCity(city.id)">
							Make Home City
						</ActionButton>
						<ActionButton icon="icon-delete" @click="deleteCity(city.id)">
							Delete
						</ActionButton>
					</template>
				</AppNavigationItem>
			</template>
			<template #footer>
				<AppNavigationSettings :title="t('weather', 'Settings')">
					<label>{{ t('weather', 'Metric') }}
						<select v-model="metric" name="metric" @change="modifyMetric">
							<option value="metric" :selected="getMetric === metric">
								°C
							</option>
							<option value="kelvin" :selected="getMetric === metric">
								°K
							</option>
							<option value="imperial" :selected="getMetric === metric">
								°F
							</option>
						</select></label>
					<!-- use background images -->
				</AppNavigationSettings>
			</template>
		</AppNavigation>
		<AppContent>
			<div :class="`mainContentContainer ${mappedImage}`">
				<div v-if="!!cityLoadError" class="errorContainer">
					{{ cityLoadError }}

					<a v-if="cityLoadNeedsApiKey" href="http://home.openweathermap.org/users/sign_in">{{ t('weather', 'Click here to get an API key') }}</a>
				</div>
				<CurrentWeather />
				<Forecast />
				<!-- forecast -->
				<!-- background image -->
			</div>
		</AppContent>
	</Content>
</template>

<script>

import { mapState } from 'vuex'

import Content from '@nextcloud/vue/dist/Components/Content'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem'
import AppNavigationSettings from '@nextcloud/vue/dist/Components/AppNavigationSettings'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import ActionInput from '@nextcloud/vue/dist/Components/ActionInput'

import CurrentWeather from './CurrentWeather'
import Forecast from './Forecast'

export default {
	name: 'App',
	components: {
		Content,
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationSettings,
		ActionButton,
		ActionInput,
		CurrentWeather,
		Forecast,
	},
	data() {
		return {
			newCityName: '',
			metric: '',
		}
	},
	computed: mapState({
		allCities: 'allCities',
		cityLoadError: 'cityLoadError',
		cityLoadNeedsApiKey: 'cityLoadNeedsApiKey',
		mappedImage(state) { // set current weather conditions as a class (in order to set background image)
			if (state.currentCity) {
				if (state.currentCity.weather[0]) {
					if (state.currentCity.weather[0].main) { return state.currentCity.weather[0].main.replace(/[^a-z]/gi, '') }
				}
			}
		},
		getMetric(state) {
			return state.metric
		},
		homeCity(state) {
			return state.allCities.home
		},
	}),
	beforeMount() {
		this.$store.dispatch('loadMetric')
	},
	mounted() {
		setTimeout(() => { this.metric = this.$store.state.metric }, 1000)
	},
	methods: {
		newButtonAction() {
			throw new Error('to implement')
		},
		addCity() {
			this.$store.dispatch('addCity', this.newCityName)
			this.newCityName = ''
		},
		deleteCity(cityId) {
			this.$store.dispatch('deleteCity', cityId)
		},
		setHomeCity(cityId) {
			this.$store.dispatch('setHome', cityId)
		},
		selectCity(cityId) {
			this.$store.dispatch('selectCity', cityId)
		},
		modifyMetric() {
			this.$store.dispatch('modifyMetric', this.metric)
		},
	},
}
</script>

<style scoped>

.mainContentContainer {
	padding: 15px;
	height: calc(100vh - 50px); /* substract header */
	overflow: auto;
	top: 0;
	bottom: 0;
	right: 0;
	left: 250px;
	background-repeat: no-repeat;
	background-position: center center;
	background-attachment: fixed;
	background-size: cover;
	-webkit-background-size: cover;
	-moz-background-size: cover;
	-o-background-size: cover;
	color: #EEE;
	display: grid;
}

.Clear {
	background-image: url('../img/sun.jpg');
}

.Clouds {
	background-image: url('../img/clouds.png');
}

.Drizzle {
	background-image: url('../img/drizzle.jpg');
}

.Sand {
	background-image: url('../img/sand.jpg');
}

.Tornado {
	background-image: url('../img/tornado.jpg');
}

.Haze, .Mist {
	background-image: url('../img/mist.jpg');
}

.Rain {
	background-image: url('../img/rain.jpg');
}

.Snow {
	background-image: url('../img/snow.png');
}

.Thunderstorm {
	background-image: url('../img/thunderstorm.jpg');
}

.Fog {
	background-image: url('../img/fog.jpg');
}

.Ash, .Squall, .Dust, .Smoke {
	background-image: url('../img/todo.png');
}

</style>
