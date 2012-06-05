//functions
// set the list selector
var setSelector = "div#forumlist";
// set the cookie name
var setCookieName = "forumlistOrder";
// set the cookie expiry time (days):
var setCookieExpiry = 365;

// function that writes the list order to a cookie
function getOrder() {
	// save custom order to cookie
	$.cookie(setCookieName, $(setSelector).sortable("toArray"), { expires: setCookieExpiry, path: "/" });
}
 
// function that restores the list order from a cookie
function restoreOrder() {
	var list = $(setSelector);
	if (list == null) return
	
	// fetch the cookie value (saved order)
	var cookie = $.cookie(setCookieName);
	if (!cookie) return;
	
	// make array from saved order
	var IDs = cookie.split(",");
	
	// fetch current order
	var items = list.sortable("toArray");
	
	// make array from current order
	var rebuild = new Array();
	for ( var v=0, len=items.length; v<len; v++ ){
		rebuild[items[v]] = items[v];
	}
	
	for (var i = 0, n = IDs.length; i < n; i++) {
		
		// item id from saved order
		var itemID = IDs[i];
		
		if (itemID in rebuild) {
		
			// select item id from current order
			var item = rebuild[itemID];
			
			// select the item according to current order
			var child = $("div#forumlist.ui-sortable").children("#" + item);
			
			// select the item according to the saved order
			var savedOrd = $("div#forumlist.ui-sortable").children("#" + itemID);
			
			// remove all the items
			child.remove();
			
			// add the items in turn according to saved order
			// we need to filter here since the "ui-sortable"
			// class is applied to all ul elements and we
			// only want the very first!  You can modify this
			// to support multiple lists - not tested!
			$("div#forumlist.ui-sortable").filter(":first").append(savedOrd);
		}
	}
}
 
jQuery(document).ready(function() {
	jQuery("div#forumlist").sortable({
		handle: '.sn-move-icon',
		placeholder: 'sn-placeholder',
		zIndex: '2',
		revert: true,
		forcePlaceholderSize: true,
		axis: 'y',
		update: function() { getOrder(); }
	});
	
	//getter
	var handle = $( ".selector" ).sortable( "option", "handle" );
	//setter
	$( ".selector" ).sortable( "option", "handle", '.sn-move-icon', '2' );
	
	//getter
	var forcePlaceholderSize = $( ".selector" ).sortable( "option", "forcePlaceholderSize" );
	//setter
	$( ".selector" ).sortable( "option", "forcePlaceholderSize", true );
	
	//getter
	var revert = $( ".selector" ).sortable( "option", "revert" );
	//setter
	$( ".selector" ).sortable( "option", "revert", true );	
	
	//getter
	var axis = $( ".selector" ).sortable( "option", "axis" );
	//setter
	$( ".selector" ).sortable( "option", "axis", 'y' );
 
	// here, we reload the saved order
	restoreOrder();
});