(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
$(document).ready(function() {
	$("#keywordstfs").css({
		'background':'rgb(160, 215, 180)',
		'color':'#000'
	});
	$("#descripttfs").css({
		'background':'rgb(160, 215, 180)',
		'color':'#000'
	});
	$("#keywordstfs").keydown(function(e) {
		var s;
		var cwords;
		var keyCode = e.which;
		s = $("#keywordstfs").val();
		console.log("PALABRAS " + cwords);
		s = s.replace(/(^\s*)|(\s*$)/gi,"");
		s = s.replace(/[ ]{2,}/gi," ");
		s = s.replace(/\n /,"\n");
		cwords=s.split(' ').length;
		console.log("PALABRAS " + cwords);
		if(keyCode==8){
			if(s==''){
				$("#keywordstfs").css({
					'background':'rgb(160, 215, 180)',
					'color':'white',
					'font-weight':'bold'
				});
				cwords=0;
			}
			if(cwords>5){
				$("#keywordstfs").css({
					'background':'rgb(160, 215, 180)',
					'color':'white',
					'font-weight':'bold'
				});
			}
			if(cwords>10){
				$("#keywordstfs").css({
					'background':'rgb(253, 177, 152)',
					'color':'white',
					'font-weight':'bold'
				});
			}
		}
		if(cwords<1){
			$("#keywordstfs").css({
				'background':'rgb(160, 215, 180)',
				'color':'white',
				'font-weight':'bold'
			});
		}
		else{
			if(cwords>5){
				$("#keywordstfs").css({
					'background':'rgb(160, 215, 180)',
					'color':'white',
					'font-weight':'bold'
				});
			}
			if(cwords>10){
				$("#keywordstfs").css({
					'background':'rgb(253, 177, 152)',
					'color':'white',
					'font-weight':'bold'
				});
			}
		}
	});
	
	$("#descripttfs").keydown(function(e) {
		var s;
		var cwords;
		var keyCode = e.which;
		s = $("#descripttfs").val();
		console.log("PALABRAS " + cwords);
		s = s.replace(/(^\s*)|(\s*$)/gi,"");
		s = s.replace(/[ ]{2,}/gi," ");
		s = s.replace(/\n /,"\n");
		cwords=s.split(' ').length;
		console.log("PALABRAS " + cwords);
		if(keyCode==8){
			if(s==''){
				$("#descripttfs").css({
					'background':'rgb(160, 215, 180)',
					'color':'white',
					'font-weight':'bold'
				});
				
				cwords=0;
			}
			if(cwords>5){
				$("#descripttfs").css({
					'background':'rgb(160, 215, 180)',
					'color':'white',
					'font-weight':'bold'
				});
			}
			if(cwords>10){
				$("#descripttfs").css({
					'background':'rgb(253, 177, 152)',
					'color':'white',
					'font-weight':'bold'
				});
			}
		}
		if(cwords<1){
			$("#descripttfs").css({
				'background':'rgb(160, 215, 180)',
				'color':'white',
				'font-weight':'bold'
			});
		}
		else{
			if(cwords>5){
				$("#descripttfs").css({
					'background':'rgb(160, 215, 180)',
					'color':'white',
					'font-weight':'bold'
				});
			}
			if(cwords>10){
				$("#descripttfs").css({
					'background':'rgb(253, 177, 152)',
					'color':'white',
					'font-weight':'bold'
				});
			}
		}
	});
});
})( jQuery );