Form2Content.Fields.Email =
{
	CheckRequired: function (id)
	{
		return jQuery('#'+id).val().trim() != '';
	}
}
