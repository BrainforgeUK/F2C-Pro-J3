function getNextKey(tableId)
{
	jQuery('#'+tableId+'MaxKey').val(parseInt(jQuery('#'+tableId+'MaxKey').val())+1);
	return jQuery('#'+tableId+'MaxKey').val();
}

function addRow(tableId, rowId, addFunction)
{
	var table = document.getElementById(tableId);
	var index = table.rows.length - 1;
	
	for(i = 1; i < table.rows.length; i++)
	{
		var row = table.rows[i];
		
		if(row.id == rowId)
		{
			index = i;
		}
	}
	
	var row = table.insertRow(index+1);
	row.id = tableId + '_' + getNextKey(tableId);
	eval(addFunction + '(tableId, row)');	
} 		

function removeRow(rowId)
{
	var tr = document.getElementById(rowId);
	
	if(tr)
	{
		while(tr.hasChildNodes()) 
		{
        	tr.removeChild(tr.lastChild);
        }
      	tr.parentNode.removeChild(tr);
	}
}

function moveUp(tableId, rowId)
{
	var table = document.getElementById(tableId);
	var index = 1;
	
	for(i = 1; i < table.rows.length; i++)
	{
		var row = table.rows[i];
		
		if(row.id == rowId)
		{
			index = i;
		}
	}
	
	if(index > 1)
	{
		var prev = table.rows[index-1];
		var current = table.rows[index];
		prev.parentNode.insertBefore(current,prev);		
	}	
}

function moveDown(tableId, rowId)
{
	var table = document.getElementById(tableId);
	var index = 1;
	
	for(i = 1; i < table.rows.length; i++)
	{
		var row = table.rows[i];
		
		if(row.id == rowId)
		{
			index = i;
		}
	}
	
	if(index < table.rows.length-1)
	{
		var prev = table.rows[index+1];
		var current = table.rows[index];
		current.parentNode.insertBefore(prev,current);		
	}	
}

function addDisplayListRow(tableId, rowId)
{
	var table = document.getElementById(tableId);
	var index = table.rows.length - 1;
		
	for(i = 1; i < table.rows.length; i++)
	{
		var row = table.rows[i];
		
		if(row.id == rowId)
		{
			index = i;
		}
	}
	
	var row = table.insertRow(index+1);
	row.id = tableId + '_' + getNextKey(tableId);
	
	var cellLeft = row.insertCell(0);
	var el1 = document.createElement('input');
	el1.type = 'text';
	el1.name = row.id + 'val';
	el1.id = row.id + 'val';
	el1.size = 40;	
	el1.maxLength = 255;	  
	cellLeft.appendChild(el1);
	  
	var elHidden = document.createElement('input');
	elHidden.type = 'hidden';
	elHidden.name = tableId + 'RowKey[]';
	elHidden.value = row.id;
	cellLeft.appendChild(elHidden);
	  
	var cellButtons = row.insertCell(1);
	var lnkUp = document.createElement('a');
	lnkUp.href = 'javascript:moveUp(\''+tableId+'\',\'' + row.id + '\');';
	lnkUp.innerHTML = '<i class="icon-f2carrow-up f2c_row_button" title="' + Joomla.JText._('COM_FORM2CONTENT_UP') + '"></i>';
	cellButtons.appendChild(lnkUp);	
	var lnkDwn = document.createElement('a');
	lnkDwn.href = 'javascript:moveDown(\''+tableId+'\',\'' + row.id + '\');';
	lnkDwn.innerHTML = '<i class="icon-f2carrow-down f2c_row_button" title="' + Joomla.JText._('COM_FORM2CONTENT_DOWN') + '"></i>';
	cellButtons.appendChild(lnkDwn);	
	var lnkDel = document.createElement('a');
	lnkDel.href = 'javascript:removeRow(\'' + row.id + '\');';
	lnkDel.innerHTML = '<i class="icon-f2cminus f2c_row_button" title="' + Joomla.JText._('COM_FORM2CONTENT_DELETE') + '"></i>';
	cellButtons.appendChild(lnkDel);	
	var lnkAdd = document.createElement('a');
	lnkAdd.href = 'javascript:addDisplayListRow(\''+tableId+'\',\'' + row.id + '\');';
	lnkAdd.innerHTML = '<i class="icon-f2cplus f2c_row_button" title="' + Joomla.JText._('COM_FORM2CONTENT_ADD') + '"></i>';
	cellButtons.appendChild(lnkAdd);		
}

function addDbLookupkMultiRow(tableId, rowId)
{
	var selectedId = jQuery('#'+tableId+'_lookup').val();
	if(selectedId==null || selectedId=='') return false;
	var selectedText = jQuery('#'+tableId+'_lookup :selected').text();
	var index = jQuery('#'+tableId+ ' tr').length - 1;
	var isPresent = false;
	
	jQuery('#'+tableId+ ' tr').each(function(i, tr) {
		if(i == 0) return true;

		if(jQuery('#'+tr.id+'val').val() == selectedId)
		{
			// Already present in the list
			isPresent = true;
			return false;
		}
		
		if(tr.id == rowId)
		{
			index = i;
		}
	});
	
	if(isPresent) return true;
	
	jQuery('#'+tableId+ ' tr').eq(index).after('<tr id="'+tableId + '_' + getNextKey(tableId)+'"><td></td><td></td></tr>');
	
	var row = jQuery('#'+tableId+' tr:last');
	var newRowId = row.attr('id');
	var leftCell = row.find('td:first');
	var rightCell = row.find('td:last');
	
	leftCell.text(selectedText);
	leftCell.append('<input type="hidden" name="'+newRowId+'val" id="'+newRowId+'val" value="'+selectedId+'">');
	leftCell.append('<input type="hidden" name="'+tableId+'RowKey[]" value="'+newRowId+'">');
	
	rightCell.append('<a href="javascript:moveUp(\''+tableId+'\',\'' + newRowId + '\');"><i class="icon-f2carrow-up f2c_row_button" title="'+Joomla.JText._('COM_FORM2CONTENT_UP')+'"></i></a>');
	rightCell.append('<a href="javascript:moveDown(\''+tableId+'\',\'' + newRowId + '\');"><i class="icon-f2carrow-down f2c_row_button" title="'+Joomla.JText._('COM_FORM2CONTENT_DOWN')+'"></i></a>');
	rightCell.append('<a href="javascript:removeRow(\''+newRowId+'\');"><i class="icon-f2cminus f2c_row_button" title="'+Joomla.JText._('COM_FORM2CONTENT_DELETE')+'"></i></a>');
}

function moveRowUp(rowId, headerRows)
{
	if(typeof(headerRows)==='undefined') headerRows = 0;
	var row = jQuery('#'+rowId);
	if(row.index() > headerRows) row.insertBefore(row.prev());
}

function moveRowDown(rowId, footerRows)
{
	if(typeof(footerRows)==='undefined') footerRows = 0;
	var row = jQuery('#'+rowId);
	if(row.index() < row.parent().children().length - footerRows -1) row.insertAfter(row.next());
}