<?php
function book_list () {
    ?>
    <script>
        var update=true;
        var total_stops = 0;
    </script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <div class="wrap">
        <h2>Reservas</h2>

        <?php
        global $wpdb;

        $rows = $wpdb->get_results("select o.ID as oid,o.START_DATE as start_date,o.CREATED_AT as createdat,o.HRS as hrs, o.NO_ORDER as no_order,o.TOTAL as total, o.AMMOUNT as amount,o.CANCELLED as ocancel, r.ID as rid,r.NAME as rnm,st.ID as stid, st.ADDRESS as saddress,e.ID as eid, e.ADDRESS as eaddress,c.id as cid, c.NAME as cnm, c.LAST_NAME as clnm FROM ".$wpdb->prefix."shuttle_order o INNER JOIN ".$wpdb->prefix."shuttle_route r ON o.ROUTE=r.ID INNER JOIN ".$wpdb->prefix."shuttle_stop st ON o.START_POINT=st.ID INNER JOIN ".$wpdb->prefix."shuttle_stop e ON o.END_POINT=e.ID INNER JOIN ".$wpdb->prefix."shuttle_client c ON o.CLIENT=c.ID group by o.ID");
        echo "<table id=\"routes\" class=\"table table-striped table-bordered\" cellspacing=\"0\" width=\"100%\">";
        echo "<thead>
            <tr>
                <th>No. Pedido</th>
                <th>Fecha Reserva</th>
                <th>Fecha Salida</th>
                <th>Hora Salida</th>
                <th>Cliente</th>                
                <th>Viajeros</th>
                <th>Total</th>
                <th>Cancelada</th>
                <th></th>
            </tr>
        </thead><tbody>";
        foreach ($rows as $row ){
            $client = $row->cnm.' '.$row->clnm;
            $route = $row->saddress.'-'.$row->eaddress;
            $cancel = $row->ocancel==1?'Sí':'No';
            echo "<tr>";
            echo "<td>$row->no_order</td>";
            echo "<td>$row->createdat</td>";
            echo "<td>$row->start_date</td>";
            echo "<td>$row->hrs</td>";
            echo "<td>$client</td>";
            echo "<td>$row->total</td>";
            echo "<td>&euro;$row->amount</td>";
            echo "<td>$cancel</td>";
            echo "<td>
                    <a href='".admin_url('admin.php?page=update_booking&id='.$row->oid)."'><i class='pe-7s-edit'></i></a>
                    <a class='cancel_booking' data-booking='".$row->oid."'><i class='pe-7s-close-circle'></i></a>
                    </td>";
            echo "</tr>";}
        echo "</tbody></table>";
        ?>
    </div>
    <?php
}