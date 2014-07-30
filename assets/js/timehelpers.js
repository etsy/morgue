function compareDateRange(start_date, end_date) {
  start_date = typeof start_date !== 'undefined' ? start_date : "#start_date";
  end_date = typeof end_date !== 'undefined' ? end_date : "#end_date";
  return new Date($(end_date).val()).valueOf() - new Date($(start_date).val()).valueOf();
}

function compareTimeRange(start_time, end_time, start_date, end_date) {
  start_time = typeof start_time !== 'undefined' ? start_time : "#start_time";
  end_time = typeof end_time !== 'undefined' ? end_time : "#end_time";
  start_date = typeof start_date !== 'undefined' ? start_date : "#start_date";
  end_date = typeof end_date !== 'undefined' ? end_date : "#end_date";
  return (compareDateRange() === 0)
      ? timeToDate($(end_time).val()).valueOf() - timeToDate($(start_time).val()).valueOf()
      : compareDateRange(start_date, end_date);
}

function timeToDate(dateString) {
  var isPM = dateString.indexOf("PM") > -1;
  var timeComponents = dateString.replace(/(AM|PM)/, '').replace(/\s.*$/, '').split(':');

  var date = new Date();
  date.setHours(timeComponents[0]);
  if (MORGUE.show_24_hours == false) {
      if ((isPM && (date.getHours() != 12) || (!isPM && (date.getHours() == 12)))) {
        date.setHours(date.getHours() + 12);
      }
  }
  date.setMinutes(timeComponents[1]);

  return date;
}

function timeStringFromDate(date) {
  var hours = date.getHours();
  var minutes = date.getMinutes();
  if (hours < 10) {
    hours = '0' + hours;
  }
  if (minutes < 10) {
    minutes = '0' + minutes;
  }

  if (MORGUE.show_24_hours) {
      return hours + ':' + minutes;
  }

  var apam = 'AM';
  if (hours >= 12) {
    hours = hours - 12;
    apam = 'PM';
  }
  if (hours == 0) {
    hours = 12;
  }
  return hours + ':' + minutes + apam;
}
//  Given the diff between two time stamps
//  returns a human readable hours, minutes string
//  Input: Number
//  Returns: String
function getTimeString(diff) {
  var hours = parseInt(diff / 1000 / 60 / 60, 10);
  var min = parseInt(diff / 1000 / 60 % 60, 10);
  if (hours === 1) {
      hours = hours + " hour";
  } else {
      hours = hours + " hours";
  }
  if (min === 1) {
    min = min + " minute";
  } else {
    min = min + " minutes";
  }
  return hours + ", " + min;
}

$('#start_date').focusout(function () {
  if (compareDateRange() < 0) {
    $('#end_date').val($('#start_date').val());
    $('#detect_date').val($('#start_date').val());
    $('#status_date').val($('#start_date').val());
  }
});

$('#start_time').focusout(function () {
  if (compareTimeRange() < 0) {
    $('#end_time').val($('#start_time').val());
    $('#detect_time').val($('#start_time').val());
    $('#status_time').val($('#start_time').val());
  }
});
$('#end_date').focusout(function () {
  if (compareDateRange() < 0) {
    $('#start_date').val($('#end_date').val());
    $('#detect_date').val($('#end_date').val());
    $('#status_date').val($('#end_date').val());
  }
});

$('#end_time').focusout(function () {
  if (compareTimeRange() < 0) {
    $('#start_time').val($('#end_time').val());
    $('#detect_time').val($('#end_time').val());
    $('#status_time').val($('#end_time').val());
  }
});

/**
 * wrapper function to update all times in the edit field
 */
function update_all_times() {
  update_startdate_for_event();
  update_starttime_for_event();
  update_enddate_for_event();
  update_endtime_for_event();
  update_detectdate_for_event();
  update_detecttime_for_event();
}

$('#event-start-input-date').focusout(function () {
  if (compareDateRange('#event-start-input-date','#event-end-input-date') < 0) {
    $('#event-end-input-date').val($('#event-start-input-date').val());
    $('#event-detect-input-date').val($('#event-start-input-date').val());
    $('#event-status-input-date').val($('#event-start-input-date').val());
    update_all_times();
  }
});

$('#event-start-input-time').focusout(function () {
  if (compareTimeRange('#event-start-input-time','#event-end-input-time',
                       '#event-start-input-date','#event-end-input-date') < 0) {
    $('#event-end-input-time').val($('#event-start-input-time').val());
    $('#event-detect-input-time').val($('#event-start-input-time').val());
    $('#event-status-input-time').val($('#event-start-input-time').val());
    update_all_times();
  }
});

$('#event-end-input-date').focusout(function () {
  if (compareDateRange('#event-start-input-date','#event-end-input-date') < 0) {
    $('#event-start-input-date').val($('#event-end-input-date').val());
    $('#event-detect-input-date').val($('#event-end-input-date').val());
    $('#event-status-input-date').val($('#event-end-input-date').val());
    update_all_times();
  }
});

$('#event-end-input-time').focusout(function () {
  if (compareTimeRange('#event-start-input-time','#event-end-input-time',
                       '#event-start-input-date','#event-end-input-date') < 0) {
    $('#event-start-input-time').val($('#event-end-input-time').val());
    $('#event-detect-input-time').val($('#event-end-input-time').val());
    $('#event-status-input-time').val($('#event-end-input-time').val());
    update_all_times();
  }
});
