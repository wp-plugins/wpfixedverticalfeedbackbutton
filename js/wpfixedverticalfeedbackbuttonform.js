

jQuery(document).ready(function($) {

    $("#fvfeedbackhandler0 h3").parent(".fvfeedbackhandler").toggleClass("fvfeedbackhandlert");
    $("#fvfeedbackhandler0 h3").next(".fvfeedbackbuttonprop").slideToggle();
    $(".fvfeedbackhandler  h3").click(function(){

        $(this).parent(".fvfeedbackhandler").toggleClass("fvfeedbackhandlert");
        $(this).next(".fvfeedbackbuttonprop").slideToggle();

    });

    $('.cbxfeedbackimage').click(function(e) {
        e.preventDefault();
        var $imagefiled = $(this);
        var image = wp.media({
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
            .on('select', function(e){
                // This will return the selected image from the Media Uploader, the result is an object
                var uploaded_image = image.state().get('selection').first();
                // We convert uploaded_image to a JSON object to make accessing it easier
                // Output to the console uploaded_image
                //console.log(uploaded_image);
                var image_url = uploaded_image.toJSON().url;
                // Let's assign the url value to the input field
                $imagefiled.val(image_url);
            });
    });

    $('.cbxfeedbackbuttontext').change(function() {
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

    $('.cbxfeedbackbuttontext').each(function() {

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


    //$('.for_custom_image').show();


    jQuery('.cb-fixedverticalbutton-wrapper input[type="radio"]').each(function () {
        if( jQuery(this).prop('checked') === true ) {
            jQuery(this).checked = true;
        }
    });

    /*
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
    */

    /*
     * Display form listing from other form plugins.
     * Showing supported forms
     */

    jQuery('.select_choose_form').each(function() {
        jQuery(this).change(function() {
            jQuery('.formPlugin_wrapper .formPluginFormList').hide();
            jQuery('.formPluginFormList_loadingImg').show();

            var buttonNumber = jQuery(this).attr('data-number');
            var val = jQuery('.form_open-'+buttonNumber+' input[name="form_open['+buttonNumber+']"]:checked').val();

            var deActivatedContactFormPlugins = jQuery('.deActivatedContactFormPlugins').val().split('_/_');

            if ( (jQuery.inArray( jQuery(this).val() , deActivatedContactFormPlugins) < 0) ) {
                var nonce = jQuery('#wpfixedverticalfeedbackbutton').val();

                var cbFvfbAdminFormIntegrationData = {};
                cbFvfbAdminFormIntegrationData['buttonNumber'] = buttonNumber;
                cbFvfbAdminFormIntegrationData['nonce'] = nonce;
                cbFvfbAdminFormIntegrationData['outsideFormId'] = jQuery(this).val();



                jQuery.ajax({
                type: 'POST',
                url: ajaxUrl,
                data: {
                    action: 'cbFvfbAdminFormIntegration',
                    cbFvfbAdminFormIntegrationData: cbFvfbAdminFormIntegrationData
                },
                success: function(data, textStatus, XMLHttpRequest){


                    if(data.length != 0) {

                    var parsedData = checkJSON(data);
                    if(parsedData === false) {
                        $('.formPlugin_wrapper .formPluginFormList').css({
                        'margin-bottom': '10px'
                        }).empty().append(data);

                        //stylizeFields();
                        //$('.formPlugin_wrapper .formPluginFormList select').selectize({ create: true, sortField: 'text' });
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
        });


        //adding color picker
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

    $( '.cbcolor' ).wpColorPicker();


});
