Form2Content.Fields.Editor =
{
	CheckRequired: function (id)
	{
		eval('result = valEditor'+id+'();');
		return result;
	}
}
