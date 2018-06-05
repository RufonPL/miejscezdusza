var $=jQuery.noConflict();
(function($, window, document, undefined) {
	var APP = {
		getMaxHeight: function(el) {
			var heights = $(el).map(function () {
				return $(this).outerHeight();
			}).get(),
			maxHeight = Math.max.apply(null, heights);
			return maxHeight;
		},
		wrapEveryNth: function(el, count, className) {
			var element = $(el),
				length 	= element.length;
			for(var i = 0; i < length ; i+=count) {
				element.slice(i, i+count).wrapAll('<div '+((typeof className == 'string')?'class="'+className+'"':'')+'/>');
			}
			return element;
		},
		owlCarouselDefaults: function(itemClass) {
			return {
				margin:10,
				nav:true,
				autoplay:false,
				autoplayTimeout:5000,
				autoplaySpeed:500,
				navSpeed:500,
				dotsSpeed:500,
				touchDrag:true,
				autoplayHoverPause:true,
				pullDrag:false,
				mouseDrag:false,
				callbacks: true,
			};
		},
		home_slider_carousel: function() {
			var carousel = $('#home-slider-carousel'),
				allItems = carousel.attr('data-images'),
				defaults = this.owlCarouselDefaults();
			
			carousel.on('initialized.owl.carousel', function(event) {
				//$(this).find('.owl-item').css('max-width', $(document).width()*0.8);
			});

			carousel.on('resized.owl.carousel', function(event) {
				//carousel.trigger('refresh.owl.carousel');
			});

			carousel.on('translate.owl.carousel', function(event) {
				$(this).find('.owl-item .home-slider-item .home-slider-item-text').animate({
					opacity: 0
				}, 500);
			});
			carousel.on('translated.owl.carousel', function(event) {
				$(this).find('.owl-item.center .home-slider-item .home-slider-item-text').animate({
					opacity: 1
				}, 500);
			});

			var carouselOptions = {
				loop: carousel.find('.home-slider-item').size() > 1 ? true : false,
				margin:0,
				center: allItems > 1 ? true : false,
    			items: allItems < 3 ? 1 : 2,
				autoWidth:true,
				controlsClass:'home-slider-controls',
				navText: [
				"",
				""
				],
			};

			var args = $.extend(defaults, carouselOptions);

			carousel.owlCarousel(args);
		},
		place_single_gallery_carousel: function() {
			var carousel = $('#place-single-gallery-carousel'),
				allItems = carousel.attr('data-images'),
				defaults = this.owlCarouselDefaults();
			
			carousel.on('initialized.owl.carousel', function(event) {
				
				var bigImage 	= $('.place-single-image'),
					thumbnail 	= carousel.find('.place-single-gallery-item');

				thumbnail.on('click', function() {
					var that 		= $(this),
						image_no 	= that.find('img').attr('data-big-image'),
						image 		= $('.place-single-image-big[data-thumbnail="' + image_no + '"]');

					if( image.length === 0 || image.hasClass('visible') ) {
						return;
					}

					bigImage.stop().animate({
						'opacity': 0
					},300, function() {
						bigImage.find('.place-single-image-big').removeClass('visible');
						image.addClass('visible');
						setTimeout(function() {
							bigImage.stop().animate({
								'opacity': 1
							}, 300);
						},200);
					});
				});

			});

			var carouselOptions = {
				loop: false,
				margin: 10,
				autoWidth: true,
				controlsClass: 'place-single-gallery-controls',
				navText: [
				"<i class='color3 absolute-center-both fa fa-angle-left'></i>",
				"<i class='color3 absolute-center-both fa fa-angle-right'></i>"
				],
				responsive: {
					0: {
						items: allItems < 3 ? allItems : 3,
					}
				}
			};

			var args = $.extend(defaults, carouselOptions);

			carousel.owlCarousel(args);
		},
		bootstrapSelectSettings: function() {
			// $('.selectpicker').on('loaded.bs.select', function (e) {
			// 	$(this).selectpicker('val', 'none');
			// });
			// $('.selectpicker').on('shown.bs.select', function (e) {
			// 	console.log('shown')
			// 	var that = $(this),
			// 		close_btn = that.parent().find('.close');
					
			// 	//close_btn.off('click');	
			// 	close_btn.on('click', function() {
			// 		that.selectpicker('val', 'none');
			// 	});
			// });
		},
		map_init: function() {
			$("#map-poland").CSSMap({
				responsive 	: false,
				tooltips 	: false,
				size		: 960,
				mapStyle 	: 'custom',
				disableClicks: $('.user-map-poland').length ? true : false,
				onClick 	: function(region) {
					var map 		= $('#map-poland'),
						hasInfo 	= map.attr('data-has-info');

					if(hasInfo == 'no') {
						return;
					}

					var	mapRegion 	= map.find('ul li'),
						link 		= map.attr('data-link-base'),
						that 		= $(region),
						pos 		= that.attr('data-info-position'),
						hash 		= that.find('a').attr('href'),
						county 		= that.find('a').text(),
						offsetLeft 	= that.find('span.m span.'+pos)[0].offsetLeft,
						offsetTop 	= that.find('span.m span.'+pos)[0].offsetTop,
						placesFound = that.attr('data-places-found'),
						word 		= ' obiektów';
					
					$('.map-info-box').remove();
				
					if(placesFound == 1) {
						word = ' obiekt';
					}
					if( (placesFound > 1 && placesFound < 5) || (placesFound > 21 && placesFound.toString().indexOf('5') != -1) || (placesFound > 21 && placesFound.toString().indexOf('1') != -1) ) {
						word = ' obiekty';
					}
					
					var boxLink = placesFound > 0 ? '<a href="' + link + hash + '" class="btn btn-primary text-uppercase btn-md">Zobacz obiekty</a>' : '';
					var box = $('<div></div>', {
						class 	: 'map-info-box text-center',
						html 	: '<p class="no-margin f30 font2 color3 bold">' + placesFound + word + '</p><p class="no-margin color3 text-uppercase">' + county + '</p>' + boxLink + '<div class="map-info-arrow"></div>',
						css 	: {
							position : 'absolute',
							'z-index' : 999
						}
					});

					box.appendTo(map).css('opacity', 0);

					var boxUp 		= box.outerHeight(),
						boxLeft 	= box.outerWidth()/2,
						diffTop 	= that.find('span.m span.'+pos).height()/2,
						diffLeft 	= that.find('span.m span.'+pos).width()/2;

					box.css({
						left : offsetLeft + diffLeft - boxLeft,
						top : offsetTop + diffTop - boxUp,
						opacity : 1
					});

				},
				onLoad: function(mapObject) {
					var markers 	= $('#map-poland-markers'),
						hasInfo 	= markers.attr('data-has-info');

					if(hasInfo == 'no') {
						return;
					}
					
					var	marker = markers.find('.map-marker');		

					marker.on('click', function() {
						var that 	= $(this),
							place 	= that.attr('data-place'),
							link 	= that.attr('data-place-link'),
							county 	= that.attr('data-county');

						$('.marker-info-box').remove();

						if( !that.hasClass('active') ) {
							var box = $('<div></div>', {
								class 	: 'marker-info-box map-info-box text-center',
								html 	: '<p class="no-margin f30 font2 color3 bold">' + place + '</p><p class="no-margin color3 text-uppercase">' + county + '</p><a href="' + link + '" class="btn btn-primary text-uppercase btn-md">Zobacz obiekt</a><div class="map-info-arrow"></div>',
								css 	: {
									position : 'absolute',
									'z-index' : 999
								}
							});

							box.appendTo(that).css('opacity', 0);

							var boxWidth 	= $('.marker-info-box').outerWidth(),
								markerWidth = that.width();

							box.css({
								left : -boxWidth/2 + markerWidth/2,
								opacity : 1
							});
							marker.removeClass('active');
							that.addClass('active');
						}else {
							that.removeClass('active');
						}


					});
				}
			});
		},
		comments_validation: function() {
			var form 	= $('#commentform'),
				comment = $('#comment');

			form.on('submit', function(e) {
				var text = $.trim(comment.val());
				
				if( text === '') {
					e.preventDefault();
					comment.addClass('field-error');
				}else {
					comment.removeClass('field-error');
				}
			});
		},
		comments_scroll_on_page_change: function() {
			var hash = window.location.hash;
			if( hash.length > 0 ) {
				if( $(hash).length ) {
					var offset = $(hash).offset().top;
					$('body, html').stop().animate({
						'scrollTop': offset - 100
					}, 1500);
				}
			}
		}
	};
	
	$(document).ready(function(e) {
		$('[data-toggle="tooltip"]').tooltip(); 
		APP.bootstrapSelectSettings();
		APP.map_init();
		APP.comments_validation();
	});
	$(window).on('load', function() {
		APP.home_slider_carousel();
		APP.place_single_gallery_carousel();
		APP.comments_scroll_on_page_change();
	});
	
})(jQuery, window, document);
(function() {

	var rfsAppDirectives = angular.module("rfsAppDirectives", []);

	rfsAppDirectives.directive('showOnLoad', function(){
		return {
			link: function(scope, element, attr) {
				if(attr.ngSrc === '') {
					scope.place.visible = true;
					scope.$emit('on-place-loaded', 'loaded');
				}else {
					element.on("load", function() {
						scope.$apply(function() {
							scope.place.visible = true;
							scope.$emit('on-place-loaded', 'loaded');
						});
					});
				}
			}
		};
	});

	rfsAppDirectives.directive('placeImg', function(){
		return {
			scope: {
				place: "="
			},
			template: '<img show-on-load data-ng-src="{{ place.imageSrc }}" alt="{{ place.imageAlt }}">'
		};
	});

	rfsAppDirectives.directive('placeItem', ["TPL_PATH", "VoteService", function(TPL_PATH, VoteService) {
		return {
			scope: {
				contestPlace: '@',
				place: '=',
				votingActive: '@'
			},
			link: function($scope, $element, $attrs) {
				$scope.voting = false;
				$scope.isVotingActive = $scope.votingActive == 1;
				
				if($scope.contestPlace) {
					$scope.voted = false;
					$scope.hasVoted = function(voted) {
						$scope.voted = voted;
					};
					
					$scope.voteOnPlace = function() {
						if( !$scope.voted && !$scope.voting ) {
							VoteService.vote($scope.place.id, $scope.contestPlace).then(function(response) {
								if(response.data == 'ok') {
									$scope.voted = true;
								}
							});
						}
						$scope.voting = true;
					};
				}
			},
			templateUrl: TPL_PATH + 'place-item.php'
		};
	}]);

	rfsAppDirectives.directive('piItem', function() {
		return {
			template: '<span data-toggle="tooltip" data-placement="top" title="{{ value }}" class="pi-icon pi-{{ key }}"></span>'
		};
	});

	rfsAppDirectives.directive('sortByOption', ["$rootScope", function($rootScope) {
		return {
			require: 'ngModel',
			scope: {
				'spoValue' : '='
			},
			link: function(scope, element, attrs) {
				scope.options 		= scope.$parent.sortOptions;
				scope.optionVal 	= attrs.spoValue;
				scope.optionLabel 	= attrs.spoLabel;

				element.bind('click', function() {
					element.toggleClass('active');
					if(element.hasClass('active')) {
						scope.options.push(attrs.spoValue);
					}else {
						var optionPos = scope.options.indexOf(attrs.spoValue);
						scope.options.splice(optionPos, 1);
					}
				});
				$rootScope.$on("sortOff", function(event, data) {
					element.removeClass('active');
				});
			},
			template: '<div class="inline-block spo-checkbox"><i class="fa fa-square absolute-center-both"></i><i class="fa fa-square-o absolute-center-both"></i></div><span class="pi-icon pi-{{ optionVal }}"></span><p class="inline-block text-uppercase color2 no-margin">{{ optionLabel }}</p>'
		};
	}]);

	rfsAppDirectives.directive('regionItem', ["TPL_PATH", function(TPL_PATH) {
		return {
			scope: {
				singleItem: '=',
				itemType: '@'
			},
			templateUrl: TPL_PATH + 'region-item.php',
			link : function($scope, $element, $attrs) {
				var image = angular.element($element).find('img');
				image.on('load', function() {
					$scope.$apply(function() {
						$scope.singleItem.visible = true;
						$scope.$emit('on-region-loaded', 'loaded');
					});
				});
			}
		};
	}]);

})();
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
(function() {

	var rfsAppControllers = angular.module("rfsAppControllers", ['ngSanitize', "ngAnimate", "duScroll"]);

	rfsAppControllers.controller("PageLoadedController", ["$scope", "$window", "$timeout", "SiteLoader", function($scope, $window, $timeout, SiteLoader){

		$window.onload = function(){
			$timeout(function() {
				angular.element('.body-inner').addClass('view-loaded');
			}, 500);
			SiteLoader.onSiteLoaded(true);
		};

	}]);

	rfsAppControllers.controller("PageLoaderController", ["$scope", "SiteLoader", function($scope, SiteLoader){
		$scope.$watch(SiteLoader.showLoader,function () {
			$scope.siteLoaded = SiteLoader.showLoader();
		});
	}]);

	rfsAppControllers.controller("LoadPlacesCtrl", ["$scope", "$rootScope", "$document", "AJAXservice", "PlacesSorting", "scrollToAnchor", function($scope, $rootScope, $document, AJAXservice, PlacesSorting, scrollToAnchor) {
		
		$scope.places 			= [];
		$scope.page 			= 1;
		$scope.lastpage 		= false;
		$scope.hasData 			= true;
		$scope.isFilter 		= false;
		$scope.notFoundByFilter = false;
		$scope.FoundByFilter 	= 0;
		$scope.isSortOn 		= false;
		$scope.exclude 			= [];

		$scope.currentFilter 	= {};
		
		$scope.dynamicData 		= {};
		
		$scope.placeHeight 		= 0;
		$scope.enableScroll 	= false;
		var placesAnchor 		= angular.element('#placesAnchor');

		$scope.$watch(scrollToAnchor.enableScroll, function(value) {
			if($scope.enableScroll === true) return;
			$scope.enableScroll = value;
		});


		$scope.loadPlaces = function(dynamicData, sortby) {
			$scope.loading = true;
			
			var staticData = {
				action: 'loadPlaces',
				ppp: 4,
				page: $scope.page,
				exclude: $scope.exclude,
			};
			
			var data = angular.extend(staticData, dynamicData);

			AJAXservice.ajaxQuery(data).then(function(response) {
				var res = response.data;
				console.log(res)
				if(res[0] == 'ok') {
					PlacesSorting.setSorting(false);

					res[1].forEach(function(element) {
						$scope.places.push(element);
					});

					if($scope.page == 1) {
						$scope.notFoundByFilter = res[3];
						$scope.FoundByFilter 	= res[5];
					}

					$scope.page 			+= 1;
					$scope.hasData 			= true;
					$scope.lastpage 		= res[2];
					
					for(var i=0; i<res[4].length; i++) {
						//if( $scope.exclude.indexOf(res[4][i]) !== -1) {
							$scope.exclude.push(res[4][i]);
						//}
					}

				}else if(res[0] == 'nodata') {
					$scope.hasData = false;
					$scope.loading = false;
				}
					$scope.loading = false;
			});
		};

		$scope.imagesLoaded = [];

		$scope.$on('on-place-loaded', function(event, data){
			$scope.imagesLoaded.push(data);
			if($scope.imagesLoaded.length == $scope.places.length) {
				$scope.loading = false;
				$('[data-toggle="tooltip"]').tooltip(); 
				scrollToAnchor.scrollMe($scope.places, '.place-item', 4, 70, $scope.enableScroll, placesAnchor);
			}
		});
		
		$scope.initLoad = function(dynamicData) {
			$scope.loadPlaces(dynamicData);
		};

		$rootScope.$on("FilterBy", function(event, data) {
			PlacesSorting.setSorting(false);
			$rootScope.$emit("sortOff", {});

			$scope.isFilter 			= true;
			$scope.exclude		 		= [];
			$scope.hasData 				= true;
			$scope.imagesLoaded		 	= [];
			$scope.places		 		= [];
			$scope.page 				= 1;
			$scope.currentFilter 		= data;
			$scope.loadPlaces(data);
		});

		$rootScope.$on("SortBy", function(even, data) {
			
			$scope.isSortOn 			= true;
			$scope.hasData 				= true;
			$scope.imagesLoaded 		= [];
			$scope.places 				= [];
			$scope.page 				= 1;
			$scope.exclude 				= [];

			if($scope.isFilter) {
				data.filter 	= $scope.currentFilter.filter;
				data.filterType = $scope.currentFilter.filterType;
			}
			$scope.loadPlaces(data, true);
		});

		$scope.$watch(PlacesSorting.isSorting, function() {
			$scope.sorting = PlacesSorting.isSorting();
		}, true);

	}]);

	rfsAppControllers.controller('SearchPlacesCtrl', ['$scope', "$rootScope", "$document", "$location", "$timeout", "PlacesSorting", function($scope, $rootScope, $document, $location, $timeout, PlacesSorting){

		$scope.ftIsRegion 	= false;
		$scope.filterby 	= 'none';
		$scope.spfInvalid 	= false;
		$scope.filterType   = 'county';
		$scope.sortOptions 	= [];

		angular.element('.selectpicker').on('loaded.bs.select', function (e) {
			if( !$location.hash() ) {
				angular.element('.selectpicker').selectpicker('val', 'none');
			}
		});

		angular.element('.selectpicker#spf-counties').on('loaded.bs.select', function (e) {
			if( $location.hash() ) {
				var county = angular.element('#spf-counties [data-county="'+$location.hash()+'"]').val();

				if(angular.isUndefined(county)) {
					return;
				}
				angular.element('.selectpicker').selectpicker('val', county);
				$scope.filterby = county;
				$timeout(function() {
					angular.element('.spf-filter-submit .btn').trigger('click');
				},500);
				
			}
		});

		angular.element('.selectpicker#spf-regions').on('loaded.bs.select', function (e) {
			if( $location.hash() ) {
				var region = angular.element('#spf-regions [data-region="'+$location.hash()+'"]').val();

				if(angular.isUndefined(region)) {
					return;
				}

				$scope.ftIsRegion = true;

				$scope.$watch('ftIsRegion', function(val) {
					if(val === true) {
						angular.element('.selectpicker').selectpicker('val', region);

						$timeout(function() {
							angular.element('.spf-filter-submit .btn').trigger('click');
						},500);
					}
				});

				$scope.filterby = region;
				$scope.filterType   = 'region';
				
			}
		});

		$scope.$watch('ftIsRegion', function(newValue, oldValue) {
			if (newValue !== oldValue) {
				if( !$location.hash() ) {
					$scope.filterby = 'none';
				}
				angular.element('.selectpicker').selectpicker('val', 'none');
			}
		});

		$scope.$watch('ftIsRegion', function(newValue, oldValue) {
			$scope.filterType = $scope.ftIsRegion === false ? 'county' : 'region';
		});

		$scope.spfCheckValue = function() {
			$scope.spfInvalid = $scope.filterby == 'none' ? true : false;
		};

		$scope.filterBy = function() {
			$scope.spfInvalid = $scope.filterby == 'none' ? true : false;

			if($scope.filterby == 'none') {
				return;
			}

			$rootScope.$emit("FilterBy", {
				filter : $scope.filterby,
				filterType: $scope.filterType
			});
		};

		$scope.sortBy = function() {
			if($scope.sortOptions.length > 0) {
				$rootScope.$emit("SortBy", {sortBy: $scope.sortOptions});
				PlacesSorting.setSorting(true);
			}
		};

		$rootScope.$on("sortOff", function(event, data) {
			$scope.sortOptions.length = 0;
		});

		$scope.$watch(PlacesSorting.isSorting, function() {
			$scope.sorting = PlacesSorting.isSorting();
		}, true);
		
	}]);

	rfsAppControllers.controller('RegionsCtrl', ["$scope", "$window", "scrollToAnchor", "AJAXservice", function($scope, $window, scrollToAnchor, AJAXservice) {

		$scope.region = '';

		angular.element('.selectpicker').on('loaded.bs.select', function (e) {
			var select = angular.element('.selectpicker'),
				dropdown = angular.element('.regions-list .bootstrap-select');

			select.selectpicker('val', 'none');

			// fix for angular adding undefined as first option
			var firstOption = select.find('option').eq(0).val();
			
			if( firstOption != 'none' ) {
				select.find('option').eq(0).remove();
				dropdown.find('.dropdown-menu.inner li').eq(0).remove();

				angular.forEach(dropdown.find('.dropdown-menu.inner li'), function(value, key){
					value.setAttribute('data-original-index', key);
				});
			}
			// end fix
		});

		$scope.goToRegion = function(dest_url) {
			
			if( $scope.region == 'none' ) {
				return;
			}
			history.pushState({}, "regions-page", $window.location.href);
			//$window.location.replace(dest_url + '#' + $scope.region);
			$window.location.replace($scope.region);

		};

		// Loading more regions
		$scope.regions 			= [];
		$scope.loading 			= false;
		$scope.lastpage 		= false;
		$scope.page 			= 2;
		$scope.error 			= false;
		$scope.regionsLoaded 	= [];

		$scope.enableScroll 	= true;
		var anchor 				= angular.element('#regionsAnchor');

		$scope.$watch(scrollToAnchor.enableScroll, function(value) {
			if($scope.enableScroll === true) return;
			$scope.enableScroll = value;
		});

		$scope.loadRegions = function() {
			$scope.loading = true;

			var data = {
				action 	: 'load_regions',
				nonce 	: frontendajax.load_regions_nonce,
				page 	: $scope.page
			};
			
			AJAXservice.ajaxQuery(data).then(function(response) {
				var res = response.data;
				
				if(res[0] == 'ok') {
					res[1].forEach(function(element) {
						$scope.regions.push(element);
					});
					$scope.page = res[2];
					$scope.lastpage = res[3];
				}else if(res[0] == 'nonce') {
					
				}
			});
		};

		$scope.$on('on-region-loaded', function(event, data) {
			$scope.regionsLoaded.push(data);
			if($scope.regionsLoaded.length == $scope.regions.length) {
				$scope.loading = false;
				scrollToAnchor.scrollMe($scope.regions, '.region-item', 4, 0, $scope.enableScroll, anchor);
			}
		});

	}]);

	rfsAppControllers.controller('WinnersCtrl', ['$scope', '$window', 'scrollToAnchor', 'AJAXservice', function($scope, $window, scrollToAnchor, AJAXservice) {

		$scope.winners 		= [];
		$scope.loading 		= false;
		$scope.lastpage 	= false;
		$scope.page 		= 1;
		$scope.noData 		= false;

		$scope.winnersLoaded 	= [];

		$scope.enableScroll 	= true;
		var anchor 				= angular.element('#placesWinnersAnchor');

		$scope.loadWinners = function(dynamicData) {
			$scope.loading = true;
			
			var staticData = {
				action: 'loadWinners',
				nonce: frontendajax.load_winners_nonce,
				ppp: 4,
				page: $scope.page,
			};

			var data = angular.extend(staticData, dynamicData);

			AJAXservice.ajaxQuery(data).then(function(response) {
				var res = response.data;
				
				if(res[0] == 'ok') {

					res[1].forEach(function(element) {
						$scope.winners.push(element);
					});

					$scope.page 	+= 1;
					$scope.lastpage = res[2];
				}else {
					$scope.noData = true;
				}

			});
		};

		$scope.winnersInit = function(data) {
			$scope.loadWinners(data);
		};

		$scope.$on('on-region-loaded', function(event, data) {
			$scope.winnersLoaded.push(data);
			if($scope.winnersLoaded.length == $scope.winners.length) {
				$scope.loading = false;
			}
		});

	}]);


	rfsAppControllers.controller('LoginCtrl', ["$scope", "$window", "$location", "AJAXservice", function($scope, $window, $location, AJAXservice) {

		$scope.forms 		= {};
		$scope.formData 	= {};
		$scope.view 	 	= 'login';
		$scope.loading 		= false;
		$scope.referrer 	= '';
		
		$scope.registered 	= false;
		$scope.success 		= false;
		$scope.reset 		= false;
		// error messages
		function clearErrors() {
			$scope.iserror 		= false;
			$scope.error 		= false;
			$scope.emailError 	= false;
			$scope.wrong 		= false;
			$scope.userexists 	= false;
			$scope.emailexists 	= false;
			$scope.notexists 	= false;

			$scope.notactive 	= false;
		}
		clearErrors();

		function clearForm(type, data) {
			data = data || false;
			if(angular.isDefined($scope.forms[type])) {
				$scope.forms[type].$setPristine();
				$scope.forms[type].$setUntouched();
			}
			if(data) {
				$scope.formData = {};
			}
		}

		$scope.changeView = function(view) {
			$scope.view 	= view;
			clearErrors();
			$scope.success = false;
			$scope.reset   = false;

			angular.forEach($scope.forms, function(val, key) {
				clearForm(key);
			});
		};

		
		$scope.$watch('forms.registerForm.$valid', function(newValue, oldValue) {
			if( newValue != oldValue ) {
				$scope.iserror = !newValue;
			}
		});
		

		if($location.hash() == 'rejestracja') {
			$scope.changeView('register');
			$location.hash('');
		}

		$scope.processLogin = function(type) {
			clearErrors();
			$scope.success = false;
			$scope.reset   = false;

			switch(type) {
				case 'login':
					if( $scope.forms.loginForm.$invalid ) {
						$scope.iserror = true;
						return;
					}
					$scope.formData.loginReferrer = $scope.referrer;	
					break;
				case 'register':
					if( $scope.forms.registerForm.$invalid ) {
						$scope.iserror = true;	
						return;
					}
					break;
				case 'remind':
					if( $scope.forms.remindForm.$invalid ) {
						$scope.iserror = true;	
						return;
					}
					break;
				default:
					break;
			}

			$scope.loading = true;
			
			var data = {
				action 	 : 'process_login',
				nonce 	 : frontendajax.login_nonce,
				type 	 : type,
				formData : $scope.formData
			};

			AJAXservice.ajaxQuery(data).then(function(response) {
				var res = response.data;
				
				switch(res[0]) {
					case 'ok':
						switch(type) {
							case 'login':
								$window.location.replace(res[1]);
								break;
							case 'register':
								$scope.success 		= true;
								$scope.registered 	= true;	
								$scope.loading 		= false;
								clearErrors();
								clearForm('register', true);
								break;
							case 'remind':
								$scope.success 		= true;
								$scope.reset 		= true;
								$scope.loading 		= false;
								clearErrors();
								clearForm('remind', true);
								break;
						}
						break;
					case 'error':
						$scope.iserror 	= true;		
						$scope.loading 	= false;

						switch(res[1]) {
							case 'wrong':
								$scope.wrong = true;
								break;
							case 'email':
								$scope.emailError = true;	
								break;
							case 'emailexists':
								$scope.emailexists = true;
								break;
							case 'userexists':
								$scope.userexists = true;
								break;
							case 'notexists':
								$scope.notexists = true;
								break;
							case 'nonce':
								$scope.error = true;
								break;
							case 'notactive':
								$scope.notactive = true;
								break;
						}

						break;
					default:
						$scope.error = true;
						break;
				}
			});

		};

	}]);

	rfsAppControllers.controller('PremiumCtrl', ["$scope", "$window", "AJAXservice", function($scope, $window, AJAXservice){
		
		$scope.promosSelected 		= [];
		$scope.showPayMethods 		= false;
		$scope.selectedSubscription = '';
		$scope.nothingSelected 		= true;
		$scope.paying 				= false;
		$scope.paymentError 		= false;
		$scope.showInvoices 		= false;
		
		$scope.$watch('tax', function(val) {
			$scope.tax = val;
		});

		_nothingSelected = function() {
			if( $scope.selectedSubscription === '' && $scope.promosSelected.length < 1 ) {
				$scope.nothingSelected = true;
			}else {
				$scope.nothingSelected = false;
				_summary_row($scope.selectedSubscription, $scope.promosSelected);
			}
		};

		_tableRow = function(product, term, price) {
			return '<tr><td>' + product +'</td><td>' + term + '</td><td class="summary-row-price"><span>' + price + '</span> PLN</td></tr>';
		};

		_summarySumPrices = function(prices) {
			var vatHtml 	= angular.element('.summary-vat'),
				vat 		= 0,
				toPay 		= 0;
				totalHtml 	= angular.element('.summary-total');

			if( prices === undefined || prices.length < 1 ) return;

			var total = prices.reduce(function(prev, current) {
				return parseFloat(prev) + parseFloat(current);
			});

			vat = total * (parseFloat($scope.tax)/100);
			toPay = parseFloat(total) + parseFloat(vat);
			
			vatHtml.text( parseFloat(vat).toFixed(2) + ' PLN' );
			totalHtml.text( parseFloat(toPay).toFixed(2) + ' PLN' );
		};

		_summary_row = function(subscription, promos) {
			var table 			= angular.element('.summary-table'),
				tableRow 		= '',
				promoProduct 	= '',
				prices 			= [];

			if( subscription !== '' ) {
				var subscriptionProduct = angular.element('#subscription-box-'+subscription);

				if( subscriptionProduct.length > 0 ) {
					tableRow += _tableRow( subscriptionProduct.find('.subscription-name').text(), subscriptionProduct.find('.subscription-term').text(), subscriptionProduct.find('.subscription-price span').text() );

					prices.push( subscriptionProduct.find('.subscription-price span').text() );
				}
			}

			if( promos !== undefined && promos.length > 0) {
				angular.forEach(promos, function(promo, index) {
					promoProduct = angular.element('#promo-box-'+promo);

					if( promoProduct.length > 0 ) {
						tableRow += _tableRow( 'Reklama - ' + promoProduct.find('.promo-name-name').text(), '30 dni', promoProduct.find('.promo-price').text() );

						prices.push( promoProduct.find('.promo-price').text() );
					}
				});
			}
			
			_summarySumPrices(prices);

			table.find('tbody').html(tableRow);
		};

		$scope.selectSubscription = function(id) {
			$scope.selectedSubscription = $scope.selectedSubscription == id ? '' : id;
			_nothingSelected();
		};

		$scope.selectPromo = function(id, event) {
			var that = angular.element(event.currentTarget);

			that.toggleClass('checked');
			if(that.hasClass('checked')) {
				$scope.promosSelected.push(id);
			}else {
				var promoPos = $scope.promosSelected.indexOf(id);
				$scope.promosSelected.splice(promoPos, 1);
			}
			_nothingSelected();
		};

		$scope.promoSelected = function(id) {
			return $scope.promosSelected.indexOf(id) > -1;
		};

		$scope.showPaymentMethods = function() {
			$scope.showPayMethods = $scope.showPayMethods ? false : true;

			if( $scope.selectedSubscription === '' && $scope.promosSelected.length < 1 ) {
				$scope.nothingSelected = true;
				//nothing selected - do nothing or message select anything
			}
		};

		$scope.showInvoicesList = function() {
			$scope.showInvoices = !$scope.showInvoices;
		}

		$scope.makePayment = function(e, method) {
			var paymentBtn 	= angular.element('.payment-btn'),
				btn 		= angular.element(e.target),
				loader 		= btn.find('.loader');

			if( $scope.paying ) return;
			
			$scope.paymentError = false;
			btn.addClass('active');
			paymentBtn.not('.active').addClass('inactive');
			loader.css('opacity', 1);
			
			var data = {
				action 	 		: 'make_payment',
				nonce 	 		: frontendajax.payment_nonce,
				method 			: method,
				subscription 	: $scope.selectedSubscription,
				promos 			: $scope.promosSelected
			};

			AJAXservice.ajaxQuery(data).then(function(response) {
				var res = response.data;
				
				switch(res[0]) {
					case 'ok':
						$window.location.replace(res[1]);
						break;
					case 'error':
						$scope.paymentError = true;
						break;
				}
			}, function(error) {
				$scope.paymentError = true;
			});

			$scope.paying = true;
		};

	}]);

	rfsAppControllers.controller('userActionsCtrl', ['$scope', 'AJAXservice', function($scope, AJAXservice) {

		$scope.seen 		= false;
		$scope.visit 		= false;
		$scope.loading 	= false;
		$scope.action 	= false;
		$scope.place 		= 0;

		$scope.tooltip = {}

		$scope.$watch('place', function(val) {
			$scope.place = val;
		});

		$scope.$watch('visit', function(val) {
			$scope.visit = val;
			$scope.tooltip.tovisit = $scope.visit ? 'Obiekt znajduje się na Twojej MAPIE MIEJSC Z DUSZĄ' : 'Zaznacz obiekt na Twojej MAPIE MIEJSC Z DUSZĄ';

			angular.element('.pua-to-visit').parent().tooltip('hide').attr('data-original-title', $scope.tooltip.tovisit).tooltip('fixTitle');
		});

		$scope.$watch('seen', function(val) {
			$scope.seen = val;
			$scope.tooltip.seen = $scope.seen == true ? 'Obiekt znajduje się na Twojej MAPIE MIEJSC Z DUSZĄ' : 'Dodaj obiekt do Twojej MAPY MIEJSC Z DUSZĄ';

			angular.element('.pua-seen').parent().tooltip('hide').attr('data-original-title', $scope.tooltip.seen).tooltip('fixTitle');
		});

		$scope.userAction = function(type) {
			if( $scope.loading ) return;

			if( $scope.seen && type == 'visit' ) return;
			
			$scope.action = type;

			$scope.loading = true;
			
			var data = {
				action: 'user_actions',
				place_id: $scope.place,
				nonce : frontendajax.user_action_nonce,
				type: type,
				do: $scope[type] ? 'remove' : 'add'
			}

			AJAXservice.ajaxQuery(data).then(function(response) {
				var res = response.data;
				
				if( res[0] == 'ok' ) {
					$scope[type] = !$scope[type];
					if( type == 'seen' ) {
						$scope.visit = false;
					}
				}
				$scope.loading 	= false;
			});
		}

	}]);

	rfsAppControllers.animation('.slide-toggle', function() {
		return {
			beforeAddClass : function(element, className, done) {
				if (className === 'ng-hide') {
					element.slideUp(500, done);
				} else {
					done();
				}
			},
			removeClass : function(element, className, done) {
				if (className === 'ng-hide') {
					element.hide().slideDown(500, done);
				} else {
					done();
				}
			}
		};
	});

})();
(function() {

	var app = angular.module('rfsApp', ["rfsAppDirectives",  "rfsAppServices", "rfsAppControllers"]);

	app.constant("AJAX_URL", frontendajax.ajaxurl);
	app.constant("TPL_PATH", angular.element('body').attr('data-site-url') + '/template-parts/app-templates/');
	app.config(["$httpProvider", function($httpProvider) {
		
			$httpProvider.defaults.cache = true;
			$httpProvider.defaults.headers.post = {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8;'};

	}]);


})();