(function ($) {

	/**
	 * Helper class for admin page
	 */
	EugeneAPIAdmin = {

		/**
		 * Target element to render the JS template.
		 */
		targetEl: null,

		/**
		 * Table list template for the API data.
		 */
		template: wp.template('eugene-api-table-html'),

		/**
		 * Holds the API response.
		 */
		response: null,

		/**
		 * Initializes class.
		 */
		init: function(){
			$( '#refresh-data-button' ).on( 'click', this.refreshData );
			this.targetEl = $('.eugene-api-page-content', '#eugene-api');
			this.loadAjaxData();
		},

		/**
		 * Refresh hook.
		 */
		refreshData: function(){
			EugeneAPIAdmin.loadAjaxData();
		},

		/**
		 * Request data from the REST API using ajax.
		 */
		loadAjaxData: function(){
			// Show loader
			this.targetEl
				.addClass('ea-loading')
				.removeClass('ea-done');

			$.ajax({
				type: 'GET',
				url: eugene_api_obj.restUrl,
				beforeSend: function(xhr){},
				data: {},
				success: function(response) {
					if( response.hasOwnProperty('data') ) {
						EugeneAPIAdmin.response = response;
						EugeneAPIAdmin.renderTemplate();
					}

					setTimeout(function(){
						EugeneAPIAdmin.ajaxComplete();
					}, 300);
				},
				error: function(xhr, status, error) {
					// Handle error
					console.error(xhr.responseText);
				}
			});
		},

		/**
		 * Render data into the template.
		 */
		renderTemplate: function(){
			this.targetEl.html(
				this.template( this.response )
			);
		},

		/**
		 * Do something when ajax complete.
		 */
		ajaxComplete: function( response ){
			// Hide loader
			this.targetEl
				.removeClass('ea-loading')
				.addClass('ea-done');
		},
	};

	$(function () {
		EugeneAPIAdmin.init();
	});
})(jQuery);
