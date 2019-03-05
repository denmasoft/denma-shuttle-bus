jQuery(document).ready(function ($) {
    $('.summernote').summernote();
    $('.contact').summernote();
    $('.body_text').summernote();
    $('#save_conf').on('click', function (e) {
        e.preventDefault();
        var $header = $('.summernote').code();
        var $contact = $('.contact').code();
        var $body_text = $('.body_text').code();
        $('#headertext').text($header);
        $('#contacttext').text($contact);
        $('#bodytext').text($body_text);
        $('#aheadForm').submit();
    });
    jQuery.fn.insertAt = function (index, element) {
        var lastIndex = this.children().size()
        if (index < 0) {
            index = Math.max(0, lastIndex + 1 + index)
        }
        this.append(element)
        if (index < lastIndex) {
            this.children().eq(index).before(this.children().last())
        }
        return this;
    };
    $.fn.getCaret = function () {
        var ctrl = this[0];
        var CaretPos = 0;
        if (document.selection) {
            ctrl.focus();
            var Sel = document.selection.createRange();
            Sel.moveStart('character', -ctrl.value.length);
            CaretPos = Sel.text.length;
        } else if (ctrl.selectionStart || ctrl.selectionStart == '0') {
            CaretPos = ctrl.selectionStart;
        }
        return (CaretPos);
    };
    $.fn.priceField = function () {
        $(this).keydown(function (e) {
            var val = $(this).val();
            var code = (e.keyCode ? e.keyCode : e.which);
            var nums = ((code >= 96) && (code <= 105)) || ((code >= 48) && (code <= 57));
            var backspace = (code == 8);
            var specialkey = (e.metaKey || e.altKey || e.shiftKey);
            var arrowkey = ((code >= 37) && (code <= 40));
            var Fkey = ((code >= 112) && (code <= 123));
            var decimal = ((code == 110 || code == 190) && val.indexOf('.') == -1);
            var misckey = (code == 9) || (code == 144) || (code == 145) || (code == 45) || (code == 46) || (code == 33) || (code == 34) || (code == 35) || (code == 36) || (code == 19) || (code == 20) || (code == 92) || (code == 93) || (code == 27);
            var properKey = (nums || decimal || backspace || specialkey || arrowkey || Fkey || misckey);
            var properFormatting = backspace || specialkey || arrowkey || Fkey || misckey || ((val.indexOf('.') == -1) || (val.length - val.indexOf('.') < 3) || ($(this).getCaret() < val.length - 2));
            if (!(properKey && properFormatting)) {
                return false;
            }
        });
        $(this).blur(function () {
            var val = $(this).val();
            if (val === '') {
                $(this).val('0.00');
            } else if (val.indexOf('.') == -1) {
                $(this).val(val + '.00');
            } else if (val.length - val.indexOf('.') == 1) {
                $(this).val(val + '00');
            } else if (val.length - val.indexOf('.') == 2) {
                $(this).val(val + '0');
            }
        });
        return $(this);
    };
    $("#driver").select2();
    $('#user').dataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {"sFirst": "Primero", "sLast": "Último", "sNext": "Siguiente", "sPrevious": "Anterior"},
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
    $('#taxi').dataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {"sFirst": "Primero", "sLast": "Último", "sNext": "Siguiente", "sPrevious": "Anterior"},
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
    $('#routes').dataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {"sFirst": "Primero", "sLast": "Último", "sNext": "Siguiente", "sPrevious": "Anterior"},
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
    $.validator.addMethod("phone", function (value, element) {
        return this.optional(element) || /^((\+?34([ \t|\-])?)?[9|6|7]((\d{1}([ \t|\-])?[0-9]{3})|(\d{2}([ \t|\-])?[0-9]{2}))([ \t|\-])?[0-9]{2}([ \t|\-])?[0-9]{2})$/.test(value);
    }, "Por favor, introduza un tel&eacute;fono v&aacute;lido.");
    $.validator.addMethod("mail", function (value, element) {
        return this.optional(element) || /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/.test(value);
    }, "Por favor, introduza un correo electr&oacute;nico v&aacute;lido.");
    $.validator.addMethod("Dni", function (value, element) {
        if (/^([0-9]{8})*[a-zA-Z]+$/.test(value)) {
            var numero = value.substr(0, value.length - 1);
            var let = value.substr(value.length - 1, 1).toUpperCase();
            numero = numero % 23;
            var letra = 'TRWAGMYFPDXBNJZSQVHLCKET';
            letra = letra.substring(numero, numero + 1);
            if (letra == let) return true;
            return false;
        }
        return this.optional(element);
    }, "Por favor, introduza un DNI v&aacute;lido.");
    $("#tdForm").validate({
        rules: {
            name: {required: true},
            last_name: {required: true},
            dni: {required: true, Dni: true},
            email: {required: true, mail: true, email: true},
            tel: {required: true, phone: 'required'},
            cell: {required: true, phone: 'required'}
        },
        messages: {
            name: {required: "Por favor, introduzca el nombre del taxista."},
            last_name: {required: "Por favor, introduzca los apellidos del taxista."},
            dni: {
                required: "Por favor, especifique el NIF/NIE del taxista.",
                Dni: "Por favor, introduza un NIF/NIE v&aacute;lido."
            },
            email: {
                required: "Por favor, introduza un correo electr&oacute;nico v&aacute;lido.",
                mail: "Por favor, introduza un correo electr&oacute;nico v&aacute;lido.",
                email: "Por favor, introduza un correo electr&oacute;nico v&aacute;lido."
            },
            tel: {required: "Por favor, especifique el tel&eacute;fono fijo del taxista."},
            cell: {required: "Por favor, especifique el tel&eacute;fono m&oacute;vil del taxista."}
        },
        submitHandler: function (form) {
            form.submit();
        }
    });
    $('#register_driver').click(function (e) {
        e.preventDefault();
        if ($('#tdForm').valid()) {
            $('#tdForm').submit();
        }
    });
    $('#register_taxi').click(function (e) {
        e.preventDefault();
        $('#taxiForm').submit();
    });
    /*$('#stop_container').on('click','.input_price',function(){        $(this).priceField();    });    /*function loaded(selector, callback) {        $(function () {            callback($(selector));        });        var parentSelector = "* > " + selector;        $(document).on('DOMNodeInserted', parentSelector, function (e) {            callback($(this).find(selector));        });    }*/
    $('.stopc').sortable({
        update: function (event, ui) {
            console.log(event.type);
            var list = $(".stopc").sortable("toArray");
            for (var l = 0; l <= list.length; l++) {
                $('div#' + list[l]).attr('data-pos', l);
                $('#posix_' + list[l]).attr('data-pos', l);
                $('#posix_' + list[l]).val(l);
            }
        }
    });
    function appendFunc(elem, elem2) {
        $(elem).priceField();
        var routeStops = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('address'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            local: shuttle_bus_vars.routes
        });
        $(elem2).typeahead(null, {name: 'route-stops', display: 'address', source: routeStops});
        $(elem2).on('typeahead:selected', function (e, datum) {
            var dcount = $(elem2).attr('data-count');
            $('#hstop_' + dcount).val(datum.sid);
        });
        $(elem2).on('keypress', '.input_route', function () {
            var dcount = $(elem2).attr('data-count');
            $('#hstop_' + dcount).val(-1);
        });
    }

    /*$('#stop_container').bind('DOMNodeInserted', function(e) {        var element = e.target;        $('.input_price',element).priceField();        var routeStops = new Bloodhound({            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('address'),            queryTokenizer: Bloodhound.tokenizers.whitespace,            local: shuttle_bus_vars.routes        });        $('.input_route',element).typeahead(null,            {                name: 'route-stops',                display: 'address',                source: routeStops            });        $('.input_route',element).on('typeahead:selected', function (e, datum) {            var dcount = $('.input_route',element).attr('data-count');            $('#hstop_'+dcount).val(datum.sid);        });        $(element).on('keypress','.input_route',function(){            var dcount = $('.input_route',element).attr('data-count');            $('#hstop_'+dcount).val(-1);        });*/
    /*if(shuttle_bus_vars.routes!='')$('.input_route',element).append(shuttle_bus_vars.routes);        $('.input_route',element).editableSelect().change(function(){            var dcount = $(this).attr('data-count');            var $id = $(this).attr('id');            $id = $id.split('_');            $id = $id[1];            $('#price_'+$id).val($(this).attr('data-price'));            var $time = $(this).attr('data-hours');            $time = $time.split(':');            $('#hours_'+$id).val($time[0]);            $('#minutes_'+$id).val($time[1]);            var dsid = -1;            if($(this).val()==$(this).attr('data-info'))dsid = $(this).attr('data-sid');            $('#hstop_'+dcount).val(dsid);        });        $(element).on('keyup','.input_route',function(){            var dcount = $(this).attr('data-count');            $('#hstop_'+dcount).val(-1);        });*/    //});
    if (update == true) {
        $('.input_price').priceField();
        /*if(shuttle_bus_vars.routes!='')$('.input_route').append(shuttle_bus_vars.routes);        $('.input_route').editableSelect().change(function(){            var dcount = $(this).attr('data-count');            var $id = $(this).attr('id');            $id = $id.split('_');            $id = $id[1];            $('#price_'+$id).val($(this).attr('data-price'));            var $time = $(this).attr('data-hours');            $time = $time.split(':');            $('#hours_'+$id).val($time[0]);            $('#minutes_'+$id).val($time[1]);            var dsid = -1;            if($(this).val()==$(this).attr('data-info'))dsid = $(this).attr('data-sid');            $('#hstop_'+dcount).val(dsid);        });        $('.input_route').on('keyup',function(){            var dcount = $(this).attr('data-count');            $('#hstop_'+dcount).val(-1);        });*/
        var routeStops = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('address'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            local: shuttle_bus_vars.routes
        });
        $('.input_route').typeahead(null, {name: 'route-stops', display: 'address', source: routeStops});
        $('.input_route').on('typeahead:selected', function (e, datum) {
            var dcount = $('.input_route').attr('data-count');
            $('#hstop_' + dcount).val(datum.sid);
        });
    }
    /*if(shuttle_bus_vars.routes!='')$('.input_route').append(shuttle_bus_vars.routes);    $('.input_route').editableSelect().change(function(){        var $id = $(this).attr('id');        $id = $id.split('_');        $id = $id[1];        $('#price_'+$id).val($(this).attr('data-price'));        var $time = $(this).attr('data-hours');        $time = $time.split(':');        $('#hours_'+$id).val($time[0]);        $('#minutes_'+$id).val($time[1]);    });*/
    /*loaded('.input_route',function(elem){        if(shuttle_bus_vars.routes!='')$(elem).append(shuttle_bus_vars.routes);        $(elem).editableSelect().change(function(){            var $id = $(elem).attr('id');            $id = $id.split('_');            $id = $id[1];            $('#price_'+$id).val($(elem).attr('data-price'));            var $time = $(elem).attr('data-hours');            $time = $time.split(':');            $('#hours_'+$id).val($time[0]);            $('#minutes_'+$id).val($time[1]);        });    });*/
    /*$('body').on('DOMNodeInserted',".input_route", function(){        if(shuttle_bus_vars.routes!='')$(this).append(shuttle_bus_vars.routes);        $(this).editableSelect().change(function(){            var $id = $(this).attr('id');            $id = $id.split('_');            $id = $id[1];            $('#price_'+$id).val($(this).attr('data-price'));            var $time = $(this).attr('data-hours');            $time = $time.split(':');            $('#hours_'+$id).val($time[0]);            $('#minutes_'+$id).val($time[1]);        });    });*/
    /*$('.input_route').on('DOMNodeInserted',function(){        if(shuttle_bus_vars.routes!='')$(this).append(shuttle_bus_vars.routes);        $(this).editableSelect().change(function(){            var $id = $(this).attr('id');            $id = $id.split('_');            $id = $id[1];            $('#price_'+$id).val($(this).attr('data-price'));            var $time = $(this).attr('data-hours');            $time = $time.split(':');            $('#hours_'+$id).val($time[0]);            $('#minutes_'+$id).val($time[1]);        });    });*/
    var $count = total_stops ? total_stops + 1 : 0;
    $('.add_stop').on('click', function () {
        $('#stop_container').append('<div id="drag_' + $count + '" class="drag-area row" data-pos="' + $count + '">\            <div class="form-group col-sm-4">\                <div class="col-sm-3">\                <label for="from" class="control-label" style="line-height: 35px;">parada:</label>\                </div>\                <div class="col-sm-9">\                    <input data-pos="' + $count + '" id="posix_drag_' + $count + '" type="hidden" name="posix[]" value="' + $count + '"/>\                    <input data-count="' + $count + '" id="hstop_' + $count + '" type="hidden" name="hstops[]" value="-1"/>\                    <input data-count="' + $count + '" id="stop_' + $count + '" name="stops[]" class="typeahead input_route route-input stop_address"/>\                </div>\            </div>\            <div class="form-group col-sm-3">\                <div class="col-sm-4">\                <label for="to" class="control-label" style="line-height: 35px;">Recogida:</label>\                </div>\                 <div class="col-sm-8">\                    <input class="route-input form-control" type="text" class="form-control" name="pick_up[]" id="pick_up_' + $count + '">\                </div>\            </div>\            <div class="form-group col-sm-2">\                <div class="col-sm-4">\                    <label for="to" class="control-label" style="line-height: 35px;">Precio:</label>\                </div>\                 <div class="col-sm-8">\                    <input class="input_price route-input form-control" type="text" class="form-control" name="price[]" id="price_' + $count + '">\                </div>\            </div>\            <div class="form-group col-sm-3">\                <div class="col-sm-3">\                <label for="to" class="control-label" style="line-height: 35px;">Hora:</label>\                </div>\                 <div class="col-sm-8">\                    <select id="hours_' + $count + '" name="hours[]" class="form-control custom-control">\                        <option value="00">00</option>\                        <option value="01">01</option>\                        <option value="02">02</option>\                        <option value="03">03</option>\                        <option value="04">04</option>\                        <option value="05">05</option>\                        <option value="06">06</option>\                        <option value="07">07</option>\                        <option value="08">08</option>\                        <option value="09">09</option>\                        <option value="10">10</option>\                        <option value="11">11</option>\                        <option value="12">12</option>\                        <option value="13">13</option>\                        <option value="14">14</option>\                        <option value="15">15</option>\                        <option value="16">16</option>\                        <option value="17">17</option>\                        <option value="18">18</option>\                        <option value="19">19</option>\                        <option value="20">20</option>\                        <option value="21">21</option>\                        <option value="22">22</option>\                        <option value="23">23</option>\                    </select>\                    <span class="dot">:</span>\                    <select id="minutes_' + $count + '" name="minutes[]" class="form-control custom-control">\                        <option value="0">00</option>\                        <option value="05">05</option>\                        <option value="10">10</option>\                        <option value="15">15</option>\                        <option value="20">20</option>\                        <option value="25">25</option>\                        <option value="30">30</option>\                        <option value="35">35</option>\                        <option value="40">40</option>\                        <option value="45">45</option>\                        <option value="50">50</option>\                        <option value="55">55</option>\                    </select>\                </div>\                <a class="remove" id="remove_' + $count + '" style="line-height: 35px;font-size: 20px;"><i class="pe-7s-trash"></i></a>\            </div>\            </div>');
        appendFunc('#price_' + $count, '#stop_' + $count);
        $count++;
    });
    $('.i-checks').iCheck({checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green'});
    /*$('#s_days').on('ifChanged',function(){        $('#several_days').toggle();    });*/
    $('.days_months').on('click', function () {
        var $v = $(this).val();
        if ($v == 'days') {
            $('#bseveral_days').show();
            $('#bseveral_months').hide();
        } else {
            $('#bseveral_days').hide();
            $('#bseveral_months').show();
        }
    });
    $('#from_blocked_hrs').on('change', function () {
        var $pivot = $(this).val();
        $.map($('#to_blocked_hrs option'), function (option) {
            if (option.value < $pivot && option.value != -1) {
                $('#to_blocked_hrs option[value=' + option.value + ']').remove();
            }
        });
    });
    $('#from_blocked_mins').on('change', function () {
        var $pivot = $(this).val();
        $.map($('#to_blocked_mins option'), function (option) {
            if (option.value < $pivot && option.value != -1) {
                $('#to_blocked_mins option[value=' + option.value + ']').remove();
            }
        });
    });
    $.fn.datepicker.dates['es'] = {
        days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
        daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
        daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
        monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
        today: "Hoy",
        monthsTitle: "Meses",
        clear: "Borrar",
        weekStart: 1,
        format: "dd/mm/yyyy"
    };
    $('#blocked_from').datepicker({
        language: 'es',
        autoclose: true,
        startDate: new Date()
    }).on('changeDate', function (e) {
        var d = new Date(e.date.valueOf);
        d.setTime(d.getTime() + 1 * 24 * 60 * 60 * 1000);
        var from = $('#blocked_from').val().split('/');
        var from_day = from[0];
        var from_month = from[1] - 1;
        var from_year = from[2];
        from = new Date(from_year, from_month, from_day);
        from = Date.parse(from);
        var to = $('#blocked_to').val().split('/');
        var to_day = to[0];
        var to_month = to[1] - 1;
        var to_year = to[2];
        to = new Date(to_year, to_month, to_day);
        to = Date.parse(to);
        if (from >= to || $('#blocked_to').val() == '') {
            $("#blocked_to").val($.datepicker.formatDate(e.format(), d));
        }
    }).on('hide', function (e) {
    });
    $('#blocked_to').datepicker({
        language: 'es',
        autoclose: true,
        startDate: new Date()
    }).on('changeDate', function (e) {
        var d = new Date(e.date.valueOf);
        d.setTime(d.getTime() - (1 * 24 * 60 * 60 * 1000));
        var to = $('#blocked_to').val().split('/');
        var to_day = to[0];
        var to_month = to[1] - 1;
        var to_year = to[2];
        to = new Date(to_year, to_month, to_day);
        to = Date.parse(to);
        var from = $('#blocked_from').val().split('/');
        var from_day = from[0];
        var from_month = from[1] - 1;
        var from_year = from[2];
        from = new Date(from_year, from_month, from_day);
        from = Date.parse(from);
        if (from >= to || $('#blocked_from').val() == '') {
            $("#blocked_from").val($.datepicker.formatDate(e.format(), d));
        }
    });
    $('#stop_container').on('click', '.remove', function () {
        $(this).parent().parent().remove();
    });
    $('#routeForm').on('keypress', '.route-input', function (e) {
        if (e.which == 13) {
            e.preventDefault();
        }
    });
    $('#s_days').on('ifUnchecked', function () {
        $('.hrs').val(-1);
        $('.mins').val(-1);
        $('.day').iCheck('uncheck');
    });
    $('#bd_container').on('click', '.closebox', function (event) {
        event.preventDefault();
        var hpanel = $(this).closest('div.hpanel');
        hpanel.parent().parent().remove();
    });
    $('#bdd_container').on('click', '.closebox', function (event) {
        event.preventDefault();
        var hpanel = $(this).closest('div.hpanel');
        hpanel.parent().parent().remove();
        var $tb = $('#bdd_container').children().length;
        if ($tb == 0) $('#block_link').show();
    });
    var $days_blocked = [];
    var $ddays_blocked = [];
    var $months_blocked = [];
    var available_items = $('#bd_container').children();
    var davailable_items = $('#bdd_container').children();
    var $cblocked = available_items ? available_items.size() + 1 : 0;
    var $cdblocked = davailable_items ? davailable_items.size() + 1 : 0;
    $('#bd_container').on('click', '.edit_item', function () {
        var $item = $(this).attr('data-item');
        $('#hid_val').val($item);
        $('#blocked_from').val($('#blocked_from_' + $item).val());
        $('#from_blocked_hrs').val($('#from_blocked_hrs_' + $item).val());
        $('#from_blocked_mins').val($('#from_blocked_mins_' + $item).val());
        $('#blocked_to').val($('#blocked_to_' + $item).val());
        $('#to_blocked_hrs').val($('#to_blocked_hrs_' + $item).val());
        $('#to_blocked_mins').val($('#to_blocked_mins_' + $item).val());
        $.each($('.available_' + $item), function (option) {
            var $m = $(option).attr('data-day');
            var $fromHrs = $(option).attr('data-day_from_hrs');
            var $fromMins = $(option).attr('data-day_from_mins');
            var $toHrs = $(option).attr('data-day_to_hrs');
            var $toMins = $(option).attr('data-day_to_mins');
            $('#days_blocked_' + $m + '_is_blocked').prop('checked', true);
            $('#days_blocked_' + $m + '_opening_hrs').val($fromHrs);
            $('#days_blocked_' + $m + '_opening_mins').val($fromMins);
            $('#days_blocked_' + $m + '_closing_hrs').val($toHrs);
            $('#days_blocked_' + $m + '_closing_mins').val($toMins);
        });
    });
    $('#bdd_container').on('click', '.edit_item', function () {
        var $item = $(this).attr('data-item');
        var $type = $(this).attr('data-type');
        $type == 'day' ? $('#rdays').prop('checked', true) : $('#rmonths').prop('checked', true);
        $('#blocked_val').val($item);
        $.each($('.davailable_' + $item), function (option) {
            if ($type == 'day') {
                var $m = $(option).attr('data-day');
                $('#dd_blocked_' + $m + '_is_blocked').prop('checked', true);
            } else {
                var $m = $(option).attr('data-month');
                $('#months_blocked_' + $m + '_is_blocked').prop('checked', true);
            }
        });
    });
    $('#save_blocked').on('click', function () {
        $days_blocked = [];
        var $blocked_from = $('#blocked_from').length ? $('#blocked_from').val() : 'all';
        var $blocked_to = $('#blocked_to').length ? $('#blocked_to').val() : 'all';
        var $from_blocked_hrs = '-1';
        var $from_blocked_mins = '-1';
        var $to_blocked_hrs = '-1';
        var $to_blocked_mins = '-1';
        $.map($('.day'), function (option) {
            if ($(option).prop('checked') == true) {
                var $pivot = $(option).attr('data-info');
                var $fbhours = '-1';
                var $fbmins = '-1';
                var $tbhours = '-1';
                var $tbmins = '-1';
                $days_blocked.push({'day': $pivot, 'from': $fbhours + ':' + $fbmins, 'to': $tbhours + ':' + $tbmins});
            }
        });
        var $summary = '';
        var $title = '';
        if ($days_blocked.length == 0) {
            if ($blocked_from != 'all' && $blocked_to != 'all') {
                $summary = 'Abierto desde el ' + $blocked_from;
                if ($from_blocked_hrs != -1 && $from_blocked_mins != -1) {
                    $summary += ' ' + $from_blocked_hrs + ':' + $from_blocked_mins;
                }
                $summary += ' hasta el ' + $blocked_to;
                if ($to_blocked_hrs != -1 && $to_blocked_mins != -1) {
                    $summary += ' ' + $to_blocked_hrs + ':' + $to_blocked_mins;
                }
            }
        } else {
            var $days = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            for (var $i = 0; $i < $days_blocked.length; $i++) {
                var $day_blocked = $days_blocked[$i];
                var $day = $days[$day_blocked.day];
                var $fromDate = $from_blocked_hrs + ':' + $from_blocked_mins;
                var $toDate = $to_blocked_hrs + ':' + $to_blocked_mins;
                $summary += 'Abierto todos los ' + $day;
                var $_from = $fromDate;
                var $_to = $toDate;
                if ($day_blocked.from != '-1:-1' && $day_blocked.to != '-1:-1') {
                    $_from = $day_blocked.from;
                    $_to = $day_blocked.to;
                    $summary += ' a partir de la(s) ' + $_from + ' hasta la(s) ' + $_to;
                }
                if ($blocked_from != 'all' && $blocked_to != 'all') {
                    $summary += ' desde el ' + $blocked_from + ' hasta el ' + $blocked_to;
                }
                $summary += '</br>';
            }
        }
        $title = ($blocked_from != 'all' && $blocked_to != 'all') ? $blocked_from + "-" + $blocked_to : '';
        $cblocked = $('#hid_val').val() ? $('#hid_val').val() : $cblocked;
        var $available_html = '<div data-index="' + $cblocked + '" class="row available_item_' + $cblocked + '">\            <div class="col-lg-12">\            <div class="hpanel">\            <div class="panel-heading">\            <div class="panel-tools">\            <a class="closebox"><i class="fa fa-times"></i></a>\            <a class="edit_item" data-item="' + $cblocked + '" data-toggle="modal" data-target="#myModal"><i class="fa fa-pencil"></i></a>\            </div>\            ' + $title + '\        </div>\        <div class="panel-body">\            <p>' + $summary + '</p>\        </div>\        <div class="panel-footer"></div>\        </div>\        </div>';
        $available_html += '<input id="blocked_from_' + $cblocked + '" type="hidden" name="blocked[' + $cblocked + '][from]" value="' + $blocked_from + '">';
        $available_html += '<input id="blocked_to_' + $cblocked + '" type="hidden" name="blocked[' + $cblocked + '][to]" value="' + $blocked_to + '">';
        $available_html += '<input id="from_blocked_hrs_' + $cblocked + '" type="hidden" name="blocked[' + $cblocked + '][from_hrs]" value="' + $from_blocked_hrs + '">';
        $available_html += '<input id="from_blocked_mins_' + $cblocked + '" type="hidden" name="blocked[' + $cblocked + '][from_mins]" value="' + $from_blocked_mins + '">';
        $available_html += '<input id="to_blocked_hrs_' + $cblocked + '" type="hidden" name="blocked[' + $cblocked + '][to_hrs]" value="' + $to_blocked_hrs + '">';
        $available_html += '<input id="to_blocked_mins_' + $cblocked + '" type="hidden" name="blocked[' + $cblocked + '][to_mins]" value="' + $to_blocked_mins + '">';
        for (var $j = 0; $j < $days_blocked.length; $j++) {
            var $fr = '';
            var $t = '';
            var $d = $days_blocked[$j].day;
            var $bfhrs = $from_blocked_hrs;
            var $bfmins = $from_blocked_hrs;
            var $tfhrs = $to_blocked_hrs;
            var $tfmins = $to_blocked_hrs;
            if ($days_blocked[$j].from != '-1:-1' && $days_blocked[$j].to != '-1:-1') {
                $fr = $days_blocked[$j].from.split(':');
                $t = $days_blocked[$j].to.split(':');
                $bfhrs = $fr[0];
                $bfmins = $fr[1];
                $tfhrs = $t[0];
                $tfmins = $t[1];
            }
            $available_html += '<div class="available_' + $cblocked + '" data-day="' + $d + '" day_from_hrs="' + $bfhrs + '" day_from_mins="' + $bfmins + '" day_to_hrs="' + $tfhrs + '" day_to_mins="' + $tfmins + '">';
            $available_html += '<input id="day_' + $cblocked + '_' + $j + '" type="hidden" name="blocked[' + $cblocked + '][days_blocked][' + $j + '][day]" value="' + $d + '">';
            $available_html += '<input id="day_from_hrs_' + $cblocked + '_' + $j + '" type="hidden" name="blocked[' + $cblocked + '][days_blocked][' + $j + '][from_hrs]" value="' + $bfhrs + '">';
            $available_html += '<input id="day_from_mins_' + $cblocked + '_' + $j + '" type="hidden" name="blocked[' + $cblocked + '][days_blocked][' + $j + '][from_mins]" value="' + $bfmins + '">';
            $available_html += '<input id="day_to_hrs_' + $cblocked + '_' + $j + '" type="hidden" name="blocked[' + $cblocked + '][days_blocked][' + $j + '][to_hrs]" value="' + $tfhrs + '">';
            $available_html += '<input id="day_to_mins_' + $cblocked + '_' + $j + '" type="hidden" name="blocked[' + $cblocked + '][days_blocked][' + $j + '][to_mins]" value="' + $tfmins + '">';
            $available_html += '</div></div>';
        }
        if ($('#hid_val').val()) {
            var hid = $('#hid_val').val();
            $('.available_item_' + hid).remove();
            $('#bd_container').insertAt(hid, $available_html);
        } else {
            $('#bd_container').append($available_html);
        }
        $('#myModal').modal('hide');
        $cblocked++;
    });
    $('#save_blockedd').on('click', function () {
        $ddays_blocked = [];
        $months_blocked = [];
        $.map($('.dd'), function (option) {
            if ($(option).prop('checked') == true) {
                var $pivot = $(option).attr('data-info');
                $ddays_blocked.push({'day': $pivot});
            }
        });
        $.map($('.month'), function (option) {
            if ($(option).prop('checked') == true) {
                var $pivot = $(option).attr('data-info');
                $months_blocked.push({'month': $pivot});
            }
        });
        var $bsummary = '';
        var $dtitle = '';
        var $available_html = '';
        if ($ddays_blocked.length != 0) {
            var $days = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            for (var $i = 0; $i < $ddays_blocked.length; $i++) {
                var $day_blocked = $ddays_blocked[$i];
                var $day = $days[$day_blocked.day];
                $bsummary += 'Bloqueado todos los ' + $day;
                $bsummary += '</br>';
            }
            $cblocked = $('#blocked_val').val() ? $('#blocked_val').val() : $cdblocked;
            $available_html += '<div data-index="' + $cdblocked + '" class="row davailable_item_' + $cdblocked + '">\                <div class="col-lg-12">\                <div class="hpanel">\                <div class="panel-heading">\                <div class="panel-tools">\                <a class="closebox"><i class="fa fa-times"></i></a>\                <a class="edit_item" data-type="day" data-item="' + $cdblocked + '" data-toggle="modal" data-target="#myBlockedModal"><i class="fa fa-pencil"></i></a>\                </div>\                ' + $dtitle + '\            </div>\            <div class="panel-body">\                <p>' + $bsummary + '</p>\            </div>\            <div class="panel-footer"></div>\            </div>\            </div>';
            for (var $j = 0; $j < $ddays_blocked.length; $j++) {
                var $d = $ddays_blocked[$j].day;
                $available_html += '<div class="davailable_' + $cdblocked + '" data-day="' + $d + '">';
                $available_html += '<input id="day_' + $cdblocked + '_' + $j + '" type="hidden" name="blockd[' + $cdblocked + '][dd_blocked][' + $j + '][day]" value="' + $d + '">';
                $available_html += '</div></div>';
            }
        } else if ($months_blocked.length != 0) {
            var $months = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octublre', 'Noviembre', 'Diciembre'];
            $bsummary += 'Meses Bloqueados:';
            $bsummary += '</br>';
            for (var $i = 0; $i < $months_blocked.length; $i++) {
                var $month_blocked = $months_blocked[$i];
                var $month = $months[$month_blocked.month];
                $bsummary += $month;
                $bsummary += '</br>';
            }
            $cdblocked = $('#blocked_val').val() ? $('#blocked_val').val() : $cdblocked;
            $available_html += '<div data-index="' + $cdblocked + '" class="row davailable_item_' + $cdblocked + '">\                <div class="col-lg-12">\                <div class="hpanel">\                <div class="panel-heading">\                <div class="panel-tools">\                <a class="closebox"><i class="fa fa-times"></i></a>\                <a class="edit_item" data-type="month" data-item="' + $cdblocked + '" data-toggle="modal" data-target="#myBlockedModal"><i class="fa fa-pencil"></i></a>\                </div>\                ' + $dtitle + '\            </div>\            <div class="panel-body">\                <p>' + $bsummary + '</p>\            </div>\            <div class="panel-footer"></div>\            </div>\            </div>';
            for (var $j = 0; $j < $months_blocked.length; $j++) {
                var $m = $months_blocked[$j].month;
                $available_html += '<div class="davailable_' + $cdblocked + '" data-month="' + $m + '">';
                $available_html += '<input id="month_' + $cdblocked + '_' + $j + '" type="hidden" name="blockd[' + $cdblocked + '][month_blocked][' + $j + '][month]" value="' + $m + '">';
                $available_html += '</div></div>';
            }
        }
        if ($('#blocked_val').val()) {
            var hid = $('#blocked_val').val();
            $('.available_item_' + hid).remove();
            $('#bdd_container').insertAt(hid, $available_html);
        } else {
            $('#bdd_container').append($available_html);
        }
        $('#myBlockedModal').modal('hide');
        $cdblocked++;
    });
    $('.delete_route').on('click', function () {
        var $route = $(this).attr('data-route');
        swal({
            title: "Estás seguro de borrar esta ruta?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: shuttle_bus_vars.ajaxurl,
                    dataType: 'json',
                    type: 'post',
                    data: {action: 'remove_route', route: $route},
                    beforeSend: function () {
                    },
                    success: function (response) {
                        window.location = shuttle_bus_vars.route_url;
                    }
                });
            }
        });
    });
    $('.cancel_booking').on('click', function () {
        var $book = $(this).attr('data-booking');
        swal({
            title: "Estás seguro de cancelar esta reserva?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: shuttle_bus_vars.ajaxurl,
                    dataType: 'json',
                    type: 'post',
                    data: {action: 'cancel_booking', book: $book},
                    beforeSend: function () {
                    },
                    success: function (response) {
                        window.location = shuttle_bus_vars.booking_url;
                    }
                });
            }
        });
    });
    $('#book_date').datepicker({language: 'es', autoclose: true, startDate: new Date()});
    $('#bfrom').on('change', function () {
        var $route = $(this).attr('data-route');
        var $stop_pos = $(this).val();
        var $stop = $stop_pos.split('_')[0];
        var $pos = $stop_pos.split('_')[1];
        $.ajax({
            url: shuttle_bus_vars.ajaxurl,
            dataType: 'json',
            type: 'post',
            data: {action: 'fetchStops', route: $route, stop: $stop, pos: $pos, orientation: 'fwd'},
            beforeSend: function () {
            },
            success: function (response) {
                $('#bto').empty();
                $('#bto').append(response.html);
            }
        });
    });
    $('#bto').on('change', function () {
        var $route = $(this).attr('data-route');
        var $stop_pos = $(this).val();
        var $stop = $stop_pos.split('_')[0];
        var $pos = $stop_pos.split('_')[1];
        $.ajax({
            url: shuttle_bus_vars.ajaxurl,
            dataType: 'json',
            type: 'post',
            data: {action: 'fetchStops', route: $route, stop: $stop, pos: $pos, orientation: 'bwd'},
            beforeSend: function () {
            },
            success: function (response) {
                $('#bfrom').empty();
                $('#bfrom').append(response.html);
            }
        });
    });
});