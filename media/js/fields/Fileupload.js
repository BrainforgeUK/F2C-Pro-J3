Form2Content.Fields.Fileupload =
{
	CheckRequired: function (id)
	{
		return jQuery('#'+id+'_originalfilename').val().trim() != '';
	}
}
