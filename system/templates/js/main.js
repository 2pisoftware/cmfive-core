// Vue filters

Vue.filter('formatDate', function (value, format) {
	if (!value) return '';
	format = format ? format : 'DD/MM/YYYY';
	value = value.toString();
	return moment(value, 'X').format(format);
});

Vue.filter('capitalise', function(val) {
	return val.charAt(0).toUpperCase() + val.slice(1)
});

// placeholder function to stop JS complaining 
function selectAutocompleteCallback(event, ui) {
}

/*
	Global file upload widget
	Drag and drop files on any page
*/
var globalFileUpload = {
	//Max upload size in bytes, should be set to php max upload size
	MAXUPLOAD: 2097152,
	filesToUpload: [],
	dragTimer: null,
	objectClass: null,
	objectId: null,
	//The main drop target the global file drop overlay
	dropTarget: null,
	targetDragLeave: function(event) {
		event.preventDefault();
		event.stopPropagation();
	},
	targetDragOver: function(event) {
		window.clearTimeout(globalFileUpload.dragTimer);
		event.preventDefault();
		event.stopPropagation();
	},
	targetDrop: function(event) {
		event.preventDefault();
		event.stopPropagation();
		$(globalFileUpload.dropTarget).hide();
		jQuery('.global_file_drop_overlay_loading').show();
		jQuery('.global_file_drop_overlay_init').hide();
		var dt = event.dataTransfer;
		var files = dt.files;
		globalFileUpload.handleFiles(files);
		jQuery(globalFileUpload.dropTarget).hide();
	},
	init: function() {
		if(jQuery('.enable_drop_attachments').length === 0) {
			return;
		}
		globalFileUpload.objectClass = jQuery('.enable_drop_attachments').data('object');
		globalFileUpload.objectId = jQuery('.enable_drop_attachments').data('id');
		globalFileUpload.dropTarget = document.getElementById('global_file_drop_area');
		document.getElementsByTagName('body')[0].addEventListener('dragenter', function(e) {
			e.preventDefault();
			var dt = e.dataTransfer;
			if(dt.types != null && (dt.types.indexOf ? dt.types.indexOf('Files') != -1 : dt.types.contains('application/x-moz-file'))) {
				jQuery(globalFileUpload.dropTarget).show();
				window.clearTimeout(globalFileUpload.dragTimer);
			}
		}, false);
		document.getElementsByTagName('body')[0].addEventListener('dragleave', function(e) {
			e.preventDefault();
			globalFileUpload.dragTimer = window.setTimeout(function() {
				jQuery(globalFileUpload.dropTarget).hide();
			}, 500);
		}, false);
		globalFileUpload.dropTarget.addEventListener('dragexit', globalFileUpload.targetDragLeave, false);
		globalFileUpload.dropTarget.addEventListener('dragover', globalFileUpload.targetDragOver, false);
		globalFileUpload.dropTarget.addEventListener('drop', globalFileUpload.targetDrop, false);
	},
	handleFiles: function(files) {
		if (files) {
			var error = true;
			$.each(files,function(key,file) {
				var k = globalFileUpload.filesToUpload.push(file);
				globalFileUpload.filesToUpload[k-1].self_id = k;
				if(file.size > globalFileUpload.MAXUPLOAD) {
					globalFileUpload.filesToUpload[k-1].error = true;
				} else {
					error = false;
				}
			});
			if(!error) {
				jQuery('.global_file_drop_overlay').show();
				globalFileUpload.uploadFiles();
			} else {
				$('.global_file_drop_overlay_loading').hide();
				$('.global_file_drop_overlay_init').show();
			}
		}
	},
	uploadFiles: function() {
		for(i in globalFileUpload.filesToUpload) {
			if(globalFileUpload.filesToUpload[i] != undefined) {
				var file = globalFileUpload.filesToUpload[i];
				var parts;
				var mime=file.type;
				var blob=new Blob([file],{type : mime});
				var reader = new FileReader();
				reader.key = parseInt(i);
				reader.fileData = file;
				reader.onload = function(event) {
					var fd = {};
					var reader = this;
					var file = reader.fileData;
					fd["fname"] = file.name;
					fd["key"] = reader.key;
					fd["description"] = '';
					fd["title"] = file.name;
					fd["file"] = event.target.result;
					fd[$('#token').prop('name')] = $('#token').val();
					if(file.error) {
						globalFileUpload.filesToUpload.splice(reader.key, 1);
						return;
					}
					$.ajax({
						xhr: function() {
							var xhr = new window.XMLHttpRequest();
							xhr.upload.addEventListener("progress", function(evt) {
								if (evt.lengthComputable) {
									var percentComplete = Math.round(evt.loaded / evt.total * 100);
									if(percentComplete == 100) {
										$('.global_file_drop_overlay_loading h4').text('Processing files, please wait...');
									} else {
										$('.global_file_drop_overlay_loading h4').text('Uploading ('+percentComplete+'%)');
									}
								}
						   }, false);

						   return xhr;
						},
						type: 'POST',
						url: '/file/new/'+globalFileUpload.objectClass+'/'+globalFileUpload.objectId,
						data: fd,
						dataType: 'json',
						complete: function(data) {
							var key = parseInt(data.responseJSON.key);
							globalFileUpload.filesToUpload.splice(key, 1);
							if(globalFileUpload.filesToUpload.length === 0) {
								jQuery('.global_file_drop_overlay').hide();
								window.location.reload();
								jQuery('.global_file_drop_overlay_loading').hide();
								jQuery('.global_file_drop_overlay_init').show();
							}
						}
					});
				};
				reader.readAsDataURL(blob);
			}
		}
	}
}

function changeTab(hash) {
    if (hash.length > 0) {
        $(".tab-body > div").each(function() {
            $(this).hide();
            $(this).removeClass("active");
        });
        $('.tab-head > a').each(function() {
            $(this).removeClass("active");
        });
        
        if (hash[0] === "#"){
            hash = hash.substr(1);
        }
        
        if(history.replaceState) {
            history.replaceState(null, null, '#' + hash);
        } else {
            location.hash = '#' + hash;
        }
        
        $(".tab-body > div[id='" + hash + "']").show().addClass("active");
        $('.tab-head > a[href$="' + hash + '"]').addClass("active");
        
        // Update codemirror instances
        $('.CodeMirror').each(function(){
           this.CodeMirror.refresh();
        }); 
    }
}

function toggleModalLoading() {
    if ($(".loading_overlay").is(":visible")) {
        $(".loading_overlay").fadeOut();
    } else {
        $(".loading_overlay").fadeIn();
    }
}

function showModalLoading() {
	if (!$(".loading_overlay").is(":visible")) {
		$(".loading_overlay").show();
	}
}

function hideModalLoading() {
	if ($(".loading_overlay").is(":visible")) {
		$(".loading_overlay").hide();
	}
}

function bindCodeMirror() {
    var _codeMirror = [];
    //setup code-mirror
    var maxCodeInstances = $(".codemirror").length;
    var codeInstanceCount = 0;
    $('textarea.codemirror').each(function() {
//        $(this).click(function() {
            var textarea = $(this)[0];
            if (!_codeMirror[codeInstanceCount]) {
                _codeMirror[codeInstanceCount] = CodeMirror.fromTextArea(textarea, {
                    lineNumbers: true,
                    mode: 'text/html',
                    matchBrackets: true,
                    autoCloseTags: true,
                    wordWrap: true,
					viewportMargin: Infinity,
					
                });
                if (codeInstanceCount < (maxCodeInstances - 1)) {
                    codeInstanceCount++;
                }
            }
//        });
    });
}
    
/**
 * Clears all elements of a form
 */
function clearForm(ele) {
    $(ele).find(':input').each(function() {
        switch (this.type) {
            case 'password':
            case 'select-multiple':
            case 'select-one':
            case 'text':
            case 'hidden':
            case 'textarea':
                $(this).val('');
                break;
            case 'checkbox':
            case 'radio':
                this.checked = false;
        }
    });
}

/* This function gets dates in dd/mm/yyyy format, 
 * splits them and rearranges it in mm/dd/yyy format
 * so JS can convert the date string into milliseconds
 * and then it is converted into days. The two day
 * strings are subtracted and that result will be returned.
 *
 * This means that it works just like a normal compare.
 * e.g. (05/08/2010 and 01/08/2010, result will be -4/
 */
function compareDates(from_date, to_date) {
    var d = from_date.split("/"); //To reuse this function, replace the variables 'from_date' here
    var temp = d[0];
    d[0] = d[1];
    d[1] = temp;
    var oStr = d.join('/');
    oStr = (Date.parse(oStr) / 86400000);
    var n = to_date.split("/"); // and 'to_date' here, with ones that need to be compared
    temp = n[0];
    n[0] = n[1];
    n[1] = temp;
    var ndStr = n.join('/');
    ndStr = (Date.parse(ndStr) / 86400000);//from milliseconds to days, divide by 86400000
    return ndStr - oStr;
}

/**
 * Parses a given time into a date object
 * 
 * From: http://stackoverflow.com/a/2188651
 * 
 * @param {string} timeString
 * @returns {Date|parseTime.d}
 */
function parseTime(timeString) {    
    if (timeString == '') return null;

    var time = timeString.match(/(\d+)(:(\d\d))?\s*(p?)/i); 
    if (time == null) return null;
	
    var hours = parseInt(time[1],10);    
	hours += (hours < 12 && time[4])? 12 : 0;   
    
	var d = new Date();             
    d.setHours(hours);
    d.setMinutes(parseInt(time[3],10) || 0);
    d.setSeconds(0, 0);  
    
	return d;
}

/**
 * Formats file size into a human readable format
 *
 * @param int bytes
 * @param int decimals (precision)
 * @return string
 */
function formatBytes(bytes,decimals) {
   if(bytes == 0) return '0 Bytes';
   var k = 1024,
       dm = decimals + 1 || 3,
       sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
       i = Math.floor(Math.log(bytes) / Math.log(k));
   return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}