$(function () {

	
	$.get("/api/anniversary", function (d) {
		console.log(d);
		var e = JSON.parse(d);
		if (e.anniversaries_today) {
			$(  $(".anniversary")[0]).append('<span class="badge badge-warning">1</span>');
//			$(  $(".anniversary")[0]).addClass("nactive");
		}

	});

});
