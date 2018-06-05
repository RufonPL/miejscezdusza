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