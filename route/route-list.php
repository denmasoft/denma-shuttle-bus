<?php
function route_list () {
    ?>
    <script>var update=false;
        var total_stops = 0;
    </script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <div class="wrap">
        <h2>Rutas
            <a class="btn btn-success" href="<?php echo admin_url('admin.php?page=new_route'); ?>">Nueva ruta</a>
        </h2>

        <?php
        global $wpdb;

        $rows = $wpdb->get_results("select (SELECT COUNT(s.id) from ".$wpdb->prefix."shuttle_stop s inner join ".$wpdb->prefix."shuttle_route_stop rs ON s.ID=rs.ID_STOP where rs.ID_ROUTE=r.ID) as stops, r.ID as rid,r.NAME as rnm,r.START_POINT as rsp, r.END_POINT as rep,r.PEOPLE as travelers,r.DRAFT as draft FROM ".$wpdb->prefix."shuttle_route r");
        echo "<table id=\"routes\" class=\"table table-striped table-bordered\" cellspacing=\"0\" width=\"100%\">";
        echo "<thead>
            <tr>
                <th>Nombre</th>
                <th>Desde</th>
                <th>Hasta</th>
                <th>No. Paradas</th>
                <th>Viajeros</th>
                <th></th>
                <th></th>
            </tr>
        </thead><tbody>";
        foreach ($rows as $row ){
            $draft = $row->draft==1?'Borrador':'';
            echo "<tr>";
            echo "<td>$row->rnm</td>";
            echo "<td>$row->rsp</td>";
            echo "<td>$row->rep</td>";
            echo "<td>$row->stops</td>";
            echo "<td>$row->travelers</td>";
            echo "<td>$draft</td>";
            echo "<td>
                    <a href='".admin_url('admin.php?page=update_route&id='.$row->rid)."'><i class='pe-7s-edit'></i></a>
                    <a class='delete_route' data-route='".$row->rid."'><i class='pe-7s-trash'></i></a>
                    </td>";
            echo "</tr>";}
        echo "</tbody></table>";
        ?>
    </div>
    <?php
}