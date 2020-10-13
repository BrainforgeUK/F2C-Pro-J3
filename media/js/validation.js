Form2Content.Validation = Form2Content.Validation || {

    SetState: function (id, valid)
    {
    	// check if it is a Joomla field
    	if(!jQuery.isNumeric(id.substring(1)))
    	{
    		id = id.substring(1);
    	}
    	
        var elm = jQuery('#'+id);
        var label = this.FindLabel(id, document.id('adminForm'));

        if(valid)
        {
            elm.removeClass('invalid').attr('aria-invalid', 'false');
            if (label) {label.removeClass('invalid');}
        }
        else
        {
            elm.addClass('invalid').attr('aria-invalid', 'true');
            if (label) {label.addClass('invalid');}
        }
    },

    FindLabel: function(id, form)
    {
          var $label, $form = jQuery(form);
          if (!id) {
               return false;
          }
          $label = $form.find('#' + id + '-lbl');
          if ($label.length) {
               return $label;
          }
          $label = $form.find('label[for="' + id + '"]');
          if ($label.length) {
               return $label;
          }
          return false;
     },
	
	CheckRequiredFields: function(arrValidation)
	{
		jQuery.each(arrValidation, function(index, value) 
		{
			var fieldId = 't'+value[0];
			var fieldType = value[1];
			var message = value[2];
			var result;
			
			eval('valid = Form2Content.Fields.'+fieldType+'.CheckRequired(\''+fieldId+'\');');
			Form2Content.Validation.SetState(fieldId, valid);
			if(!valid) messages.error.push(message);
		});
	},
	
	CheckDateField: function(id, format, label, displayformat)
	{
		var valid = this.IsValidDate(id, format);
		this.SetState(id, valid);
		if(!valid) messages.error.push(jQuery.sprintf(Joomla.JText._('COM_FORM2CONTENT_ERROR_DATE_FIELD_INCORRECT_DATE'),label, displayformat));
	},
    
    IsValidDate: function(id, format)
    {
    	var elm = jQuery('#'+id);
    	
    	if(elm.val().trim() == '')
    	{
    		return true;
    	}
    	
    	var value = format + '@' + elm.val(); 
     	var regexDate = /^(.*)@(\d{1,4})[\.\-\/](\d{1,4})[\.\-\/](\d{1,4})(\s((\d{1,2}):(\d{1,2}):(\d{1,2})))?$/;
     	var bits = regexDate.exec(value);
     	
     	if(!bits)
     	{
     		return false;
     	}
     	
     	var regexDateFormat = new RegExp('^%([dmY])-%([dmY])-%([dmY])$');
     	var m = regexDateFormat.exec(bits[1]);	
     	var day = 0;
     	var month = 0;
     	var year = 0;
     	var hours = 0;
     	var minutes = 0;
     	var seconds = 0;
     	
     	if (m != null && m.length == 4) 
     	{
     		for(i=1;i<=4;i++)
     		{
     			switch(m[i])
     			{
     				case 'd':
     					day = parseInt(bits[i+1],10);
     					break;
     				case 'm':
     					month = parseInt(bits[i+1],10);
     					break;
     				case 'Y':
     					year = parseInt(bits[i+1],10);
     					break;
     			}
     		}

     		if(month < 1 || month > 12)
     		{
     			return false;
     		}

     		if(day < 1)
     		{
     			return false;
     		}

     		switch(month)
     		{
     			case 1:
     			case 3:
     			case 5:
     			case 7:
     			case 8:
     			case 10:
     			case 12:
     				if(day > 31)
     				{
     					return false;
     				}
     				break;
     			case 2:
     				var leapTest = new Date().set('FullYear', year);
     				if(leapTest.isLeapYear())
     				{
     					if(day > 29)
     					{
     						return false;
     					}
     				}
     				else
     				{
     					if(day > 28)
     					{
     						return false;
     					}
     				}
     				break;
     			case 4:
     			case 6:
     			case 9:
     			case 11:
     				if(day > 30)
     				{
     					return false;
     				}
     				break; 
     		}
     	}
     	else
     	{
     		return false;			
     	}

     	if(bits[6] != undefined)
     	{
     		hours = parseInt(bits[7]);
     		minutes = parseInt(bits[8]);
     		seconds = parseInt(bits[9]);

     		if(hours < 0 || hours > 23)
     		{
     			return false;
     		}

     		if(minutes < 0 || minutes > 60)
     		{
     			return false;
     		}

     		if(seconds < 0 || seconds > 60)
     		{
     			return false;
     		}
     	}
     	
     	return true;
    },
     
	IsPatternMatch: function(id, pattern)
	{
		return (jQuery('#'+id).val().match(pattern) != null);
	},
    
	CheckPatternField: function(id, pattern, message)
	{
		var valid = this.IsPatternMatch(id, pattern);
		this.SetState(id, valid);
		if(!valid) messages.error.push(message);
	}
} 