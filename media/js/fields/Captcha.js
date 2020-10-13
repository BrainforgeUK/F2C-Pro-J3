Form2Content.Fields.Captcha =
{
	CheckRequired: function (id)
	{
		var response = grecaptcha.getResponse();		
		return response.length != 0;
	}
}
