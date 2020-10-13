Form2Content.Fields.Hyperlink =
{
	CheckRequired: function (id)
	{
		return jQuery('#'+id).val().trim() != '';
	}
}
