function addTags() {
    var field = $("#tags");
    var tags = field.attr("value");
    if (tags !== '') {
      store_tags_for_event(get_current_event_id(), tags, function(data) {
        data = JSON.parse(data);
        $("#tag_paragraph").empty();

        for (var i in data) {
          var tag = data[i];
          var label = "<span class=\"label tag\" id=\"tag-" + tag.id + "\">" + tag.title + "  <a>&times;</a></span>";
          $("#tag_paragraph").append(label);
        }
        field.attr("value", '');
      });
    }
}

function clearSelectedTags() {
  window.location = "/";
}

//Applied to tags in edit.php
$("#tag_paragraph").on("click", ".tag a", function() {
  var label = $(this).parent();
  var id = label.attr("id").split("-")[1];
  delete_tag_for_event(get_current_event_id(), id, function(data, textStatus, jqXHR) {
    if (jqXHR.status == 204) {
      label.remove();
    }
  });
});


//Applied to tags in sidebar.php
$("#tag_well .tag").hover(function() {
  if ($(this).attr('id').split('-')[2] != 'selected') {
    $(this).addClass('label-info');
  }
}, function() {
  if ($(this).attr('id').split('-')[2] != 'selected') {
    $(this).removeClass('label-info');
  }
});

//Applied to tags in sidebar.php
$("#tag_well").on("click", ".tag", function() {
  var tag = $(this);
  var attributes = tag.attr("id").split("-");

  var id = attributes[1];
  var selected = (attributes[2] == 'selected');

  //Get the current tags from the page URL
  var tags = [];
  var url = window.location.toString();
  var query = url.substring(url.lastIndexOf('?') + 1);
  var params = $.map(query.split('&'), function(param) {
    param = param.split('=');
    if (param[0] == 'tags' && param[1].length > 0) {
      tags = tags.concat(param[1].split(','));
    } 
  });

  //Either add or remove the tag ID to the URL (as applicable)
  if ($.inArray(id, tags) != -1 && selected) {
    tags.splice(tags.indexOf(id), 1);
  } else if ($.inArray(id, tags) == -1 && !selected) {
    tags.push(id);
  }

  if (tags.length > 0) {
    window.location = "/?tags=" + tags.join(',');
  } else {
    window.location = "/";
  }
});