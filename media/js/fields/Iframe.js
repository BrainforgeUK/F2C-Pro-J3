Form2Content.Fields.Iframe =
{
	CheckRequired: function (id)
	{
		return jQuery('#'+id).val().trim() != '';
	}
}
