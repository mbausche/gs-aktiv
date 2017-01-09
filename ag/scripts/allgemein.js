function storeSessionData(prefix, key, value) {
	$.ajax({
    	  url: prefix + "storeSessionData.php?key=" + key + "&value=" + value,
    	  error: function(jqXHR, textStatus, errorThrown) {
    		  console.log(jqXHR.status + ' (' + errorThrown + ')');
    	  } 
    	});
}    


function checkIban(iban) {
	if (iban.length != 22) {
		return "Die IBAN sollte 22 Zeichen lang sein";
	} else {
		if (iban.substring(0,2).toLowerCase() != "de") {
			return "Die IBAN sollte mit DE beginnen";
		}
		else if (!$.isNumeric( iban.substring(2) )) {
			return "Ab der 3. Stelle sollte die IBAN numerisch sein";
		} else {
			return "";
		}
	}

}

function ajaxCall(url, okfunction) {
	$.ajax({
    	  url: url,
    	  error: function(jqXHR, textStatus, errorThrown) {
    		  console.log(jqXHR.status + ' (' + errorThrown + ')');
    	  },
    	  complete: okfunction
		  
    });
}    

function getDate(dateString) {
	if (dateString) {
		var values = dateString.split(".");
		return new Date(values[2], values[1] - 1, values[0]);
	} else {
		return undefined;
	}
	
}

function checkDateField(jqueryTextField,dateFrom, dateTo, imageDir) {
	if (jqueryTextField.val().length < 5) 
		return;
	
	var name = jqueryTextField.attr("name");
	
	$("#info_" + name).removeClass( "infocal_cal_error" );
	jqueryTextField.removeClass( "infocal_cal_error" );
	
	$("#info_" + name).removeClass( "infocal_cal_holiday" );
	jqueryTextField.removeClass( "infocal_cal_holiday" );

	var values = jqueryTextField.val().split(".");
	if (values.length != 3) {
		$("#info_" + name).html("Datum hat nicht das Format dd.mm.yyyy");
		$("#info_" + name).addClass("infocal_cal_error");
		jqueryTextField.addClass("infocal_cal_error");
	} else {
		
		
		var dFrom = getDate(dateFrom);
		var dTo =  getDate(dateTo);
		var date =  getDate(jqueryTextField.val());
		
		if (date < dFrom) {
			$("#info_" + name).html("Datum liegt vor dem Startdatum (" + dateFrom + ")");
			$("#info_" + name).addClass("infocal_cal_error");
			jqueryTextField.addClass("infocal_cal_error");
		} else if (date > dTo) {
			$("#info_" + name).html("Datum liegt nach dem Endedatum (" + dateTo + ")");
			$("#info_" + name).addClass("infocal_cal_error");
			jqueryTextField.addClass("infocal_cal_error");
		} else {
			var result = checkDate(date);
			if (result[1] != "") {
				$("#info_" + name).addClass("infocal_" + result[1]);
				jqueryTextField.addClass("infocal_" + result[1]);
			}
			var text = result[2].replace("\n","<br>");
			
			if (text != "") {
				text = text.replace("\n","<br>");
				$("#info_" + name).html("<img src='" + imageDir + "/warning.png'>&nbsp;" + text);
			}
			else
				$("#info_" + name).html("");
		}
	}

}

function addDatePicker(dateFrom, dateTo, imageDir) {
	$( "[dateField='true']" ).datepicker({
		showOn: "button",
		onSelect: function(dateText,inst) {

			var result = ["","",""];
			
			if (dateText != "") {
				var parts = dateText.split(".");
				var date = new Date(parseInt(parts[2]),parseInt(parts[1])-1, parseInt(parts[0]));
				result = checkDate(date);
			}
			var name = $(this).attr("name");
			var html = $("#info_" + name).html();
			$(this).prop("class","hasDatepicker infocal_" + result[1]);
			$(this).prop("title",result[2]);
			
			$("#info_" + name).prop("class","infocal_" + result[1]);
			if (result[2] != "") {
				result[2] = result[2].replace(/\n/g,"<br>");
				$("#info_" + name).html("<table class=hintTable><tr><td><img src='" + imageDir + "/warning.png'></td><td>" + result[2] + "</td></tr></table>");
			} else {
				$("#info_" + name).html(result[2]);
			}
			
		},
	    buttonImage: imageDir + "/calendar.png",
	    buttonImageOnly: true,  
	    minDate: dateFrom,
	    maxDate: dateTo,
	    buttonText: "Datum auswählen (Bereits verplante Tage werden rot dargestellt, Ferientermine violet)",
	    dayNames: [ "Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag" ],
	    dayNamesMin: [ "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa" ],
	    monthNames: [ "Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember" ],
	    dateFormat: "dd.mm.yy",
		beforeShowDay: function(date) {
			return checkDate(date);
		}
	});    
	
	$( "[dateField='true']" ).bind("propertychange change click keyup input paste", function() {
		checkDateField($(this),dateFrom, dateTo, imageDir);		
	});
}

function addDatePickerOverview(dateFrom, dateTo) {
	
	var count = 1;
	var start = getDate(dateFrom).getMonth();
	var end = getDate(dateTo).getMonth();
	
	while (start != end) {
		count++;
		start++;
		if (start == 12)
			start = 0;
	}
	
	$( "#datepicker_uebersicht" ).datepicker({
	    minDate: dateFrom,
	    maxDate: dateTo,
	    numberOfMonths: count,
	    dayNames: [ "Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag" ],
	    dayNamesMin: [ "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa" ],
	    monthNames: [ "Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember" ],
	    dateFormat: "dd.mm.yy",
		beforeShowDay: function(date) {
			return checkDate(date);
		}
	});    
}

function showAlert(title, msgContent) {
	if ($( "#dialog-message" ).length > 0) {
		$("#dialog-message-content").html(msgContent);
		$( "#dialog-message" ).attr("title",title);
		$( "#dialog-message" ).dialog({
			modal: true,
			position: { my: "top", at: "top+10%", of: window},
			buttons: {
				Ok: function() {
					$( this ).dialog( "close" );
				}
			}
		});
		
	} else if(typeof($.fn.popover) != 'undefined') { //bootstrap?
		$("body").append("<div id='bs_alert' class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='OK'>&times;</a><strong>" + title + ": </strong>" + msgContent + "</div>");
	} else {
		alert(msgContent);
	}
}

Date.prototype.format = function(){
	return this.getDate().padLeft(2,"0") + "." + (this.getMonth() + 1).padLeft(2,"0") + "." + this.getFullYear();
};

Date.prototype.addDays = function(days)
{
    var dat = new Date(this.valueOf());
    dat.setDate(dat.getDate() + days);
    return dat;
}


Number.prototype.padLeft = function(length, padWith){
	var result = "" + this;
	while (result.length < length) {
		result = padWith + result;
	}
	return result;
	
};

function showHideStepTable() {
	if (window.innerWidth < 1000) { //Small Devices
		$("#stepTableLarge").hide();
		$("#stepTableSmall").show();
	} else { //Large Devices
		$("#stepTableLarge").show();
		$("#stepTableSmall").hide();
	}
}


