jQuery(document).ready(function() {
	domain = document.location.href.substring(0,document.location.href.indexOf('/checkout'));

	postcodeField = jQuery("input[id='billing\:postcode']");
	postcodeField.live('change', function () {
		postcode = postcodeField.val();
		Dataprovider.ajaxCall('postcode',postcode,domain);
	});
	
	taxField = jQuery("input[id='billing\:taxvat']");
	taxField.live('change', function () {
		tax = taxField.val();
		ajaxCall('tax',tax,domain);
	});
	
	cocField = jQuery("input[id='billing\:kvknummer']");
	cocField.live('change', function () {
		coc = cocField.val();
		ajaxCall('coc',coc,domain);
	});
	
	phoneField = jQuery("input[id='billing\:telephone']");
	phoneField.live('change', function () {
		phone = phoneField.val();
		ajaxCall('phone',phone,domain);
	});
	
	emailField = jQuery("input[id='billing\:email']");
	emailField.live('change', function () {
		email = emailField.val();
		ajaxCall('email',email,domain);
	});
	
	companyField = jQuery("input[id='billing\:company']");
	companyField.live('change', function () {
		company = companyField.val();
		ajaxCall('company',company,domain);
	});
	
}); 

jQuery.fn.outerhtml = function() {
  return jQuery('<div />').append(this.eq(0).clone()).html();
};

Dataprovider = {

	fill : function (key,field) {
		result = Dataprovider.data[key];
		Dataprovider.fillFields(result,field);
	},
	
	fillFields : function(result,field) {
		if(result) {
		  	filledout = 0;
			previewString = '';
		  	jQuery.each(result, function (index,value) {
				fieldElement = jQuery("[id='billing\:"+index+"']");
				if(fieldElement.length>0) {
					label = jQuery("[id='billing\:"+index+"']").parent().parent().find('label').text().replace('*','');
					previewString += label+': '+value+'\n';
					value = fieldElement.val();
					if(value && index!='country_id' && index!=field) {
						filledout++;
					}
				}
			});
			
			// check whether the user wants to overwrite the current values with the newly found values
			if(filledout===0 || (filledout!==0 && confirm('Do you want to replace the values with\n'+result.company+'\'s data ?\n\n'+previewString))) {
				jQuery.each(result, function (index,value) {
					field = jQuery("[id='billing\:"+index+"']");
					if(field) {
						field.val(value);
						if(field.prop('tagName') == 'SELECT') {
							field.trigger('change');
						}
					}
				});
			}
		}
	},
	
	ajaxCall : function(field,value,domain) {
		new Ajax.Request(domain+'/dataprovider/index/index/'+field+'/'+value, {
		  onCreate : function() {
		  	originalImg = jQuery('#billing-please-wait img').outerhtml();
		  	originalText = jQuery('#billing-please-wait').text(); 
		  	jQuery('#billing-please-wait').html(originalImg + '\nRetrieving company information..');
		  	jQuery('#billing-please-wait').show();
		  	
		  },
		  onSuccess : function(data) {
		  	if(data.status==200) {
			  	Dataprovider.data = jQuery.parseJSON(data.responseText);
			  	// check whether some fields have already been filled out
			  	result = false;
			  	if(Dataprovider.data.length===1) {
			  		result = Dataprovider.data[0];
			  	} else if(Dataprovider.data.length>1) {
					win = new Window({className: "magento", title: "Multiple results found.", width:400, height:400, destroyOnClose: true, recenterAuto:false});
					content = "<p>We have found the following companies with the information you gave us;</p>";
					jQuery.each(Dataprovider.data, function (key,result) {
						content += "<a href='javascript:Dataprovider.fill("+key+","+field+");'>"+result.company+'</a><br />';
					});
					win.getContent().update(content);
					win.setZIndex(100);
					win.showCenter(true);
				}
		  		
		  		fillFields(result,field);
				
			}
		  },
		  onComplete : function() {
		  	jQuery('#billing-please-wait').html(originalImg + '\n' + originalText);
		  	jQuery('#billing-please-wait').hide();
		  }
		});
	}
}
