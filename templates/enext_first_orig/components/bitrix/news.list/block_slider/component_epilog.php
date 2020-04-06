<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $arSettings;

$smartSpeed = $arSettings["SMART_SPEED"]["VALUE"] ? $arSettings["SMART_SPEED"]["VALUE"] : 1000;
$loop = count($arResult["ELEMENTS"]) > 1 ? "true" : "false";
$autoplayTimeout = $arSettings["AUTOPLAY_TIMEOUT"]["VALUE"] ? $arSettings["AUTOPLAY_TIMEOUT"]["VALUE"] : 5000;
$animateOut = $arSettings["ANIMATE_OUT"]["VALUE"] != "none" ? "'".$arSettings["ANIMATE_OUT"]["VALUE"]."'" : "false";
$animateIn = $arSettings["ANIMATE_IN"]["VALUE"] != "none" ? "'".$arSettings["ANIMATE_IN"]["VALUE"]."'" : "false";

$APPLICATION->AddHeadString("
	<style type='text/css'>
		.owl-carousel .animated{
			-webkit-animation-duration:".$smartSpeed."ms;
			animation-duration:".$smartSpeed."ms;
		}
	</style>
", true);

$APPLICATION->AddHeadString("
	<script type='text/javascript'>		
		$(function() {
			var slider = document.body.querySelector('.slider');
			if(!!slider) {
				$(slider).owlCarousel({
					items: 1,
					loop: ".$loop.",
					nav: true,
					navText: ['<i class=\"icon-arrow-left\"></i>', '<i class=\"icon-arrow-right\"></i>'],				
					autoplay: true,
					autoplayTimeout: ".$autoplayTimeout.",			
					autoplayHoverPause: true,
					smartSpeed: ".$smartSpeed.",
					responsiveRefreshRate: 0,
					animateOut: ".$animateOut.",
					animateIn: ".$animateIn.",
					navContainer: '.slider'
				});
				
				function sliderItemVideoPlay() {
					var sliderItemVideoAll = slider.querySelectorAll('.slider-item__video');					
					if(!!sliderItemVideoAll) {
						for(var i in sliderItemVideoAll) {
							if(sliderItemVideoAll.hasOwnProperty(i)) {
								var owlItem = BX.findParent(sliderItemVideoAll[i], {className: 'owl-item'});
								if(!!owlItem && BX.hasClass(owlItem, 'active'))
									sliderItemVideoAll[i].play();
							}
						}
					}
				}
				
				sliderItemVideoPlay();
				BX.bind(window, 'resize', function() {
					sliderItemVideoPlay();
				});
				
				$(slider).on('translate.owl.carousel', function(event) {
					var sliderItemVideoAll = slider.querySelectorAll('.slider-item__video');					
					if(!!sliderItemVideoAll) {
						for(var i in sliderItemVideoAll) {
							if(sliderItemVideoAll.hasOwnProperty(i)) {
								sliderItemVideoAll[i].pause();
							}
						}
					}

					var sliderItemBlock = slider.querySelectorAll('.slider-item__block');
					if(!!sliderItemBlock) {
						for(var i in sliderItemBlock) {
							if(sliderItemBlock.hasOwnProperty(i)) {
								BX.removeClass(sliderItemBlock[i], 'fadeInLeftBig');
								BX.style(sliderItemBlock[i], 'opacity', '0');
							}
						}
					}
				});
				
				$(slider).on('translated.owl.carousel', function(event) {
					var sliderItemVideoAll = slider.querySelectorAll('.slider-item__video');					
					if(!!sliderItemVideoAll) {
						for(var i in sliderItemVideoAll) {
							if(sliderItemVideoAll.hasOwnProperty(i)) {
								var owlItem = BX.findParent(sliderItemVideoAll[i], {className: 'owl-item'});
								if(!!owlItem && BX.hasClass(owlItem, 'active'))
									sliderItemVideoAll[i].play();
							}
						}
					}

					var sliderItemBlock = slider.querySelectorAll('.slider-item__block');
					if(!!sliderItemBlock) {
						for(var i in sliderItemBlock) {
							if(sliderItemBlock.hasOwnProperty(i)) {
								var owlItem = BX.findParent(sliderItemBlock[i], {className: 'owl-item'});
								if(!!owlItem && BX.hasClass(owlItem, 'active')) {
									BX.style(sliderItemBlock[i], 'opacity', '1');
									BX.addClass(sliderItemBlock[i], 'fadeInLeftBig');								
								}								
							}
						}
					}
				});

				BX.addCustomEvent(window, 'slideMenu', function() {
					$(slider).trigger('refresh.owl.carousel');
				});
			}
		});		
	</script>
", true);?>