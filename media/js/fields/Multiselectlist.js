Form2Content.Fields.Multiselectlist =
{
	CheckRequired: function (id)
	{
		checked = false;
		
		jQuery('[name="'+id+'[]"]').each(function(){
			if(jQuery(this).is(':checked'))
			{
				checked = true;
				return false;
			}
		});

		return checked;
	}
}
