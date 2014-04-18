function addGraph() {
    var zabbix_input = $("#zabbix_key_input");
    var zabbix_keys = (zabbix_input.attr("value"));
	if( zabbix_keys.length > 4 ) {
		$.get(
		  "/events/"+get_current_event_id()+"/zabbixhost/"+zabbix_keys,
		  function(data) {
			//data = JSON.parse(data);
			$('#zabbix_table_body > tr').remove();
            for (var i in data) {
              // add entries
                    var entry = "<tr class=\"jira-row\">";
					entry += "<td> <span class='label tag'><INPUT type='checkbox' onclick='saveTrigger("+ i +")' name='toto' id='toto' value='"+ i +"'> "+ data[i].name +"</span></td>";
					entry += "<td>";
					for (var j in data[i].graphs) {
						entry += "<span class='label tag'><INPUT type='checkbox' onclick='saveGraph("+ data[i].graphs[j].graphid +")' name='toto' id='toto' value='"+ data[i].graphs[j].graphid +"'> "+ data[i].graphs[j].name +"</span>";
						}
					entry += "</td>";
                    entry += "</tr>";
                
                $('#zabbix_table_body').append(entry);
           
            }
		  },
		  'json' // forces return to be json decoded
		);
	}
}

function saveTrigger(id) {
	var sD = new Date($("input#event-start-input-date").val());
	sD = new Date(sD - 3600000);
	var enddate = new Date($("input#event-end-input-date").val());
	endate = new Date( enddate + 3600000);

	$.get(
		  "/events/"+get_current_event_id()+"/zabbixtrigger/"+id+"/"+(sD.getTime()/1000)+"/"+(endate.getTime()/1000),
		  function(data) {
			var state = [];
			state[0] = "OK"; 
			state[1] = "Problem"; 
			state[2] = "Unknown";
			for( var i in data ) {
				var entry = "<tr class=\"jira-row\">";
				eD = new Date(data[i].clock*1000);
				entry +=  "<td>"+ eD +"</td><td>"+ data[i].hosts[0].host +"</td><td>"+ data[i].triggers[0].description +"</td><td>"+ state[data[i].value] +"</td></tr>";
				$('#zabbix_triggers_table_body').append(entry)
			}
          },
		  'json' // forces return to be json decoded
		);

}

function saveGraph(id) {
	var sD = new Date($("input#event-start-input-date").val());
	sD = new Date(sD - 3600000);
    var sT = timeToDate($("input#event-start-input-time").val());
	sT=sD
	var month = sD.getMonth()+1;
	if( month < 10 ) {
		month = "0" + month;
		}
	var day = sD.getDate();
	if( day < 10 ) {
		day = "0" + day;
		}
	var hour = sT.getHours();
	if( hour < 10 ) {
		hour = "0" + hour;
		}
	var minute = sT.getMinutes();
	if( minute < 10 ) {
		minute = "0" + minute;
		}
	var startDate = "" + sD.getFullYear() + "" + month + "" + day + "" + hour + "" + minute + "00";
	
	var startdate = sD;
	var starttime = sT;
	var enddate = new Date($("input#event-end-input-date").val());
	var endtime = timeToDate($("input#event-end-input-time").val());
	endate = new Date( enddate + 3600000);
	endtime=endate;

	startdate.setHours(starttime.getHours());
	startdate.setMinutes(starttime.getMinutes());
	enddate.setHours(endtime.getHours());
	enddate.setMinutes(endtime.getMinutes());

	period = (enddate - startdate)/1000;
	if( period < 3600) {
		period=3600;
		}
	
	
	//event-end-input-date
	$('#graphpreview').html("Go to zabbix /chart2.php?graphid="+ id +"&period="+ period +"&stime="+ startDate);
	
}


