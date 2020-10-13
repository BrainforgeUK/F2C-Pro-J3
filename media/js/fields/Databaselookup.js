Form2Content.Fields.Databaselookup =
{
	CheckRequired: function (id)
	{
		return jQuery('#'+id).val().trim() != '';
	}
}
