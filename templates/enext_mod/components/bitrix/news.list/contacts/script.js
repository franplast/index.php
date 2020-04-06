(function(window) {
	'use strict';

	if(!!window.JCNewsListContacts)
		return;

	window.JCNewsListContacts = function(arParams) {		
		this.visual = {
			ID: ''
		};
		
		this.item = {			
			timezone: '',
			workingHours: {}
		};
		
		this.sPanel = null;
		this.obItem = null;
		
		this.errorCode = 0;
		
		if(typeof arParams === 'object') {
			this.visual = arParams.VISUAL;			
			this.item.timezone = arParams.ITEM.TIMEZONE;
			this.item.workingHours = arParams.ITEM.WORKING_HOURS;
			
			BX.ready(BX.delegate(this.init, this));
		}
	};

	window.JCNewsListContacts.prototype = {
		init: function() {
			this.obItem = BX(this.visual.ID);
			if(!this.obItem) {
				this.errorCode = -1;
			}

			if(this.errorCode === 0) {
				this.sPanel = document.body.querySelector('.slide-panel');
				
				this.showWorkingHoursToday();
				
				var itemBtn = this.obItem.querySelector('[data-entity="callback"]');
				if(!!itemBtn)
					BX.bind(itemBtn, 'click', BX.proxy(this.showCallbackForm, this));
				
				BX.bind(document, 'click', BX.delegate(function(e) {
					if(BX.findParent(e.target, {attrs: {id: this.visual.ID}}) && BX.hasClass(e.target, 'icon-arrow-down')) {
						var workingHoursToday = BX.findParent(e.target, {attrs: {'data-entity': 'working-hours-today'}});
						if(!!workingHoursToday)
							BX.style(workingHoursToday, 'display', 'none');
						
						var workingHoursAll = this.obItem.querySelector('[data-entity="working-hours-all"]');
						if(!!workingHoursAll)
							BX.style(workingHoursAll, 'display', '');
						
						e.stopPropagation();
					}
				}, this));
				BX.bind(document, 'click', BX.delegate(function(e) {
					if(BX.findParent(e.target, {attrs: {id: this.visual.ID}}) && BX.hasClass(e.target, 'icon-arrow-up')) {
						var workingHoursAll = BX.findParent(e.target, {attrs: {'data-entity': 'working-hours-all'}});
						if(!!workingHoursAll)
							BX.style(workingHoursAll, 'display', 'none');
						
						var workingHoursToday = this.obItem.querySelector('[data-entity="working-hours-today"]');
						if(!!workingHoursToday)
							BX.style(workingHoursToday, 'display', '');
						
						e.stopPropagation();
					}
				}, this));
			}
		},

		showWorkingHoursToday: function() {
			var itemHours = this.obItem.querySelector('.contacts-item-working-hours');			
			if(!!itemHours) {
				itemHours.innerHTML = '<div class="contacts-item-icon"><i class="fa fa-spinner fa-pulse"></i></div><div class="contacts-item-text">' + BX.message('CONTACTS_LOADING') + '</div>';
				BX.removeClass(itemHours, 'contacts-item-working-hours-hidden');
				BX.ajax({
					url: BX.message('CONTACTS_TEMPLATE_PATH') + '/ajax.php',
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
									content += '<div class="contacts-item-working-hours-today" data-entity="working-hours-today">';
										content += '<div class="contacts-item-today-container">';
											content += '<div class="contacts-item-today">';
												content += '<span class="contacts-item-icon contacts-item-today-icon"><i class="icon-clock"></i></span>';
												content += '<span class="contacts-item-today-title">' + BX.message('CONTACTS_ITEM_TODAY') + '</span>';
												if(this.item.workingHoursToday[i].STATUS) {
													content += '<span class="contacts-item-today-status contacts-item-today-status-' + (this.item.workingHoursToday[i].STATUS == 'OPEN' ? 'open' : 'closed') + '"></span>';
												}
											content += '</div>';
										content += '</div>';
										content += '<div class="contacts-item-hours-break">';
											content += '<div class="contacts-item-hours contacts-item-hours-first">';
												content += '<span class="contacts-item-hours-title">';
													if(this.item.workingHoursToday[i].WORK_START && this.item.workingHoursToday[i].WORK_END) {
														if(this.item.workingHoursToday[i].WORK_START != this.item.workingHoursToday[i].WORK_END) {
															content += this.item.workingHoursToday[i].WORK_START + ' - ' + this.item.workingHoursToday[i].WORK_END;
														} else {
															content += BX.message('CONTACTS_ITEM_24_HOURS');
														}
													} else {
														content += BX.message('CONTACTS_ITEM_OFF');
													}
												content += '</span>';
												content += '<span class="contacts-item-hours-icon"><i class="icon-arrow-down"></i></span>';
											content += '</div>';
											if(this.item.workingHoursToday[i].WORK_START && this.item.workingHoursToday[i].WORK_END) {
												if(this.item.workingHoursToday[i].WORK_START != this.item.workingHoursToday[i].WORK_END) {
													if(this.item.workingHoursToday[i].BREAK_START && this.item.workingHoursToday[i].BREAK_END) {
														if(this.item.workingHoursToday[i].BREAK_START != this.item.workingHoursToday[i].BREAK_END) {
															content += '<div class="contacts-item-break">';
																content += BX.message('CONTACTS_ITEM_BREAK') + ' ' + this.item.workingHoursToday[i].BREAK_START + ' - ' + this.item.workingHoursToday[i].BREAK_END;
															content += '</div>';
														}
													}
												}
											}
										content += '</div>';
									content += '</div>';
								}
							}
						}
						
						if(this.item.workingHours) {
							content += '<div class="contacts-item-working-hours-all" data-entity="working-hours-all"' + (this.item.workingHoursToday ? 'style="display: none;"' : '') + '>';
								var key = 0;
								for(var i in this.item.workingHours) {
									if(this.item.workingHours.hasOwnProperty(i)) {										
										content += '<div class="contacts-item-working-hours-today">';
											content += '<div class="contacts-item-today-container">';
												content += '<div class="contacts-item-today">';
													if(key == 0) {
														content += '<span class="contacts-item-icon contacts-item-today-icon"><i class="icon-clock"></i></span>';
													}
													content += '<span class="contacts-item-today-title">' + (this.item.workingHoursToday && this.item.workingHoursToday.hasOwnProperty(i) ? BX.message('CONTACTS_ITEM_TODAY') : this.item.workingHours[i].NAME) + '</span>';
													if(this.item.workingHoursToday && this.item.workingHoursToday.hasOwnProperty(i) && this.item.workingHoursToday[i].STATUS) {
														content += '<span class="contacts-item-today-status contacts-item-today-status-' + (this.item.workingHoursToday[i].STATUS == 'OPEN' ? 'open' : 'closed') + '"></span>';
													}
												content += '</div>';
											content += '</div>';
											content += '<div class="contacts-item-hours-break">';
												content += '<div class="contacts-item-hours' + (key == 0 ? ' contacts-item-hours-first' : '') + '">';
													content += '<span class="contacts-item-hours-title">';
														if(this.item.workingHours[i].WORK_START && this.item.workingHours[i].WORK_END) {
															if(this.item.workingHours[i].WORK_START != this.item.workingHours[i].WORK_END) {
																content += this.item.workingHours[i].WORK_START + ' - ' + this.item.workingHours[i].WORK_END;
															} else {
																content += BX.message('CONTACTS_ITEM_24_HOURS');
															}
														} else {
															content += BX.message('CONTACTS_ITEM_OFF');
														}
													content += '</span>';
													if(this.item.workingHoursToday && key == 0) {
														content += '<span class="contacts-item-hours-icon"><i class="icon-arrow-up"></i></span>';
													}
												content += '</div>';
												if(this.item.workingHours[i].WORK_START && this.item.workingHours[i].WORK_END) {
													if(this.item.workingHours[i].WORK_START != this.item.workingHours[i].WORK_END) {
														if(this.item.workingHours[i].BREAK_START && this.item.workingHours[i].BREAK_END) {
															if(this.item.workingHours[i].BREAK_START != this.item.workingHours[i].BREAK_END) {
																content += '<div class="contacts-item-break">';
																	content += BX.message('CONTACTS_ITEM_BREAK') + ' ' + this.item.workingHours[i].BREAK_START + ' - ' + this.item.workingHours[i].BREAK_END;
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
						
						itemHours.innerHTML = content;
						if(content.length == 0)
							BX.addClass(itemHours, 'contacts-item-working-hours-hidden');
					}, this)
				});
			}
		},
		
		showCallbackForm: function() {
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
										}
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
								html: result
							})
						);

						var sPanelContent = this.sPanel.querySelector('.slide-panel__content');
						if(!!sPanelContent)
							$(sPanelContent).scrollbar();
						
						var sPanelTitle = this.sPanel.querySelector('.slide-panel__title'),
							sPanelFormTitle = this.sPanel.querySelector('.slide-panel__form-title'),
							sPanelFormBtn = this.sPanel.querySelector('[type="submit"]');
						if(!!sPanelFormTitle) {
							sPanelTitle.innerHTML = sPanelFormTitle.innerHTML;
							if(!!sPanelFormBtn)
								sPanelFormBtn.innerHTML = sPanelFormTitle.innerHTML;
							BX.remove(sPanelFormTitle);
						}
						
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