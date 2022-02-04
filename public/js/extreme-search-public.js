(function( $ ) {
	'use strict';

    if(es_obj.es_main == 1){

    	let es_selector = es_obj.es_selector;
    	es_selector.trim();

        if ($(es_selector).length == 0){
            es_selector = 'input[name="s"]:not(#adminbar-search)';
        }

        es_selector = es_selector+':visible:first';

    	let es_popup = (es_obj.es_popup == 'yes') ? true : false;

    	if(es_popup){
    		$(es_selector).addClass('es_popup_input');
    		$(es_selector).wrap( '<div class="es_popup_wrap"></div>' );
    		$(es_selector).after('<span class="es_close">&#215;</span>');

    		$(es_selector).on('focus', function() {
    		  $(es_selector).parent('.es_popup_wrap').addClass('active');
    		  $('body').addClass('es_popup_active');
    		});

    		$('.es_popup_wrap').on('click touchstart','.es_close', function() {
    		  $('.es_popup_wrap').removeClass('active');
    		});
    	}

        var retrievedData = localStorage.getItem("es_terms");

        if (retrievedData !== '' && retrievedData !== undefined && retrievedData !== null && retrievedData.length > 0) {
            var data = JSON.parse(retrievedData);
            $.post(es_obj.es_admin_ajax_url, data, function(response) {
                // console.log('updated');
            });
            localStorage.setItem("es_terms", '');
        }



    	$(es_selector).devbridgeAutocomplete({
    	    	serviceUrl: es_obj.es_ajax_url,
                /*noCache:true,*/
                maxHeight: 850,
                autoSelectFirst:false,
                onSearchStart:function (query) {
                    query.query = query.query.replace("?", '"');
                    query.query = query.query.replace("\u2033", '"');
                    return query;
                },
                formatResult: function (item, currentValue) {

                    if(item.type === "c"){
                        var html = "<div><div class='es-inr'><div class='itm-meta'><div class='es_cat_wrap'><strong>" + item.value + " </strong><small> "+es_obj.es_trans_cat_desc+"</small></div></div></div></div>";
                    }else{

                        var html = "<div><div class='es-inr'>";

                        if( $.inArray("thumb", es_obj.es_display_field) !== -1 ) {
                            html += "<div class='itm-img'><img src='" + (item.data.icon ? item.data.icon : es_obj.es_woo_placeholder) + "'/></div>";
                        }

                        html += "<div class='itm-meta'><div class='es_title'><strong class='ttl'>" + item.value + "</strong>";

                        if( $.inArray("price", es_obj.es_display_field) !== -1 ) {
                             html += "<strong class='price'>" + es_obj.es_currency_symbol + parseFloat(item.data.price).toFixed(2) + "</strong>";
                        }

                        html += "</div>";

                        html += "<div class='es_prio_meta'>";
                        
                        if( $.inArray("sku", es_obj.es_display_field) !== -1 && item.data.fields.sku != '') {
                             html += "<span class='sml'>" + es_obj.es_trans_sku + " : " + item.data.fields.sku + "</span>";
                        }
                        html += "</div>";
                        html += "<div class='es_t_meta'>";
                        if( $.inArray("cat", es_obj.es_display_field) !== -1 && item.data.fields.cat != '') {
                             html += "<span class='sml'>" + es_obj.es_trans_cat + " : " + item.data.fields.cat + "</span> &nbsp; ";
                        }

                        $.each(es_obj.es_display_terms, function(index, s_terms) {
                            if(item.data.attr[s_terms] != ''){
                                html += "<span class='sml'><span class='captl'>" + s_terms + "</span> : " + item.data.attr[s_terms] + "</span> &nbsp; ";
                            }
                        });
                        html += "</div>";
                        
                        html += "</div></div></div>";
                    }
                    return html;
                },
                triggerSelectOnValidInput : true,
                autoFocus: false,
                minChars: es_obj.es_min_char,
                onSelect: function (suggestion) {

                    localStorage.setItem("es_terms", JSON.stringify({"type": suggestion.type, "id": suggestion.id, "action": "es_update_term"}));

                    if(es_obj.es_redirect == 'yes'){
                        $(es_selector).val('');
                        window.open(suggestion.data.link);
                    }else{
                        location.href = suggestion.data.link;
                    }
                }
    	});

    }

})( jQuery );
