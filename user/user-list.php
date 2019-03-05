<?php
function user_list () {
    ?>
    <div class="wrap">
        <h2>Taxistas
            <a class="btn btn-success" href="<?php echo admin_url('admin.php?page=new_taxi_driver'); ?>">Nuevo taxista</a>
        </h2>

        <?php
        global $wpdb;
        $rows = $wpdb->get_results("select t.IMAGE as timg,t.ID as tid,t.DNI as tdni, t.NAME as tnm, t.LAST_NAME as tlnm, t.EMAIL as temail,d.TYPE as dnm from ".$wpdb->prefix."shuttle_driver t LEFT JOIN ".$wpdb->prefix."shuttle_taxi d ON d.DRIVER=t.ID");
        echo "<table id=\"user\" class=\"table table-striped table-bordered\" cellspacing=\"0\" width=\"100%\">";
        echo "<thead>
            <tr>
                <th></th>
                <th>Dni</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Email</th>
                <th>Taxi</th>
                <th></th>
            </tr>
        </thead><tbody>";
        foreach ($rows as $row ){
            $taxi = $row->dnm?:'-';
            $uploads = wp_upload_dir();
            $img = $uploads['baseurl'].'/shuttlebus/users/'.$row->timg;
            echo "<tr>";
            echo "<td style='width: 15%;'><img width='100%' src='".$img."'/></td>";
            echo "<td style='width: 15%;'>$row->tdni</td>";
            echo "<td style='width: 15%;'>$row->tnm</td>";
            echo "<td style='width: 15%;'>$row->tlnm</td>";
            echo "<td style='width: 15%;'>$row->temail</td>";
            echo "<td style='width: 15%;'>$taxi</td>";
            echo "<td style='width: 15%;'>
                    <a href='".admin_url('admin.php?page=update_user&id='.$row->tid)."'><i class='pe-7s-edit'></i></a>
                    <a href='"/*.admin_url('admin.php?page=delete_user&id='.$row->tid)*/."'><i class='pe-7s-trash'></i></a>
                </td>";
            echo "</tr>";}
        echo "</tbody></table>";
        ?>
    </div>
    <?php
}