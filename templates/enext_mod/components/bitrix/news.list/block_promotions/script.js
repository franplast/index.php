(function(window) {
	'use strict';

	if(!window.JCNewsListPromoComponent) {
		window.JCNewsListPromoComponent = function(params) {
			this.container = BX(params.container);
			this.itemsCount = params.itemsCount;

			BX.ready(BX.delegate(this.init, this));
		};

		window.JCNewsListPromoComponent.prototype = {
			init: function() {
				BX.addClass(this.container, 'owl-carousel');
				$(this.container).owlCarousel({
					nav: true,
					navText: ['<div class="owl-prev-icon"><i class="icon-arrow-left"></i></div>', '<div class="owl-next-icon"><i class="icon-arrow-right"></i></div>'],
					navContainer: '.promotions-items-container',
					dots: false,
					responsive: {
						0: {
							items: 1
						},
						992: {
							items: this.itemsCount > 3 ? 3 : this.itemsCount
						}
					}
				});
			}
		}
	}
	
	if(!window.JCNewsListPromo) {
		window.JCNewsListPromo = function(arParams) {		
			this.visual = {
				ID: ''
			};
			
			this.item = {
				activeTo: {}
			};
			
			this.obItem = null;
			
			this.errorCode = 0;
			
			if(typeof arParams === 'object') {
				this.visual = arParams.VISUAL;
				this.item.activeTo = arParams.ITEM.ACTIVE_TO;

				BX.ready(BX.delegate(this.init, this));
			}
		};

		window.JCNewsListPromo.prototype = {
			init: function() {
				this.obItem = BX(this.visual.ID);
				if(!this.obItem) {
					this.errorCode = -1;
				}

				if(this.errorCode === 0) {
					var itemTimer = this.obItem.querySelector('[data-entity="timer"]');
					if(!!itemTimer) {
						$(itemTimer).countdown({
							until: new Date(this.item.activeTo.YYYY, this.item.activeTo.MM - 1, this.item.activeTo.DD, this.item.activeTo.HH ? this.item.activeTo.HH : '00', this.item.activeTo.MI ? this.item.activeTo.MI : '00'),
							padZeroes: true,
							expiryText: BX.message('PROMOTIONS_ITEM_COMPLETED'),
							alwaysExpire: true
						});
					}
				}
			}
		}
	}
})(window);