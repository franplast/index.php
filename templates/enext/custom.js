 $(function(){

 	$(".top-panel__menu-icon-container").on('click', function(){
		var menu = $('.cont_menu');
 		menu.toggleClass('show');
 		$(this).toggleClass('open');
		/*if(menu.hasClass("show")){
			var t = $(".top-panel-wrapper"),
				t_h = t.height(),
				w_h = $(window).height(),
				h_menu = w_h-t_h;
			console.log(t_h,w_h);
			$(".decktop_menu>.slide-menu_desc").css("height",h_menu+"px");
		}*/
 	})
 })