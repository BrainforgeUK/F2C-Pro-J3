<?php
// no direct access
defined('_JEXEC') or die;

// Include jQuery.
JHtml::_('jquery.framework');
?>
<div class="modal hide fade" id="collapseModalBatchRefresh" style="padding: 10px;">
	<div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3><?php echo JText::_('COM_FORM2CONTENT_BATCH_REFRESH_TITLE');?></h3>
	</div>
	<div class="modal-body">
	
		<div id="progress" class="progress progress-striped active">
			<div id="progress-bar" class="bar bar-success" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
		</div>	
		<div><h4><span id="process-state"></span> <span id="num-processed"></span>/<span id="total-process"></span></h4></div>
		<div id="errors" class="alert alert-danger" style="visibility: none;"></div>
		<div id="warnings" class="alert alert-warning" style="visibility: none;"></div>
	</div>
	<div class="modal-footer">
		<button id="btnBatchRefreshClose" class="btn" type="button" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
	</div>
</div>
<script type="text/javascript">
var isProcessing = false;

jQuery('#btnBatchRefesh').click(function(event)
{
	if(document.adminForm.boxchecked.value==0)
	{
		alert('<?php echo addslashes(JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST')); ?>');
		event.stopPropagation();  
	}
});

jQuery('#btnBatchRefreshClose').click(function(event)
{
	if(isProcessing)
	{
		isProcessing = false;
		jQuery('#btnBatchRefreshClose').prop('disabled', true);
		event.stopPropagation(); 
	}
});	

jQuery('#collapseModalBatchRefresh').on('show', function()
{
	var list = jQuery('input:checkbox[id^="cb"]:checked').map(function(){return jQuery(this).val();}).get();	
	initControls(list.length);
	processChunk(list, 0);
});

function initControls(total)
{
	jQuery('#process-state').text('<?php echo JText::_('COM_FORM2CONTENT_PROCESSING_ITEMS', true);?>');
	jQuery('#btnBatchRefreshClose').text('<?php echo JText::_('JCANCEL', true); ?>');	
	jQuery('#total-process').text(total);
	jQuery('#progress-bar').addClass('bar-success');
	jQuery('#errors').empty();
	jQuery('#errors').hide();
	jQuery('#warnings').empty();
	jQuery('#warnings').hide();	
	updateProgress(0);
	isProcessing = true;	
}

function processChunk(list, processed)
{
	// check if processing is still allowed
	if(!isProcessing)
	{
		jQuery('#process-state').text('<?php echo JText::_('COM_FORM2CONTENT_PROCESSING_ABORTED', true);?>');
		jQuery('#btnBatchRefreshClose').text('<?php echo JText::_('JTOOLBAR_CLOSE', true); ?>');
		jQuery('#btnBatchRefreshClose').prop('disabled', false);
		jQuery('#progress-bar').removeClass('bar-success');
		jQuery('#progress-bar').addClass('bar-warning');
		return;
	}
	
	var chunkSize = <?php echo F2cFactory::getConfig()->get('batch-refresh-chunksize', 10); ?>;	
	var ids = list.splice(0,chunkSize);
	var data = new FormData();
	data.append('option', 'com_form2content');
	data.append('task', 'forms.refresh');
	data.append('format', 'raw');
	data.append('ids', ids);

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
	    	processed += data['processed'];
	    	
	    	if(data['error'])
	    	{
	    		jQuery('#warnings').show();

		    	if(data['error'].length)
		    	{
		    		jQuery('#warnings').show();

		    		jQuery.each(data['error'], function(i, val){
		    			jQuery('#warnings').append('<p>'+val+'</p>');
			    		});			    	
		    	}		    
	    	}

	    	updateProgress(processed);
			
			if(list.length)
			{
				// processs the remainder of the list
				processChunk(list, processed);
			}
			else
			{
				// we're ready
				isProcessing = false;
				jQuery('#process-state').text('<?php echo JText::_('COM_FORM2CONTENT_PROCESSING_FINISHED', true);?>');
				jQuery('#btnBatchRefreshClose').text('<?php echo JText::_('JTOOLBAR_CLOSE', true); ?>');
				jQuery('#btnBatchRefreshClose').prop('disabled', false);
				
				// check for errors/warnings
				if(jQuery('#warnings').css('display') == 'none' && jQuery('#errors').css('display') == 'none')
				{
					// uncheck all checkboxes
					jQuery("#adminForm input:checkbox:checked").each(function() {
						var id = jQuery(this).attr('id');

						if(typeof id !== typeof undefined && id !== false && id.indexOf('cb') === 0)
						{
							jQuery(this).attr('checked', false);
						}
						else
						{
							if(jQuery(this).attr('name') === 'checkall-toggle')
							{
								jQuery(this).attr('checked', false);
							}
						}
					});

					// close window
					jQuery('#btnBatchRefreshClose').click();
				}
			} 
	    },
		error: function(jqXHR, textStatus, errorThrown)
		{
			jQuery('#errors').show();
			jQuery('#errors').append('<p>'+textStatus+': '+errorThrown+'</p>');
		}		
	});				
}

function updateProgress(processed)
{
	var perc = parseInt((processed/parseInt(jQuery('#total-process').text()))*100);	    	
	jQuery('#num-processed').text(processed);
	jQuery('#progress-bar').css('width', perc + '%').attr('aria-valuenow', perc);	
}
</script>