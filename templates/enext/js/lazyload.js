let imgLazyLoad = function() {
	var lazyImages = [].slice.call(document.querySelectorAll('img[data-lazyload-src]'));
	
	if('IntersectionObserver' in window) {
		let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
			entries.forEach(function(entry) {
				if(entry.isIntersecting) {
					let lazyImage = entry.target;
					lazyImage.src = lazyImage.dataset.lazyloadSrc;
					lazyImage.classList.add('bx-lazyload-success');
					lazyImageObserver.unobserve(lazyImage);
				}
			});
		});

		lazyImages.forEach(function(lazyImage) {
			lazyImageObserver.observe(lazyImage);
		});
	} else {
		lazyImages.forEach(function(lazyImage) {
			let newImage = new Image();
			newImage.src = lazyImage.dataset.lazyloadSrc;
			newImage.onload = function() {
				if(lazyImage.dataset.lazyloadSrc)
					lazyImage.src = lazyImage.dataset.lazyloadSrc;
				lazyImage.classList.add('bx-lazyload-success');
			}
		});
	}
}

document.addEventListener('DOMContentLoaded', function() {
	imgLazyLoad();
});