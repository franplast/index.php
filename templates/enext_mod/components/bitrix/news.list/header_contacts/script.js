(function(window) {
	'use strict';

	if(!!window.JCNewsListHeaderContacts)
		return;

	window.JCNewsListHeaderContacts = function(arParams) {		
		this.visual = {
			ID: ''
		};
		
		this.item = {
			address: '',
			timezone: '',
			workingHours: {},			
			phone: {},			
			email: {},
			skype: {}
		};
		
		this.sPanel = null;
		this.sPanelContent = null;
		this.obItem = null;
		
		this.errorCode = 0;
		
		if(typeof arParams === 'object') {
			this.visual = arParams.VISUAL;
			this.item.address = arParams.ITEM.ADDRESS;
			this.item.timezone = arParams.ITEM.TIMEZONE;
			this.item.workingHours = arParams.ITEM.WORKING_HOURS;			
			this.item.phone = arParams.ITEM.PHONE.VALUE;
			this.item.phoneDescription = arParams.ITEM.PHONE.DESCRIPTION;
			this.item.email = arParams.ITEM.EMAIL.VALUE;
			this.item.emailDescription = arParams.ITEM.EMAIL.DESCRIPTION;
			this.item.skype = arParams.ITEM.SKYPE.VALUE;
			this.item.skypeDescription = arParams.ITEM.SKYPE.DESCRIPTION;

			BX.ready(BX.delegate(this.init, this));
		}
	};

	window.JCNewsListHeaderContacts.prototype = {
		init: function() {
			this.sPanel = document.body.querySelector('.slide-panel');
			
			this.obItem = BX(this.visual.ID);
			if(!this.obItem) {
				this.errorCode = -1;
			}

			if(this.errorCode === 0) {
				BX.bind(this.obItem, 'click', BX.proxy(this.getWorkingHoursToday, this));

				BX.addCustomEvent(this, 'workingHoursTodayReceived', BX.proxy(this.adjustContacts, this));
				BX.addCustomEvent(this, 'contactsAdjusted', BX.proxy(this.showContacts, this));
				
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
			}
		},

		getWorkingHoursToday: function() {			
			BX.ajax({
				url: BX.message('HEADER_CONTACTS_TEMPLATE_PATH') + '/ajax.php',
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: {							
					action: 'workingHoursToday',						
					timezone: this.item.timezone,
					workingHours: this.item.workingHours
				},
				onsuccess: BX.delegate(function(result) {
					if(!!result.today)
						this.item.workingHoursToday = result.today;
					BX.onCustomEvent(this, 'workingHoursTodayReceived');
				}, this)
			});
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
												content += '<span class="slide-panel__contacts-item__today-title">' + BX.message('HEADER_CONTACTS_ITEM_TODAY') + '</span>';
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
															content += BX.message('HEADER_CONTACTS_ITEM_24_HOURS');
														}
													} else {
														content += BX.message('HEADER_CONTACTS_ITEM_OFF');
													}
												content += '</span>';
												content += '<span class="slide-panel__contacts-item__hours-icon"><i class="icon-arrow-down"></i></span>';
											content += '</div>';
											if(this.item.workingHoursToday[i].WORK_START && this.item.workingHoursToday[i].WORK_END) {
												if(this.item.workingHoursToday[i].WORK_START != this.item.workingHoursToday[i].WORK_END) {
													if(this.item.workingHoursToday[i].BREAK_START && this.item.workingHoursToday[i].BREAK_END) {
														if(this.item.workingHoursToday[i].BREAK_START != this.item.workingHoursToday[i].BREAK_END) {
															content += '<div class="slide-panel__contacts-item__break">';
																content += BX.message('HEADER_CONTACTS_ITEM_BREAK') + ' ' + this.item.workingHoursToday[i].BREAK_START + ' - ' + this.item.workingHoursToday[i].BREAK_END;
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
												content += '<span class="slide-panel__contacts-item__today-title">' + (this.item.workingHoursToday && this.item.workingHoursToday.hasOwnProperty(i) ? BX.message('HEADER_CONTACTS_ITEM_TODAY') : this.item.workingHours[i].NAME) + '</span>';
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
															content += BX.message('HEADER_CONTACTS_ITEM_24_HOURS');
														}
													} else {
														content += BX.message('HEADER_CONTACTS_ITEM_OFF');
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
																content += BX.message('HEADER_CONTACTS_ITEM_BREAK') + ' ' + this.item.workingHours[i].BREAK_START + ' - ' + this.item.workingHours[i].BREAK_END;
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

				this.sPanelContent = content;
				
				BX.onCustomEvent(this, 'contactsAdjusted');
			}
		},
		
		showContacts: function() {
			if(!!this.sPanel) {
				BX.ajax.post(
					BX.message('SITE_DIR') + 'ajax/slide_panel.php',
					{
						action: 'callback'
					},
					BX.delegate(function(result) {
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
										html: BX.message('HEADER_CONTACTS_TITLE')
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
								html: this.sPanelContent + result
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

							var sectionPanel = document.querySelector('.catalog-section-panel');
							if(!!sectionPanel && BX.hasClass(sectionPanel, 'fixed'))
								BX.style(sectionPanel, 'padding-right', scrollWidth + 'px');

							var tabsPanel = document.querySelector('[data-entity="tabs"]');							
							if(!!tabsPanel && BX.hasClass(tabsPanel, 'fixed'))
								BX.style(tabsPanel, 'padding-right', scrollWidth + 'px');

							var objectsMap = document.querySelector('.objects-map');
							if(!!objectsMap)
								BX.style(objectsMap, 'padding-right', scrollWidth + 'px');
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
					}, this)
				);
			}
		}
	};
})(window);