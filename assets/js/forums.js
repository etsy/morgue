/* Validate that the comment text is less than 100
 * characters
 * @param  comment_text: text for the comment
 * @returns boolean: true if valid, false if greater than 100 char
 */
function validateCommentLength(comment_text) {
    if (comment_text.length > 100) {
        return false;
    }
    return true;
}
/*  Shows an error message after the given element
 *  @param  element: element to show error message after
 *  @param  error_text: error to display
 */
function showError(element, error_text) {
    element.after('<div class="alert alert-error" id="forum_error">'+error_text+'</div>');
    return;
}

function showForumLink() {
    var url = $("#forum_url");
    var comment = $("#forum_comment");
    var error = $("#forum_error");
    if (typeof error !== 'undefined') {
        error.remove();
    }
    var existing = $("#forumlinks_table_body").find(".forums-row");
    var existing_ids = [];
    $.each(existing, function(index, forum_row) {
      var classes = forum_row.className.split(/\s+/);
      $.each(classes, function (idx, cls) {
        var matches = cls.match(/forum-(\d*)/);
        if (matches !== null) {
          existing_ids.push(matches[1]);
        }
      });
    });

    if (!validateCommentLength(comment.val())) {
        showError(comment, 'Forum description must be less than 100 characters');
        return;
    }

    if (url.val() === '') {
        showError(comment, 'Forum link cannot be blank');
        return;
    }

    if (comment.val() === '') {
        showError(comment, 'Forum description cannot be blank');
        return;
    }

    if (url.val() !== '') {
      store_forum_url_for_event(get_current_event_id(), url.val(), comment.val(), function(data) {
        var id = "";
        data = JSON.parse(data);
        for (var i in data) {
          if (0 > $.inArray(data[i].id, existing_ids)) {
            existing_ids.push(data[i].id);
            var entry = "<tr class=\"forums-row forum-"+data[i].id+"\">";
            entry += "<td><a target=\"_blank\" role=\"button\" class=\"btn\" href=\"" + data[i].forum_link +"\">";
            if (data[i].comment === '') {
                entry += data[i].forum_link;
            } else {
                entry += data[i].comment;
            }
            entry += "</a></td>";
            entry += "<td><span id=\"forum-"+data[i].id+"\" class=\"close\">&times;</span></td>";
            entry += "</tr>";
            $("#forumlinks_table_body").append(entry);
          }
        }
        url.val("");
        comment.val("");
      });
    }
    return false;
}

$('#forumlinks_table_body').on('click', 'span.close', function () {
  $(this).fadeOut(100);
  var row = $(this).parents('tr');
  var newRow = "<tr><td colspan=\"2\"><div id=\"forum_placeholder\"></div></tr></td>";

  newRow = $(newRow).insertAfter(row);
  var placeholder = newRow.find("#forum_placeholder");

  confirm_delete("Are you sure you want to delete this link?", placeholder, this, function() {
    var self = $(this);
    var id = $(this).attr("id").split("-")[1];
    delete_forum_url_for_event(get_current_event_id(), id, function(data) {
       ($(self).parents('.forums-row')).remove();
    });
  }, function() {
    $(this).fadeIn(100);
    newRow.remove();
  });
});
