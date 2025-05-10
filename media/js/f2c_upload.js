/*
 * Check if browser supports FormData
 */
var formDataSupport;

if ('undefined' === typeof window.FormData) 
{
	formDataSupport = false;
}
else
{
    fd = new FormData;
    formDataSupport	= ('undefined' === typeof fd.append) ? false : true;
}

function blockUiUpload()
{
	jQuery.blockUI({message: jBusyUploading});
}

function clearElement(elm)
{
	// Clear the element and prevent the onchange event from firing
	var onchange = elm.attr('onchange');
	elm.attr('onchange', '');
	elm.val('');
	elm.attr('onchange', onchange);
}

function uploadFile(field, extensions)
{
	var elm = (jQuery('#t'+field.id+'_fileupload'));

	if(extensions != null && extensions.length > 0)
	{
		var extensionFound = false;
		var ext = elm.val().substr(elm.val().lastIndexOf('.') + 1).toLowerCase();
		
		for(var i = 0; i < extensions.length; i++) {
			if(extensions[i] === ext) {
				extensionFound = true;
				break;
			}
		}
		
		if(!extensionFound)
		{
			var extensionList = '';
			for(var i = 0; i < extensions.length; i++) {
				extensionList += '.' + extensions[i];
				if(i < extensions.length -1) {
					extensionList += ', ';
				}
			}

			alert(jQuery.sprintf(Joomla.JText._('COM_FORM2CONTENT_EXTENSION_UPLOAD_NOT_ALLOWED'), '.'+ext, extensionList));
			return false;
		}
	}
	
	var data = new FormData();
	
	if(elm[0].files.length)
	{
		// Add all parameters to the data object so we create a POST upload for the data.
		// When using GET with querystring parameters, the Joomla Language Switcher plug-in will redirect the 
		// request which will discard the image information
		data.append(0, elm[0].files[0]);
		data.append('option', 'com_form2content');
		data.append('task', 'form.fileupload');
		data.append('format', 'raw');
		data.append('contenttypeid', field.contenttypeid);
		data.append('fieldid', field.id);
		data.append('fieldtypeid', field.fieldtypeid);
	}

	// unblock when ajax activity stops 
	jQuery(document).ajaxStop(jQuery.unblockUI); 
	jQuery.blockUI({message: jBusyUploading});
	
	jQuery.ajax({
	    type: 'POST',
	    dataType: 'JSON',
	    data: data,
		/* Modified Brainforge.uk 20250510 */
	    url: '',
	    cache: false,
	    contentType: false,
	    processData: false,
	    success: function(data)
	    {
	    	if(data['error'] == '')
	    	{
	    		setPreview(field, data);
	    	}
	    	else
	    	{
	    		alert(data['error']);
	    	}	
	    	
	    	clearElement(elm);
	    },
		error: function(jqXHR, textStatus, errorThrown)
		{
			alert(textStatus + '\r\n' + errorThrown);
		}
	});
}

/*
 * Handle the result data received from an iFrame upload
 */
function iFrameUpload(fieldId, fieldTypeId, contentTypeId, resultdata)
{
	var field = {id:fieldId, fieldtypeid:fieldTypeId, contenttypeid:contentTypeId};
	var data = jQuery.parseJSON(resultdata);
	
	// test for error
	if(data['error'] == '')
	{
		if(fieldTypeId == 18) // Image Gallery
		{
			addImageGalleryRow(fieldId, data);
		}
		else
		{
			setPreview(field, data);
		}	
	}
	else
	{
		alert(data['error']);
	}	
	
	jQuery.unblockUI();
}

function setPreview(field, data)
{
	jQuery('#t'+field.id+'_tmpfilename').val(data['filename']);
	jQuery('#t'+field.id+'_originalfilename').val(data['originalfilename']);
	jQuery('#t'+field.id+'_previewcontainer').html(data['preview']);
	// Unmark file for delete
	if(jQuery('#t'+field.id+'_del').length > 0) jQuery('#t'+field.id+'_del').val('');

	if(field.fieldtypeid == 6) // Image
	{
    	// Set the correct url for the cropping window and show the button
    	var cropButton = jQuery('#t'+field.id+'_crop');
    	var cropUrl = jQuery('#t'+field.id+'_crop_url');
    	
    	if(cropButton)
    	{
    		cropButton.css('display', '');
    		modalCropUrls[field.id] = rootUrl+"index.php?option=com_form2content&task=form.cropdisplay&tmpl=component&view=crop&fieldid="+field.id+"&contenttypeid="+jQuery('#jform_projectid').val()+"&image="+data['filename'];
     		cropButton.off('click').on('click', function()
    				{
    					modalUrl = modalCropUrls[field.id];
    					//jQuery('#modalCropWindow').css({"left":(jQuery(window).width()-jQuery('#modalCropWindow').width())/2,"top":(jQuery(window).height()-jQuery('#modalCropWindow').height())/2,"margin-left":"0px"});	
    					jQuery('#modalCropWindow').modal('show');
    					return false;
    				});
    		
    		// check if cropping is mandatory
    		if(parseInt(data['cropping']) == 2)
    		{
     			// launch the crop window
    			launchCropWindow(field.id);
    		}
    	}
	}
}

function deleteUploadedFile(field)
{
	var elm = (jQuery('#t'+field.id+'_tmpfilename'));

	if(elm.val())
	{
		// unblock when ajax activity stops 
		jQuery(document).ajaxStop(jQuery.unblockUI); 
		jQuery.blockUI({message: jBusyDeleting});
		
		jQuery.ajax({
		    type: 'POST',
		    dataType: 'JSON',
		    data: null,
		    url: 'index.php?option=com_form2content&task=form.fileclear&format=raw&contenttypeid='+field.contenttypeid+'&fieldid='+field.id+'&file='+elm.val(),
		    cache: false,
		    contentType: false,
		    processData: false,
		    success: function(data)
		    {
		    },
		});
	}
	
	jQuery('#t'+field.id+'_tmpfilename').val('');
	jQuery('#t'+field.id+'_originalfilename').val('');
	jQuery('#t'+field.id+'_previewcontainer').html('');
	// Mark file for delete
	if(jQuery('#t'+field.id+'_del').length > 0) jQuery('#t'+field.id+'_del').val('1');	

	if(field.fieldtypeid == 6) // Image
	{
		// Hide the crop button
		var cropButton = jQuery('#t'+field.id+'_crop');
		
		if(cropButton)
		{
			cropButton.css('display', 'none');
		}
	}
}


