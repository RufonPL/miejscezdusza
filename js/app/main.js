(function() {

	var app = angular.module('rfsApp', ["rfsAppDirectives",  "rfsAppServices", "rfsAppControllers"]);

	app.constant("AJAX_URL", frontendajax.ajaxurl);
	app.constant("TPL_PATH", angular.element('body').attr('data-site-url') + '/template-parts/app-templates/');
	app.config(["$httpProvider", function($httpProvider) {
		
			$httpProvider.defaults.cache = true;
			$httpProvider.defaults.headers.post = {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8;'};

	}]);


})();