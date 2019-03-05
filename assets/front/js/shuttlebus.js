

jQuery(document).ready(function($) {

    function searchShuttles(){

        $.ajax({

            url : shuttle_params.ajax_url,

            dataType: 'json',

            type : 'post',

            data : {

                action : 'check_available',

                from : localStorage.getItem('from'),

                to : localStorage.getItem('to'),

                date: localStorage.getItem('curdate')

            },

            beforeSend:function(){

                $('.lio').show();

            },

            success : function( response ) {



                if(response.success==true && response.shuttles)

                {

                    $('#form_travelers').val();

                    $('#form_price').val();

                    $('#form_route').val();

                    $('#form_from').val();

                    $('#form_to').val();

                    $('.list-group').empty();

                    for(var s = 0;s<response.shuttles.length;s++)

                    {

                        var $route = response.shuttles[s];

                        console.log($route);

                        var $select='<select class="selectbox" id="select_'+$route.rid+'" data-price="'+$route.price+'" data-route="'+$route.rid+'">';

                        var $tprice = parseFloat($route.min * $route.price).toFixed(2);

                        var mindef = $route.min_defined;


                        for(var i=Number($route.min);i<=Number($route.travelers);i++)

                        {
                            var min_pas = shuttle_bus_vars.min_pax;
                            min_pas = min_pas.replace('_pas_',$route.min);
                            var $mindef = mindef==true?min_pas:i;
                            $select+="<option value='"+i+"'>"+$mindef+"</option>";
                            mindef=false;
                        }

                        $select+='</select>';

                        var $div='<div class="row results_card mco_out">\
                            <div class="col-xs-6 col-md-7">\
                        <section class="col-xs-12 col-md-6 pho_information">\
                        <h4>'+$route.rnm+'</h4>\
                        <p class="pho-location" style="font-size: 12px !important;"><i class="fa fa-map-marker"></i><!-- react-text: 1865 -->'+$route.start_point+'-'+$route.end_point+'<!-- /react-text --></p>\
                    <p class="pho-location"><i class="fa fa-map-marker"></i><!-- react-text: 1865 -->'+$route.hrs+'<!-- /react-text --></p>\
                    </section>\
                    <section class="col-xs-12 col-md-6 filters">\
                        '+$select+'\
                        </section>\
                        </div>\
                        <div class="col-xs-6 col-md-5">\
                        <section class="col-xs-12 col-md-6 price_container">\
                        <div>\
                        <p id="tprice_'+$route.rid+'" class="price"><!-- react-text: 1872 -->&euro;<!-- /react-text --><!-- react-text: 1873 -->'+$tprice+'<!-- /react-text --></p>\
                    </div>\
                    </section>\
                    <section class="col-xs-12 col-md-6 continue_button">\
                        <a id="reserve_'+$route.rid+'" data-price="'+$route.price+'" data-hrs="'+$route.hrs+'" data-route="'+$route.rid+'" data-pickup="'+$route.pickup+'" class="reservebox btn btn-primary__orange">Reserve ›</a>\
                    </section>\
                    </div>\
                    </div>';
                        $('.list-group').append($div);
                    }
                    $('#booking-passengers').show();
                    $('.lio').hide();
                }
                else{
                    $('.list-group').empty();
                    $('#booking-passengers').hide();
                    $('#start').popover({
                        'popoverShadow':"true",
                        'popoverPosition':"top",
                        'popoverText':response.msg,
                        'popoverBackground':"bg-red",
                        'popoverColor':"fg-white"
                    });
                    $('#start').popover('show');
                    $('.lio').hide();
                    /*$.Notify({

                        caption: 'Shuttle',

                        content: response.msg,

                        type: 'failure'

                    });*/
                }
            }
        });
    }
    $('#first_step').on('click',function(e){
        $('#user_info').hide();
        $('.bpb').hide();
        $('#booking-first').show();
        $('#booking-passengers').show();
    });
    $('.list-group').on('change','.selectbox',function(){
        var $travelers = $(this).val();
        var $rid = $(this).attr('data-route');
        var $price = $(this).attr('data-price');
        var $result = parseFloat($travelers * $price).toFixed(2);
        $('#tprice_'+$rid).text('€'+$result);
    });
    $('.list-group').on('click','.reservebox',function(){
        var $rid = $(this).attr('data-route');
        var $price = $(this).attr('data-price');
        var $hrs = $(this).attr('data-hrs');
        $('#form_travelers').val($('#select_'+$rid).val());
        $('#form_price').val($price);
        $('#form_route').val($rid);
        $('#form_hrs').val($hrs);
        $('#form_from').val(localStorage.getItem('from'));
        $('#form_to').val(localStorage.getItem('to'));
        $('#form_date').val($('#datetimepicker1').val());
        if($(this).attr('data-pickup')!='undefined' && $(this).attr('data-pickup')!='')
        {
            $('#pick_up').val($(this).attr('data-pickup'));
        }
        $('.bpb').show();
        $('#booking-first').hide();
        $('#booking-passengers').hide();
        $('#user_info').show();
    });

    $('#start').on('click',function(){

        var $action = $(this).attr('action');
        if($action=='start')
        {
            if($('#select_to').val()!=-1 && $('#select_from').val()!=-1)
            {
                $('#booking-datetime').show();
            }
            else{
                $(this).popover({
                    'popoverShadow':"true",
                    'popoverPosition':"top",
                    'popoverText':shuttle_bus_vars.location,
                    'popoverBackground':"bg-red",
                    'popoverColor':"fg-white"
                });
                $(this).popover('show');
            }
        }
        else
        {
            localStorage.setItem('curdate',$('#datetimepicker1').val());
            if($('#select_to').val()!=-1 && $('#select_from').val()!=-1)
            {
                searchShuttles();
            }
            else{
                $(this).popover({
                    'popoverShadow':"true",
                    'popoverPosition':"top",
                    'popoverText':shuttle_bus_vars.location,
                    'popoverBackground':"bg-red",
                    'popoverColor':"fg-white"
                });
                $(this).popover('show');
            }
        }
    });
    $('#booking-passengers').hide();
    function log (evt) {
        if (!evt) {
            var args = "{}";
        } else {
            var args = JSON.stringify(evt.params, function (key, value) {
                if (value && value.nodeName) return "[DOM node]";
                if (value instanceof $.Event) return "[$.Event]";
                return value;
            });
        }
        return JSON.parse(args).data.id;
    };
    function getStops($stop,$elem)
    {
        $.ajax({
            url : shuttle_params.ajax_url,
            dataType: 'json',
            type : 'post',
            data : {
                action : 'get_stops',
                stop: $stop,
                origin: $elem
            },
            beforeSend: function () {
                $('.lio').show();
            },
            success: function(response){
                $('#'+$elem).empty();
                $('#'+$elem).append(response.html);
                $('.lio').hide();
            }
        });
    }
    $("#select_from").select2().on('select2:select', function (evt) {
        var $data = log(evt);

        var $stop = $data;

        localStorage.setItem('from',$stop.split('_')[0]);

        if($('#select_to').val()!=-1 && $data!=-1)

        {
            $('#booking-datetime').show();

            $('#start').attr('action','search');

            $('#start').text('Search');

        }

        if($('#select_to').val()==-1)

        {

            $('#select_to').select2("val", "-1");

            getStops($stop,'select_to');

        }

        if($('#select_to').val()==$('#select_from').val())

        {

            $('#select_to').select2("val", "-1");

            getStops($stop,'select_to');

        }

    });

    $("#select_to").select2().on('select2:select', function (evt) {

        var $data = log(evt);

        var $stop = $data;

        localStorage.setItem('to',$stop.split('_')[0]);

        if($('#select_from').val()!=-1 && $data!=-1)

        {

            $('#booking-datetime').show();

            $('#start').attr('action','search');

            $('#start').text('Search');

        }

        if($('#select_from').val()==-1)

        {

            $('#select_from').select2("val", "-1");

            getStops($stop,'select_from');

        }

        var $stt = $stop.split('_')[0];

        var $frm = $('#select_from').val();

        if($stt==$frm)

        {

            $('#select_from').select2("val", "-1");

            getStops($stop,'select_from');

        }

    });

    $("#datepicker").datepicker({

        format:"dd/mm/yyyy",

        locale:'es',

        minDate: true,

        onSelect:function(d,d0){

            localStorage.setItem('curdate',d);

            if($('#select_from').val()!=-1 && $('#select_to').val()!=-1 && d!=null)

            {

                //$('#search_availables').show();

                $('#start').attr('action','search');

                $('#start').text('Search');

            }

        }

    });

    /*function rebuild(elem,route,stop)

    {

        var matches=[];

        $('#'+elem).typeahead('destroy');

        $.each(routes, function(i, r) {

            if (r.route==route && r.id!=stop) {

                matches.push(r);

            }

        });

        var routeStops = new Bloodhound({

            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('address'),

            queryTokenizer: Bloodhound.tokenizers.whitespace,

            local: matches

        });

        $('#'+elem).typeahead(null,

            {

                name: 'route-stops',

                display: 'address',

                source: routeStops

            });

        $('#'+elem).on('typeahead:selected', function (e, datum) {

            localStorage.setItem(elem,datum.id);

            localStorage.setItem('route',datum.route);

            var route = datum.route;

            var stop = datum.id;

            elem=='to'? rebuild('from',route,stop):rebuild('to',route,stop);

        })

    }

    var fromRouteStops = new Bloodhound({

        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('address'),

        queryTokenizer: Bloodhound.tokenizers.whitespace,

        local: routes

    });

    $('#from').typeahead(null,

        {

            name: 'from-route-stops',

            display: 'address',

            source: fromRouteStops

        });

    $('#from').on('typeahead:selected', function (e, datum) {

        localStorage.setItem('from',datum.id);

        localStorage.setItem('route',datum.route);

        rebuild('to',datum.route,datum.id);

    });

    var routeStops = new Bloodhound({

        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('address'),

        queryTokenizer: Bloodhound.tokenizers.whitespace,

        local: routes

    });

    $('#to').typeahead(null,

        {

            name: 'route-stops',

            display: 'address',

            source: routeStops

        });

    $('#to').on('typeahead:selected', function (e, datum) {

        localStorage.setItem('to',datum.id);

        localStorage.setItem('route',datum.route);

        rebuild('from',datum.route,datum.id);

    })

    //var addressPicker = new AddressPicker({autocompleteService: {types: ['(cities)'], componentRestrictions: {country: 'ES'}}});





    /*$('#from').typeahead(null, {

        displayKey: 'description',

        source: addressPicker.ttAdapter()

    });

    addressPicker.bindDefaultTypeaheadEvent($('#from'));

    $(addressPicker).on('addresspicker:selected', function (event, result) {

        $('#from').attr('lat',result.lat());

        $('#from').attr('lng',result.lng());

        $('#from').attr('address',result.address());

    })



    var _addressPicker = new AddressPicker({autocompleteService: {types: ['(cities)'], componentRestrictions: {country: 'ES'}}});



    $('#to').typeahead(null, {

        displayKey: 'description',

        source: _addressPicker.ttAdapter()

    });

    _addressPicker.bindDefaultTypeaheadEvent($('#to'));

    $(_addressPicker).on('addresspicker:selected', function (event, result) {

        $('#to').attr('lat',result.lat());

        $('#to').attr('lng',result.lng());

        $('#to').attr('address',result.address());

    });*/

    jQuery.validator.addMethod("phone", function (value, element) {

        return this.optional(element) || value.length >= 9 && value.length < 20;

    }, shuttle_bus_vars.invalid_phone);

    jQuery.validator.addMethod("number", function (value, element) {

        return this.optional(element) || /^\d{4}\-\d{4}-\d{4}-\d{4}( x\d{1,6})?$/.test(value);

    }, shuttle_bus_vars.invalid_card);



    jQuery.validator.addMethod("expiration_date", function (value, element) {

        var date = new Date();

        var since = date.getMonth();

        var since_year = date.getFullYear();

        since_year = since_year.slice(-2);

        var month = value.split('/')[0];

        var year = value.split('/')[1];

        if(year==since_year)

        {return this.optional(element) || month > since && /^(0[123456789]|10|11|12)([/])(\d{2})?$/.test(value);}

        else

        {return this.optional(element) || year > since_year && /^(0[123456789]|10|11|12)([/])(\d{2})?$/.test(value);}

    }, shuttle_bus_vars.invalid_card_exp);



    jQuery.validator.addMethod("cvc", function (value, element) {

        return this.optional(element) || /^\d{3}( x\d{1,6})?$/.test(value);

    }, shuttle_bus_vars.invalid_card_cvv);



    jQuery.validator.addMethod('titular', function (value,element) {

        return this.optional(element) || /^[A-Za-zÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ ]+$/.test(value);

    }, shuttle_bus_vars.invalid_val);

    jQuery.validator.addMethod('creditcard', function (value,element) {
        var isValid = false;
        jQuery('#sb_cardnumber').validateCreditCard(function(result) {
            if(result.valid){
                isValid = true;
            }
        });
        return isValid;
    }, shuttle_bus_vars.invalid_val);

    jQuery.validator.addMethod("mail", function (value, element) {
        return this.optional(element) || /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/.test(value);
    }, shuttle_bus_vars.invalid_email);
    jQuery.validator.addMethod("Dni", function(value, element) {
        if(/^([0-9]{8})*[a-zA-Z]+$/.test(value)){
            var numero = value.substr(0,value.length-1);
            var let = value.substr(value.length-1,1).toUpperCase();
            numero = numero % 23;
            var letra='TRWAGMYFPDXBNJZSQVHLCKET';
            letra = letra.substring(numero,numero+1);
            if (letra==let) return true;
            return false;
        }
        return this.optional(element);
    }, shuttle_bus_vars.invalid_dni);
    jQuery('#sbFormBook').validate({
        rules: {
            sb_email: {
                required: true,
                mail:true
            },
            sb_name: {
                required: true
            },
            sb_lastname: {
                required: true
            },
            sb_phone: {
                required: true,
                phone: 'required'
            }
            /*sb_card: {

                required: true,

                creditcard: true



            },

            sb_cardholder: {

                required: true,

                titular:'required'



            },

            sb_cvc: {

             required: true,

             cvc: 'required'

             },

            sb_expdate:{

                required: true

                //expiration_date: 'required'

            }*/
        },
        messages: {
            email: {
                required: shuttle_bus_vars.invalid_email,
                email: shuttle_bus_vars.invalid_email
            },
            name: {
                required: shuttle_bus_vars.blank_name
            },
            Apellidos: {
                required: shuttle_bus_vars.blank_lnm
            },
            phone: {
                required: shuttle_bus_vars.blank_phone
            }
            /*,

            card: {

                required: "Por favor, Debe introducir el número de su tarjeta."

            },

            cvc: {

             required: "Por favor, Debe introducir el cvc de su tarjeta."

             },

            titular:{

                required: "Por favor, Debe introducir el titular de la tarjeta."

            }*/
        },
        submitHandler: function (form) {
            form.submit();
        }
    });
    /*$('#start').click(function(e){
    });*/
    $('#confirm').click(function(e){
        e.preventDefault();
        if($('#sbFormBook').valid())
        {
            //var $date = $('#datetimepicker1').val();
            var $hour = $('#hours').val();
            var $mins = $('#minutes').val();
            //var totalpassengers = $('#totalpassengers').val();
            //$('#form_from').val(localStorage.getItem('from'));
            //$('#form_to').val(localStorage.getItem('to'));
            //$('#form_route').val(localStorage.getItem('route'));
            //$('#form_total').val(totalpassengers);
            //$('#form_date').val($date);
            $('#sbFormBook').submit();
            /*$.ajax({
                url : shuttle_params.ajax_url,
                type : 'post',
                data : {
                    action : 'confirm_shuttle',
                    data: $('#sbFormBook').serialize()
                },
                beforeSend:function(){
                    $('.lio').show();
                },
                success : function( response ) {
                    if(response.success==true)
                    {
                        $('#tshuttles tbody').empty();
                        $('#booking-passengers').hide();
                        $('.lio').hide();
                        window.location = '/';
                    }
                    else{
                        $('.lio').hide();
                        $.Notify({
                            caption: 'Shuttle',
                            content: 'Ha ocurrido un error en los datos.',
                            type: 'failure'
                        });
                    }
                }
            });*/
        }
    });
    $('#search_cars').click(function(e){
        $.ajax({
            url : shuttle_params.ajax_url,
            type : 'post',
            data : {
                action : 'reserve_route',
                total : totalpassengers,
                from: localStorage.getItem('from'),
                to: localStorage.getItem('to'),
                route: localStorage.getItem('route'),
                date: {'date':$date,'hour':$hour,'mins':$mins},
                client: {'nm':$client_name,'lnm':$client_lastname,'email':$email,'phone':$phone}
            },
            beforeSend:function(){},
            success : function( response ) {}
        });
    });
});