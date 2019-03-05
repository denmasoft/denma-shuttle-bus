<?php

function getRangeDates($dates){

    $start_date=$dates['start_date'];

    $end_date=$dates['end_date'];

    $dow=$dates['dow'];

    $from_hrs=$dates['from_hrs'];

    $to_hrs=$dates['to_hrs'];

    $startDate = \DateTime::createFromFormat('d/m/Y', $start_date);

    $endDate = \DateTime::createFromFormat('d/m/Y', $end_date);

    $endDate = strtotime($endDate->format('Y-m-d'));

    $days=array('0'=>'Monday','1' => 'Tuesday','2' => 'Wednesday','3'=>'Thursday','4' =>'Friday','5' => 'Saturday','6'=>'Sunday');

    for($i = strtotime($days[$dow], strtotime($startDate->format('Y-m-d'))); $i <= $endDate; $i = strtotime('+1 week', $i))

    {

        $pdate = date('Y-m-d',$i);

        $open_date = \DateTime::createFromFormat('Y-m-d H:i:s', $pdate.' '.$from_hrs);

        $close_date = \DateTime::createFromFormat('Y-m-d H:i:s', $pdate.' '.$to_hrs);

        $date_array[] = array('from'=>$open_date,'to'=>$close_date);

    }

    return $date_array;

}

function getAvailableDates($dates){

    $start_date=$dates['start_date'];

    $end_date=$dates['end_date'];    

    $from_hrs=$dates['from_hrs'];

    $to_hrs=$dates['to_hrs'];

    $startDate = \DateTime::createFromFormat('d/m/Y H:i:s', $start_date.' '.$from_hrs);

    $endDate = \DateTime::createFromFormat('d/m/Y H:i:s', $end_date.' '.$to_hrs);

    $date_array[]=array('from'=>$startDate,'to'=>$endDate);

    /*$endDate = strtotime($endDate->format('Y-m-d'));

    for($i = strtotime($startDate->format('Y-m-d')); $i <= $endDate; $i = strtotime('+1 day', $i))

        $date_array[]=date('Y-m-d',$i).' '.$from_hrs.'-'.$to_hrs;*/

    return $date_array;

}

function processDates($blocked)

{

    $result = null;

    foreach ($blocked as $block)

    {

        $dates = null;

        $start_date = $block['from'];

        $end_date = $block['to'];

        $from_hrs = $block['from_hrs'].':'.$block['from_mins'];

        $to_hrs = $block['to_hrs'].':'.$block['to_mins'];

        if(isset($block['days_blocked']))

        {

            foreach($block['days_blocked'] as $day)

            {

                $dow = $day['day'];

                $blocked_from = (isset($day['from_hrs']) && isset($day['from_mins']))? $day['from_hrs'].':'.$day['from_mins']: '-1:-1';

                $blocked_to = (isset($day['to_hrs']) && isset($day['to_mins'])) ? $day['to_hrs'].':'.$day['to_mins']: '-1:-1';

                if($blocked_from!='-1:-1' && $blocked_to!='-1:-1')

                {

                    $from_hrs = $blocked_from;

                    $to_hrs = $blocked_to;

                }

                //$args = array('start_date'=>$start_date,'end_date'=>$end_date,'dow'=>$dow,'from_hrs'=>$from_hrs,'to_hrs'=>$to_hrs);

                //$dates[] = getRangeDates($args);

                $dates[]=array('day'=>$dow,'from_hrs'=>$from_hrs,'to_hrs'=>$to_hrs);

            }

        }        

        if($dates==null)

        {

            $days=null;

            for($i=0;$i<=6;$i++)

            {

                $days[]=array('day'=>$i,'from_hrs'=>$from_hrs,'to_hrs'=>$to_hrs);

            }

            $result[] = array('start_date'=>$start_date,'end_date'=>$end_date,'days'=>$days);

        }

        else{

            $result[] = array('start_date'=>$start_date,'end_date'=>$end_date,'days'=>$dates);

        }

    }

    return $result;

}

function processBlockedDays($dd_blocked){

    $dates = null;

    foreach($dd_blocked as $day)

    {

        $dow = $day['day'];

        $dates[]=array('day'=>$dow);

    }

    return serialize($dates);

}

function processBlockedMonths($months_blocked)

{

    $dates = null;

    foreach ($months_blocked as $month) {

        $moy = $month['month'];

        $dates[] = array('month' => $moy);

    }

    return serialize($dates);

}

function addRoute(){

    $route_name = $_POST['route_name'];
    /*$from = $_POST["from"];*/
    $people = $_POST['people'];
    $prices = $_POST['price'];
    $pickups = $_POST['pick_up'];
    $hours = $_POST['hours'];
    $minutes = $_POST['minutes'];
    $stops = $_POST['stops'];
    $hstops = $_POST['hstops'];
    $posix = $_POST['posix'];
    $bdays = null;
    $pblocked = null;
    $pranges = null;
    $curdate = new \DateTime();
    $to = $stops[count($stops) - 1];
    $from = $stops[0];
    if($route_name=='')
    {
        $route_name = $from.'-'.$to;
    }
    $draft=0;
    if(isset($_POST['draft']))$draft =$_POST['draft'];
    global $wpdb;
    $start_end = $wpdb->get_row($wpdb->prepare("SELECT s.ID as sf, e.ID as se FROM ".$wpdb->prefix."shuttle_stop s,".$wpdb->prefix."shuttle_stop e WHERE s.ADDRESS=%s and e.ADDRESS=%s",$from,$to));
    $wpdb->insert($wpdb->prefix.'shuttle_route',array('NAME'=>$route_name,'START_POINT'=>$from,'END_POINT'=>$to,'PEOPLE'=>$people,'DRAFT'=>$draft,'CREATED_AT'=>$curdate->format('Y-m-d H:i:s'),'start'=>$start_end->sf,'end'=>$start_end->se),array('%s','%s'));
    $route = $wpdb->insert_id;
    $created_at = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s');
    if(isset($_POST['daysb']))
    {
        foreach($_POST['daysb'] as $day)
        {
            $pblocked[]=$day;
        }
    }
    if(isset($_POST['daysf']))
    {
        $fcount=0;
        foreach($_POST['daysf'] as $dayf)
        {
            $period = '';
            $open_date = $dayf;
            if(isset($_POST['dayst'])){
                $close_date = $_POST['dayst'][$fcount];
            }
            if(isset($_POST['dayswd']))
            {
                $bdays = $_POST['dayswd'][$fcount];
            }
            if(isset($_POST['daysp']))
            {
                $period = $_POST['daysp'][$fcount];
                if($period=='week')$period=1;
                if($period=='month')$period=2;
                if($period=='year')$period=3;
            }
            $pranges[] = array('OPEN_DATE'=>$open_date,'CLOSE_DATE'=>$close_date,'DAYS'=>$bdays,'PERIOD'=>$period);
            $fcount++;
        }
    }
    $data = array('ranges'=>null,'bdd'=>null);
    if(isset($pranges))
    {
        $data['ranges'] = $pranges;
    }
    if(isset($pblocked))
    {
        $data['bdd']=$pblocked;
    }
    if(isset($data['ranges']) || isset($data['bdd']))
    {
        $dt = serialize($data);
        $wpdb->insert($wpdb->prefix.'shuttle_route_available',array('ROUTE'=>$route,'DATA'=>$dt,'CREATEDAT'=>$created_at,'UPDATEDAT'=>$created_at,'VERSION'=>0),array('%s','%s'));
    }
    $count = 0;
    foreach($hstops as $hstop)
    {
        $price = $prices[$count];
        $pick_up =  $pickups[$count];
        $hrs = $hours[$count];
        $mins = $minutes[$count];
        $pos = $posix[$count];
        if($hstop=='-1')
        {
            $sstop = $wpdb->get_row($wpdb->prepare("select s.ID as sid FROM ".$wpdb->prefix."shuttle_stop s where s.ADDRESS=%s LIMIT 1",$stops[$count]));
            if(!$sstop)
            {
                $wpdb->insert($wpdb->prefix.'shuttle_stop',array('ADDRESS' => $stops[$count]),array('%s','%s'));
                $sid = $wpdb->insert_id;
                $wpdb->insert($wpdb->prefix.'shuttle_route_stop',array('ID_ROUTE' => $route,'ID_STOP'=>$sid,'PRICE'=>$price,'POS'=>$pos,'HRS'=>$hrs.':'.$mins,'PICK_UP'=>$pick_up),array('%s','%s'));
            }
            else{
                $wpdb->insert($wpdb->prefix.'shuttle_route_stop',array('ID_ROUTE' => $route,'ID_STOP'=>$sstop->sid,'PRICE'=>$price,'POS'=>$pos,'HRS'=>$hrs.':'.$mins,'PICK_UP'=>$pick_up),array('%s','%s'));
            }
        }
        else{
            $wpdb->insert($wpdb->prefix.'shuttle_route_stop',array('ID_ROUTE' => $route,'ID_STOP'=>$hstop,'PRICE'=>$price,'POS'=>$pos,'HRS'=>$hrs.':'.$mins,'PICK_UP'=>$pick_up),array('%s','%s'));
        }
        $count++;
    }
    /*foreach ($stops as $stop)

    {

        $price = $prices[$count];

        $hrs = $hours[$count];

        $mins = $minutes[$count];

        $wpdb->insert($wpdb->prefix.'shuttle_stop',array('ADDRESS' => $stop,'PRICE'=>$price,'POS'=>$count+1,'HRS'=>$hrs.':'.$mins),array('%s','%s'));

        $sid = $wpdb->insert_id;

        $wpdb->insert($wpdb->prefix.'shuttle_route_stop',array('ID_ROUTE' => $route,'ID_STOP'=>$sid),array('%s','%s'));

        $count++;

    }*/
    return $route_name;
}

function new_route () {
    $message='';
    $success='';
    if(isset($_POST['insert']))
    {
        if(!isset($_POST['people']) || $_POST['people']==0){
            $message.= 'La cantidad de viajeros es incorrecta.<br>';
        }
        if(!isset($_POST['stops'])){
            $message.= 'Debe especificar al menos una parada.';
        }
        if($message=='')
        {
            $route_name = addRoute();
            $success = 'La ruta '.$route_name.' ha sido creada.';
        }
    }
    ?>
    <link type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />

    <link href="<?php echo WP_PLUGIN_URL; ?>/shuttleBus/assets/front/css/addressPicker.css" rel="stylesheet">

    <style>

        .twitter-typeahead{width: 100%;}

        .custom-control{float:left;margin-left:5px;margin-right:5px;width:40%;margin-bottom:9px;padding:6px 12px !important;height:auto !important;}

        .cm-control{float:left;width:45%;padding:6px 1px !important;height:auto !important;}

        .dot{float:left;line-height:25px;}

        .cm_dot{line-height:25px;}

        body.modal-open {position:relative !important;}

        #dates_container .remove,#days_container .remove{position:absolute;float:right;right:2%;top:-5%;}

        /*.route-input{height:auto !important;}*/

        .typeahead,

        .tt-query,

        .tt-hint {

            width: 100%;

            height: 34px;

            padding: 8px 12px;

            font-size: 18px;

            line-height: 30px;

            border: 2px solid #ccc;

            -webkit-border-radius: 8px;

            -moz-border-radius: 8px;

            border-radius: 8px;

            outline: none;

        }

        .typeahead {

            background-color: #fff;

        }

        .typeahead:focus {

            border: 2px solid #0097cf;

        }

        .tt-query {

            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);

            -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);

            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);

        }

        .tt-hint {

            color: #999

        }

        .tt-menu {

            width: 100%;

            margin: 12px 0;

            padding: 8px 0;

            background-color: #fff;

            border: 1px solid #ccc;

            border: 1px solid rgba(0, 0, 0, 0.2);

            -webkit-border-radius: 8px;

            -moz-border-radius: 8px;

            border-radius: 8px;

            -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);

            -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);

            box-shadow: 0 5px 10px rgba(0,0,0,.2);

        }

        .tt-suggestion {

            padding: 3px 20px;

            font-size: 18px;

            line-height: 24px;

        }

        .tt-suggestion:hover {

            cursor: pointer;

            color: #fff;

            background-color: #0097cf;

        }

        .tt-suggestion.tt-cursor {

            color: #fff;

            background-color: #0097cf;

        }

        .tt-suggestion p {

            margin: 0;

        }

        .gist {

            font-size: 14px;

        }

        #rtl-support .tt-menu {

            text-align: right;

        }

        body.dragging, body.dragging * {

            cursor: move !important;

        }



        .drag-area {

            margin-right: 10px;

            margin-left: 10px;

        }

    </style>

    <script src="<?php echo WP_PLUGIN_URL; ?>/shuttleBus/assets/front/js/typeahead.bundle.min.js"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>var update=false;var total_stops = 0;</script>

    <div class="wrap">

        <h2>Nueva ruta

            <a class="btn btn-success" href="<?php echo admin_url('admin.php?page=route_list')?>">&laquo; ir a rutas</a>

        </h2>

        <?php if ($message!=''): ?>



            <div class="alert alert-danger">

                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

                <strong>Error!</strong><br><?php echo $message; ?>

            </div>

        <?php endif;?>

        <?php if ($success!=''): ?>



            <div class="alert alert-success">

                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

                <strong>Exito!</strong><br><?php echo $success; ?>

            </div>

        <?php endif;?>

        <div class="col-lg-12">

            <div class="hpanel">

                <div class="panel-body">

                    <form class="form" id="routeForm" role="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

                        <div class="row">

                            <div class="form-group col-lg-4">

                                <label for="from" class="col-sm-2 control-label" style="line-height: 35px;">Nombre:</label>

                                <div class="col-sm-10">

                                    <input type="text" class="route-input form-control" name="route_name" id="route_name">

                                </div>

                            </div>

                            <!--<div class="form-group col-lg-4">

                                <label for="from" class="col-sm-2 control-label" style="line-height: 35px;">Desde:</label>

                                <div class="col-sm-10">

                                    <input type="text" class="route-input form-control" name="from" id="from">

                                    <input type="hidden" id="route_from" name="route_from">

                                </div>

                            </div>-->

                            <div class="form-group col-lg-4">

                                <label for="people" class="col-sm-2 control-label" style="line-height: 35px;">Viajeros:</label>

                                <div class="col-sm-10">

                                    <input type="number" min="1" class="route-input form-control" name="people" id="people">

                                </div>

                            </div>

                            <div style="display:none"><button id="hsub" type="submit" value="save" name="insert"></button></div>

                        </div>

                        <div class="row">

                            <div class="col-lg-12">

                                <div class="hpanel hgreen">

                                    <div class="panel-heading">

                                        <div class="panel-tools">

                                            <a class="add_stop"><i class="fa fa-plus"></i></a>

                                        </div>

                                        Paradas

                                    </div>

                                    <div class="panel-body" style="display: block;">

                                        <div class="stopc row" id="stop_container"></div>

                                    </div>

                                    <div class="panel-footer" style="display: block;">

                    <span class="pull-right"></span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-lg-12">

                                <div class="hpanel hgreen">

                                    <div class="panel-heading">

                                        <div class="panel-tools">



                                            <div class="modal fade" id="myBlockedModal" tabindex="-1" role="dialog" aria-hidden="true">

                                                <div class="modal-dialog">

                                                    <div class="modal-content">

                                                        <div class="modal-header text-center">

                                                            <h4 class="modal-title"></h4>

                                                        </div>

                                                        <div class="modal-body">

                                                            <div class="row" id="blocked_container">

                                                                <form novalidate>

                                                                    <input type="hidden" value="" id="blocked_val" name="blocked_val">

                                                                </form>

                                                            </div>

                                                            <div class="row">

                                                                <div class="col-lg-12">

                                                                    <div class="hpanel hgreen">

                                                                        <div class="panel-heading">

                                                                            <div class="panel-tools">

                                                                                <a class="add_day"><i class="fa fa-plus"></i></a>

                                                                            </div>

                                                                            Elegir dias

                                                                        </div>

                                                                        <div class="panel-body" style="display: block;">

                                                                            <div class="row" id="days_container"></div>

                                                                        </div>

                                                                        <div class="panel-footer" style="display: block;">

                                                                            <span class="pull-right"></span>

                                                                        </div>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                            <div class="row">

                                                                <div class="col-lg-12">

                                                                    <div class="hpanel hgreen">

                                                                        <div class="panel-heading">

                                                                            <div class="panel-tools">

                                                                                <a class="add_date"><i class="fa fa-plus"></i></a>

                                                                            </div>

                                                                            Fechas Recurrentes

                                                                        </div>

                                                                        <div class="panel-body" style="display: block;">

                                                                            <div class="row" id="dates_container"></div>

                                                                        </div>

                                                                        <div class="panel-footer" style="display: block;">

                                                                            <span class="pull-right"></span>

                                                                        </div>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                        </div>

                                                        <div class="modal-footer">

                                                            <a class="btn btn-default" data-dismiss="modal">Cerrar</a>

                                                            <a class="btn btn-primary" id="publish_blocked">Guardar</a>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        Fechas Bloqueadas

                                    </div>

                                    <div class="panel-body" style="display: block;">

                                        <div class="row" id="block_link">

                                            <div class="col-lg-4"></div>

                                            <div class="col-lg-4">

                                                <a style="left: 25%;position: relative;" id="blocked_date" data-toggle="modal" data-target="#myBlockedModal" class="btn btn-success" style="color:white"><i class="fa fa-calendar"></i> Bloquear Fechas</a>

                                            </div>

                                            <div class="col-lg-4"></div>

                                        </div>

                                        <div class="row" id="bdd_container"></div>

                                    </div>

                                    <div class="panel-footer" style="display: block;">

                                        <span class="pull-right"></span>

                                    </div>

                                </div>

                                <input type="hidden" name="insert" value="save" />

                                <a id="add_new_route" class="btn btn-sm btn-primary m-t-n-xs" >Publicar</a>

                                <a id="draft_new_route" class="btn btn-sm btn-primary m-t-n-xs" >Guardar como borrador</a>

                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-lg-10"></div>

                            <div class="form-group col-lg-2"></div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

    <?php

}