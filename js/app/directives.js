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