/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Selwyn Orren
 *  @copyright 2017 Linuxweb
 *  @license   LICENSE.md
 */
 $(document).ready(function(){
	$('.owl-carousel').owlCarousel({
		loop:true,
		margin:20,
		nav:false,
		autoHeight:true,
		responsive:{
			0:{
				items:MOBILE
			},
			600:{
				items:TABLET
			},
			1000:{
				items:DESKTOP
			}
		}
	});
});
