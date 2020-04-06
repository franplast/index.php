(function (window){
	'use strict';

	if(window.JCGiftItem)
		return;	

	window.JCGiftItem = function (arParams) {
		this.productType = 0;		
		this.showAbsent = true;
		this.basketAction = 'ADD';
		this.showSubscription = false;
		this.visual = {
			ID: '',
			PICT_ID: '',									
			BUY_LINK: '',
			BASKET_ACTIONS_ID: '',
			MORE_LINK: '',
			SUBSCRIBE_LINK: '',			
			PRICE_ID: '',
			OLD_PRICE_ID: '',			
			TREE_ID: '',
			BASKET_PROP_DIV: ''
		};
		this.product = {
			canBuy: true,
			name: '',
			pict: {},
			id: 0,
			addUrl: '',
			buyUrl: ''
		};

		this.basketMode = '';
		this.basketData = {
			useProps: false,
			emptyProps: false,
			quantity: 'quantity',
			props: 'prop',
			basketUrl: '',
			sku_props: '',
			sku_props_var: 'basket_props',
			add_url: '',
			buy_url: ''
		};
		
		this.defaultPict = {
			pict: null
		};
			
		this.canBuy = true;
		this.fullDisplayMode = false;
		this.viewMode = '';
		
		this.currentPrices = [];
		this.currentPriceSelected = 0;
		
		this.offers = [];
		this.offerNum = 0;
		this.treeProps = [];
		this.selectedValues = {};

		this.obProduct = null;
		this.obPict = null;		
		this.obPrice = null;		
		this.obPriceCurrent = null;		
		this.obPriceOld = null;
		this.obTree = null;
		this.obBuyBtn = null;
		this.obBasketActions = null;
		this.obMore = null;
		this.obSubscribe = null;
		
		this.obPopupWin = null;
		this.basketUrl = '';
		this.basketParams = {};
		this.isTouchDevice = BX.hasClass(document.documentElement, 'bx-touch');
		this.hoverTimer = null;
		this.hoverStateChangeForbidden = false;
		
		this.useEnhancedEcommerce = false;
		this.dataLayerName = 'dataLayer';
		this.brandProperty = false;

		this.errorCode = 0;

		if(typeof arParams == 'object') {
			this.productType = parseInt(arParams.PRODUCT_TYPE, 10);			
			this.showAbsent = arParams.SHOW_ABSENT;
			this.showSubscription = arParams.USE_SUBSCRIBE;

			if(arParams.ADD_TO_BASKET_ACTION) {
				this.basketAction = arParams.ADD_TO_BASKET_ACTION;
			}
			
			this.fullDisplayMode = arParams.PRODUCT_DISPLAY_MODE == 'Y';
			this.viewMode = arParams.VIEW_MODE || '';			
			this.useEnhancedEcommerce = arParams.USE_ENHANCED_ECOMMERCE == 'Y';
			this.dataLayerName = arParams.DATA_LAYER_NAME;
			this.brandProperty = arParams.BRAND_PROPERTY;

			this.visual = arParams.VISUAL;

			switch(this.productType) {
				case 0: // no catalog
				case 1: // product
				case 2: // set
					if(arParams.PRODUCT && typeof arParams.PRODUCT == 'object') {
						this.currentPrices = arParams.PRODUCT.ITEM_PRICES;
						this.currentPriceSelected = arParams.PRODUCT.ITEM_PRICE_SELECTED;
						
						this.canBuy = arParams.PRODUCT.CAN_BUY;
						this.product.name = arParams.PRODUCT.NAME;
						this.product.pict = arParams.PRODUCT.PICT;
						this.product.id = arParams.PRODUCT.ID;
						
						if(arParams.PRODUCT.ADD_URL) {
							this.product.addUrl = arParams.PRODUCT.ADD_URL;
						}

						if(arParams.PRODUCT.BUY_URL) {
							this.product.buyUrl = arParams.PRODUCT.BUY_URL;
						}

						if(arParams.BASKET && typeof arParams.BASKET == 'object') {
							this.basketData.useProps = arParams.BASKET.ADD_PROPS;
							this.basketData.emptyProps = arParams.BASKET.EMPTY_PROPS;
						}
					} else {
						this.errorCode = -1;
					}
					break;
				case 3: // sku
					if(arParams.PRODUCT && typeof arParams.PRODUCT == 'object') {
						this.product.name = arParams.PRODUCT.NAME;
						this.product.id = arParams.PRODUCT.ID;
					}

					if(arParams.OFFERS && BX.type.isArray(arParams.OFFERS)) {
						this.offers = arParams.OFFERS;
						this.offerNum = 0;

						if(arParams.OFFER_SELECTED) {
							this.offerNum = parseInt(arParams.OFFER_SELECTED, 10);
						}

						if(isNaN(this.offerNum)) {
							this.offerNum = 0;
						}

						if(arParams.TREE_PROPS) {
							this.treeProps = arParams.TREE_PROPS;
						}

						if(arParams.DEFAULT_PICTURE) {
							this.defaultPict.pict = arParams.DEFAULT_PICTURE.PICTURE;							
						}
					}
					break;
				default:
					this.errorCode = -1;
			}
			
			if(arParams.BASKET && typeof arParams.BASKET == 'object') {
				if(arParams.BASKET.QUANTITY) {
					this.basketData.quantity = arParams.BASKET.QUANTITY;
				}

				if(arParams.BASKET.PROPS) {
					this.basketData.props = arParams.BASKET.PROPS;
				}

				if(arParams.BASKET.BASKET_URL) {
					this.basketData.basketUrl = arParams.BASKET.BASKET_URL;
				}

				if(3 == this.productType) {
					if(arParams.BASKET.SKU_PROPS) {
						this.basketData.sku_props = arParams.BASKET.SKU_PROPS;
					}
				}

				if(arParams.BASKET.ADD_URL_TEMPLATE) {
					this.basketData.add_url = arParams.BASKET.ADD_URL_TEMPLATE;
				}

				if(arParams.BASKET.BUY_URL_TEMPLATE) {
					this.basketData.buy_url = arParams.BASKET.BUY_URL_TEMPLATE;
				}

				if(this.basketData.add_url == '' && this.basketData.buy_url == '') {
					this.errorCode = -1024;
				}
			}
		}
		
		if(this.errorCode == 0) {
			BX.ready(BX.delegate(this.init,this));
		}
	};

	window.JCGiftItem.prototype = {
		init: function() {
			var i = 0,
				treeItems = null;

			this.obProduct = BX(this.visual.ID);
			if(!this.obProduct) {
				this.errorCode = -1;
			}

			this.obPict = BX(this.visual.PICT_ID);
			if(!this.obPict) {
				this.errorCode = -2;
			}			

			this.obPrice = BX(this.visual.PRICE_ID);			
			this.obPriceCurrent = this.obPrice.querySelector('[data-entity="price-current"]');			
			this.obPriceOld = BX(this.visual.OLD_PRICE_ID);			
			this.obPriceMeasure = this.obPrice.querySelector('[data-entity="price-measure"]');
			if(!this.obPrice) {
				this.errorCode = -16;
			}
			
			if(this.productType == 3 && this.fullDisplayMode) {
				if(this.visual.TREE_ID) {
					this.obTree = BX(this.visual.TREE_ID);
					if(!this.obTree) {
						this.errorCode = -256;
					}
				}
			}

			this.obBasketActions = BX(this.visual.BASKET_ACTIONS_ID);
			if(this.obBasketActions) {
				if(this.visual.BUY_LINK) {
					this.obBuyBtn = BX(this.visual.BUY_LINK);
				}
			}

			this.obMore = BX(this.visual.MORE_LINK);

			if(this.showSubscription) {
				this.obSubscribe = BX(this.visual.SUBSCRIBE_LINK);
			}
			
			if(this.errorCode == 0) {
				//product slider events
				if(!this.isTouchDevice) {
					if(this.viewMode == 'CARD') {
						//product hover events
						BX.bind(this.obProduct, 'mouseenter', BX.proxy(this.hoverOn, this));
						BX.bind(this.obProduct, 'mouseleave', BX.proxy(this.hoverOff, this));
					}
				}
				
				switch(this.productType) {
					case 0: // no catalog
					case 1: // product
					case 2: // set
						break;
					case 3: // sku
						if(this.offers.length > 0) {
							treeItems = BX.findChildren(this.obTree, {tagName: 'li'}, true);
							if(treeItems && treeItems.length) {
								for(i = 0; i < treeItems.length; i++) {
									BX.bind(treeItems[i], 'click', BX.delegate(this.selectOfferProp, this));
								}
							}
							this.setCurrent();
						}
						break;
				}

				if(this.obBuyBtn) {
					if(this.basketAction == 'ADD') {
						BX.bind(this.obBuyBtn, 'click', BX.proxy(this.add2Basket, this));
					} else {
						BX.bind(this.obBuyBtn, 'click', BX.proxy(this.buyBasket, this));
					}
				}
			}
		},
			
		setAnalyticsDataLayer: function(action) {
			if(!this.useEnhancedEcommerce || !this.dataLayerName)
				return;

			var item = {},
				info = {},
				variants = [],
				i, k, j, propId, skuId, propValues;

			switch(this.productType) {
				case 0: //no catalog
				case 1: //product
				case 2: //set
					item = {
						'id': this.product.id,
						'name': this.product.name,
						'price': this.currentPrices[this.currentPriceSelected] && this.currentPrices[this.currentPriceSelected].PRICE,
						'brand': BX.type.isArray(this.brandProperty) ? this.brandProperty.join('/') : this.brandProperty
					};
					break;
				case 3: //sku
					for(i in this.offers[this.offerNum].TREE) {
						if(this.offers[this.offerNum].TREE.hasOwnProperty(i)) {
							propId = i.substring(5);
							skuId = this.offers[this.offerNum].TREE[i];

							for(k in this.treeProps) {
								if(this.treeProps.hasOwnProperty(k) && this.treeProps[k].ID == propId) {
									for(j in this.treeProps[k].VALUES) {
										propValues = this.treeProps[k].VALUES[j];
										if(propValues.ID == skuId) {
											variants.push(propValues.NAME);
											break;
										}
									}
								}
							}
						}
					}

					item = {
						'id': this.offers[this.offerNum].ID,
						'name': this.offers[this.offerNum].NAME,
						'price': this.currentPrices[this.currentPriceSelected] && this.currentPrices[this.currentPriceSelected].PRICE,
						'brand': BX.type.isArray(this.brandProperty) ? this.brandProperty.join('/') : this.brandProperty,
						'variant': variants.join('/')
					};
					break;
			}

			switch(action) {
				case 'addToCart':
					info = {
						'event': 'addToCart',
						'ecommerce': {
							'currencyCode': this.currentPrices[this.currentPriceSelected] && this.currentPrices[this.currentPriceSelected].CURRENCY || '',
							'add': {
								'products': [{
									'name': item.name || '',
									'id': item.id || '',
									'price': item.price || 0,
									'brand': item.brand || '',
									'category': item.category || '',
									'variant': item.variant || ''
								}]
							}
						}
					};
					info.ecommerce.add.products[0].quantity = this.currentPrices[this.currentPriceSelected] ? this.currentPrices[this.currentPriceSelected].MIN_QUANTITY : '';
					break;
			}

			window[this.dataLayerName] = window[this.dataLayerName] || [];
			window[this.dataLayerName].push(info);
		},

		hoverOn: function(event) {
			clearTimeout(this.hoverTimer);
			this.obProduct.style.height = getComputedStyle(this.obProduct).height;
			BX.addClass(this.obProduct, 'hover');

			BX.PreventDefault(event);
		},

		hoverOff: function(event) {
			if(this.hoverStateChangeForbidden)
				return;

			BX.removeClass(this.obProduct, 'hover');
			this.hoverTimer = setTimeout(
				BX.delegate(function() {
					this.obProduct.style.height = 'auto';
				}, this),
				300
			);

			BX.PreventDefault(event);
		},
			
		quantitySet: function(index) {
			var newOffer = this.offers[index];

			if(this.errorCode == 0) {
				this.canBuy = newOffer.CAN_BUY;
				
				this.currentPrices = newOffer.ITEM_PRICES;
				this.currentPriceSelected = newOffer.ITEM_PRICE_SELECTED;
				
				var price = this.currentPrices[this.currentPriceSelected],
					partnersUrl = newOffer.PARTNERS_URL;

				if(this.canBuy) {
					if(price) {
						if(!partnersUrl) {
							this.obBuyBtn && BX.adjust(this.obBuyBtn, {props: {disabled: false}, style: {display: ''}});
							this.obMore && BX.style(this.obMore, 'display', 'none');
						} else {
							this.obBuyBtn && BX.style(this.obBuyBtn, 'display', 'none');
							this.obMore && BX.style(this.obMore, 'display', '');
						}
					} else {
						if(!partnersUrl) {
							this.obBuyBtn && BX.adjust(this.obBuyBtn, {props: {disabled: true}, style: {display: ''}});
							this.obMore && BX.style(this.obMore, 'display', 'none');
						} else {
							this.obBuyBtn && BX.style(this.obBuyBtn, 'display', 'none');
							this.obMore && BX.style(this.obMore, 'display', '');
						}
					}					
					this.obSubscribe && BX.style(this.obSubscribe, 'display', 'none');
				} else {
					if(this.obSubscribe) {
						if(newOffer.CATALOG_SUBSCRIBE == 'Y') {
							BX.style(this.obSubscribe, 'display', '');
							this.obSubscribe.setAttribute('data-item', newOffer.ID);
							BX(this.visual.SUBSCRIBE_LINK + '_hidden').click();
							this.obBuyBtn && BX.adjust(this.obBuyBtn, {props: {disabled: true}, style: {display: 'none'}});
							this.obMore && BX.style(this.obMore, 'display', 'none');
						} else {
							BX.style(this.obSubscribe, 'display', 'none');
							if(!partnersUrl) {
								this.obBuyBtn && BX.adjust(this.obBuyBtn, {props: {disabled: true}, style: {display: ''}});
								this.obMore && BX.style(this.obMore, 'display', 'none');
							} else {
								this.obBuyBtn && BX.style(this.obBuyBtn, 'display', 'none');
								this.obMore && BX.style(this.obMore, 'display', '');
							}
						}
					} else {
						if(!partnersUrl) {
							this.obBuyBtn && BX.adjust(this.obBuyBtn, {props: {disabled: true}, style: {display: ''}});
							this.obMore && BX.style(this.obMore, 'display', 'none');
						} else {
							this.obBuyBtn && BX.style(this.obBuyBtn, 'display', 'none');
							this.obMore && BX.style(this.obMore, 'display', '');
						}
					}
				}

				if(this.obPriceMeasure) {
					if(newOffer.MEASURE) {
						BX.adjust(this.obPriceMeasure, {html: '/' + newOffer.MEASURE});
					} else {
						BX.adjust(this.obPriceMeasure, {html: ''});
					}
				}
			}
		},
		
		selectOfferProp: function() {
			var i = 0,
				value = '',
				strTreeValue = '',
				arTreeItem = [],
				rowItems = null,
				target = BX.proxy_context;

			if(target && target.hasAttribute('data-treevalue')) {
				if(BX.hasClass(target, 'selected'))
					return;

				strTreeValue = target.getAttribute('data-treevalue');
				arTreeItem = strTreeValue.split('_');
				if(this.searchOfferPropIndex(arTreeItem[0], arTreeItem[1])) {
					rowItems = BX.findChildren(target.parentNode, {tagName: 'li'}, false);
					if(rowItems && 0 < rowItems.length) {
						for(i = 0; i < rowItems.length; i++) {
							value = rowItems[i].getAttribute('data-onevalue');
							if(value == arTreeItem[1]) {
								BX.addClass(rowItems[i], 'selected');
							} else {
								BX.removeClass(rowItems[i], 'selected');
							}
						}
					}
				}
			}
		},

		searchOfferPropIndex: function(strPropID, strPropValue) {
			var strName = '',
				arShowValues = false,
				i, j,
				arCanBuyValues = [],
				allValues = [],
				index = -1,
				arFilter = {},
				tmpFilter = [];

			for(i = 0; i < this.treeProps.length; i++) {
				if(this.treeProps[i].ID == strPropID) {
					index = i;
					break;
				}
			}

			if(-1 < index) {
				for(i = 0; i < index; i++) {
					strName = 'PROP_'+this.treeProps[i].ID;
					arFilter[strName] = this.selectedValues[strName];
				}
				strName = 'PROP_'+this.treeProps[index].ID;
				arShowValues = this.getRowValues(arFilter, strName);
				if(!arShowValues) {
					return false;
				}
				if(!BX.util.in_array(strPropValue, arShowValues)) {
					return false;
				}
				arFilter[strName] = strPropValue;
				for(i = index+1; i < this.treeProps.length; i++) {
					strName = 'PROP_'+this.treeProps[i].ID;
					arShowValues = this.getRowValues(arFilter, strName);
					if(!arShowValues) {
						return false;
					}
					allValues = [];
					if(this.showAbsent) {
						arCanBuyValues = [];
						tmpFilter = [];
						tmpFilter = BX.clone(arFilter, true);
						for(j = 0; j < arShowValues.length; j++) {
							tmpFilter[strName] = arShowValues[j];
							allValues[allValues.length] = arShowValues[j];
							if(this.getCanBuy(tmpFilter))
								arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
						}
					} else {
						arCanBuyValues = arShowValues;
					}
					if(this.selectedValues[strName] && BX.util.in_array(this.selectedValues[strName], arCanBuyValues)) {
						arFilter[strName] = this.selectedValues[strName];
					} else {
						if(this.showAbsent)
							arFilter[strName] = (arCanBuyValues.length > 0 ? arCanBuyValues[0] : allValues[0]);
						else
							arFilter[strName] = arCanBuyValues[0];
					}
					this.updateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
				}
				this.selectedValues = arFilter;
				this.changeInfo();
			}
			return true;
		},

		updateRow: function(intNumber, activeID, showID, canBuyID) {
			var i = 0,
				value = '',
				isCurrent = false,
				rowItems = null;

			var lineContainer = this.obTree.querySelectorAll('[data-entity="sku-line-block"]'),
				listContainer;

			if(intNumber > -1 && intNumber < lineContainer.length) {
				listContainer = lineContainer[intNumber].querySelector('ul');
				rowItems = BX.findChildren(listContainer, {tagName: 'li'}, false);
				if(rowItems && 0 < rowItems.length) {
					for(i = 0; i < rowItems.length; i++) {
						value = rowItems[i].getAttribute('data-onevalue');
						isCurrent = value == activeID;

						if(isCurrent) {
							BX.addClass(rowItems[i], 'selected');
						} else {
							BX.removeClass(rowItems[i], 'selected');
						}

						if(BX.util.in_array(value, canBuyID)) {
							BX.removeClass(rowItems[i], 'notallowed');
						} else {
							BX.addClass(rowItems[i], 'notallowed');
						}

						rowItems[i].style.display = BX.util.in_array(value, showID) ? '' : 'none';

						if(isCurrent) {
							lineContainer[intNumber].style.display = (value == 0 && canBuyID.length == 1) ? 'none' : '';
						}
					}
				}
			}
		},

		getRowValues: function(arFilter, index) {
			var i = 0,
				j,
				arValues = [],
				boolSearch = false,
				boolOneSearch = true;

			if(0 == arFilter.length) {
				for(i = 0; i < this.offers.length; i++) {
					if(!BX.util.in_array(this.offers[i].TREE[index], arValues)) {
						arValues[arValues.length] = this.offers[i].TREE[index];
					}
				}
				boolSearch = true;
			} else {
				for(i = 0; i < this.offers.length; i++) {
					boolOneSearch = true;
					for(j in arFilter) {
						if(arFilter[j] !== this.offers[i].TREE[j]) {
							boolOneSearch = false;
							break;
						}
					}
					if(boolOneSearch) {
						if(!BX.util.in_array(this.offers[i].TREE[index], arValues)) {
							arValues[arValues.length] = this.offers[i].TREE[index];
						}
						boolSearch = true;
					}
				}
			}
			return (boolSearch ? arValues : false);
		},

		getCanBuy: function(arFilter) {
			var i, j,
				boolSearch = false,
				boolOneSearch = true;

			for(i = 0; i < this.offers.length; i++) {
				boolOneSearch = true;
				for(j in arFilter) {
					if(arFilter[j] !== this.offers[i].TREE[j]) {
						boolOneSearch = false;
						break;
					}
				}
				if(boolOneSearch) {
					if(this.offers[i].CAN_BUY) {
						boolSearch = true;
						break;
					}
				}
			}

			return boolSearch;
		},

		setCurrent: function() {
			var i,
				j = 0,
				arCanBuyValues = [],
				strName = '',
				arShowValues = false,
				arFilter = {},
				tmpFilter = [],
				current = this.offers[this.offerNum].TREE;

			for(i = 0; i < this.treeProps.length; i++) {
				strName = 'PROP_'+this.treeProps[i].ID;
				arShowValues = this.getRowValues(arFilter, strName);
				if(!arShowValues) {
					break;
				}
				if(BX.util.in_array(current[strName], arShowValues)) {
					arFilter[strName] = current[strName];
				} else {
					arFilter[strName] = arShowValues[0];
					this.offerNum = 0;
				}
				if(this.showAbsent) {
					arCanBuyValues = [];
					tmpFilter = [];
					tmpFilter = BX.clone(arFilter, true);
					for(j = 0; j < arShowValues.length; j++) {
						tmpFilter[strName] = arShowValues[j];
						if(this.getCanBuy(tmpFilter)) {
							arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
						}
					}
				} else {
					arCanBuyValues = arShowValues;
				}
				this.updateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
			}
			this.selectedValues = arFilter;
			this.changeInfo();
		},

		changeInfo: function() {
			var i, j,
				index = -1,
				boolOneSearch = true;

			for(i = 0; i < this.offers.length; i++) {
				boolOneSearch = true;
				for(j in this.selectedValues) {
					if(this.selectedValues[j] !== this.offers[i].TREE[j]) {
						boolOneSearch = false;
						break;
					}
				}
				if(boolOneSearch) {
					index = i;
					break;
				}
			}
			if(index > -1) {
				//show pict containers
				if(this.obPict) {
					this.obPictImg = BX.findChild(this.obPict, {tagName: 'IMG'}, true, false);
					if(this.obPictImg) {
						if(this.offers[index].PREVIEW_PICTURE) {							
							BX.adjust(this.obPictImg, {
								props: {
									src: this.offers[index].PREVIEW_PICTURE.SRC,
									width: this.offers[index].PREVIEW_PICTURE.WIDTH,
									height: this.offers[index].PREVIEW_PICTURE.HEIGHT
								}
							});
						} else {							
							BX.adjust(this.obPictImg, {
								props: {
									src: this.defaultPict.pict.SRC,
									width: this.defaultPict.pict.WIDTH,
									height: this.defaultPict.pict.HEIGHT
								}
							});
						}
					}

					this.obPict.style.display = '';
				}
				
				this.quantitySet(index);
				this.setPrice();
				this.setBuyedAdded(this.offers[index].BUYED_ADDED);
				
				this.offerNum = index;
			}
		},
			
		setPrice: function() {
			var price = this.currentPrices[this.currentPriceSelected];
			
			if(price) {
				this.obPriceCurrent && BX.adjust(this.obPriceCurrent, {
					html: BX.Currency.currencyFormat(price.PRICE, price.CURRENCY, true),
					style: {display: ''}
				});
				
				this.obPriceOld && BX.adjust(this.obPriceOld, {
					html: price.BASE_PRICE > 0 ? BX.Currency.currencyFormat(price.BASE_PRICE, price.CURRENCY, true) : '',
					style: {display: price.BASE_PRICE > 0 ? '' : 'none'}
				});
			} else {
				this.obPriceCurrent && BX.adjust(this.obPriceCurrent, {html: '', style: {display: 'none'}});
				this.obPriceOld && BX.adjust(this.obPriceOld, {html: '', style: {display: 'none'}});
			}
		},
			
		setBuyedAdded: function(state) {
			if(!this.obBuyBtn)
				return;
			
			if(state) {
				BX.adjust(this.obBuyBtn, {
					props: {
						className: 'btn btn-buy-ok',
						title: BX.message('ADD_BASKET_OK_MESSAGE')
					},
					html: '<i class="icon-ok-b"></i>'
				});
				BX.unbindAll(this.obBuyBtn);
				BX.bind(this.obBuyBtn, "click", BX.delegate(this.basketRedirect, this));				
			} else {
				BX.adjust(this.obBuyBtn, {
					props: {
						className: 'btn btn-buy',
						title: BX.message('ADD_BASKET_MESSAGE')
					},
					html: '<i class="icon-cart"></i>'
				});
				BX.unbindAll(this.obBuyBtn);
				BX.bind(this.obBuyBtn, "click", BX.proxy(this.basketAction == 'BUY' ? this.buyBasket : this.add2Basket, this));
			}
		},

		setBuyAddInfo: function(buyedAddedIds) {
			if(!BX.type.isArray(buyedAddedIds))
				return;

			for(var i in this.offers) {
				if(this.offers.hasOwnProperty(i)) {
					this.offers[i].BUYED_ADDED = BX.util.in_array(this.offers[i].ID, buyedAddedIds);
				}
			}
		},
			
		initBasketUrl: function() {
			this.basketUrl = (this.basketMode == 'ADD' ? this.basketData.add_url : this.basketData.buy_url);
			switch(this.productType) {
				case 1: // product
				case 2: // set
					this.basketUrl = this.basketUrl.replace('#ID#', this.product.id.toString());
					break;
				case 3: // sku
					this.basketUrl = this.basketUrl.replace('#ID#', this.offers[this.offerNum].ID);
					break;
			}
			this.basketParams = {
				'ajax_basket': 'Y'
			};

			this.basketParams[this.basketData.quantity] = this.currentPrices[this.currentPriceSelected] ? this.currentPrices[this.currentPriceSelected].MIN_QUANTITY : '';
			
			if(this.basketData.sku_props) {
				this.basketParams[this.basketData.sku_props_var] = this.basketData.sku_props;
			}
		},

		fillBasketProps: function() {
			if(!this.visual.BASKET_PROP_DIV)
				return;
			
			var i = 0,
				propCollection = null,
				foundValues = false,
				obBasketProps = null;
			
			obBasketProps = BX(this.visual.BASKET_PROP_DIV);
			if(obBasketProps) {
				propCollection = obBasketProps.getElementsByTagName('input');
				if(propCollection && propCollection.length) {
					for(i = 0; i < propCollection.length; i++) {
						if(!propCollection[i].disabled) {
							switch(propCollection[i].type.toLowerCase()) {
								case 'hidden':
									this.basketParams[propCollection[i].name] = propCollection[i].value;
									foundValues = true;
									break;
								case 'radio':
									if(propCollection[i].checked) {
										this.basketParams[propCollection[i].name] = propCollection[i].value;
										foundValues = true;
									}
									break;
								default:
									break;
							}
						}
					}
				}
			}
			if(!foundValues) {
				this.basketParams[this.basketData.props] = [];
				this.basketParams[this.basketData.props][0] = 0;
			}
		},

		showBasketPropsDropDownPopup: function(element, popupId) {
			var contentNode = element.querySelector('[data-entity="dropdownContent"]');

			if(!!this.obPopupWin)
				this.obPopupWin.close();

			this.obPopupWin = BX.PopupWindowManager.create('basketPropsDropDown' + popupId + '_' + this.visual.ID, element, {
				autoHide: true,
				offsetLeft: 0,
				offsetTop: 3,
				overlay : false,
				draggable: {restrict: true},
				closeByEsc: true,
				className: 'bx-drop-down-popup-window',
				content: BX.clone(contentNode)
			});	
			contentNode.parentNode.appendChild(BX('basketPropsDropDown' + popupId + '_' + this.visual.ID));
			this.obPopupWin.show();
		},
			
		selectBasketPropsDropDownPopupItem: function(element, valueId) {
			var wrapContainer = BX.findParent(element, {className: 'product-item-basket-props-drop-down'}, false),
				currentValue = wrapContainer.querySelector('INPUT'),
				currentOption = wrapContainer.querySelector('[data-entity="current-option"]');
			currentValue.value = valueId;
			currentOption.innerHTML = element.innerHTML;
			BX.PopupWindowManager.getCurrentPopup().close();
		},

		add2Basket: function() {
			this.basketMode = 'ADD';
			this.basket();
		},

		buyBasket: function() {
			this.basketMode = 'BUY';
			this.basket();
		},

		sendToBasket: function() {
			if(!this.canBuy)
				return;
			
			this.initBasketUrl();
			this.fillBasketProps();
			BX.ajax({
				method: 'POST',
				dataType: 'json',
				url: this.basketUrl,
				data: this.basketParams,
				onsuccess: BX.proxy(this.basketResult, this)
			});
		},

		basket: function() {			
			if(!this.canBuy)
				return;
			this.sendToBasket();
		},

		basketResult: function(arResult) {
			if(arResult.STATUS == 'OK') {
				if(this.basketMode == 'BUY') {
					this.basketRedirect();
				} else {
					var strPict,
						strPictWidth,
						strPictContainer = this.obProduct.querySelector('[data-entity="image-wrapper"]');
					
					switch(this.productType) {
						case 1: // product
						case 2: // set
							strPict = this.product.pict.SRC;
							strPictWidth = this.product.pict.WIDTH;
							break;
						case 3: // sku
							strPict = this.offers[this.offerNum].PREVIEW_PICTURE
								? this.offers[this.offerNum].PREVIEW_PICTURE.SRC
								: this.defaultPict.pict.SRC;
							strPictWidth = this.offers[this.offerNum].PREVIEW_PICTURE
								? this.offers[this.offerNum].PREVIEW_PICTURE.WIDTH
								: this.defaultPict.pict.WIDTH;
							break;
					}
					
					if(!!strPict) {
						document.body.appendChild(
							BX.create('IMG', {
								props: {
									className: 'animated-image'
								},
								style: {
									width: strPictWidth + 'px',								
									position: 'absolute',
									'z-index': '1100'
								},
								attrs: {
									src: strPict
								}
							})
						);
					}

					var animatedImg = document.body.querySelector('.animated-image');
					if(!!animatedImg) {
						var topPanel = document.querySelector('.top-panel'),
							miniCart = topPanel.querySelector('.mini-cart__cart');
						
						new BX.easing({
							duration: 500,
							start: {
								width: Number(strPictWidth),
								left: BX.pos(strPictContainer).left,
								top: BX.pos(strPictContainer).top
							},
							finish: {
								width: 70,							
								left: BX.pos(miniCart).left,
								top: BX.pos(miniCart).top
							},
							transition: BX.easing.transitions.linear,
							step: BX.delegate(function(state) {
								animatedImg.style.width = state.width + 'px';							
								animatedImg.style.left = state.left + 'px';
								animatedImg.style.top = state.top + 'px';
							}, this),
							complete: BX.delegate(function() {
								BX.remove(animatedImg);
								this.setBuyedAdded(true);
								if(this.offers.length > 0) {
									this.offers[this.offerNum].BUYED_ADDED = true;
								}
								BX.onCustomEvent('OnBasketChange');
								this.setAnalyticsDataLayer('addToCart');
								if(window.location.pathname == '/personal/cart/') {
									setTimeout(function() {
										window.location.reload(true);
									}, 1000);
								}
							}, this)
						}).animate();
					}
				}
			}
		},

		basketRedirect: function() {
			window.location.href = (this.basketData.basketUrl ? this.basketData.basketUrl : BX.message('BASKET_URL'));
		}
	};
})(window);