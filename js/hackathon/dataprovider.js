jQuery(document).ready(function() {
	
	postcodeField = jQuery("input[id='billing\:postcode']");
	postcodeField.live('blur', function () {
		postcode = postcodeField.val();
		ajaxCall('postcode',postcode);
	});
	
	taxField = jQuery("input[id='billing\:taxvat']");
	taxField.live('blur', function () {
		tax = taxField.val();
		ajaxCall('tax',tax);
	});
	
	cocField = jQuery("input[id='billing\:kvknummer']");
	cocField.live('blur', function () {
		coc = cocField.val();
		ajaxCall('coc',coc);
	});
	
	phoneField = jQuery("input[id='billing\:telephone']");
	phoneField.live('blur', function () {
		phone = phoneField.val();
		ajaxCall('phone',phone);
	});
	
	emailField = jQuery("input[id='billing\:email']");
	emailField.live('blur', function () {
		email = emailField.val();
		ajaxCall('email',email);
	});
	
	companyField = jQuery("input[id='billing\:company']");
	companyField.live('blur', function () {
		company = companyField.val();
		ajaxCall('company',company);
	});
	
}); 

function ajaxCall(field,value) {
	jQuery.getJSON('/peterjaap/magento/1702b/index.php/dataprovider/index/index/'+field+'/'+value, function(data) {
		jQuery.each(data, function (index,value) {
			field = jQuery("[id='billing\:"+index+"']");
			if(field) {
				field.val(value);
				if(field.get(0).tagName == 'SELECT') {
					field.change();
				}
			}
		});
	});
}
