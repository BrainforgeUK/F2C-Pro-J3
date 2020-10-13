var previewInit = false;

jQuery(function($)
{
	jQuery('#cropimage').attr("src", jQuery('#imageDir').val() + '/' + tmpImage);
	
	// Create variables (in this scope) to hold the API and image size
    var jcrop_api, boundx, boundy;

    jQuery('#cropimage').Jcrop(
   	{
    	onChange: updatePreview,
      	onSelect: updatePreview,
      	aspectRatio: jQuery('#aspectWidth').val() != '' ? jQuery('#aspectWidth').val() / jQuery('#aspectHeight').val() : 0
    },
    function()
    {
    	// Use the API to get the real image size
      	var bounds = this.getBounds();
      	boundx = bounds[0];
      	boundy = bounds[1];

      	// Store the API in the jcrop_api variable
      	jcrop_api = this;

      	// Move the preview into the jcrop container for css positioning
      	jQuery('#preview-pane').appendTo(jcrop_api.ui.holder);
      	// center the image
      	jcrop_api.ui.holder.css('top', jQuery('#top').val()+'px');
      	jcrop_api.ui.holder.css('left', jQuery('#left').val()+'px');
    });

    function updatePreview(c)
    {
    	if(!previewInit)
        {
        	jQuery('#preview').attr("src", jQuery('#imageDir').val() + '/' + tmpImage);
          	previewInit = true;
        }
          
        if (parseInt(c.w) > 0)
        {
    		// recalculate preview image boundaries
    		var prv = jQuery('.preview-container');
    		var previewWidth = MAXPREVIEWSIZE;
    		var previewHeight = MAXPREVIEWSIZE;
    		
    		if(c.w > c.h)
    		{
    			// landscape
    			var aspectRatio = c.w / c.h;
    			previewHeight = parseInt(MAXPREVIEWSIZE / aspectRatio);
    		}
    		else
    		{
    			// portrait
    			var aspectRatio = c.h / c.w;
    			previewWidth = parseInt(MAXPREVIEWSIZE / aspectRatio);
    		}

    		prv.css('height',previewHeight+'px');
    		prv.css('width',previewWidth+'px');
    		
            var rx = previewWidth / c.w;
            var ry = previewHeight / c.h;
              
            jQuery('#preview-pane .preview-container img').css({
              width: Math.round(rx * boundx) + 'px',
              height: Math.round(ry * boundy) + 'px',
              marginLeft: '-' + Math.round(rx * c.x) + 'px',
              marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
    	}

      	// update coordinates
      	jQuery('#x').val(c.x);
    	jQuery('#y').val(c.y);
    	jQuery('#w').val(c.w);
    	jQuery('#h').val(c.h);		
    };    
});
  
function checkCoordinates()
{
	if(!parseInt(jQuery('#w').val()))
	{ 
		alert(Joomla.JText._('COM_FORM2CONTENT_ERROR_CROPPING_EMPTY_REGION'));
	    return false;
	}

	var minSelectionWidth  = parseInt(jQuery('#minSelectionWidth').val());
	var minSelectionHeight = parseInt(jQuery('#minSelectionHeight').val());

	if(minSelectionWidth > 0 && (jQuery('#w').val() < minSelectionWidth))
	{
		alert(Joomla.JText._('COM_FORM2CONTENT_ERROR_IMAGE_CROP_MIN_WIDTH'));
	    return false;
	}

	if(minSelectionHeight > 0 && (jQuery('#w').val() < minSelectionHeight))
	{
		alert(Joomla.JText._('COM_FORM2CONTENT_ERROR_IMAGE_CROP_MIN_HEIGHT'));
	    return false;
	}
	
	return true;
};

function crop()
{
	if(checkCoordinates())
	{
		jQuery(document).ajaxStop(jQuery.unblockUI); 
		jQuery.blockUI({message: jBusyCroppingImage});
			
		var contentTypeFieldId = jQuery('#fieldid').val();
		var row = jQuery('#row').val();
		var url = 	'index.php?option=com_form2content&task=form.imagecrop&format=raw' +
					'&x='+jQuery('#x').val()+'&y='+jQuery('#y').val()+
					'&w='+jQuery('#w').val()+'&h='+jQuery('#h').val()+
					'&filename='+tmpImage +
					'&fieldid='+jQuery('#fieldid').val() +
					'&contenttypeid='+jQuery('#contenttypeid').val() +
					'&cropthumbonly='+jQuery('#cropthumbonly').val();

		jQuery.ajax({
		    type: 'POST',
		    dataType: 'JSON',
		    data: null,
		    url: url,
		    cache: false,
		    contentType: false,
		    processData: false,
		    success: function(data)
		    {
		    	if(data['error'] == '')
		    	{
		        	keyValue = '#t'+contentTypeFieldId;

		        	if(row >= 0)
		        	{
		        		keyValue += '_'+row;
		        	} 

		        	// Set the preview image
	        		window.parent.jQuery(keyValue+'_preview').attr("src", data['thumbnail']);
		        	window.parent.jQuery(keyValue+'_preview').css('display', 'block');
		        	// Indicate that cropping was performed
		        	window.parent.jQuery(keyValue+'_cropped').val('1');

		        	var cropUrl = rootUrl+'index.php?option=com_form2content&task=form.cropdisplay&tmpl=component&view=crop&fieldid='+contentTypeFieldId+'&contenttypeid='+jQuery('#contenttypeid').val()+'&image='+data['filename'];
		        	
		        	if(row >= 0)
		        	{
			        	cropUrl += '&row='+row;
			        	
			        	window.parent.jQuery('#t'+contentTypeFieldId+'_'+row+'filename').val(data['filename']);			
			        	// Set the correct url for the cropping window
			    		window.parent.modalCropUrls['t'+contentTypeFieldId+'_'+row] = cropUrl;
		        	}
		        	else
		        	{
			        	window.parent.jQuery('#t'+contentTypeFieldId+'_tmpfilename').val(data['filename']);			        	
			        	// Set the correct url for the cropping window
			        	window.parent.modalCropUrls[contentTypeFieldId] = cropUrl;
		        	}
		        	window.parent.jQuery('#modalCropWindow').modal('hide');
		    	}
		    	else
		    	{
		    		alert(data['error']);
		    	}
		    },
		});
	}
}