$(function(){
	$items = $('.masonry');
	
	$items.imagesLoaded(function(){
		$items.masonry({
			itemSelector: 'li',
			columnWidth: 180
		});
	});
});