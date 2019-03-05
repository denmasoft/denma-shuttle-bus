
jQuery(document).ready(function($) {
    var $day_count = 0;
    var $date_count = 0;
    var avail = 0;
    var $days = ['','Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
    var $longDays = ['','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
    $('#myBlockedModal').on('click','.add_day',function(){
        $day_count = $('.date').length;
        $('#days_container').append('<div class="row">\
            <div class="form-group col-lg-6">\
                <div class="input-group date">\
                    <input id="day_'+$day_count+'" type="text" name="days[]" class="_bday form-control" readonly>\
                    <span class="input-group-addon">\
                        <span class="fa fa-calendar"></span>\
                    </span>\
                </div>\
            </div>\
            <div class="form-group col-lg-1">\
                <a class="remove" id="day_remove_'+$day_count+'" style="line-height: 35px;font-size: 20px;"><i class="pe-7s-trash"></i></a>\
            </div>\
            </div>');
        $('#day_'+$day_count).datepicker({
            language: 'es',
            autoclose:true,startDate: new Date()});
        $day_count++;
    });
    $('#days_container').on('click','.remove',function(){
        $(this).parent().parent().remove();
    });

    if($('#bdd_container').children().length > 0)
    {
        $('#block_link').hide();
    }
    $('#myBlockedModal').on('click','.add_date',function(){
        var $ths = '';
        var $tds = '';
        $date_count = $('.datec').length;
        for(var $i=1;$i<$days.length;$i++)
        {
            $ths+='<th class="one">'+$days[$i]+'</th>';
            $tds+='<td class="center"><input type="checkbox" data-info="'+$i+'" class="i-checks wd" id="wd_blocked_'+$i+'_is_blocked" name="wd_blocked['+$i+'][is_blocked]"></td> ';
        }
        var $hr = $date_count>0 ? '<hr>':'';
        $('#dates_container').append($hr+'<div class="row">\
            <div class="datec form-group col-lg-12" data-datec="'+$date_count+'" id="datec_'+$date_count+'">\
                <a class="remove" id="date_remove_'+$date_count+'" style="line-height: 35px;font-size: 20px;"><i class="pe-7s-trash"></i></a>\
                <div class="row">\
                    <div class="col-lg-6">\
                        <label>Desde el dia </label>\
                        <div class="input-group date">\
                            <input id="day_from_'+$date_count+'" type="text" name="day_from" class="form-control" readonly>\
                            <span class="input-group-addon">\
                                <span class="fa fa-calendar"></span>\
                            </span>\
                        </div>\
                    </div>\
                    <div class="col-lg-6">\
                        <label>hasta el dia </label>\
                        <div class="input-group date">\
                            <input id="day_to_'+$date_count+'" type="text" name="day_to" class="form-control" readonly>\
                            <span class="input-group-addon">\
                                <span class="fa fa-calendar"></span>\
                            </span>\
                        </div>\
                    </div>\
                </div><hr>\
                <div class="row"><div class="col-lg-12">\
                    <p>Todos los:</p></div>\
                </div>\
                <div class="row"><div class="col-lg-12">\
                    <table style="width: 100%;margin-left: 35px;">\
                        <thead>\
                            <tr>\
                            '+$ths+'\
                            </tr>\
                        </thead>\
                        <tbody>\
                            <tr>\
                                '+$tds+'\
                            </tr>\
                        </tbody>\
                    </table></div>\
                </div><hr>\
                <div class="row"><div class="col-lg-12">\
                    <p style="display: inline-block;">Repetir cada:</p>\
                    <select style="display: inline-block;" id="repeat_period_'+$date_count+'" name="repeat_period">\
                        <option value="week">Semana</option>\
                        <option value="month">Mes</option>\
                        <option value="year">Año</option>\
                    </select>\
                    </div>\
                </div>\
            </div></div>');
        $('#day_from_'+$date_count).datepicker({
            language: 'es',
            autoclose:true,startDate: new Date()});
        $('#day_to_'+$date_count).datepicker({
            language: 'es',
            autoclose:true,startDate: new Date()});
        $date_count++;
    });
    $('#dates_container').on('click','.remove',function(){
        $(this).parent().parent().remove();
    });
    $('#publish_blocked').on('click',function(){
        $('#bdd_container').empty();
        var $summary='';
        var $dblkd = '';
        var $datecs = '';
        var $data = [];
        $.map($('._bday') ,function(input) {
            $dblkd+=$(input).val()+', ';
        });
        $dblkd = $dblkd.slice(0,-2);
        $.map($('.datec') ,function(elem) {
            var $ct = $(elem).data('datec');
            var id = $(elem).attr('id');
            var $_f = $('#day_from_'+$ct).val();
            var $_t = $('#day_to_'+$ct).val();
            var $period = $('#repeat_period_'+$ct).val();
            var $_period = 'Semana';
            switch ($period) {
                case 'week':
                    $_period = 'Semana';
                    break;
                case 'month':
                    $_period = 'Mes';
                    break;
                case 'year':
                    $_period = 'Año';
                    break;
            }
            var wds = '';
            var pivots = [];
            //var $twds = $('#'+id+' .wd:checkbox:checked').length;
            var ltwd = $('#'+id+' .wd:checkbox:checked').last();
            var ltwdi = $(ltwd).attr('data-info');
            $.map($('#'+id+' .wd:checkbox:checked'),function(wd){
                var $pivot = $(wd).attr('data-info');
                pivots.push($pivot);
                var $chr=', ';
                if(ltwdi==$pivot)
                {
                    wds = wds.slice(0,-2);
                    wds += ' y ' + $longDays[$pivot];
                }
                else{
                    wds+= $longDays[$pivot] + $chr;
                }
            });
            $datecs+='Bloqueado ';
            if(wds!='')$datecs+='todos los '+wds;
            if($_f)$datecs+=' desde '+$_f;
            if($_t)$datecs+=' hasta '+$_t;
            $datecs+=' de cada '+$_period+'<br>';
            $data.push(pivots.join(',')+'_'+$_f+'_'+$_t+'_'+$period);
        });
        $data = $data.join('-');
        if($dblkd!='')$summary+='Dias bloqueados: '+$dblkd+'<br>';
        $summary+=$datecs+'<br>';
        var $available_html='<div data-index="'+avail+'" class="row davailable_item_'+avail+'">\
                <div class="col-lg-12">\
                <div class="hpanel">\
                <div class="panel-heading">\
                <div class="panel-tools" style="margin-top: -10px;">\
                <a class="closebox"><i class="fa fa-times"></i></a>\
                <a class="edit_blocked" data-days="'+$dblkd+'" data-recur="'+$data+'" data-item="'+avail+'" data-toggle="modal" data-target="#myBlockedModal"><i class="fa fa-pencil"></i></a>\
                </div></div>\
            <div class="panel-body">\
                <p>'+$summary+'</p>\
            </div>\
            <div class="panel-footer"></div>\
            </div>\
            </div>';
        $('#block_link').hide();
        $('#bdd_container').append($available_html);
        $('#days_container div').empty();
        $('#dates_container div').empty();
        $('#myBlockedModal').modal('hide');
        avail++;
    });
    $('#bdd_container').on('click','.edit_blocked',function(){
        $('#days_container div').empty();
        $('#dates_container').empty();
        var $item =$(this).data('item');
        var $ds =$(this).data('days');
        $ds = $ds.split(',');

        for(var $i=0;$i<$ds.length;$i++)
        {            
            $('#days_container').append('<div class="row">\
            <div class="form-group col-lg-6">\
                <div class="input-group date">\
                    <input id="day_'+$i+'" type="text" name="days[]" class="_bday form-control" readonly value="'+$ds[$i]+'">\
                    <span class="input-group-addon">\
                        <span class="fa fa-calendar"></span>\
                    </span>\
                </div>\
            </div>\
            <div class="form-group col-lg-1">\
                <a class="remove" id="day_remove_'+$i+'" style="line-height: 35px;font-size: 20px;"><i class="pe-7s-trash"></i></a>\
            </div>\
            </div>');
            $('#day_'+$i).datepicker({
                language: 'es',
                autoclose:true,startDate: new Date()});
        }
        var $data = $(this).data('recur');
        $data = $data.split('-');
        for(var $j=0;$j<$data.length;$j++)
        {
            $date_count = $j;
            var $item = $data[$j];
            var $info = $item.split('_');
            var $dys =$info[0];$dys=$dys.split(',');
            var $f = $info[1];
            var $t = $info[2];
            var $p = $info[3];
            var $ths = '';
            var $tds = '';
            var $found = '';
            for(var $i=1;$i<$days.length;$i++)
            {
                $ths+='<th class="one">'+$days[$i]+'</th>';
                for(var $x=0;$x<$dys.length;$x++)
                {
                    if($dys[$x]==$i)
                    {
                        $found = 'checked';break;
                    }
                    else{
                        $found='';
                    }
                }
                $tds+='<td class="center"><input '+$found+' type="checkbox" data-info="'+$i+'" class="i-checks wd" id="wd_blocked_'+$i+'_is_blocked" name="wd_blocked['+$i+'][is_blocked]"></td> ';
            }
            var $hr = $date_count>0 ? '<hr>':'';
            $('#dates_container').append($hr+'<div class="row">\
            <div class="datec form-group col-lg-12" data-datec="'+$date_count+'" id="datec_'+$date_count+'">\
                <a class="remove" id="date_remove_'+$date_count+'" style="line-height: 35px;font-size: 20px;"><i class="pe-7s-trash"></i></a>\
                <div class="row">\
                    <div class="col-lg-6">\
                        <label>Desde el dia </label>\
                        <div class="input-group date">\
                            <input id="day_from_'+$date_count+'" type="text" name="day_from" class="form-control" readonly value="'+$f+'">\
                            <span class="input-group-addon">\
                                <span class="fa fa-calendar"></span>\
                            </span>\
                        </div>\
                    </div>\
                    <div class="col-lg-6">\
                        <label>hasta el dia </label>\
                        <div class="input-group date">\
                            <input id="day_to_'+$date_count+'" type="text" name="day_to" class="form-control" readonly value="'+$t+'">\
                            <span class="input-group-addon">\
                                <span class="fa fa-calendar"></span>\
                            </span>\
                        </div>\
                    </div>\
                </div><hr>\
                <div class="row"><div class="col-lg-12">\
                    <p>Todos los:</p></div>\
                </div>\
                <div class="row"><div class="col-lg-12">\
                    <table style="width: 100%;margin-left: 35px;">\
                        <thead>\
                            <tr>\
                            '+$ths+'\
                            </tr>\
                        </thead>\
                        <tbody>\
                            <tr>\
                                '+$tds+'\
                            </tr>\
                        </tbody>\
                    </table></div>\
                </div><hr>\
                <div class="row"><div class="col-lg-12">\
                    <p style="display: inline-block;">Repetir cada:</p>\
                    <select style="display: inline-block;" id="repeat_period_'+$date_count+'" name="repeat_period">\
                        <option value="week">Semana</option>\
                        <option value="month">Mes</option>\
                        <option value="year">Año</option>\
                    </select>\
                    </div>\
                </div>\
            </div></div>');
            $('#day_from_'+$date_count).datepicker({
                language: 'es',
                autoclose:true,startDate: new Date()});
            $('#day_to_'+$date_count).datepicker({
                language: 'es',
                autoclose:true,startDate: new Date()});
            if($p==1 || $p=='week')
            {
                $('#repeat_period_'+$date_count).val('week');
            }
            if($p==2 || $p=='month')
            {
                $('#repeat_period_'+$date_count).val('month');
            }
            if($p==3 || $p=='year')
            {
                $('#repeat_period_'+$date_count).val('year');
            }
        }
        $('#hid_val').val($item);
    });
    function appendNewRoute($draft)
    {
        if($('.edit_blocked').length > 0)
        {
            $.map($('.edit_blocked'),function(elem){
                var $item =$(elem).data('item');
                var $ds =$(elem).data('days');
                $ds = $ds.split(',');
                for(var $i=0;$i<$ds.length;$i++)
                {
                    $('#routeForm').append('<input type="hidden" name="daysb[]" value="'+$ds[$i]+'">');
                }
                var $data = $(elem).data('recur');
                $data = $data.split('-');
                for(var $j=0;$j<$data.length;$j++)
                {
                    $date_count = $j;
                    var $item = $data[$j];
                    var $info = $item.split('_');
                    var $dys =$info[0];
                    var $f = $info[1];
                    var $t = $info[2];
                    var $p = $info[3];
                    $('#routeForm').append('<input type="hidden" name="daysf[]" value="'+$f+'">');
                    $('#routeForm').append('<input type="hidden" name="dayst[]" value="'+$t+'">');
                    $('#routeForm').append('<input type="hidden" name="dayswd[]" value="'+$dys+'">');
                    $('#routeForm').append('<input type="hidden" name="daysp[]" value="'+$p+'">');
                }
            });
            $('#routeForm').append('<input type="hidden" name="draft" value="'+$draft+'">');
        }
        $('#hsub').trigger('click');
    }
    $('#add_new_route').on('click',function(e){
        e.preventDefault();
        appendNewRoute(0);
    });
    $('#draft_new_route').on('click',function(e){
        e.preventDefault();
        appendNewRoute(1);
    });
    $('#publish_route').on('click',function(e){
        e.preventDefault();
        appendNewRoute(0);
    });
    $('#keep_draft_route').on('click',function(e){
        e.preventDefault();
        appendNewRoute(0);
    });
    $('#clone_route').on('click',function(e){
        e.preventDefault();
        appendNewRoute(2);
    });
});

