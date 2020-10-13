Form2Content.Fields.Geocoder =
{
	CheckRequired: function (id)
	{
		var re = new RegExp('^\\(-?\\d{1,3}\\.\\d+,\\s?-?\\d{1,3}\\.\\d+\\)$');
		return (jQuery('#'+id+'_latlon').html().match(re) && jQuery('#'+id+'_address').val().trim() != '');
	},
	
	Init: function()
	{
		jQuery(document).ready(function($) {
			geocoder = new google.maps.Geocoder();
			
			jQuery.each(geocoderInit, function(key, field) {
				
				if(field.showMap)
				{
					var centerLocation = {lat: field.lat, lng: field.lon};
	
			        var map = new google.maps.Map(document.getElementById(field.id+'_map_canvas'), {
			          zoom: field.zoom,
			          mapTypeId: field.mapTypeId,
			          center: centerLocation
			        });
		        
			        if(field.showMarker)
			        {
			        	Form2Content.Fields.Geocoder.AddDraggableMarker(field.id, map, centerLocation);
			        }
				}
				
		        /* Set-up the geocoding button */
		        jQuery('#'+field.id+'_geocode').click(function() {
		        	Form2Content.Fields.Geocoder.GeocodeAddress(geocoder, map, field.id);
		        });
		        
		        /* Set-up the autocomplete searchbox */
		        //var autocomplete = new google.maps.places.Autocomplete(document.getElementById(field.id+'_address'));
			});
		});
	},
	
	GeocodeAddress: function(geocoder, resultsMap, prefix) 
	{
		if(geocoder)
		{
			var address = jQuery('#'+prefix+'_address').val();
			
	        geocoder.geocode({'address': address}, function(results, status) {
	          if (status === 'OK') 
	          {
	        	Form2Content.Fields.Geocoder.RemoveMarker(prefix);
	        	
	        	jQuery('#'+prefix+'_latlon').html('(' + results[0].geometry.location.toUrlValue(5) + ')');
	        	jQuery('#'+prefix+'_hid_lat').val(results[0].geometry.location.lat().toFixed(5));
	        	jQuery('#'+prefix+'_hid_lon').val(results[0].geometry.location.lng().toFixed(5));
	        	jQuery('#'+prefix+'_error').hide();
	        	
	        	if(resultsMap)
	        	{
		            resultsMap.setCenter(results[0].geometry.location);
		            Form2Content.Fields.Geocoder.AddDraggableMarker(prefix, resultsMap, results[0].geometry.location);
	        	}
	          } 
	          else 
	          {
	        	  Form2Content.Fields.Geocoder.ClearResults(prefix);
	        	  jQuery('#'+prefix+'_address').val(address);
	        	  jQuery('#'+prefix+'_error').show();
	          }
	        });
		}
    },
	
	RemoveMarker: function(prefix)
	{
		var localmarker;
		eval('localmarker='+prefix+'_marker;');
		if(localmarker) localmarker.setMap(null);		
	},
	
	ClearResults: function(prefix)
	{
		jQuery('#'+prefix+'_latlon').html('');
		jQuery('#'+prefix+'_hid_lat').val('');
		jQuery('#'+prefix+'_hid_lon').val('');	
		jQuery('#'+prefix+'_address').val('');
		jQuery('#'+prefix+'_error').hide();
		Form2Content.Fields.Geocoder.RemoveMarker(prefix);
	},
	
	AddDraggableMarker: function(prefix, map, position)
	{
        var marker = new google.maps.Marker({ map: map,position: position, draggable: true });
          
        google.maps.event.addListener(marker, 'dragend', function() {
        	var position = marker.getPosition();
			jQuery('#'+prefix+'_latlon').html('(' + position.toUrlValue(5) + ')');
			jQuery('#'+prefix+'_hid_lat').val(position.lat().toFixed(5));
			jQuery('#'+prefix+'_hid_lon').val(position.lng().toFixed(5));
		});
          
        eval(prefix+'_marker = marker');
	}
}