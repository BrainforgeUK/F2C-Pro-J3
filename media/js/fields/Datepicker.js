Form2Content.Fields.Datepicker =
{
	CheckRequired: function (id)
	{
		return jQuery('#jform_'+id).val().trim() != '';
	}
}
