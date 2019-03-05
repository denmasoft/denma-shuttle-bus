<?php
function delete_user () {
    global $wpdb;
    $id = $_GET["id"];
    $name=$_POST["name"];
//update
    if(isset($_POST['update'])){
        $wpdb->update(
            $wpdb->prefix.'child', //table
            array('name' => $name), //data
            array( 'id' => $id ), //where
            array('%s'), //data format
            array('%s') //where format
        );
    }
//delete
    else if(isset($_POST['delete'])){
        $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."child WHERE id = %s",$id));
    }
    else{//selecting value to update
        $childs = $wpdb->get_results($wpdb->prepare("SELECT id,name from ".$wpdb->prefix."child where id=%s",$id));
        foreach ($childs as $s ){
            $name=$s->name;
        }
    }
    ?>
    <link type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Cumpleañeros
            <a class="btn btn-success" href="<?php echo admin_url('admin.php?page=birthday_child_list')?>">&laquo; ir a cumpleañeros</a>
        </h2>


            <form class="form-inline" role="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <div class="form-group">
                    <label for="name">Nombre del cumpleañero:</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo $name;?>">
                </div>
                <input type='submit' name="update" value='Actualizar' class='button'> &nbsp;&nbsp;
                <input type='submit' name="delete" value='Eliminar' class='button' onclick="return confirm('&iquest;Est&aacute;s seguro de borrar este elemento?')">
            </form>

    </div>
    <?php
}