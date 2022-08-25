<template>
	<div class="currentValueRow cityCurrentTemp">
		<span class="label">{{ name }} :</span>
		<span class="value">{{ value }}&nbsp;{{ unit }}</span>
	</div>
</template>

<script>
export default {
	props: {
		name: {
			type: String,
			default: '',
			required: true,
		},
		unit: {
			type: String,
			default: '',
		},
		value: {
			type: [String, Number],
			required: true,
		},
	},

}
</script>
