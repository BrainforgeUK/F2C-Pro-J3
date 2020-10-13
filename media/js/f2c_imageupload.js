function uploadGalleryImage(contentTypeId, contentTypeFieldId, extensions)
{	
	var elm = (jQuery('#t'+contentTypeFieldId+'_fileupload'));
	var data = new FormData();
	
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
	
	if(elm[0].files.length)
	{
		// Add all parameters to the data object so we create a POST upload for the data.
		// When using GET with querystring parameters, the Joomla Language Switcher plug-in will redirect the 
		// request which will discard the image information
		data.append(0, elm[0].files[0]);
		data.append('option', 'com_form2content');
		data.append('task', 'form.imageupload');
		data.append('format', 'raw');
		data.append('contenttypeid', contentTypeId);
		data.append('fieldid', contentTypeFieldId);
	}

	// unblock when ajax activity stops 
	jQuery(document).ajaxStop(jQuery.unblockUI); 
	jQuery.blockUI({message: jBusyUploading});
	
	jQuery.ajax({
	    type: 'POST',
	    dataType: 'JSON',
	    data: data,
	    url: 'index.php',
	    cache: false,
	    contentType: false,
	    processData: false,
	    success: function(data)
	    {	
	    	if(data['error'])
	    	{
	    		alert(data['error']);
	    	}
	    	else
	    	{	
	    		addImageGalleryRow(contentTypeFieldId, data);
	    	}
	    	
	    	// Clear the upload field
	    	clearElement(elm);
	    },
		error: function(jqXHR, textStatus, errorThrown)
		{
			alert(textStatus + '\r\n' + errorThrown);
		}
	});	
}

function transferImage(contentTypeId, contentTypeFieldId)
{	
	var data = new FormData();
	var elm = (jQuery('#jform_t'+contentTypeFieldId+'_browse'));

	if(elm.val() == '')
	{
		return;
	}

	// unblock when ajax activity stops 
	jQuery(document).ajaxStop(jQuery.unblockUI); 
	jQuery.blockUI({message: jBusyUploading});
	
	data.append('image', elm.val());
	
	jQuery.ajax({
	    type: 'POST',
	    dataType: 'JSON',
	    data: data,
	    url: 'index.php?option=com_form2content&task=form.imagetransfer&format=raw&contenttypeid=' + contentTypeId + '&fieldid=' + contentTypeFieldId,
	    cache: false,
	    contentType: false,
	    processData: false,
	    success: function(data)
	    {
	    	var field = {id:contentTypeFieldId, fieldtypeid:6, contenttypeid:contentTypeId};
	    	setPreview(field, data);
	    	clearElement(elm);
	    },
	});
}

function transferGalleryImage(contentTypeId, contentTypeFieldId)
{	
	var data = new FormData();
	var elm = (jQuery('#jform_t'+contentTypeFieldId+'_browse'));

	if(elm.val() == '')
	{
		return;
	}

	// unblock when ajax activity stops 
	jQuery(document).ajaxStop(jQuery.unblockUI); 
	jQuery.blockUI({message: jBusyUploading});
	
	data.append('image', elm.val());
	
	jQuery.ajax({
	    type: 'POST',
	    dataType: 'JSON',
	    data: data,
	    url: 'index.php?option=com_form2content&task=form.imagetransfer&format=raw&contenttypeid=' + contentTypeId + '&fieldid=' + contentTypeFieldId,
	    cache: false,
	    contentType: false,
	    processData: false,
	    success: function(data)
	    {
	    	addImageGalleryRow(contentTypeFieldId, data);
	    	clearElement(elm);
	    },
	});
}

function launchCropWindow(fieldId)
{
	var link = document.getElementById('t'+fieldId+'_crop');
	
	if(link.click)
	{
		link.click();
	}
}

function getNextRowKey(tableId)
{
	var fldMaxKey = jQuery('#'+tableId+'MaxKey');	
	fldMaxKey.val(parseInt(fldMaxKey.val()) + 1);
	return fldMaxKey.val();
}

function addImageGalleryRow(contentTypeFieldId, data)
{	
	var tableId = 't'+contentTypeFieldId;
	var fldMaxKey = jQuery('#'+tableId+'MaxKey');
	var settings = jQuery.parseJSON(eval('t' + contentTypeFieldId + '_settings'));
	
	addRow(tableId, '', 'prepareRowGallery');

	// get the new row
	var rowKey = fldMaxKey.val();
	var newRow = jQuery('#' + tableId + '_' + rowKey);
	var rowId = newRow.attr('id');
	var cropping = parseInt(jQuery('#'+tableId+'Cropping').val());
	
	newRow.find('td:first').append('<img id="t'+contentTypeFieldId+'_'+rowKey+'_preview" src="' + data['thumbnail'] + '">');
	
	if(settings['show_title_tag'] == 1 || settings['show_alt_tag'] == 1)
	{
		var html = '<td><table class="f2c_image_gallery_tbl_alt_title">';
		if(settings['show_alt_tag'] == 1)
		{
			html += '<tr><td>'+settings['jAltTag']+'</td><td><input type="text" id="'+rowId+'alt" name="'+rowId+'alt" size="40" maxlength="255"/></tr>';
		}
		if(settings['show_title_tag'] == 1)
		{
			html += '<tr><td>'+settings['jTitleTag']+'</td><td><input type="text" id="'+rowId+'title" name="'+rowId+'title" size="40" maxlength="255"/></tr>';
		}
		html += '</table></td>';
		newRow.find('td:first').after(html);
	}
	
	var cropUrl = rootUrl+"index.php?option=com_form2content&task=form.cropdisplay&tmpl=component&view=crop&fieldid="+contentTypeFieldId+"&contenttypeid="+jQuery('#jform_projectid').val()+"&row="+rowKey+"&image="+data['filename'];
	
	newRow.find('td:first').append('<input type="hidden" name="'+rowId+'state"  id="'+rowId+'state" value="0"/>');
	newRow.find('td:first').append('<input type="hidden" name="'+tableId + 'RowKey[]" value="'+rowId+'"/>');
	newRow.find('td:first').append('<input type="hidden" name="'+rowId + 'filename" id="'+rowId + 'filename" value="'+data['filename']+'"/>');
	newRow.find('td:first').append('<input type="hidden" name="'+rowId + 'originalfilename" id="'+rowId + 'originalfilename" value="'+data['originalfilename']+'"/>');
	newRow.find('td:first').append('<input type="hidden" name="'+rowId + '_cropped" id="'+rowId + '_cropped" value="0"/>');
	
	if(cropping > 0)
	{
		newRow.find('td:last').append('<a id="'+rowId+'_crop" href="#modalCropWindow" class="btn">Crop</a>');
		modalCropUrls[rowId] = cropUrl;

		jQuery('#'+rowId+'_crop').off('click').on('click', function()
				{
					modalUrl = modalCropUrls[rowId];
					//jQuery('#modalCropWindow').css({"left":(jQuery(window).width()-jQuery('#modalCropWindow').width())/2,"top":(jQuery(window).height()-jQuery('#modalCropWindow').height())/2,"margin-left":"0px"});	
					jQuery('#modalCropWindow').modal('show');
					return false;
				});
		
		// check if cropping is mandatory
		if(cropping == 2)
		{
 			// launch the crop window
			launchCropWindow(contentTypeFieldId+'_'+rowKey);
		}
	}
	
	setUploadState(tableId);
}

function prepareRowGallery(tableId, row)
{
	var jRow = jQuery(row);
	
	var cellLeft = jQuery('<td></td>');
	var jCellButtons = jQuery('<td><a href="javascript:moveRowUp(\''+row.id+'\');"><i class="icon-f2carrow-up f2c_row_button" title="'+Joomla.JText._('COM_FORM2CONTENT_UP')+'"></i></a><a href="javascript:moveRowDown(\''+row.id+'\');"><i class="icon-f2carrow-down f2c_row_button" title="'+Joomla.JText._('COM_FORM2CONTENT_DOWN')+'"></i></a><a href="javascript:deleteImageGalleryRow(\''+ row.id+'\');"><i class="icon-f2cminus f2c_row_button" title="'+Joomla.JText._('COM_FORM2CONTENT_DELETE')+'"></i></a></td>');
	
	jRow.append(cellLeft);
	jRow.append(jCellButtons);
}

function deleteImageGalleryRow(rowId)
{
	jQuery('#'+rowId+'state').val(2);
	jQuery('#'+rowId).hide();
	// Get the elementid from the rowId
	var arr = rowId.split('_');
	setUploadState(arr[0]);
}

/*
 * Count the gallery images as present on the screen
 */
function countGalleryImages(id)
{
	count = 0;
	
	for(i = 1; i <= jQuery('#'+id+'MaxKey').val(); i++)
	{
		row  = jQuery('#'+id+'_'+i+'state');

		// Do not count deleted rows
		if(row != null && row.val() != 2)
		{
			count++;
		}
	}

	return count;
}

/*
 * Hide the image gallery upload mechanism when the maximum number of images has been reached
 */
function setUploadState(id)
{
	var maxUploads = jQuery('#'+id+'MaxUploads').val();
	var style = (maxUploads == '') ? 'visible' : (countGalleryImages(id) < maxUploads) ? 'visible' : 'hidden';
	jQuery('#'+id+'_upload_area').css('visibility', style);
}
