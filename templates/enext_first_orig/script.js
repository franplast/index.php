BX.showSlidePanel = function() {
	var slidePanel = document.body.querySelector('.slide-panel');
	if(!!slidePanel) {			
		BX.ajax.post(
			BX.message('SITE_DIR') + 'ajax/slide_panel.php',
			{
				action: 'login',
				REQUEST_URI: window.location.pathname
			},
			BX.delegate(function(result) {
				slidePanel.appendChild(
					BX.create('DIV', {
						props: {
							className: 'slide-panel__title-wrap'
						},
						children: [
							BX.create('I', {
								props: {
									className: 'icon-user'
								}
							}),						
							BX.create('SPAN', {
								props: {
									className: 'slide-panel__title'
								},
								html: BX.message('SLIDE_PANEL_LOGIN_TITLE')
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
					
				slidePanel.appendChild(
					BX.create('DIV', {
						props: {
							className: 'slide-panel__content scrollbar-inner'
						},
						html: result
					})
				);
					
				var slidePanelContent = slidePanel.querySelector('.slide-panel__content');
				if(!!slidePanelContent)
					$(slidePanelContent).scrollbar();
					
				var scrollWidth = window.innerWidth - document.body.clientWidth;
				if(scrollWidth > 0) {
					BX.style(document.body, 'padding-right', scrollWidth + 'px');
					
					var pageBg = document.body.querySelector('.page-bg');
					if(!!pageBg)
						BX.style(pageBg, 'margin-right', scrollWidth + 'px');

					var topPanel = document.body.querySelector('.top-panel');
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

					var sectionPanel = document.body.querySelector('.catalog-section-panel');
					if(!!sectionPanel && BX.hasClass(sectionPanel, 'fixed'))
						BX.style(sectionPanel, 'padding-right', scrollWidth + 'px');

					var tabsPanel = document.body.querySelector('[data-entity="tabs"]');
					if(!!tabsPanel && BX.hasClass(tabsPanel, 'fixed'))
						BX.style(tabsPanel, 'padding-right', scrollWidth + 'px');

					var objectsMap = document.body.querySelector('.objects-map');
					if(!!objectsMap)
						BX.style(objectsMap, 'padding-right', scrollWidth + 'px');
				}
					
				var scrollTop = BX.GetWindowScrollPos().scrollTop;
				if(!!scrollTop && scrollTop > 0)
					BX.style(document.body, 'top', '-' + scrollTop + 'px');
					
				BX.addClass(document.body, 'slide-panel-active');
				BX.addClass(slidePanel, 'active');
				
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

if(window.frameCacheVars !== undefined) {
	BX.addCustomEvent("onFrameDataReceived", function() {
		var loginLink = document.body.querySelector('[data-entity="login"]');
		if(!!loginLink)
			BX.bind(loginLink, 'click', BX.delegate(BX.showSlidePanel, BX));
	});
} else {
	BX.ready(function() {
		var loginLink = document.body.querySelector('[data-entity="login"]');
		if(!!loginLink)
			BX.bind(loginLink, 'click', BX.delegate(BX.showSlidePanel, BX));
	});
}