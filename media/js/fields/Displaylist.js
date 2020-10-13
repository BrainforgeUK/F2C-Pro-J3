Form2Content.Fields.Displaylist =
{
	CheckRequired: function (id)
	{
		for(var i=0;i<=jQuery('#'+id+'MaxKey').val();i++)
		{
			var elm = jQuery('#'+id+'_'+i+'val');			
			if(elm.length && elm.val().trim() != '') return true;
		}

		return false;
	}
}

