jQuery(document).ready(function() 
{
	// Click the fancy upload button, because the real upload control is hidden.
	jQuery('button.f2c_select_file').on("click", function() {
		jQuery(this).next('input[type="file"]').click();
	});
});