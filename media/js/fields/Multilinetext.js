Form2Content.Fields.Multilinetext =
{
	CheckRequired: function (id)
	{
		return jQuery('#'+id).val().trim() != '';
	},
	
	LimitText: function(id, maxlimit) 
	{
		var field = jQuery('#'+id);

		if(field.val().length > maxlimit)
		{
			field.val(field.val().substring(0, maxlimit))
		}
		else
		{
			jQuery('#'+id+'remLen').val(maxlimit - field.val().length);
		}
	}
}
