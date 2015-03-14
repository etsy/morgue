$(function () {

	
	$.get("/api/anniversary", function (d) {
		console.log(d);
		var e = JSON.parse(d);
		if (parseInt(e.anniversaries_today) > 0) {
			$(  $(".anniversary")[0]).append('<span class="badge badge-warning">'+ e.anniversaries_today +'</span>');
//			$(  $(".anniversary")[0]).addClass("nactive");
		}

	});

});
