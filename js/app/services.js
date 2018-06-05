(function() {

	var rfsAppServices = angular.module("rfsAppServices", []);

	rfsAppServices.factory("SiteLoader", function() {
		
		var hasLoaded = false;

		var onSiteLoaded = function(ifLoaded) {
			hasLoaded = ifLoaded;
		};

		var showLoader = function() {
			return hasLoaded;
		};

		return {
			onSiteLoaded : onSiteLoaded,
			showLoader : showLoader
		};

	});

	rfsAppServices.factory("AJAXservice", ["$http", "$httpParamSerializerJQLike", "AJAX_URL", function($http, $httpParamSerializerJQLike, AJAX_URL) {

		function ajaxQuery(data) {
			return $http({
				method: 'POST',
				url: AJAX_URL,
				data: $httpParamSerializerJQLike(data)
			});
		}
		

		return {
			ajaxQuery : ajaxQuery
		};

	}]);

	rfsAppServices.factory('VoteService', ['AJAXservice', function(AJAXservice) {

		function vote(placeID, contestType) {
			var data = {
				action 	: 'voteOnPlace',
				id 		: placeID,
				nonce 	: frontendajax.vote_nonce,
				type: contestType
			};
			return AJAXservice.ajaxQuery(data);
		}

		return {
			vote :  vote
		};

	}]);

	rfsAppServices.factory("PlacesSorting", function() {
		var sorting = false;

		var isSorting = function() {
			return sorting;
		};

		var setSorting = function(value) {
			sorting = value;
		};

		return {
			isSorting: isSorting,
			setSorting: setSorting
		};
	});

	rfsAppServices.factory('scrollToAnchor', ["$document", function($document) {
		var enableScroll = false;

		var setEnable = function(value) {
			enableScroll = value;
		};

		var isEnabled = function() {
			return enableScroll;
		};

		var scrollMe = function($items, $item, $ppp, $extra, $enableOnLoad, $anchor) {
			
			var item 		= angular.element($item),
				itemHeight 	= parseInt(item.css('height')) + 20, //20 -margin bottom
				extra 		= $extra, // heigth from anchor to first item
				items 		= $items.length,
				modulo 	 	= items%$ppp,
				levels 		= 0;
				
			if( modulo === 0 ) {
				levels = items - $ppp;
			}else {
				levels = items - Math.floor(modulo);
			}
			
			if($enableOnLoad === true) {
				var offset = (levels * itemHeight) + extra;
				$document.scrollToElementAnimated($anchor, -offset, 500);
			}
			setEnable(true);
		};

		return {
			scrollMe : scrollMe,
			enableScroll : isEnabled
		};
	}]);

})();