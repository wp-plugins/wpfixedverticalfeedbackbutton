
function loadPopup() {
    jQuery("#popupbackground").css({
	"opacity": "0.7"
    });

    jQuery("#popupbackground").fadeIn("slow");
    jQuery("#popup").fadeIn("slow");
    //jQuery("#popupbackground").css("color" , '#ff00ff');
}

function unloadPopup() {
    //$("#popup").animate({"top": "+=15px", "opacity":"20%"}, "slow").fadeOut("slow");
    //$("#popup").animate({"top": "+=15px"}, "fast").hide("slow");
    jQuery("#popup").fadeOut("slow");
    jQuery("#popupbackground").fadeOut("slow");
}

function centerPopup() {
    //request data for centering
    var windowWidth = document.documentElement.clientWidth;
    var windowHeight = document.documentElement.clientHeight;
    var popupHeight = jQuery("#popup").height();
    var popupWidth = jQuery("#popup").width();

    //centering
    jQuery("#popup").css({
        "position": "fixed",
        "top": windowHeight / 2 - popupHeight / 2,
        "left": windowWidth / 2 - popupWidth / 2

    });

    jQuery("#popupbackground").css({
	    "height": windowHeight
    });

}


jQuery(document).ready(function($) {
    jQuery("#popup").hide();

    jQuery(".contactform").click(function() {
	    centerPopup();
	    loadPopup();
    });
    jQuery("#popupclose").click(function() {
	//$("p").show();
	unloadPopup();
    });
    jQuery("#popupbackground").click(function() {
	//$("p").show();
	unloadPopup();
    });


    $('.select_buttontext').change(function() {
		var val = $(this).val();
		var selnumber = $(this).attr('data-selnumber');
		//console.log(this);			
		if(val == 'custom_img') {
			//console.log($(this).next('div.for_custom_image'));
		    $('div.for_custom_image'+selnumber).show();
		    $('div.for_custom_text'+selnumber).hide();
		} else if(val == 'custom_text') {
			//console.log(val);
		    $('div.for_custom_image'+selnumber).hide();
		    $('div.for_custom_text'+selnumber).show();
		} else {
			//console.log(val);
			//console.log($(this).next('div.for_custom_image'));
		    $('div.for_custom_image'+selnumber).hide();
		    $('div.for_custom_text'+selnumber).hide();
		}
    });

    $('.select_buttontext').each(function() {

		var val = $(this).val();
		var selnumber = $(this).attr('data-selnumber');
		//console.log(this);			
		if(val == 'custom_img') {
			//console.log($(this).next('div.for_custom_image'));
		    $('div.for_custom_image'+selnumber).show();
		    $('div.for_custom_text'+selnumber).hide();
		} else if(val == 'custom_text') {
			//console.log(val);
		    $('div.for_custom_image'+selnumber).hide();
		    $('div.for_custom_text'+selnumber).show();
		} else {
			//console.log(val);
			//console.log($(this).next('div.for_custom_image'));
		    $('div.for_custom_image'+selnumber).hide();
		    $('div.for_custom_text'+selnumber).hide();
		}
    });




    jQuery('.cb-fixedverticalbutton-wrapper input[type="radio"]').each(function () {
        if( jQuery(this).prop('checked') === true ) {
            jQuery(this).checked = true;
        }
    });

    stylizeFields();

    function stylizeFields() {
        var elems = ['.cb-fixedverticalbutton-wrapper'];

        for(i in elems) {
            $(elems[i] + ' input[type="checkbox"], input[type="radio"]').uniform();

            $(elems[i] + ' select').selectize({
            //create: true,
            sortField: 'text'
            });

        }
    }

    /*
     * Display form listing from other form plugins.
     */

    jQuery('.select_choose_form').each(function() {
	jQuery(this).change(function() {
	    jQuery('.formPlugin_wrapper .formPluginFormList').hide();
	    jQuery('.formPluginFormList_loadingImg').show();

	    var buttonNumber = jQuery(this).attr('data-number');
	    var val = jQuery('.form_open-'+buttonNumber+' input[name="form_open['+buttonNumber+']"]:checked').val();

	    var deActivatedContactFormPlugins = jQuery('.deActivatedContactFormPlugins').val().split('_/_');
	    //var val = jQuery('input[name="'+name+'"]').val();

	    console.log(val, buttonNumber, deActivatedContactFormPlugins );

	    if( (val == 'yes') && (jQuery(this).val().length !== 0)  ) {

		if ( (jQuery.inArray( jQuery(this).val() , deActivatedContactFormPlugins) < 0) ) {
		    var nonce = jQuery('#wpfixedverticalfeedbackbutton').val();

		    var cbFvfbAdminFormIntegrationData = {};
		    cbFvfbAdminFormIntegrationData['buttonNumber'] = buttonNumber;
		    cbFvfbAdminFormIntegrationData['nonce'] = nonce;
		    cbFvfbAdminFormIntegrationData['outsideFormId'] = jQuery(this).val();

		    //console.log(cbFvfbAdminFormIntegrationData);

		    jQuery.ajax({
			type: 'POST',
			url: ajaxUrl,
			data: {
			    action: 'cbFvfbAdminFormIntegration',
			    cbFvfbAdminFormIntegrationData: cbFvfbAdminFormIntegrationData
			},
			success: function(data, textStatus, XMLHttpRequest){
			    //console.log(data);

			    if(data.length != 0) {

				var parsedData = checkJSON(data);
				if(parsedData === false) {
				    $('.formPlugin_wrapper .formPluginFormList').css({
					'margin-bottom': '10px'
				    }).empty().append(data);

				    //stylizeFields();
				    $('.formPlugin_wrapper .formPluginFormList select').selectize({ create: true, sortField: 'text' });
				} else {
				    if(parsedData.hasOwnProperty('errorMessage')) {
					$('.formPlugin_wrapper .formPluginFormList').css({
					    'margin-bottom': '10px',
					    'color': 'red'
					}).text(parsedData.errorMessage).show();

					jQuery('.formPluginFormList_loadingImg').hide();
				    } else {
					jQuery('.formPluginFormList_loadingImg').hide();
				    }
				}


				jQuery('.formPluginFormList_loadingImg').hide();
				$('.formPlugin_wrapper .formPluginFormList').show();
			    } else {
				$('.formPlugin_wrapper .formPluginFormList').css({
				    'margin-bottom': '10px',
				    'color': 'red'
				}).text('No form has been made.').show();

				jQuery('.formPluginFormList_loadingImg').hide();
			    }

			},
			error: function(MLHttpRequest, textStatus, errorThrown) {
			    alert(errorThrown);

			    jQuery('.formPluginFormList_loadingImg').hide();
			}
		    });
		} else {
		    $('.formPlugin_wrapper .formPluginFormList').css({
			'margin-bottom': '10px',
			'color': 'red'
		    }).html('Plugin is inactive. Please active and make at least one form there at first.').show();

		    jQuery('.formPluginFormList_loadingImg').hide();
		}
	    } else {
		$('.formPlugin_wrapper .formPluginFormList').css({
		    'margin-bottom': '10px',
		    'color': 'red'
		}).html('Please enable <a href="#form_open-'+buttonNumber+'">"Integrate form with button"</a> first.').show();

		jQuery('.formPluginFormList_loadingImg').hide();
	    }
	});
    });



    function checkJSON(string) {
        try {
            var json = jQuery.parseJSON(string);

            return json;
        }
        catch(e) {
            return false;
        }
    }




});
