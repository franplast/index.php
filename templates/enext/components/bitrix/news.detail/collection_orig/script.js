(function() {
	'use strict';

	if(!!window.JCNewsDetailCollection)
		return;
	
	window.JCNewsDetailCollection = function(arParams) {
		this.config = {
			IMG_LAZYLOAD: true,
			PARAMS: ''
		};
		
		this.visual = {
			ID: ''
		};
		
		this.item = {
			productsIds: ''
		};

		this.obItem = null;
		
		this.obTabs = null;
		this.obTabsBlock = null;
		this.obTabContainers = null;

		this.obSectionsLinks = null;
		
		this.errorCode = 0;
		
		if(typeof arParams === 'object') {
			this.config = arParams.CONFIG;
			this.visual = arParams.VISUAL;
			this.item.productsIds = arParams.ITEM.PRODUCTS_IDS;

			BX.ready(BX.delegate(this.init, this));
		}
	};

	window.JCNewsDetailCollection.prototype = {
		init: function() {
			this.obItem = BX(this.visual.ID);
			if(!this.obItem) {
				this.errorCode = -1;
			}

			if(this.errorCode === 0) {
				this.obTabs = this.obItem.querySelector('.collections-detail-tabs-container');
				this.obTabsBlock = !!this.obTabs && this.obTabs.querySelector('[data-entity="tabs"]');
				this.obTabContainers = this.obItem.querySelector('.collections-detail-tabs-content');
				
				if(!!this.obTabs) {
					this.initTabs();
					
					if(this.obTabsBlock) {
						this.tabsPanelFixed = false;
						this.tabsPanelScrolled = false;
						this.lastScrollTop = 0;
						this.checkTopTabsBlockScroll();
						BX.bind(window, 'scroll', BX.proxy(this.checkTopTabsBlockScroll, this));
						BX.bind(window, 'resize', BX.proxy(this.checkTopTabsBlockResize, this));

						this.checkActiveTabsBlock();
						BX.bind(window, 'scroll', BX.proxy(this.checkActiveTabsBlock, this));
						BX.bind(window, 'resize', BX.proxy(this.checkActiveTabsBlock, this));
					}
				}
				
				var slider = this.obItem.querySelector('.collections-item-detail-slider');
				if(!!slider) {
					BX.addClass(slider, 'owl-carousel');
					$(slider).owlCarousel({
						nav: true,
						navText: ['<div class="owl-prev-icon"><i class="icon-arrow-left"></i></div>', '<div class="owl-next-icon"><i class="icon-arrow-right"></i></div>'],
						navContainer: '.collections-item-detail-slider-container',
						dots: false,
						responsive: {
							0: {
								stagePadding: 0,
								items: 3
							},
							992: {
								stagePadding: 100,
								items: 3
							},
							1272: {
								stagePadding: 100,
								items: 4
							},
							1440: {
								stagePadding: 100,
								items: 5
							},
							1920: {
								stagePadding: 200,
								items: 5
							}
						}
					});

					var sliderItems = slider.querySelectorAll('.fancyimage');
					if(!!sliderItems) {
						$(sliderItems).fancybox({
							helpers: {
								title: {
									type: 'inside',
									position: 'bottom'
								}
							}
						});
					}
				}
				
				this.obSectionsLinks = this.obItem.querySelector('.collections-detail-sections-links');
				if(!!this.obSectionsLinks)
					this.initSectionsLinks();
			}
		},

		initTabs: function() {
			var tabsList = this.obTabs.querySelector('.collections-detail-tabs-list'),
				tabs = !!tabsList && tabsList.querySelectorAll('[data-entity="tab"]'),
				tabValue, targetTab, haveActive = false;			
			
			if(!!tabsList) {
				BX.addClass(tabsList, 'owl-carousel');
				$(tabsList).owlCarousel({								
					autoWidth: true,
					nav: true,
					navText: ['<i class=\"icon-arrow-left\"></i>', '<i class=\"icon-arrow-right\"></i>'],
					navContainer: '.collections-detail-tabs-scroll',
					dots: false,			
				});
				
				if(!!tabs) {
					for(var i in tabs) {
						if(tabs.hasOwnProperty(i) && BX.type.isDomNode(tabs[i])) {
							tabValue = tabs[i].getAttribute('data-value');
							if(tabValue) {
								targetTab = this.obTabContainers.querySelector('[data-value="' + tabValue + '"]');
								if(BX.type.isDomNode(targetTab)) {
									BX.bind(tabs[i], 'click', BX.proxy(this.changeTab, this));

									if(!haveActive) {
										BX.addClass(tabs[i], 'active');
										haveActive = true;
									} else {
										BX.removeClass(tabs[i], 'active');
									}
								}
							}
						}
					}
				}
			}
		},

		checkTopTabsBlockScroll: function() {
			var topPanel = document.querySelector('.top-panel'),
				topPanelHeight = 0,
				topPanelThead = !!topPanel && topPanel.querySelector('.top-panel__thead'),
				topPanelTfoot = !!topPanel && topPanel.querySelector('.top-panel__tfoot'),
				tabsPanelContainerTop = BX.pos(this.obTabs).top,
				tabsPanel = this.obTabsBlock,				
				tabsPanelHeight = tabsPanel.offsetHeight,
				scrollTop = BX.GetWindowScrollPos().scrollTop;
			
			if(window.innerWidth < 992) {
				if(!!topPanelThead && !!BX.hasClass(topPanelThead, 'fixed')) {
					topPanelHeight = topPanelThead.offsetHeight;
					if(!!topPanelTfoot && !!BX.hasClass(topPanelTfoot, 'visible'))
						topPanelHeight += topPanelTfoot.offsetHeight;
				}

				if(scrollTop + topPanelHeight >= tabsPanelContainerTop) {
					if(!this.tabsPanelFixed) {
						this.tabsPanelFixed = true;
						BX.style(this.obTabs, 'height', tabsPanelHeight + 'px');				
						BX.style(tabsPanel, 'top', topPanelHeight + 'px');	
						BX.addClass(tabsPanel, 'fixed');
					} else {
						if(!this.tabsPanelScrolled && topPanelHeight > 0 && scrollTop < this.lastScrollTop) {
							this.tabsPanelScrolled = true;
							var tabsPanelScrolled = this.tabsPanelScrolled;
							new BX.easing({
								duration: 300,
								start: {top: Math.abs(parseInt(BX.style(tabsPanel, 'top'), 10))},
								finish: {top: topPanelHeight},
								transition: BX.easing.transitions.linear,
								step: function(state) {
									if(!!tabsPanelScrolled)
										BX.style(tabsPanel, 'top', state.top + 'px');								
								}
							}).animate();
						} else if(!!this.tabsPanelScrolled && topPanelHeight > 0 && scrollTop > this.lastScrollTop) {
							this.tabsPanelScrolled = false;
							new BX.easing({
								duration: 300,
								start: {top: Math.abs(parseInt(BX.style(tabsPanel, 'top'), 10))},
								finish: {top: topPanelHeight},
								transition: BX.easing.transitions.linear,
								step: function(state) {
									BX.style(tabsPanel, 'top', state.top + 'px');								
								}
							}).animate();
						}
					}
				} else if(!!this.tabsPanelFixed && (scrollTop + topPanelHeight < tabsPanelContainerTop)) {
					this.tabsPanelFixed = false;
					this.tabsPanelScrolled = false;
					this.obTabs.removeAttribute('style');
					tabsPanel.removeAttribute('style');
					BX.removeClass(tabsPanel, 'fixed');
				}
			} else {
				if(!!topPanel && !!BX.hasClass(topPanel, 'fixed'))
					topPanelHeight = topPanel.offsetHeight;
				
				if(!this.tabsPanelFixed && (scrollTop + topPanelHeight >= tabsPanelContainerTop)) {
					this.tabsPanelFixed = true;
					BX.style(this.obTabs, 'height', tabsPanelHeight + 'px');
					BX.style(tabsPanel, 'top', topPanelHeight + 'px');
					BX.addClass(tabsPanel, 'fixed');
				} else if(!!this.tabsPanelFixed && (scrollTop + topPanelHeight < tabsPanelContainerTop)) {
					this.tabsPanelFixed = false;
					this.obTabs.removeAttribute('style');
					tabsPanel.removeAttribute('style');
					BX.removeClass(tabsPanel, 'fixed');
				}
			}
			this.lastScrollTop = scrollTop;
		},

		checkTopTabsBlockResize: function() {
			if(!!BX.hasClass(this.obTabsBlock, 'fixed')) {
				var topPanel = document.querySelector('.top-panel'),
					topPanelHeight = 0,
					topPanelThead = !!topPanel && topPanel.querySelector('.top-panel__thead'),
					topPanelTfoot = !!topPanel && topPanel.querySelector('.top-panel__tfoot');					
				
				if(window.innerWidth < 992) {
					if(!!topPanelThead && !!BX.hasClass(topPanelThead, 'fixed')) {
						topPanelHeight = topPanelThead.offsetHeight;
						if(!!topPanelTfoot && !!BX.hasClass(topPanelTfoot, 'visible'))
							topPanelHeight += topPanelTfoot.offsetHeight;
					}
				} else {
					if(!!topPanel && !!BX.hasClass(topPanel, 'fixed'))
						topPanelHeight = topPanel.offsetHeight;
					this.tabsPanelScrolled = false;
				}
				
				BX.style(this.obTabsBlock, 'top', topPanelHeight + 'px');
			}
		},

		checkActiveTabsBlock: function() {
			var topPanel = document.querySelector('.top-panel'),
				topPanelHeight = 0,
				topPanelThead = !!topPanel && topPanel.querySelector('.top-panel__thead'),
				topPanelTfoot = !!topPanel && topPanel.querySelector('.top-panel__tfoot'),
				tabsPanel = this.obTabsBlock,
				tabsPanelHeight = 0,				
				containers = this.obTabContainers.querySelectorAll('[data-entity="tab-container"]'),
				tabs = this.obTabs.querySelectorAll('[data-entity="tab"]'),
				scrollTop = BX.GetWindowScrollPos().scrollTop;

			if(!!containers && !!tabs) {
				if(window.innerWidth < 992) {
					if(!!topPanelThead && !!BX.hasClass(topPanelThead, 'fixed')) {
						topPanelHeight = topPanelThead.offsetHeight;
						if(!!topPanelTfoot && !!BX.hasClass(topPanelTfoot, 'visible'))
							topPanelHeight += topPanelTfoot.offsetHeight;
					}
				} else {
					if(!!topPanel && !!BX.hasClass(topPanel, 'fixed'))
						topPanelHeight = topPanel.offsetHeight;
				}

				if(!!tabsPanel && !!BX.hasClass(tabsPanel, 'fixed'))
					tabsPanelHeight = tabsPanel.offsetHeight;

				var fullScrollTop = scrollTop + topPanelHeight + tabsPanelHeight;
				
				var containersLength = Object.keys(containers).length;
				for(var i in containers) {
					if(containers.hasOwnProperty(i) && BX.type.isDomNode(containers[i])) {
						var containerValue = containers[i].getAttribute('data-value');
						if(containerValue) {
							if(fullScrollTop >= BX.pos(containers[i]).top && fullScrollTop <= BX.pos(containers[containersLength - 1]).bottom) {
								for(var j in tabs) {
									if(tabs.hasOwnProperty(j) && BX.type.isDomNode(tabs[j])) {
										var tabValue = tabs[j].getAttribute('data-value');
										if(tabValue) {
											if(tabValue === containerValue)
												BX.addClass(tabs[j], 'active');
											else
												BX.removeClass(tabs[j], 'active');
										}
									}
								}
							} else if(fullScrollTop > BX.pos(containers[containersLength - 1]).bottom) {
								for(var j in tabs) {
									if(tabs.hasOwnProperty(j) && BX.type.isDomNode(tabs[j]))
										BX.removeClass(tabs[j], 'active');
								}
							}
						}
					}
				}
			}
		},
		
		changeTab: function(event) {			
			BX.PreventDefault(event);

			BX.unbind(window, 'scroll', BX.proxy(this.checkActiveTabsBlock, this));
			
			var targetTabValue = BX.proxy_context && BX.proxy_context.getAttribute('data-value'),
				containers, tabs;

			if(!!targetTabValue) {				
				containers = this.obTabContainers.querySelectorAll('[data-entity="tab-container"]');
				if(!!containers) {
					for(var i in containers) {
						if(containers.hasOwnProperty(i) && BX.type.isDomNode(containers[i])) {
							if(containers[i].getAttribute('data-value') === targetTabValue) {
								var topPanel = document.querySelector('.top-panel'),
									topPanelHeight = 0,
									topPanelThead = !!topPanel && topPanel.querySelector('.top-panel__thead'),
									topPanelTfoot = !!topPanel && topPanel.querySelector('.top-panel__tfoot'),
									tabContainerTop = BX.pos(containers[i]).top,
									scrollTop = BX.GetWindowScrollPos().scrollTop;

								if(window.innerWidth < 992) {
									if(!!topPanelThead) {
										topPanelHeight = topPanelThead.offsetHeight;
										if(scrollTop + this.obTabsBlock.offsetHeight + topPanelHeight > tabContainerTop) {
											if(!!topPanelTfoot)
												topPanelHeight += topPanelTfoot.offsetHeight;
										}
									}
								} else {
									if(!!topPanel)
										topPanelHeight = topPanel.offsetHeight;
								}
								
								new BX.easing({
									duration: 500,
									start: {scroll: scrollTop},
									finish: {scroll: tabContainerTop - this.obTabsBlock.offsetHeight - topPanelHeight},
									transition: BX.easing.makeEaseOut(BX.easing.transitions.quint),
									step: BX.delegate(function(state) {
										window.scrollTo(0, state.scroll);
									}, this),
									complete: BX.delegate(function() {
										BX.bind(window, 'scroll', BX.proxy(this.checkActiveTabsBlock, this));
									}, this)
								}).animate();
							}
						}
					}
				}
				
				tabs = this.obTabs.querySelectorAll('[data-entity="tab"]');
				if(!!tabs) {
					for(var i in tabs) {
						if(tabs.hasOwnProperty(i) && BX.type.isDomNode(tabs[i])) {
							if(tabs[i].getAttribute('data-value') === targetTabValue)
								BX.addClass(tabs[i], 'active');
							else
								BX.removeClass(tabs[i], 'active');
						}
					}
				}
			}
		},
			
		initSectionsLinks: function() {
			var sectionLinks = this.obSectionsLinks.querySelectorAll('.collections-detail-section-link'),
				haveActive = false;

			if(!!sectionLinks) {
				for(var i in sectionLinks) {
					if(sectionLinks.hasOwnProperty(i) && BX.type.isDomNode(sectionLinks[i])) {
						BX.bind(sectionLinks[i], 'click', BX.proxy(this.changeSectionLink, this));

						if(!haveActive) {
							BX.addClass(sectionLinks[i], 'active');
							haveActive = true;
						} else {
							BX.removeClass(sectionLinks[i], 'active');
						}
					}
				}
			}
		},

		changeSectionLink: function(event) {
			BX.PreventDefault(event);

			var sectionId = BX.proxy_context && BX.proxy_context.getAttribute('data-section-id');			
			if(!BX.hasClass(BX.proxy_context, 'active') && sectionId) {
				var itemProductsContainer = this.obItem.querySelector('.collections-detail-products');
				if(!!itemProductsContainer) {					
					itemProductsContainer.style.opacity = 0.2;
					BX.ajax.post(
						BX.message('COLLECTION_TEMPLATE_PATH') + '/ajax.php',
						{							
							action: 'changeSectionLink',
							REQUEST_URI: window.location.href,
							siteId: BX.message('SITE_ID'),
							parameters: this.config.PARAMS,
							productsIds: this.item.productsIds,
							sectionId: sectionId
						},
						BX.delegate(function(result) {
							itemProductsContainer.innerHTML = result;

							if(this.config.IMG_LAZYLOAD)
								imgLazyLoad();

							new BX.easing({
								duration: 2000,
								start: {opacity: 20},
								finish: {opacity: 100},
								transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
								step: function(state) {
									itemProductsContainer.style.opacity = state.opacity / 100;
								},
								complete: function() {
									itemProductsContainer.removeAttribute('style');
								}
							}).animate();
						}, this)
					);
				}
				
				var sectionLinks = this.obSectionsLinks.querySelectorAll('.collections-detail-section-link');
				if(!!sectionLinks) {
					for(var i in sectionLinks) {
						if(sectionLinks.hasOwnProperty(i) && BX.type.isDomNode(sectionLinks[i])) {
							if(sectionLinks[i].getAttribute('data-section-id') === sectionId) {
								BX.addClass(sectionLinks[i], 'active');
							} else {
								BX.removeClass(sectionLinks[i], 'active');
							}
						}
					}
				}
			}
		}
	}
})();