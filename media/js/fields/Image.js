Form2Content.Fields.Image =
{
	CheckRequired: function (id)
	{
		return jQuery('#'+id+'_originalfilename').val().trim() != '';
	}
}
