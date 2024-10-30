(function($){
	'use strict';
	$(document).ready(function() {

		$(".dc-mc-enable-menu-state-toggle input[type=checkbox]").each(function(i){
			const thisItem = $(this);
			if( dc_mc_data.nocache_menus.indexOf(thisItem.data("menu-slug") ) === -1 ) {
				thisItem.prop( "checked", true );
			}
		});

		$('#dc_mc_enable_save').on('click', function(){

			let nocacheMenus = [];
			let saveBtn = $(this);
			saveBtn.prop('disabled', true);

			$('.dc-mc-enable-menu-state-toggle').each( function(i){
				const thisItem = $(this);
				if( thisItem.find('input[type=checkbox]:checked').length < 1 ) {
					const menuSlug = thisItem.find('input[type=checkbox]').data("menu-slug");
					nocacheMenus.push(menuSlug);
				}
			});

			$.ajax({
				url: dc_mc_data.ajaxurl,
				type: "post",
				data:{
					action: "dc_save_nocache_menus",
					nocache_menus: nocacheMenus,
					nonce_ajax: dc_mc_data.nonce
				},
				success: function(response) {
					if ( response.success === true ) {
						saveBtn.prop('disabled', false);
					}
				}
			});
		});

		$('#dc_menu_caching_purge_all').on('click',function(){

			let btn = $(this);
			btn.prop('disabled', true);

			$.ajax({
				url: dc_mc_data.ajaxurl,
				type: "post",
				data:{
					action: "dc_menu_caching_purge_all",
					nonce_ajax: dc_mc_data.nonce
				},
				success: function(response){
					if (response.success === true) {
						alert(dc_mc_data.message);
						btn.prop('disabled', false);
					}
				}
			});
		});
	});
})(jQuery);
