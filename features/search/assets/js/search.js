
$("#search_field").keyup(function(event){
        if(event.keyCode == 13){
            var q = encodeURIComponent($("#search_field").val());
            window.location.href = "/search?q="+q;
        }
});
