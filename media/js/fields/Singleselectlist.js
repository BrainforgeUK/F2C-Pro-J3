Form2Content.Fields.Singleselectlist =
{
	CheckRequired: function (id)
	{
		var elm = jQuery('#'+id);

		if(elm.is(':radio'))
		{
			// Radio buttons
			checked = false;
			
			jQuery('[name="'+id+'"]').each(function(){
				if(jQuery(this).is(':checked') && jQuery(this).val() != '')
				{
					checked = true;
					return false;
				}
			});

			return checked;			
		}	
		else
		{
			// Drop down list
			return (elm.val() != '');
		}
	}
}
