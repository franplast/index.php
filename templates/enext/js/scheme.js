        (jQuery)(function ($) {

        	// documents - http://www.outsharked.com/imagemapster/docs.html#tooltips
        	// http://www.outsharked.com/imagemapster/
        	var imgMap = $("#imageMap, #floor1_map, #floor2_map, #floor3_map, #floor4_map"),
        		tooltipe = {
        			a: 'Ландшафтный дизайн, декор, интерьер, фонтаны, краски, лаки',
        			b: 'Декоративно-отделочные материалы, обои, краски, лаки',
        			c: 'Светильники, лампы, электрооборудование, кабель',
        			d: 'Напольные покрытия, окна, двери, фурнитура',
        			e: 'Плитка, сантехника, аксессуары для ванной',
        			f: 'Вентиляция, кондиционирование, отопление, крепеж, инструменты',
        			g: 'Лестницы, строительные материалы, печи, камины, баня, сауна',
        			h: 'Сад, дача, строительные материалы, кровля, фасад',
        			k: 'Сад, дача, тяжелая стройка, брус, металлопрокат, кирпич, песок, щебень, строительные смеси',
        			f1_1: 'Сантехника, аксессуары для ванных комнат, плитка, вентиляция, климат',
        			f1_2: 'Напольные покрытия',
        			f2_1: 'Свет, электрика',
        			f2_2: 'Обои, лепнина, краски, шторы',
        			f2_3: 'Двери, фурнитура',
        			f3_1: 'Товары для дома, сада и интерьера',
        			f4_1: 'Ресторанный дворик'
        		};

        	$("area").each(function () {
        		attribute = $(this).attr("alt");
        		//if(!$(this).attr('data-desc')) $(this).attr('data-desc',$(this).parent().siblings('.imageMapClass').attr('alt'));
        		//if(!$(this).attr('data-renter')) $(this).attr('data-renter',$(this).parent().siblings('.imageMapClass').attr('alt'));

        		$(this).attr('data-key', attribute);
        	});

        	function recall(e, elem) {
				console.log(1)
        		var currentToolTip = '';
        		if (!elem.data('desc')) {
        			if (elem.parent().siblings('.pic_title').html()) {
        				currentToolTip = elem.parent().siblings('.pic_title').html();
        			} else {
        				currentToolTip = 'Схема торгового комплекса';
        			}
        		} else {
        			currentToolTip = e.desc;
        		}
        		if (elem.data('renter')) {
        			currentToolTip += '<br>' + elem.data('renter');
        		}
        		imgMap.mapster('set_options', {
        			'areas': [{
        				'key': e.key,
        				'toolTip': currentToolTip,
        				'strokeColor': "332000",
        				'selected': true
        			}]
        		});

        	}

        	imgMap.mapster({
        		'fillOpacity': 0.4,
        		'stroke': true,
        		'strokeColor': "FFFFFF",
        		'strokeOpacity': 0.8,
        		'strokeWidth': 1,
        		'singleSelect': true,
        		'mapKey': 'data-key',
        		'listKey': 'data-key',
        		'showToolTip': true,
        		'toolTipContainer': '<div class="tooltype"><div class="end"></div></div>',
        		'toolTipClose': ['area-mouseout'],
        		'onConfigured': function () {
        			$("area").each(function (i) {
        				var currItem = $(this).data();
        				recall(currItem, $(this));
        			});
        		}
        	});
            $(".fancybox").fancybox({
                autoSize: true,
                scrolling: 'no'
            });

        });
