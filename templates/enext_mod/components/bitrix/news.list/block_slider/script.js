(function() {
	'use strict';

	if(!!window.JCNewsListBlockSlider)
		return;

	window.JCNewsListBlockSlider = function(params) {
		this.container = BX(params.container);
		
		BX.ready(BX.delegate(this.adjustBlockSlider, this));
		BX.bind(window, 'resize', BX.proxy(this.adjustBlockSlider, this));
	};

	window.JCNewsListBlockSlider.prototype =	{		
		adjustBlockSlider: function() {
			var sliderItems = this.container.querySelectorAll('.slider-item');
			if(!!sliderItems) {
				for(var i in sliderItems) {
					if(sliderItems.hasOwnProperty(i)) {
						if(window.innerWidth >= 992) {
							var sliderItemVideo = sliderItems[i].querySelector('.slider-item__video');
							if(!sliderItemVideo) {
								var sliderItemVideoSrc = sliderItems[i].getAttribute('data-video-src'),
									sliderItemVideoWidth = sliderItems[i].getAttribute('data-video-width'),
									sliderItemVideoHeight = sliderItems[i].getAttribute('data-video-height');
									
								if(!!sliderItemVideoSrc)
									$(sliderItems[i]).prepend('<video class="slider-item__video" muted loop' +
										(!!sliderItemVideoWidth && sliderItemVideoWidth > this.container.parentNode.offsetWidth ? ' style="max-height: 100%;"' : '') +
										'><source src="' + sliderItemVideoSrc + '" type="video/mp4"></video>'
									);
							}
						} else {
							var sliderItemVideo = sliderItems[i].querySelector('.slider-item__video');
							if(!!sliderItemVideo)
								BX.remove(sliderItemVideo);
						}
					}
				}
			}
		}
	}
})();