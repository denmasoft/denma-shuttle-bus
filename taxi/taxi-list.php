<?php
function taxi_list () {
    ?>
    <div class="wrap">
        <h2>Taxis
            <a class="btn btn-success" href="<?php echo admin_url('admin.php?page=new_taxi'); ?>">Registrar taxi</a>
        </h2>

        <?php
        global $wpdb;
        $rows = $wpdb->get_results("select t.ID as tid, t.TYPE as tname,t.IMAGE as timg,d.NAME as dnm, d.LAST_NAME as dlnm,r.ID as rid,r.NAME as rnm  from ".$wpdb->prefix."shuttle_taxi t LEFT JOIN ".$wpdb->prefix."shuttle_driver d ON t.DRIVER=d.ID LEFT JOIN ".$wpdb->prefix."shuttle_route r ON t.ROUTE=r.ID ");
        echo "<table id=\"taxi\" class=\"table table-striped table-bordered\" cellspacing=\"0\" width=\"100%\">";
        echo "<thead>
            <tr>
                <th></th>
                <th>Type</th>                
                <th>Driver</th>
                <th>Route</th>                
                <th></th>
            </tr>
        </thead><tbody>";
        foreach ($rows as $row ){
            $uploads = wp_upload_dir();
            $img = $uploads['baseurl'].'/shuttlebus/taxis/'.$row->timg;
            echo "<tr>";
            echo "<td style='width: 15%;'><img  width='100%' src='".$img."'/></td>";
            echo "<td style='width: 15%;'>$row->tname</td>";
            echo "<td style='width: 15%;'>$row->dnm $row->dlnm</td>";
            echo "<td style='width: 15%;'>$row->rnm</td>";
            echo "<td style='width: 15%;'> 
                    <a href='".admin_url('admin.php?page=update_taxi&id='.$row->tid)."'><i class='pe-7s-edit'></i></a>
                    <a href='"/*admin_url('admin.php?page=delete_taxi&id='.$row->tid)*/."'><i class='pe-7s-trash'></i></a>
                </td>";
            echo "</tr>";}
        echo "</tbody></table>";
        ?>
    </div>
    <?php
}