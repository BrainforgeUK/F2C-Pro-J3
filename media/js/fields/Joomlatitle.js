Form2Content.Fields.Joomlatitle =
{
	CheckRequired: function (id)
	{
		// Remove the starting 't' character from the id
		return jQuery('#'+id.substring(1)).val().trim() != '';
	}
}