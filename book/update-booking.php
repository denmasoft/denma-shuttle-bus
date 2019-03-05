<?php
function updateBooking($id){
    global $wpdb;
    $total_passenger = $_POST['people'];
    $start_date = $_POST['book_date'];
    $bfrom = $_POST['bfrom'];
    $bto = $_POST['bto'];
    $book_cancelled = $_POST['book_cancelled'];
    $book_cancelled= ($book_cancelled=='on' || $book_cancelled=='1')?1:0;
    $curdate = new \DateTime();
    $data = array('TOTAL' => $total_passenger,'START_DATE'=>$start_date,'START_POINT'=>$bfrom,'END_POINT'=>$bto,'CANCELLED'=>$book_cancelled,'UPDATED_AT'=>$curdate->format('Y-m-d H:i:s'));
    $wpdb->update($wpdb->prefix.'shuttle_order',$data,array('ID'=>$id),array('%s','%s'));
    $order = $wpdb->get_row($wpdb->prepare("select o.ID as oid,o.TOTAL as total, st.ID as stid,e.ID as eid,r.ID as rid FROM ".$wpdb->prefix."shuttle_order o INNER JOIN ".$wpdb->prefix."shuttle_route r ON o.ROUTE=r.ID INNER JOIN ".$wpdb->prefix."shuttle_stop st ON o.START_POINT=st.ID INNER JOIN ".$wpdb->prefix."shuttle_stop e ON o.END_POINT=e.ID WHERE o.ID=%s",$id));
    $rft = $wpdb->get_row($wpdb->prepare("SELECT s.POS as sfrom,sp.POS as sto from ".$wpdb->prefix."shuttle_route_stop s INNER JOIN ".$wpdb->prefix."shuttle_route r ON s.ID_ROUTE=r.ID INNER JOIN ".$wpdb->prefix."shuttle_route_stop sp ON sp.ID_ROUTE=r.ID WHERE s.ID_STOP=$order->stid AND sp.ID_STOP=$order->eid AND r.ID=%s",$order->rid));
    $posix=null;
    foreach (range($rft->sfrom, $rft->sto) as $number)
    {
        $posix[] = $number;
    }
    unset($posix[0]);
    $posix = implode(',',$posix);
    $rp = $wpdb->get_row($wpdb->prepare("SELECT SUM(s.PRICE) as price from ".$wpdb->prefix."shuttle_route_stop s INNER JOIN ".$wpdb->prefix."shuttle_route r ON s.ID_ROUTE=r.ID WHERE s.POS IN ($posix) AND r.ID=%s",$order->rid));
    $price = $rp->price * $order->total;
    $wpdb->update($wpdb->prefix.'shuttle_order',array('AMMOUNT'=>$price),array('ID'=>$id),array('%s','%s'));
}
function update_booking () {
    $id = $_GET['id'];
        if(isset($_POST['update']))
        {
            updateBooking($id);
        }
    global $wpdb;
    $order = $wpdb->get_row($wpdb->prepare("select o.ID as oid,o.START_DATE as start_date,o.CREATED_AT as createdat,o.HRS as hrs, o.NO_ORDER as no_order,o.TOTAL as total, o.AMMOUNT as amount,o.CANCELLED as ocancel, r.ID as rid,r.NAME as rnm,r.PEOPLE as travelers, st.ID as stid, st.ADDRESS as saddress,e.ID as eid, e.ADDRESS as eaddress,c.id as cid, c.NAME as cnm, c.LAST_NAME as clnm FROM ".$wpdb->prefix."shuttle_order o INNER JOIN ".$wpdb->prefix."shuttle_route r ON o.ROUTE=r.ID INNER JOIN ".$wpdb->prefix."shuttle_stop st ON o.START_POINT=st.ID INNER JOIN ".$wpdb->prefix."shuttle_stop e ON o.END_POINT=e.ID INNER JOIN ".$wpdb->prefix."shuttle_client c ON o.CLIENT=c.ID WHERE o.ID=%s",$id));
    $total_ordered = $wpdb->get_row($wpdb->prepare("SELECT SUM(o.TOTAL) as total from ".$wpdb->prefix."shuttle_route r LEFT JOIN ".$wpdb->prefix."shuttle_order o ON r.ID=o.ROUTE WHERE r.ID=%s AND o.START_DATE=%s AND o.CANCELLED=%s AND r.DRAFT=%s GROUP BY r.ID",$order->rid,$order->start_date,0,0));
    $stops = $wpdb->get_results($wpdb->prepare("SELECT s.ID as sid, s.ADDRESS as saddr,rs.POS as rspos from ".$wpdb->prefix."shuttle_stop s INNER JOIN ".$wpdb->prefix."shuttle_route_stop rs ON s.ID=rs.ID_STOP INNER JOIN ".$wpdb->prefix."shuttle_route r ON r.ID=rs.ID_ROUTE WHERE rs.ID_ROUTE=%s order by s.ADDRESS ASC",$order->rid));
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
        var total_stops = 0;
    </script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <div class="wrap">
        <h2>Reserva <?php echo $order->no_order; ?>
            <a class="btn btn-success" href="<?php echo admin_url('admin.php?page=book_list')?>">&laquo; ir a las reservas</a>
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
                    <input type="hidden" name="route_id" id="route_id" value="<?php echo $order->oid;?>">
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label for="from" class="col-sm-2 control-label" style="line-height: 35px;">Cliente:</label>
                                <div class="col-sm-10">
                                    <input type="text" readonly class="form-control" name="route_name" id="route_name" value="<?php echo $order->cnm.' '.$order->clnm;?>">
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="people" class="col-sm-2 control-label" style="line-height: 35px;">Viajeros:</label>
                                <div class="col-sm-10">
                                    <input type="number" min="1" max="<?php echo $order->travelers - $total_ordered;?>" class="route-input form-control" name="people" id="people" value="<?php echo $order->total;?>">
                                </div>
                            </div>
                             <div style="display:none"><div class="stopc"></div><button id="hsub" type="submit" value="save" name="insert"></button></div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label for="from" class="col-sm-2 control-label" style="line-height: 35px;">Fecha de Salida:</label>
                                <div class="col-sm-3">
                                    <input type="text" readonly class="form-control" name="book_date" id="book_date" value="<?php echo $order->start_date;?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label for="from" class="col-sm-2 control-label" style="line-height: 35px;">Desde:</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="bfrom" id="bfrom" data-route="<?php echo $order->rid;?>">
                                        <?php foreach ($stops as $stop){
                                            if($stop->sid==$order->eid){continue;}
                                            $selected = $stop->sid==$order->stid?'selected':'';
                                        ?>
                                        <option <?php echo $selected;?> value="<?php echo $stop->sid.'_'.$stop->rspos;?>"><?php echo $stop->saddr;?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="from" class="col-sm-2 control-label" style="line-height: 35px;">Hasta:</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="bto" id="bto" data-route="<?php echo $order->rid;?>">
                                        <?php foreach ($stops as $stop){
                                            if($stop->sid==$order->stid){continue;}
                                        $selected = $stop->sid==$order->eid?'selected':'';
                                        ?>
                                        <option <?php echo $selected;?> value="<?php echo $stop->sid.'_'.$stop->rspos;?>"><?php echo $stop->saddr;?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label for="from" class="col-sm-2 control-label" style="line-height: 26px;">Cancelada:</label>
                                <div class="col-sm-10">
                                    <?php
                                    $cancel = $order->ocancel==1?'checked':'';
                                    ?>
                                    <input <?php echo $cancel;?> type="checkbox" class="form-control" name="book_cancelled" id="book_cancelled">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-10"></div>
                            <div class="form-group col-lg-2">
                                <button id="update_book" type="submit" class="btn btn-sm btn-primary m-t-n-xs" name="update" value='Guardar'>Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}