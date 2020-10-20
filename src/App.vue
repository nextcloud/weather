<template>
	<Content app-name="weather">
		<AppNavigation>
			<!-- default : add city -->
			<AppNavigationNew
				:text="t('weather', 'Add City')"
				:disabled="false"
				@click="newButtonAction" />
			<!-- list of cities -->
			<template #list>
				<AppNavigationItem icon="icon-home" title="This is the home city">
					<template slot="actions">
						<ActionButton icon="icon-delete" @click="alert('Delete')">
							Delete
						</ActionButton>
					</template>
				</AppNavigationItem>
				<AppNavigationItem icon="icon-address" title="This is a city">
					<template slot="actions">
						<ActionButton icon="icon-delete" @click="alert('Delete')">
							Make Home City
						</ActionButton>
						<ActionButton icon="icon-delete" @click="alert('Delete')">
							Delete
						</ActionButton>
					</template>
				</AppNavigationItem>
			</template>
			<template #footer>
				<AppNavigationSettings :title="t('weather', 'Settings')">
					<!-- settings: F / C -->
					<!-- use background images -->
					This is a placeholder
				</AppNavigationSettings>
			</template>
		</AppNavigation>
		<AppContent>
			<div class="mainContentContainer">
				<div class="errorContainer">
					this is where errors are. only visible when there are errors
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

import Content from '@nextcloud/vue/dist/Components/Content'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem'
import AppNavigationNew from '@nextcloud/vue/dist/Components/AppNavigationNew'
import AppNavigationSettings from '@nextcloud/vue/dist/Components/AppNavigationSettings'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'

import CurrentWeather from './CurrentWeather'
import Forecast from './Forecast'

export default {
	name: 'App',
	components: {
		Content,
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationNew,
		AppNavigationSettings,
		ActionButton,
		CurrentWeather,
		Forecast,
	},
	data() {
		return {
		}
	},
	methods: {
		newButtonAction() {
			throw new Error('to implement')
		},
	},
}
</script>
