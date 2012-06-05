(function($) {
	$.fn.bb2Spoiler = function(options){

	initPost($(this));

	function imgFit (img, spoilerMaxW)
	{
		if (typeof(img.naturalHeight) == undefined) {
			img.naturalHeight = img.height;
			img.naturalWidth  = img.width;
		}

		if (img.width > spoilerMaxW) {
			img.height = Math.round((spoilerMaxW/img.width)*img.height);
			img.width  = spoilerMaxW;
			//img.title  = 'Click image to view full size';
			img.style.cursor = 'pointer';
			return false;
		}
		else if (img.width == spoilerMaxW && img.width < img.naturalWidth) {
			img.height = img.naturalHeight;
			img.width  = img.naturalWidth;
			//img.title  = 'Click to fit in the browser window';
			return false;
		}
		else {
			return true;
		}
	}

	function initPost(context)
	{
		initPostImages(context);
		initSpoilers(context);
	}
	function initPostImages(context)
	{
		var $in_spoilers = $('div.sp-body var.postImg', context);

		$('var.postImg', context).not($in_spoilers).each(function(){
			var $v = $(this);
			var src = $v.attr('title');
			var img_align = $v.attr('align')!=undefined ? ' align="'+ $v.attr('align') +'"' : '';
			var className = $v.attr('class')!=undefined ? ' class="'+ $v.attr('class') +'"' : '';
			var is_href = $v.parent().attr('href');
			if(is_href)
			{
				$v.removeClass('postImg');
			}
			var $img = $('<img src="'+ src +'"'+ img_align + className +' alt="" />');

			var is_signature = $v.parents().hasClass('signature') ? true : false;

			if(!is_signature)
			{
				if (hidePostImg)
				{
					return $(this).replaceWith('');
				}

				if(banned_image_hosts && src.match(banned_image_hosts))
				{
					return $(this).replaceWith('<a href="#" title="' + bannedImageHosts + '"><img  src="./tracker/addons/images/spoiler/tr_oops.gif" alt="' + bannedImageHosts + '" /></a>');//Link to rules
				}
				else
				{
					$img = fixPostImage($img);
				}

				if(open_type==2)
				{
					//Original
					$img.bind('click', function(){ imgFit(this, spoilerMaxW); });
				}
				else if(open_type==1)
				{
					//Default
					$img.bind('click', function(){ return !window.open(src); });
				}
				else
				{
					//prettyPhoto
					$img.bind('click', function()
					{
						//$(this).prettyPhoto({'path':src});
						api_images = [src];
						//api_titles = ['Title 1'];
						//api_descriptions = ['Description 1']
						$.prettyPhoto.open(api_images);
					 });
				 }

				$('#preload').append($img);
				var loading_icon = '<a href="'+ src +'" target="_blank"><img src="./tracker/addons/images/spoiler/pic_loading.gif" alt="" /></a>';
				$v.html(loading_icon);

				if ($.browser.msie || $.browser.opera)
				{
					$img.one('load', function(){ imgFit(this, spoilerMaxW); });
					$v.empty().append($img);
					$v.after('<wbr>');
				}
				else
				{
					$img.one('load', function(){
						imgFit(this, spoilerMaxW);
						$v.empty().append(this);
					});
				}

				if(is_href)
				{
					$img.unbind('click');
				}
			}
			else
			{
				if (hideSigImg){ return $(this).replaceWith(''); }
				$v.empty().append($img);
			}
		});
	}
	function initSpoilers(context)
	{
		$('div.sp-body', context).each(function(){
			var $sp_body = $(this);
			var name = this.title || hiddenText;
			this.title = '';
			$('<div class="sp-head folded clickable">'+ name +'</div>').insertBefore($sp_body).click(function(e){
				if (!$sp_body.hasClass('inited')) {
					initPostImages($sp_body);
					$sp_body.prepend('<div class="clear"></div>').append('<div class="clear"></div>').addClass('inited');
					$('<div class="sp-head unfolded clickable">' + spoilerClose + '</div>').insertAfter($sp_body).click(function(){
						if($(document).scrollTop() > $sp_body.prev().offset().top)
						{
							$(document).scrollTop($sp_body.prev().offset().top - 200);
						}
						$(this).slideToggle('fast');
						$sp_body.slideToggle('fast');
						$sp_body.prev().toggleClass('unfolded');
					});
				}
				else
				{
					$sp_body.next().slideToggle('fast');
				}
				if (e.shiftKey) {
					e.stopPropagation();
					e.shiftKey = false;
					var fold = $(this).hasClass('unfolded');
					$('div.sp-head', $($sp_body.parents('td')[0])).filter( function(){ return $(this).hasClass('unfolded') ? fold : !fold } ).click();
				}
				else {
					$(this).toggleClass('unfolded');
					$sp_body.slideToggle('fast');
				}
			});
		});
	}
	function fixPostImage ($img)
	{
		var src = $img[0].src;
		// keep4u
		if (src.match(/keep4u/i)) {
			var new_src = src.replace(/http:\/\/keep4u.ru\/imgs\/\w\/(.*)\/(.*)\.(.*)/, "http://keep4u.ru/imgs/s/$1/$2.$3");
			//var new_url = src.replace(/http:\/\/keep4u.ru\/imgs\/\w\/(.*)\/(.*)\.(.*)/, "http://keep4u.ru/full/$1/$2/$3");
			$img.attr('src', new_src).addClass('clickable');
		}
		return $img;
	}
}})(jQuery);

jQuery(document).ready(function($){
	$(this).bb2Spoiler();
});
