<?php
function updatedDates($blocked)

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

                $blocked_from = $day['from_hrs'].':'.$day['from_mins'];

                $blocked_to = $day['to_hrs'].':'.$day['to_mins'];

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

        /*else{

            $args = array('start_date'=>$start_date,'end_date'=>$end_date,'from_hrs'=>$from_hrs,'to_hrs'=>$to_hrs);

            $dates[] = getAvailableDates($args);

        }*/

        if($dates==null)

        {

            $result[] = array('start_date'=>$start_date.' '.$from_hrs,'end_date'=>$end_date.' '.$to_hrs,'days'=>$dates);

        }

        else{

            $result[] = array('start_date'=>$start_date,'end_date'=>$end_date,'days'=>$dates);

        }

    }

    return $result;

}

function updatedBlockedDays($dd_blocked){

    $dates = null;

    foreach($dd_blocked as $day)

    {

        $dow = $day['day'];

        $dates[]=array('day'=>$dow);

    }

    return serialize($dates);

}

function updatedBlockedMonths($months_blocked){

    $dates = null;

    foreach($months_blocked as $month)

    {

        $moy = $month['month'];

        $dates[]=array('month'=>$moy);

    }

    return serialize($dates);

}

function updateRoute($id){

    global $wpdb;
    $success='';
    $route_name = $_POST["route_name"];
    $people = $_POST["people"];
    $prices = $_POST["price"];
    $pickups = $_POST["pick_up"];
    $hours = $_POST["hours"];
    $minutes = $_POST["minutes"];
    $stops = $_POST["stops"];
    $hstops = $_POST['hstops'];
    $posix = $_POST['posix'];
    $buses = $_POST["buses"];
    $to = $stops[count($stops) - 1];
    $from = $stops[0];
    $pblocked = null;
    $pranges = null;
    $count = 0;
    if($route_name=='')
    {
        $route_name = $from.'-'.$to;
    }
    $originalRoute =  $route_name;
    $draft=0;
    if(isset($_POST['draft']) && $_POST['draft']==2)
    {
        $route_name.='-copia';

        $copts = $wpdb->get_results("SELECT s.ID from ".$wpdb->prefix."shuttle_route s where s.NAME like '$route_name%'");

        $c = count($copts) + 1;

        $route_name = $route_name.' ('.$c.')';

        $curdate = new \DateTime();
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

        /*$data = array('ranges'=>$pranges,'bdd'=>$pblocked);

        $data = serialize($data);

        $wpdb->insert($wpdb->prefix.'shuttle_route_available',array('ROUTE'=>$route,'DATA'=>$data,'CREATEDAT'=>$created_at,'UPDATEDAT'=>$created_at,'VERSION'=>0),array('%s','%s'));

        */

        $count = 0;

        foreach($hstops as $hstop)

        {

            $price = $prices[$count];
            $pick_up = $pickups[$count];

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

        $success='La ruta '.$originalRoute.' ha sido clonada ('.$route_name.')';

    }

    else{
        $start_end = $wpdb->get_row($wpdb->prepare("SELECT s.ID as sf, e.ID as se FROM ".$wpdb->prefix."shuttle_stop s,".$wpdb->prefix."shuttle_stop e WHERE s.ADDRESS=%s and e.ADDRESS=%s",$from,$to));
        $wpdb->update($wpdb->prefix.'shuttle_route',array('NAME'=>$route_name,'START_POINT' => $from,'END_POINT'=>$to,'PEOPLE'=>$people,'DRAFT'=>$draft,'start'=>$start_end->sf,'end'=>$start_end->se,'PICK_UP'=>$pick_up),array('ID'=>$id),array('%s','%s'));

        $created_at = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))->format('Y-m-d H:i:s');

        if(isset($_POST['daysb']) || isset($_POST['daysf']))

        {

            $updated_at = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));

            //$rv = $wpdb->get_row($wpdb->prepare("select a.ID as aid,a.VERSION as version FROM ".$wpdb->prefix."shuttle_route_available a where a.ROUTE=%s ORDER BY a.VERSION DESC LIMIT 1",$id));

            //$version= $rv->version > 0 ?  $rv->version++: 1;

            $wpdb->delete($wpdb->prefix.'shuttle_route_available',array('ROUTE'=>$id),array('%s','%s'));

        }

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

            $wpdb->insert($wpdb->prefix.'shuttle_route_available',array('ROUTE'=>$id,'DATA'=>$dt,'CREATEDAT'=>$created_at,'UPDATEDAT'=>$created_at,'VERSION'=>0),array('%s','%s'));

        }

        /*$data = array('ranges'=>$pranges,'bdd'=>$pblocked);

        $data = serialize($data);

        //$wpdb->update($wpdb->prefix.'shuttle_route_available', array('ROUTE'=>$id),array('%s','%s'));

        $wpdb->insert($wpdb->prefix.'shuttle_route_available',array('ROUTE'=>$id,'DATA'=>$data,'CREATEDAT'=>$created_at,'UPDATEDAT'=>$created_at,'VERSION'=>0),array('%s','%s'));

        */

        if(isset($_POST['stops']))

        {

            $count = 0;

            $temps = $wpdb->get_results("SELECT s.ID from ".$wpdb->prefix."shuttle_stop s INNER JOIN ".$wpdb->prefix."shuttle_route_stop rs ON s.ID=rs.ID_STOP WHERE rs.ID_ROUTE=$id order by rs.POS ASC");

            foreach ($temps as $temp)

                $astops[] = $temp->ID;

            foreach($hstops as $hstop)

            {

                $price = $prices[$count];
                $pick_up = $pickups[$count];

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

                        $wpdb->insert($wpdb->prefix.'shuttle_route_stop',array('ID_ROUTE' => $id,'ID_STOP'=>$sid,'PRICE'=>$price,'POS'=>$pos,'HRS'=>$hrs.':'.$mins,'PICK_UP'=>$pick_up),array('%s','%s'));

                    }

                    else{

                        $wpdb->insert($wpdb->prefix.'shuttle_route_stop',array('ID_ROUTE' => $id,'ID_STOP'=>$sstop->sid,'PRICE'=>$price,'POS'=>$pos,'HRS'=>$hrs.':'.$mins,'PICK_UP'=>$pick_up),array('%s','%s'));

                    }

                }

                else

                {

                    //$key = array_search($hstop, $astops);

                    if(in_array($hstop, $astops))

                    {

                        $wpdb->update($wpdb->prefix.'shuttle_stop',array('ADDRESS' => $stops[$count]),array('ID'=>$hstop),array('%s','%s'));

                        $wpdb->update($wpdb->prefix.'shuttle_route_stop',array('PRICE'=>$price,'POS'=>$pos,'HRS'=>$hrs.':'.$mins,'PICK_UP'=>$pick_up),array('ID_STOP'=>$hstop,'ID_ROUTE'=>$id),array('%s','%s'));

                    }

                    else{

                        $wpdb->insert($wpdb->prefix.'shuttle_route_stop',array('ID_ROUTE' => $id,'ID_STOP'=>$hstop,'PRICE'=>$price,'POS'=>$pos,'HRS'=>$hrs.':'.$mins,'PICK_UP'=>$pick_up),array('%s','%s'));

                    }

                }

                $count++;

            }

            foreach($astops as $astop)

            {

                if(!in_array($astop, $hstops))

                {

                    $wpdb->delete($wpdb->prefix.'shuttle_route_stop', array('ID_STOP'=>$astop,'ID_ROUTE'=>$id),array('%s','%s'));

                }

            }

        }

        $success = 'La ruta '. $originalRoute.' ha sido modificada.';

    }

    return $success;

    /*if(isset($_POST['mstops']))

    {

        $route_stops = $wpdb->get_results($wpdb->prepare("SELECT s.ID as sid FROM ".$wpdb->prefix."shuttle_stop s where s.ROUTE=%s",$id));

        $mstops = $_POST['mstops'];

        $mprices = $_POST['mprice'];

        $mhours = $_POST['mhours'];

        $mhours = $_POST['mminutes'];

        $mprice = $mprices[$count];

        $mhrs = $mhours[$count];

        $mmins = $mminutes[$count];

        /*foreach ($route_stops as $rstop) {

            if()

        }

        foreach ($mstops as $mstop) {

            

        }*/

   // }

    /*if(isset($_POST['stops']))

    {

        $route_stops = $wpdb->get_results($wpdb->prepare("SELECT s.ID as sid FROM ".$wpdb->prefix."shuttle_stop s where s.ROUTE=%s",$id));

        $count = 0;

        $rs = null;

        if(isset($_POST['route_stops']))

        {

            $rs =$_POST['route_stops'];

            foreach ($route_stops as $route_stop)

            {

                if(!in_array($route_stop->sid, $rs))

                {

                    $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."shuttle_stop s where s.ID=%s",$route_stop->sid));

                }                

            }

        }

        foreach ($stops as $stop)

        {

            $price = $prices[$count];

            $hrs = $hours[$count];

            $mins = $minutes[$count];

            if(isset($_POST['route_stops']))

            {

                $rs = $_POST['route_stops'];

                $rt = $rs[$count];

                foreach($route_stops as $route_stop)

                {

                    if(in_array($route_stop->sid, $rs))

                    {

                        $wpdb->update($wpdb->prefix.'shuttle_stop',array('ADDRESS' => $stop,'ROUTE'=>$id,'PRICE'=>$price,'HRS'=>$hrs.':'.$mins),array('ID'=>$route_stop->sid),array('%s','%s'));

                    }

                }                

                

            }

            $wpdb->insert($wpdb->prefix.'shuttle_stop',array('ADDRESS' => $stop,'ROUTE'=>$id,'PRICE'=>$price,'POS'=>$count+1,'HRS'=>$hrs.':'.$mins),array('%s','%s'));

            $count++;

        }

    }*/



}

function update_route () {

    $id = $_GET["id"];

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

                  $success = updateRoute($id);

            }

        }

    global $wpdb;

    $route = $wpdb->get_row($wpdb->prepare("select r.ID as rid,r.NAME as rnm,r.START_POINT as rsp, r.END_POINT as rep,r.PEOPLE as travelers,r.DRAFT as draft,r.PICK_UP as pick_up FROM ".$wpdb->prefix."shuttle_route r where ID=%s",$id));



    $copts = $wpdb->get_results("SELECT s.ID as total from ".$wpdb->prefix."shuttle_stop s INNER JOIN ".$wpdb->prefix."shuttle_route_stop rs ON s.ID=rs.ID_STOP WHERE rs.ID_ROUTE=$id order by rs.POS ASC");



    ?>

    <link type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />

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

    </style>

    <script>

        var update=true;

        var total_stops = <?php echo count($copts); ?>;

    </script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <div class="wrap">

        <h2>Ruta <?php echo $route->rnm.' '.$route->draft==1?' (Borrador)':''; ?>

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

                    <input type="hidden" name="route_id" id="route_id" value="<?php echo $route->rid;?>">

                        <div class="row">

                            <div class="form-group col-lg-4">

                                <label for="from" class="col-sm-2 control-label" style="line-height: 35px;">Nombre:</label>

                                <div class="col-sm-10">

                                    <input type="text" class="route-input form-control" name="route_name" id="route_name" value="<?php echo $route->rnm;?>">

                                </div>

                            </div>

                            <div class="form-group col-lg-4">

                                <label for="people" class="col-sm-2 control-label" style="line-height: 35px;">Viajeros:</label>

                                <div class="col-sm-10">

                                    <input type="number" min="1" class="route-input form-control" name="people" id="people" value="<?php echo $route->travelers;?>">

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

                                        <div class="stopc row" id="stop_container">

                                            <?php

                                            global $wpdb;

                                            $stops = $wpdb->get_results("SELECT s.ID,s.ADDRESS,rs.PRICE,rs.POS as pos,rs.HRS as hours,rs.PICK_UP as pickup from ".$wpdb->prefix."shuttle_stop s INNER JOIN ".$wpdb->prefix."shuttle_route_stop rs ON s.ID=rs.ID_STOP WHERE rs.ID_ROUTE=$route->rid order by rs.POS ASC");

                                            $count = 0;

                                            foreach($stops as $stop){

                                                list($hrs,$mins) = explode(':',$stop->hours);

                                            ?>                                                

                                                <div id="drag_<?php echo $stop->pos; ?>" class="drag-area row" data-pos="<?php echo $stop->pos; ?>">

                                                    <div class="form-group col-sm-4">
                                                        <div class="col-sm-3">
                                                            <label for="from" class="control-label" style="line-height: 35px;">parada:</label>
                                                        </div>
                                                        <div class="col-sm-9">
                                                            <input data-pos="<?php echo $stop->pos; ?>" id="posix_drag_<?php echo $stop->pos; ?>" type="hidden" name="posix[]" value="<?php echo $stop->pos; ?>"/>
                                                            <input data-count="<?php echo $count; ?>" id="hstop_<?php echo $count; ?>" type="hidden" name="hstops[]" value="<?php echo $stop->ID;?>"/>
                                                            <input data-count="<?php echo $count; ?>" id="stop_<?php echo $count; ?>" type="text" name="stops[]" class="typeahead input_route route-input stop_address" value="<?php echo $stop->ADDRESS;?>" />
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-3">
                                                        <div class="col-sm-4">
                                                            <label for="to" class="control-label" style="line-height: 35px;">Recogida:</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <input class="route-input form-control" type="text" class="form-control" name="pick_up[]" id="pick_up_<?php echo $count; ?>" value="<?php echo $stop->pickup;?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-2">
                                                        <div class="col-sm-4">
                                                            <label for="to" class="control-label" style="line-height: 35px;">Precio:</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <input class="input_price route-input form-control" type="text" class="form-control" name="price[]" id="price_<?php echo $count; ?>" value="<?php echo $stop->PRICE;?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-3">
                                                        <div class="col-sm-3">
                                                            <label for="to" class="control-label" style="line-height: 35px;">Hora:</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <select id="hours_<?php echo $count; ?>" name="hours[]" class="custom-control" style="border-radius: 4px;">

                                                                <?php for($i=0;$i<=23;$i++){?>
                                                                    <option <?php echo $hrs==str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected':''; ?> value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT);?>"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT);?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <span class="dot">:</span>
                                                            <select id="minutes_<?php echo $count; ?>" name="minutes[]" class="custom-control" style="border-radius: 4px;">
                                                                <option value="0">00</option>
                                                                <?php $pivot = 5;for($i=0;$i<11;$i++){
                                                                    ?>
                                                                    <option <?php echo $mins==str_pad($pivot, 2, '0', STR_PAD_LEFT) ? 'selected':''; ?> value="<?php echo str_pad($pivot, 2, '0', STR_PAD_LEFT);?>"><?php echo str_pad($pivot, 2, '0', STR_PAD_LEFT);?></option>
                                                                    <?php $pivot+=5;} ?>
                                                            </select>
                                                        </div>
                                                        <a class="remove" id="remove_<?php echo $count; ?>" style="line-height: 35px;font-size: 20px;"><i class="pe-7s-trash"></i></a>
                                                    </div>
                                                </div>

                                                <?php $count++;}?>

                                        </div>

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

                                        <?php $availables = $wpdb->get_row("SELECT a.ID as aid,a.DATA as rdata from ".$wpdb->prefix."shuttle_route_available a WHERE a.ROUTE=$id");

                                            $display = isset($availables)?'none':'block';

                                        ?>

                                        <div class="row" id="block_link" style="display: <?php echo $display;?>">

                                            <div class="col-lg-4"></div>

                                            <div class="col-lg-4">

                                                <a style="left: 25%;position: relative;" id="blocked_date" data-toggle="modal" data-target="#myBlockedModal" class="btn btn-success" style="color:white"><i class="fa fa-calendar"></i> Bloquear Fechas</a>

                                            </div>

                                            <div class="col-lg-4"></div>

                                        </div>

                                        <div class="row" id="bdd_container">

                                            <?php

                                            global $wpdb;

                                            $count = 0;

                                            $summary='';$days=null;

                                            $concat=null;

                                            $available = $wpdb->get_row("SELECT a.ID as aid,a.DATA as rdata from ".$wpdb->prefix."shuttle_route_available a WHERE a.ROUTE=$id");

                                            if(isset($available)){

                                                $dias=null;

                                                if($available->rdata!=null)

                                                {

                                                    $dows=['','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];

                                                    $data = unserialize($available->rdata);

                                                    $days = $data['bdd'];

                                                    $ranges = $data['ranges'];

                                                    if(isset($data['bdd']) && $data['bdd']!=null)

                                                    {

                                                        $days = implode(',', $days);

                                                        if($days!=null && $days!='')

                                                        {

                                                            $summary.='Dias bloqueados: '.$days.'<br>';

                                                        }

                                                    }

                                                    if(isset($data['ranges']) && $data['ranges']!=null)

                                                    {

                                                        foreach ($ranges as $range)

                                                        {

                                                            if(isset($range))

                                                            {

                                                                $summary.=($range['DAYS']!=null)? 'Bloqueado ':'';

                                                                $dias = $range['DAYS'];

                                                                $dys = explode(',',$range['DAYS']);

                                                                $dayString=null;

                                                                foreach($dys as $dy)

                                                                {

                                                                    $dayString[] = $dows[$dy];

                                                                }

                                                                $dayString = implode(',',$dayString );

                                                                $summary.= ($range['DAYS']!=null)?'todos los '.$dayString:'';

                                                                if(\DateTime::createFromFormat('d/m/Y', $range['OPEN_DATE'])!==FALSE){

                                                                    $summary.= ($range['OPEN_DATE']!=null) ?' desde '.$range['OPEN_DATE']:'';

                                                                }

                                                                if(\DateTime::createFromFormat('d/m/Y', $range['CLOSE_DATE'])!==FALSE){

                                                                    $summary.= ($range['CLOSE_DATE']!=null) ?' hasta '.$range['CLOSE_DATE']:'';

                                                                }

                                                                //$summary.=$desde.$hasta;

                                                                switch ($range['PERIOD'])

                                                                {

                                                                    case 1:

                                                                        $summary.=' de cada Semana';

                                                                        break;

                                                                    case 2:

                                                                        $summary.=' de cada Mes';

                                                                        break;

                                                                    case 3:

                                                                        $summary.=' de cada Año';

                                                                        break;

                                                                    default:break;

                                                                }

                                                                $summary.='</br>';

                                                                $concat[] = $dias.'_'.$range['OPEN_DATE'].'_'.$range['CLOSE_DATE'].'_'.$range['PERIOD'];

                                                            }

                                                        }

                                                    }

                                                }?>

                                                <div class="row available_item_<?php echo $count; ?>" data-index="<?php echo $count; ?>">

                                                    <div class="col-lg-12">

                                                        <div class="hpanel">

                                                            <div class="panel-heading">

                                                                <div class="panel-tools" style="margin-top: -10px;">

                                                                    <a class="closebox"><i class="fa fa-times"></i></a>

                                                                    <a class="edit_blocked" data-days="<?php echo $days;?>" data-recur="<?php $conct=implode('-',$concat );echo $conct;?>" data-type="day" data-item="<?php echo $count; ?>" data-toggle="modal" data-target="#myBlockedModal"><i class="fa fa-pencil"></i></a>

                                                                </div>

                                                            </div>

                                                            <div class="panel-body">

                                                                <p><?php echo $summary; ?></p>

                                                                

                                                            </div>

                                                            <div class="panel-footer"></div>

                                                        </div>

                                                    </div>



                                        </div>

                                        <?php } ?>

                                    </div>

                                    <div class="panel-footer" style="display: none;">

                                        <span class="pull-right"></span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-lg-10"></div>

                            <div class="form-group col-lg-2">

                                <?php

                                if($route->draft==1){?>

                                  <a id="publish_route" class="btn btn-sm btn-primary m-t-n-xs" name="update" value='Update'>Publicar</a>

                                  <a id="keep_draft_route" class="btn btn-sm btn-primary m-t-n-xs" name="update" value='Update'>Mantener como borrador</a>

                                <?php }else{

                                ?>

                                <a id="add_new_route" class="btn btn-sm btn-primary m-t-n-xs" name="update" value='Update'>guardar</a>

                                <a id="clone_route" class="btn btn-sm btn-primary m-t-n-xs" name="update" value='Update'>Clonar ruta</a>

                                <?php }?>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

    <?php

}