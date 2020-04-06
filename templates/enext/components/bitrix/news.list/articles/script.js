(function(window) {
	'use strict';

	if(!window.JCNewsListArticlesComponent) {
		window.JCNewsListArticlesComponent = function(params) {
			this.formPosting = false;
			this.imgLazyLoad = params.imgLazyLoad || '';
			this.siteId = params.siteId || '';
			this.templatePath = params.templatePath || '';
			this.template = params.template || '';			
			this.parameters = params.parameters || '';
			
			if(params.navParams) {
				this.navParams = {
					NavNum: params.navParams.NavNum || 1,
					NavPageNomer: parseInt(params.navParams.NavPageNomer) || 1,
					NavPageCount: parseInt(params.navParams.NavPageCount) || 1
				};
			}
			
			this.container = document.querySelector("[data-entity='container-" + this.navParams.NavNum + "']");
			this.showMoreButton = null;
			this.showMoreButtonMessage = null;
			this.showMoreButtonContainer = null;		

			if(params.lazyLoad) {
				this.showMoreButton = document.querySelector('[data-use="show-more-' + this.navParams.NavNum + '"]');
				this.showMoreButtonMessage = this.showMoreButton.innerHTML;
				this.showMoreButtonContainer = document.querySelector('[data-entity="articles-show-more-container"]');
				BX.bind(this.showMoreButton, 'click', BX.proxy(this.showMore, this));
			}
		};

		window.JCNewsListArticlesComponent.prototype = {
			checkButton: function() {
				if(this.showMoreButton) {
					if(this.navParams.NavPageNomer == this.navParams.NavPageCount) {
						BX.remove(this.showMoreButtonContainer);
					} else {
						this.container.appendChild(this.showMoreButtonContainer);
					}
				}
			},

			enableButton: function() {
				if(this.showMoreButton) {
					BX.removeClass(this.showMoreButton, 'disabled');
					this.showMoreButton.innerHTML = this.showMoreButtonMessage;
				}
			},

			disableButton: function() {
				if(this.showMoreButton) {
					BX.addClass(this.showMoreButton, 'disabled');
					this.showMoreButton.innerHTML = BX.message('ARTICLES_LOADING');
				}
			},
			
			showMore: function() {
				if(this.navParams.NavPageNomer < this.navParams.NavPageCount) {
					var data = {};
					data['action'] = 'showMoreArticles';
					data['REQUEST_URI'] = window.location.pathname;
					data['PAGEN_' + this.navParams.NavNum] = this.navParams.NavPageNomer + 1;

					if(!this.formPosting) {
						this.formPosting = true;
						this.disableButton();
						this.sendRequest(data);
					}
				}
			},

			sendRequest: function(data) {
				var defaultData = {
					siteId: this.siteId,
					template: this.template,
					parameters: this.parameters
				};
				
				BX.ajax({
					url: this.templatePath + '/ajax.php' + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : ''),
					method: 'POST',
					dataType: 'json',
					timeout: 60,
					data: BX.merge(defaultData, data),
					onsuccess: BX.delegate(function(result) {
						if(!result || !result.JS)
							return;

						BX.ajax.processScripts(
							BX.processHTML(result.JS).SCRIPT,
							false,
							BX.delegate(function() {
								this.processShowMoreAction(result);
							}, this)
						);
					}, this)
				});
			},

			processShowMoreAction: function(result) {
				this.formPosting = false;
				this.enableButton();

				if(result) {
					this.navParams.NavPageNomer++;
					this.processItems(result.items);
					this.processPagination(result.pagination);
					this.checkButton();
				}
			},

			processItems: function(itemsHtml, position) {
				if(!itemsHtml)
					return;

				var processed = BX.processHTML(itemsHtml, false),
					temporaryNode = BX.create('DIV'),
					items;

				temporaryNode.innerHTML = processed.HTML;
				items = temporaryNode.querySelectorAll('[data-entity="item"]');

				if(items.length) {
					for(var k in items) {
						if(items.hasOwnProperty(k)) {
							if(this.imgLazyLoad) {
								var itemPics = items[k].querySelectorAll('img');
								if(!!itemPics) {
									for(var i in itemPics) {
										if(itemPics.hasOwnProperty(i)) {
											var src = itemPics[i].getAttribute('src');
											if(!!src) {
												itemPics[i].removeAttribute('src');
												itemPics[i].setAttribute('data-lazyload-src', src);
											}
										}
									}
								}
							}
							
							items[k].style.opacity = 0;
							this.container.appendChild(items[k]);
						}
					}

					if(this.imgLazyLoad)
						imgLazyLoad();
					
					new BX.easing({
						duration: 2000,
						start: {opacity: 0},
						finish: {opacity: 100},
						transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
						step: function(state) {
							for(var k in items) {
								if(items.hasOwnProperty(k)) {
									items[k].style.opacity = state.opacity / 100;
								}
							}
						},
						complete: function() {
							for(var k in items) {
								if(items.hasOwnProperty(k)) {
									items[k].removeAttribute('style');
								}
							}
						}
					}).animate();
				}

				BX.ajax.processScripts(processed.SCRIPT);
			},

			processPagination: function(paginationHtml) {
				if(!paginationHtml)
					return;

				var pagination = document.querySelector('[data-pagination-num="' + this.navParams.NavNum + '"]');
				if(!!pagination)
					pagination.innerHTML = paginationHtml;
			}
		}
	}
})(window);