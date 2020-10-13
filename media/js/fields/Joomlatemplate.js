Form2Content.Fields.Joomlatemplate =
{
	CheckRequired: function (id)
	{
		// Remove the starting 't' character from the id
		return jQuery('#'+id.substring(1)).val().trim() != '';
	}
}