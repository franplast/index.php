(function() {
	'use strict';

	if(!!window.JCNewsDetailObjects)
		return;
	
	window.JCNewsDetailObjects = function(arParams) {
		this.config = {
			IMG_LAZYLOAD: true,
			PARAMS: ''
		};
		
		this.visual = {
			ID: ''
		};
		
		this.item = {
			id: 0,
			name: '',
			address: '',
			timezone: '',
			workingHours: {},			
			phone: {},				
			email: {},
			skype: {},
			callbackForm: false,
			productsIds: ''
		};

		this.sPanel = null;
		this.sPanelContent = null;
		this.obItem = null;
		
		this.obTabs = null;
		this.obTabsBlock = null;
		this.obTabContainers = null;

		this.obSectionsLinks = null;
		
		this.errorCode = 0;
		
		if(typeof arParams === 'object') {
			this.config = arParams.CONFIG;
			this.visual = arParams.VISUAL;
			this.item.id = arParams.ITEM.ID;
			this.item.name = arParams.ITEM.NAME;
			this.item.address = arParams.ITEM.ADDRESS;
			this.item.timezone = arParams.ITEM.TIMEZONE;
			this.item.workingHours = arParams.ITEM.WORKING_HOURS;			
			this.item.phone = arParams.ITEM.PHONE.VALUE;
			this.item.phoneDescription = arParams.ITEM.PHONE.DESCRIPTION;
			this.item.email = arParams.ITEM.EMAIL.VALUE;
			this.item.emailDescription = arParams.ITEM.EMAIL.DESCRIPTION;
			this.item.skype = arParams.ITEM.SKYPE.VALUE;
			this.item.skypeDescription = arParams.ITEM.SKYPE.DESCRIPTION;
			this.item.callbackForm = arParams.ITEM.CALLBACK_FORM;
			this.item.productsIds = arParams.ITEM.PRODUCTS_IDS;

			BX.ready(BX.delegate(this.init, this));
		}
	};

	window.JCNewsDetailObjects.prototype = {
		init: function() {
			this.obItem = BX(this.visual.ID);
			if(!this.obItem) {
				this.errorCode = -1;
			}

			if(this.errorCode === 0) {
				this.obTabs = this.obItem.querySelector('.objects-detail-tabs-container');
				this.obTabsBlock = !!this.obTabs && this.obTabs.querySelector('[data-entity="tabs"]');
				this.obTabContainers = this.obItem.querySelector('.objects-detail-tabs-content');
				
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

				this.sPanel = document.body.querySelector('.slide-panel');
				
				this.showWorkingHoursToday();

				var itemBtn = this.obItem.querySelector('.objects-item-detail-btn');
				if(!!itemBtn)
					BX.bind(itemBtn, 'click', BX.proxy(this.item.callbackForm ? this.showContactsWidthForm : this.showContacts, this));

				BX.addCustomEvent(this, 'showContactsWidthFormRequest', BX.proxy(this.showContactsWidthFormRequest, this));

				if(!!this.sPanel) {		
					BX.bind(document, 'click', BX.delegate(function(e) {
						if(BX.hasClass(this.sPanel, 'active') && BX.findParent(e.target, {attrs: {id: this.visual.ID + '_contacts'}}) && BX.hasClass(e.target, 'icon-arrow-down')) {
							var workingHoursToday = BX.findParent(e.target, {attrs: {'data-entity': 'working-hours-today'}});
							if(!!workingHoursToday)
								BX.style(workingHoursToday, 'display', 'none');
							
							var workingHours = BX(this.visual.ID + '_contacts').querySelector('[data-entity="working-hours"]');
							if(!!workingHours)
								BX.style(workingHours, 'display', '');
							
							e.stopPropagation();
						}
					}, this));
					BX.bind(document, 'click', BX.delegate(function(e) {
						if(BX.hasClass(this.sPanel, 'active') && BX.findParent(e.target, {attrs: {id: this.visual.ID + '_contacts'}}) && BX.hasClass(e.target, 'icon-arrow-up')) {
							var workingHours = BX.findParent(e.target, {attrs: {'data-entity': 'working-hours'}});
							if(!!workingHours)
								BX.style(workingHours, 'display', 'none');
							
							var workingHoursToday = BX(this.visual.ID + '_contacts').querySelector('[data-entity="working-hours-today"]');
							if(!!workingHoursToday)
								BX.style(workingHoursToday, 'display', '');
							
							e.stopPropagation();
						}
					}, this));
				}
				
				this.obSectionsLinks = this.obItem.querySelector('.objects-detail-sections-links');
				if(!!this.obSectionsLinks)
					this.initSectionsLinks();
			}
		},

		initTabs: function() {
			var tabsList = this.obTabs.querySelector('.objects-detail-tabs-list'),
				tabs = !!tabsList && tabsList.querySelectorAll('[data-entity="tab"]'),
				tabValue, targetTab, haveActive = false;			
			
			if(!!tabsList) {
				BX.addClass(tabsList, 'owl-carousel');
				$(tabsList).owlCarousel({								
					autoWidth: true,
					nav: true,
					navText: ['<i class=\"icon-arrow-left\"></i>', '<i class=\"icon-arrow-right\"></i>'],
					navContainer: '.objects-detail-tabs-scroll',
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

									if(window.location.hash.indexOf(tabValue) > -1) {
										tabs[i].click();
										window.history.pushState("", document.title, window.location.pathname + window.location.search);
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

		showWorkingHoursToday: function() {
			var itemHours = this.obItem.querySelector('.objects-item-detail-hours');
			if(!!itemHours) {
				itemHours.innerHTML = '<div class="objects-item-detail-hours-loader"><div><span></span></div></div>' + BX.message('OBJECTS_ITEM_DETAIL_LOADING');
				BX.removeClass(itemHours, 'objects-item-detail-hours-hidden');
				BX.ajax({
					url: BX.message('OBJECT_TEMPLATE_PATH') + '/ajax.php',
					method: 'POST',
					dataType: 'json',
					timeout: 60,
					data: {							
						action: 'workingHoursToday',						
						timezone: this.item.timezone,
						workingHours: this.item.workingHours
					},
					onsuccess: BX.delegate(function(result) {
						var content = '';
						
						if(!!result.today) {
							this.item.workingHoursToday = result.today;

							for(var i in this.item.workingHoursToday) {
								if(this.item.workingHoursToday.hasOwnProperty(i)) {
									if(this.item.workingHoursToday[i].STATUS) {
										content += '<span class="objects-item-detail-hours-icon objects-item-detail-hours-icon-' + (this.item.workingHoursToday[i].STATUS == 'OPEN' ? 'open' : 'closed') + '"></span>';
									}
									if(this.item.workingHoursToday[i].WORK_START && this.item.workingHoursToday[i].WORK_END) {
										if(this.item.workingHoursToday[i].WORK_START != this.item.workingHoursToday[i].WORK_END) {
											content += this.item.workingHoursToday[i].WORK_START + ' - ' + this.item.workingHoursToday[i].WORK_END;
											if(this.item.workingHoursToday[i].BREAK_START && this.item.workingHoursToday[i].BREAK_END) {
												if(this.item.workingHoursToday[i].BREAK_START != this.item.workingHoursToday[i].BREAK_END) {
													content += '<span class="objects-item-detail-hours-break">';
														content += BX.message('OBJECTS_ITEM_DETAIL_BREAK') + ' ' + this.item.workingHoursToday[i].BREAK_START + ' - ' + this.item.workingHoursToday[i].BREAK_END;
													content += '</span>';
												}
											}
										} else {
											content += BX.message('OBJECTS_ITEM_DETAIL_24_HOURS');
										}
									} else {
										content += BX.message('OBJECTS_ITEM_DETAIL_OFF');
									}
								}
							}
						}
						
						itemHours.innerHTML = content;
						if(content.length == 0)
							BX.addClass(itemHours, 'objects-item-detail-hours-hidden');
					}, this)
				});
			}
		},

		adjustContacts: function() {
			var content = '';
			
			if(this.item.address || this.item.workingHours || this.item.workingHoursToday || this.item.phone || this.item.email || this.item.skype) {
				content += '<div class="slide-panel__contacts" id="' + this.visual.ID + '_contacts">';

					if(this.item.address) {
						content += '<div class="slide-panel__contacts-item">';
							content += '<div class="slide-panel__contacts-item__block">';
								content += '<div class="slide-panel__contacts-item__icon"><i class="icon-map-marker"></i></div>';
								content += '<div class="slide-panel__contacts-item__text">' + this.item.address + '</div>';
							content += '</div>';
						content += '</div>';
					}

					if(this.item.workingHoursToday) {
						for(var i in this.item.workingHoursToday) {
							if(this.item.workingHoursToday.hasOwnProperty(i)) {
								content += '<div class="slide-panel__contacts-item" data-entity="working-hours-today">';
									content += '<div class="slide-panel__contacts-item__hours-today">';
										content += '<div class="slide-panel__contacts-item__today-container">';
											content += '<div class="slide-panel__contacts-item__today">';
												content += '<span class="slide-panel__contacts-item__today-icon"><i class="icon-clock"></i></span>';
												content += '<span class="slide-panel__contacts-item__today-title">' + BX.message('OBJECTS_ITEM_DETAIL_TODAY') + '</span>';
												if(this.item.workingHoursToday[i].STATUS) {
													content += '<span class="slide-panel__contacts-item__today-status slide-panel__contacts-item__today-status-' + (this.item.workingHoursToday[i].STATUS == 'OPEN' ? 'open' : 'closed') + '"></span>';
												}
											content += '</div>';
										content += '</div>';
										content += '<div class="slide-panel__contacts-item__hours-break">';
											content += '<div class="slide-panel__contacts-item__hours slide-panel__contacts-item__hours-first">';
												content += '<span class="slide-panel__contacts-item__hours-title">';
													if(this.item.workingHoursToday[i].WORK_START && this.item.workingHoursToday[i].WORK_END) {
														if(this.item.workingHoursToday[i].WORK_START != this.item.workingHoursToday[i].WORK_END) {
															content += this.item.workingHoursToday[i].WORK_START + ' - ' + this.item.workingHoursToday[i].WORK_END;
														} else {
															content += BX.message('OBJECTS_ITEM_DETAIL_24_HOURS');
														}
													} else {
														content += BX.message('OBJECTS_ITEM_DETAIL_OFF');
													}
												content += '</span>';
												content += '<span class="slide-panel__contacts-item__hours-icon"><i class="icon-arrow-down"></i></span>';
											content += '</div>';
											if(this.item.workingHoursToday[i].WORK_START && this.item.workingHoursToday[i].WORK_END) {
												if(this.item.workingHoursToday[i].WORK_START != this.item.workingHoursToday[i].WORK_END) {
													if(this.item.workingHoursToday[i].BREAK_START && this.item.workingHoursToday[i].BREAK_END) {
														if(this.item.workingHoursToday[i].BREAK_START != this.item.workingHoursToday[i].BREAK_END) {
															content += '<div class="slide-panel__contacts-item__break">';
																content += BX.message('OBJECTS_ITEM_DETAIL_BREAK') + ' ' + this.item.workingHoursToday[i].BREAK_START + ' - ' + this.item.workingHoursToday[i].BREAK_END;
															content += '</div>';
														}
													}
												}
											}
										content += '</div>';
									content += '</div>';
								content += '</div>';
							}
						}
					}

					if(this.item.workingHours) {
						content += '<div class="slide-panel__contacts-item" data-entity="working-hours"' + (this.item.workingHoursToday ? 'style="display: none;"' : '') + '>';
							var key = 0;
							for(var i in this.item.workingHours) {
								if(this.item.workingHours.hasOwnProperty(i)) {										
									content += '<div class="slide-panel__contacts-item__hours-today">';
										content += '<div class="slide-panel__contacts-item__today-container">';
											content += '<div class="slide-panel__contacts-item__today">';
												if(key == 0) {
													content += '<span class="slide-panel__contacts-item__today-icon"><i class="icon-clock"></i></span>';
												}
												content += '<span class="slide-panel__contacts-item__today-title">' + (this.item.workingHoursToday && this.item.workingHoursToday.hasOwnProperty(i) ? BX.message('OBJECTS_ITEM_DETAIL_TODAY') : this.item.workingHours[i].NAME) + '</span>';
												if(this.item.workingHoursToday && this.item.workingHoursToday.hasOwnProperty(i) && this.item.workingHoursToday[i].STATUS) {
													content += '<span class="slide-panel__contacts-item__today-status slide-panel__contacts-item__today-status-' + (this.item.workingHoursToday[i].STATUS == 'OPEN' ? 'open' : 'closed') + '"></span>';
												}
											content += '</div>';
										content += '</div>';
										content += '<div class="slide-panel__contacts-item__hours-break">';
											content += '<div class="slide-panel__contacts-item__hours' + (key == 0 ? ' slide-panel__contacts-item__hours-first' : '') + '">';
												content += '<span class="slide-panel__contacts-item__hours-title">';
													if(this.item.workingHours[i].WORK_START && this.item.workingHours[i].WORK_END) {
														if(this.item.workingHours[i].WORK_START != this.item.workingHours[i].WORK_END) {
															content += this.item.workingHours[i].WORK_START + ' - ' + this.item.workingHours[i].WORK_END;
														} else {
															content += BX.message('OBJECTS_ITEM_DETAIL_24_HOURS');
														}
													} else {
														content += BX.message('OBJECTS_ITEM_DETAIL_OFF');
													}
												content += '</span>';
												if(this.item.workingHoursToday && key == 0) {
													content += '<span class="slide-panel__contacts-item__hours-icon"><i class="icon-arrow-up"></i></span>';
												}
											content += '</div>';
											if(this.item.workingHours[i].WORK_START && this.item.workingHours[i].WORK_END) {
												if(this.item.workingHours[i].WORK_START != this.item.workingHours[i].WORK_END) {
													if(this.item.workingHours[i].BREAK_START && this.item.workingHours[i].BREAK_END) {
														if(this.item.workingHours[i].BREAK_START != this.item.workingHours[i].BREAK_END) {
															content += '<div class="slide-panel__contacts-item__break">';
																content += BX.message('OBJECTS_ITEM_DETAIL_BREAK') + ' ' + this.item.workingHours[i].BREAK_START + ' - ' + this.item.workingHours[i].BREAK_END;
															content += '</div>';
														}
													}
												}
											}
										content += '</div>';
									content += '</div>';
									key++;
								}
							}
						content += '</div>';
					}
					
					if(this.item.phone) {
						for(var i in this.item.phone) {
							if(this.item.phone.hasOwnProperty(i)) {
								content += '<div class="slide-panel__contacts-item">';
									content += '<div class="slide-panel__contacts-item__block">';
										content += '<div class="slide-panel__contacts-item__icon"><i class="icon-phone"></i></div>';
										content += '<div class="slide-panel__contacts-item__text">';
											content += '<a class="slide-panel__contacts-item__phone slide-panel__contacts-item__link" href="tel:' + this.item.phone[i].replace(/[^\d\+]/g,'') + '">' + this.item.phone[i] + '</a>';
											if(this.item.phoneDescription.hasOwnProperty(i) && this.item.phoneDescription[i].length > 0) {
												content += '<span class="slide-panel__contacts-item__descr">' + this.item.phoneDescription[i] + '</span>';
											}
										content += '</div>';
									content += '</div>';
								content += '</div>';
							}
						}
					}

					if(this.item.email) {
						for(var i in this.item.email) {
							if(this.item.email.hasOwnProperty(i)) {
								content += '<div class="slide-panel__contacts-item">';
									content += '<div class="slide-panel__contacts-item__block">';
										content += '<div class="slide-panel__contacts-item__icon"><i class="icon-mail"></i></div>';
										content += '<div class="slide-panel__contacts-item__text">';
											content += '<a class="slide-panel__contacts-item__link" href="mailto:' + this.item.email[i] + '">' + this.item.email[i] + '</a>';
											if(this.item.emailDescription.hasOwnProperty(i) && this.item.emailDescription[i].length > 0) {
												content += '<span class="slide-panel__contacts-item__descr">' + this.item.emailDescription[i] + '</span>';
											}
										content += '</div>';
									content += '</div>';
								content += '</div>';
							}
						}
					}

					if(this.item.skype) {
						for(var i in this.item.skype) {
							if(this.item.skype.hasOwnProperty(i)) {
								content += '<div class="slide-panel__contacts-item">';
									content += '<div class="slide-panel__contacts-item__block">';
										content += '<div class="slide-panel__contacts-item__icon"><i class="fa fa-skype"></i></div>';
										content += '<div class="slide-panel__contacts-item__text">';
											content += '<a class="slide-panel__contacts-item__link" href="skype:' + this.item.skype[i] + '?chat">' + this.item.skype[i] + '</a>';
											if(this.item.skypeDescription.hasOwnProperty(i) && this.item.skypeDescription[i].length > 0) {
												content += '<span class="slide-panel__contacts-item__descr">' + this.item.skypeDescription[i] + '</span>';
											}
										content += '</div>';
									content += '</div>';
								content += '</div>';
							}
						}
					}
				
				content += '</div>';
			}

			this.sPanelContent = content;
		},

		showContactsWidthFormRequest: function(sPanelContent) {
			this.adjustContacts();

			BX.ajax({
				url: BX.message('SITE_DIR') + 'ajax/slide_panel.php',
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: {
					action: 'callback_objects'
				},
				onsuccess: BX.delegate(function(result) {
					if(!result.content || !result.JS) {
						sPanelContent.innerHTML = this.sPanelContent;
					} else {
						BX.ajax.processScripts(
							BX.processHTML(result.JS).SCRIPT,
							false,
							BX.delegate(function() {
								var processed = BX.processHTML(result.content),
									temporaryNode = BX.create('DIV');

								temporaryNode.innerHTML = processed.HTML;

								var sPanelFormObjectIdInput = temporaryNode.querySelector('[name="OBJECT_ID"]');
								if(!!sPanelFormObjectIdInput)
									sPanelFormObjectIdInput.value = this.item.id;
								
								sPanelContent.innerHTML = this.sPanelContent + temporaryNode.innerHTML;
								
								BX.ajax.processScripts(processed.SCRIPT);
							}, this)
						);
					}
					
					$(sPanelContent).scrollbar();
				}, this)
			});
		},

		showContactsWidthForm: function(e) {
			if(!!this.sPanel) {
				this.sPanel.appendChild(
					BX.create('DIV', {
						props: {
							className: 'slide-panel__title-wrap'
						},
						children: [
							BX.create('I', {
								props: {
									className: 'icon-phone-call'
								}
							}),						
							BX.create('SPAN', {
								props: {
									className: 'slide-panel__title'
								},
								html: this.item.name
							}),
							BX.create('SPAN', {
								props: {
									className: 'slide-panel__close'
								},
								children: [
									BX.create('I', {
										props: {
											className: 'icon-close'
										}
									})
								]
							})
						]
					})
				);

				this.sPanel.appendChild(
					BX.create('DIV', {
						props: {
							className: 'slide-panel__content scrollbar-inner'
						},
						children: [
							BX.create('DIV', {
								props: {
									className: 'slide-panel__loader'
								},
								html: '<div><span></span></div>'
							})
						]
					})
				);

				var sPanelContent = this.sPanel.querySelector('.slide-panel__content');
				if(!!sPanelContent)
					BX.onCustomEvent(this, 'showContactsWidthFormRequest', [sPanelContent]);

				var scrollWidth = window.innerWidth - document.body.clientWidth;
				if(scrollWidth > 0) {
					BX.style(document.body, 'padding-right', scrollWidth + 'px');

					var pageBg = document.querySelector('.page-bg');
					if(!!pageBg)
						BX.style(pageBg, 'margin-right', scrollWidth + 'px');
					
					var topPanel = document.querySelector('.top-panel');
					if(!!topPanel) {
						if(BX.hasClass(topPanel, 'fixed'))
							BX.style(topPanel, 'padding-right', scrollWidth + 'px');
						
						var topPanelThead = topPanel.querySelector('.top-panel__thead');
						if(!!topPanelThead && BX.hasClass(topPanelThead, 'fixed'))
							BX.style(topPanelThead, 'padding-right', scrollWidth + 'px');
						
						var topPanelTfoot = topPanel.querySelector('.top-panel__tfoot');
						if(!!topPanelTfoot && BX.hasClass(topPanelTfoot, 'fixed'))
							BX.style(topPanelTfoot, 'padding-right', scrollWidth + 'px');
					}

					if(!!this.obTabsBlock && !!this.tabsPanelFixed)
						BX.style(this.obTabsBlock, 'padding-right', scrollWidth + 'px');
				}

				var scrollTop = BX.GetWindowScrollPos().scrollTop;
				if(!!scrollTop && scrollTop > 0)
					BX.style(document.body, 'top', '-' + scrollTop + 'px');

				BX.addClass(document.body, 'slide-panel-active')
				BX.addClass(this.sPanel, 'active');

				document.body.appendChild(
					BX.create('DIV', {
						props: {
							className: 'modal-backdrop slide-panel__backdrop fadeInBig'
						}
					})
				);

				e.stopPropagation();
			}
		},
			
		showContacts: function(e) {
			if(!!this.sPanel) {
				this.adjustContacts();	

				this.sPanel.appendChild(
					BX.create('DIV', {
						props: {
							className: 'slide-panel__title-wrap'
						},
						children: [
							BX.create('I', {
								props: {
									className: 'icon-phone-call'
								}
							}),						
							BX.create('SPAN', {
								props: {
									className: 'slide-panel__title'
								},
								html: this.item.name
							}),
							BX.create('SPAN', {
								props: {
									className: 'slide-panel__close'
								},
								children: [
									BX.create('I', {
										props: {
											className: 'icon-close'
										}
									})
								]
							})
						]
					})
				);

				this.sPanel.appendChild(
					BX.create('DIV', {
						props: {
							className: 'slide-panel__content scrollbar-inner'
						},
						html: this.sPanelContent
					})
				);

				var sPanelContent = this.sPanel.querySelector('.slide-panel__content');
				if(!!sPanelContent)
					$(sPanelContent).scrollbar();

				var scrollWidth = window.innerWidth - document.body.clientWidth;
				if(scrollWidth > 0) {
					BX.style(document.body, 'padding-right', scrollWidth + 'px');

					var pageBg = document.querySelector('.page-bg');
					if(!!pageBg)
						BX.style(pageBg, 'margin-right', scrollWidth + 'px');
					
					var topPanel = document.querySelector('.top-panel');
					if(!!topPanel) {
						if(BX.hasClass(topPanel, 'fixed'))
							BX.style(topPanel, 'padding-right', scrollWidth + 'px');
						
						var topPanelThead = topPanel.querySelector('.top-panel__thead');
						if(!!topPanelThead && BX.hasClass(topPanelThead, 'fixed'))
							BX.style(topPanelThead, 'padding-right', scrollWidth + 'px');
						
						var topPanelTfoot = topPanel.querySelector('.top-panel__tfoot');
						if(!!topPanelTfoot && BX.hasClass(topPanelTfoot, 'fixed'))
							BX.style(topPanelTfoot, 'padding-right', scrollWidth + 'px');
					}

					if(!!this.obTabsBlock && !!this.tabsPanelFixed)
						BX.style(this.obTabsBlock, 'padding-right', scrollWidth + 'px');
				}

				var scrollTop = BX.GetWindowScrollPos().scrollTop;
				if(!!scrollTop && scrollTop > 0)
					BX.style(document.body, 'top', '-' + scrollTop + 'px');
				
				BX.addClass(document.body, 'slide-panel-active')
				BX.addClass(this.sPanel, 'active');
			
				document.body.appendChild(
					BX.create('DIV', {
						props: {
							className: 'modal-backdrop slide-panel__backdrop fadeInBig'
						}
					})
				);

				e.stopPropagation();
			}
		},
			
		initSectionsLinks: function() {
			var sectionLinks = this.obSectionsLinks.querySelectorAll('.objects-detail-section-link'),
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
				var itemProductsContainer = this.obItem.querySelector('.objects-detail-products');
				if(!!itemProductsContainer) {					
					itemProductsContainer.style.opacity = 0.2;
					BX.ajax.post(
						BX.message('OBJECT_TEMPLATE_PATH') + '/ajax.php',
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
				
				var sectionLinks = this.obSectionsLinks.querySelectorAll('.objects-detail-section-link');
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