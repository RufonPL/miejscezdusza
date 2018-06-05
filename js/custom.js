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