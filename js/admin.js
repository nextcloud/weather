/*
 * Copyright (c) 2017
 *
 * This file is licensed under the Affero General Public License version 3
 * or later.
 *
 * See the COPYING-README file.
 *
 */

(function() {
	if (!OCA.Weather) {
		/**
		 * Namespace for the files app
		 * @namespace OCA.Weather
		 */
		OCA.Weather = {};
	}

	/**
	 * @namespace OCA.Weather.Admin
	 */
	OCA.Weather.Admin = {
		initialize: function() {
			$('#submitOWMApiKey').on('click', _.bind(this._onClickSubmitOWMApiKey, this));
		},

		_onClickSubmitOWMApiKey: function () {
			OC.msg.startSaving('#OWMApiKeySettingsMsg');

			var request = $.ajax({
				url: OC.generateUrl('/apps/weather/settings/apikey'),
				type: 'POST',
				data: {
					apiKey: $('#openweathermap-api-key').val()
				}
			});

			request.done(function (data) {
				$('#openweathermap-api-key').val(data.apiKey);
				OC.msg.finishedSuccess('#OWMApiKeySettingsMsg', 'Saved');
			});

			request.fail(function () {
				OC.msg.finishedError('#OWMApiKeySettingsMsg', 'Error');
			});
		}
	}
})();

$(document).ready(function() {
	OCA.Weather.Admin.initialize();
});
