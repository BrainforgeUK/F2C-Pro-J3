Form2Content.Fields.Imagegallery =
{
	CheckRequired: function (id)
	{
		for(var i=0;i<=jQuery('#'+id+'MaxKey').val();i++)
		{
			var state = jQuery('#'+id+'_'+i+'state').val();
			
			if(state != null && parseInt(state) != 2)
			{
				return true;
			}
		}

		return false;
	},

	CheckRequiredCropping: function(id)
	{
		for(var i=0;i<=jQuery('#'+id+'MaxKey').val();i++)
		{
			if(jQuery('#'+id+'_'+i+'state').length > 0 && jQuery('#'+id+'_'+i+'state').val() != 2)
			{
				if(parseInt(jQuery('#'+id+'_'+i+'_cropped').val()) != 1)
				{
					return false;
				}
			}
		}
	
		return true;
	}
}
