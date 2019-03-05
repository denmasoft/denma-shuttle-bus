<?php
function delete_taxi () {
    global $wpdb;
    $id = $_GET["id"];
    $name=$_POST["name"];

    if(isset($_POST['delete'])){
        $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."child WHERE id = %s",$id));
    }
    $taxi = $wpdb->get_results($wpdb->prepare("SELECT ID,TYPE from ".$wpdb->prefix."taxi where ID=%s",$id));
    ?>
    <link type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Taxis
            <a class="btn btn-success" href="<?php echo admin_url('admin.php?page=taxi_list')?>">&laquo; ir a taxis</a>
        </h2>

            <form class="form-inline" role="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <input type="hidden" name="id" value="<?php echo $taxi->ID; ?>">
                <div class="form-group">
                    <label for="name">Taxi:</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo $name;?>">
                </div>
                <input type='submit' name="delete" value='Eliminar' class='button' onclick="return confirm('&iquest;Est&aacute;s seguro de borrar este taxi?')">
            </form>

    </div>
    <?php
}