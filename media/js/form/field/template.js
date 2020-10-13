function f2cSelectTemplate(field,template) 
{
	var old_template = jQuery("#"+field).val();
	
	if (old_template != template) 
	{
		jQuery("#"+field+"_id").val(template);
		jQuery("#"+field+"_name").val(template);
		jQuery("#modalTemplate"+field).modal("hide");
	}
}
