document.observe("dom:loaded", function (){

    AjaxPro.observe('addObservers', function() {

        setLocation = setLocation.wrap(function(callOriginal, url) {

            //variável do formulário do magento
            if(typeof productAddToCartForm  !== 'undefined' && !productAddToCartForm.validator.validate()) {
                return;
            }
            //

            //capturando valores configuráveis do produto, caso hajam
            var options;
            var optionValue = $$('select.product-custom-option').each(function(elem) {
                var elementId = elem.id.replace("select_", "");
                options = {'custom_option': elementId, 'custom_option_value_id' : elem.options[elem.options.selectedIndex].value};
            });

            //para página de catálogo e blocos de carrossel (que não possuem opções selecionáveis nos produtos)
            //na listagem dos produtos, é definido 2 inputs type hidden para armazenar os menores múltiplos
            productId = url.match("/product/(.*)/form_key/");
            if(productId != undefined) {
                productId = productId[1];
                if (productId != undefined && $('product_options_id_' + productId)) {
                    if ($('product_options_id_' + productId).getValue() != 'undefined') {
                        //options[$('product_presentation_' + productId).getValue()] = $('product_options_' + productId).getValue();

                        options = {'custom_option': $('product_options_id_' + productId).getValue(), 'custom_option_value_id' : $('product_option_value_id_' + productId).getValue()};
                    }
                }
            }

            console.log(options);

            if (-1 != url.search('checkout/cart/add')) {
                return AjaxPro.request({'url' : url, 'parameters' : options});
            }
            return callOriginal(url);
        });

    //    setPLocation = setPLocation.wrap(function(callOriginal, url, setFocus) {
    //        if (-1 == url.search('checkout/cart/add')) {
    //            return callOriginal(url, setFocus);
    //        }
    //        AjaxPro.request({'url' : url});
    //        if (window.opener) {
    //            window.opener.location.reload(false);
    //        }
    //    });

//         redeclare submit form function on product page
//         add product action checkout/cart/add
        var submit = function() {
            var url = this.form.action;
            if (!url && productAddToCartForm.action) { //click for price bug was fixed
                window.location = productAddToCartForm.action;
                return;
            }
            var isUploadFile = false;//$(this.form).select('input[type=file]').length;
            $(this.form).select('input[type=file]').each(function(el){
                if ('' !== el.getValue()) {
                    isUploadFile = true;
                }
            });
            if (isUploadFile) {
                if(this.validator && !this.validator.validate()){
                   return false;
                }
                var template = '<iframe id="product_addtocart_form_frame" name="product_addtocart_form_frame" style="width:0; height:0; border:0;display:none;"><\/iframe>';
                Element.insert($('product_addtocart_form'), {after: template});
                this.form.setAttribute('target', 'product_addtocart_form_frame');
                //add product
                this.form.submit();

                var interval = window.setInterval(function() {

                    var iframe = $('product_addtocart_form_frame');
                    if ('undefined' == typeof iframe) {
                        window.clearInterval(interval);
                        return;
                    }

                    var isAfterSubmit = false;
                    try {

                        if (iframe.contentDocument) {
                             isAfterSubmit = iframe.contentDocument.body.innerHTML.length > 1 ? true : false;
                        } else if (iframe.contentWindow) {
                             isAfterSubmit = iframe.contentWindow.document.body.innerHTML.length > 1 ? true : false;
                        } else if (iframe.document) {
                             isAfterSubmit = iframe.document.body.innerHTML.length > 1 ? true : false;
                        }

                    } catch (exception) {
                        console.log(exception);
                    }

                    if(!isAfterSubmit) {
                        return;
                    }
                    AjaxPro.request({
                        'url'      : url.replace('/product', ''),
                        parameters : {'onlyblocks': 1}
                    });
                    Element.remove(iframe);
                    window.clearInterval(interval);
                }, 1000);
                this.form.removeAttribute('target');
                return false;
            }

            if ($('pp_checkout_url') &&
        	(-1 !== $('pp_checkout_url').value.indexOf('paypaluk/express/start') 
                    || -1 !== $('pp_checkout_url').value.indexOf('paypal/express/start')) &&
        	'function' == typeof productAddToCartForm.oldSubmit) { // paypal exprerss button was clicked

        	productAddToCartForm.oldSubmit();
        	return false;
            }

            var params = this.form.serialize(true);
            if(this.validator && this.validator.validate()){
                AjaxPro.request({
                    'url'      : url,
                    parameters : params
                });
            }

            return false;
        };

        if (typeof productAddToCartFormOld != 'undefined') {

            productAddToCartForm.submit = submit.bind(productAddToCartFormOld);

        } else if(typeof productAddToCartForm != 'undefined') {

            productAddToCartForm.oldSubmit = productAddToCartForm.submit;
            productAddToCartForm.submit = submit;
            //
            if($('qty')){
                $('qty').observe('keypress', function(e){
                    if (13 === e.keyCode) {
                        Event.stop(e);
                        productAddToCartForm.submit();
                    }
                });
            }
        }

        if ($('pp_checkout_url')) {
            $('pp_checkout_url').value = '';
	}

//        redeclare submit form function on shopping cart page
//        update action checkout/cart/updatePost
        //var shoppingCartTable = $('shopping-cart-table');
        //if (shoppingCartTable) {
        var shoppingCartTableBind = function(shoppingCartTable) {

            var shoppingCartForm = shoppingCartTable.up('form');
            if(typeof shoppingCartForm != 'undefined'){

                //prototype  multiple submit bugfix
                //http://www.developpez.net/forums/d577369/webmasters-developpement-web/javascript/bibliotheques-frameworks/prototype-script-aculo-us/prototype-serialize-multiple-submit/
                shoppingCartForm.select(':submit').each(function(submitInput){
                    submitInput._submitted = false;
                    submitInput.onclick = function(){
                        submitInput._submitted = true;
                    }.bind(this);
                }.bind(this));

                shoppingCartForm.observe('submit', function(e) {
                    var el = Event.element(e);
                    el.stopObserving('submit');
                    Event.stop(e);
                    var params = el.serialize(true);

                    shoppingCartForm.select(':submit').each(function(_el){
                        if (_el._submitted === true){
                            params[_el.name] = _el.value;
                        }
                        _el._submitted = false;
                    }.bind(this));

                    var url = el.action;

                    AjaxPro.request({
                        'url'      : url,
                        parameters : params
                    });

                    return false;
                });
            }
        }
        //}

        if ($$('.shopping-cart-table'))
        {
            $$('.shopping-cart-table').each(function(shoppingCartTable) {
                shoppingCartTableBind(shoppingCartTable);
            })
        }
        else if ($('shopping-cart-table'))
        {
            var shoppingCartTable = $('shopping-cart-table');
            shoppingCartTableBind(shoppingCartTable);
        }

        $$('a').each(function(element){
            var url = element.getAttribute('href');

            if (url == '#') {
                var onclickHandler = element.getAttribute('onclick');
                if (
                    onclickHandler
                    && typeof onclickHandler == 'string'
                    && onclickHandler != ''
                    && onclickHandler.search('setLocation') != -1
                    && onclickHandler.search('checkout/cart/add') != -1
                ) {

                    element.stopObserving('click');
                    element.observe('click', function(e) {
                        Event.stop(e);
                    });
                }
            }
            if (url && url.search('checkout/cart/delete') != -1) {
                element.stopObserving('click');
                element.setAttribute('onclick', '');
                element.observe('click', function(e) {
                    AjaxPro.fire('click:stop', e);
                    Event.stop(e);
                    if (AjaxPro.config.get('checkoutCart/enabledDeleteConfirm')) {
                        if(!confirm(Translator.translate('Are you sure you would like to remove this item from the shopping cart?'))) {
                            return false;
                        }
                    }
                    AjaxPro.request({
                        'url' : url,
                        'parameters': {'methodToExecute':'bindGiftResources()'}
                    });
                    return false;
                });
                return false;
            }
        });

        var discountCartForm = $('discount-coupon-form');
        if(discountCartForm){
//            discountForm.oldSubmit = discountForm.submit;
            discountForm.submit = function(isRemove){
                var url = this.form.action;
                if (isRemove) {
                    $('coupon_code').removeClassName('required-entry');
                    $('remove-coupone').value = "1";
                } else {
                    $('coupon_code').addClassName('required-entry');
                    $('remove-coupone').value = "0";
                }
                var params = this.form.serialize(true);
                if(this.validator && this.validator.validate()){
                    AjaxPro.request({
                        'url'      : url,
                        parameters : params
                    });
                }
            }
        }

        var shippingZipForm = $('shipping-zip-form');
        if(shippingZipForm){
//            coShippingMethodForm.oldSubmit = coShippingMethodForm.submit;
            coShippingMethodForm.submit = function(){
                var country = $F('country');
                var optionalZip = false;
                for (i=0; i < countriesWithOptionalZip.length; i++) {
                    if (countriesWithOptionalZip[i] == country) {
                        optionalZip = true;
                    }
                }
                if (optionalZip) {
                    $('postcode').removeClassName('required-entry');
                }
                else {
                    $('postcode').addClassName('required-entry');
                }

                var url = this.form.action, params = this.form.serialize(true);
                if(this.validator && this.validator.validate()){
                    AjaxPro.request({
                        'url'      : url,
                        parameters : params
                    });
                }
            }
        }

        var elCoShippingMethodForm = $('co-shipping-method-form');
        if(elCoShippingMethodForm){
            //prototype  multiple submit bugfix
            //http://www.developpez.net/forums/d577369/webmasters-developpement-web/javascript/bibliotheques-frameworks/prototype-script-aculo-us/prototype-serialize-multiple-submit/
            elCoShippingMethodForm.select(':submit').each(function(submitInput){
                submitInput._submitted = false;
                submitInput.onclick = function(){
                    submitInput._submitted = true;
                }.bind(this);
            }.bind(this));

            elCoShippingMethodForm.observe('submit', function(e) {
                var el = Event.element(e);
                el.stopObserving('submit');
                Event.stop(e);
                var params = el.serialize(true);

                elCoShippingMethodForm.select(':submit').each(function(_el){
                    if (_el._submitted === true){
                        params[_el.name] = _el.value;
                    }
                    _el._submitted = false;
                }.bind(this));

                var url = el.action;

                AjaxPro.request({
                    'url'      : url,
                    parameters : params
                });

                return false;
            });
        }
    });

    AjaxPro.observe('onLoading:checkout', function() {
        AjaxPro.opacity
            .setSelector('.block-cart')
            .show(0.5)
        ;
    });

    AjaxPro.observe('onFailure:checkout', function() {
        AjaxPro.opacity
            .setSelector('.block-cart')
            .hide()
        ;
    });

    AjaxPro.observe('onComplete:checkout', function() {
        if (typeof truncateOptions === 'function') {
            truncateOptions();
        }
        if (AjaxPro.message.visible() 
            && '0' == AjaxPro.config.get('checkoutCart/enabledForm')) { // configurable product window showed
            
            AjaxPro.message.hide();
        }
    });

});