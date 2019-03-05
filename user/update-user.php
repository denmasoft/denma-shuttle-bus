<?php
function update_user () {
    global $wpdb;
    $id = $_GET["id"];
    $dni = $_POST["dni"];
    $name = $_POST["name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $cell = $_POST["cell"];
    if(isset($_POST['update']))
    {
        global $wpdb;
        $image = null;
        if(isset($_FILES['image']))
        {
            $img = uploadFile($_FILES['image'],$dni);
            if($img!=1 && $img!=2)
            {
                $image = $img;
            }
        }
        $data = array('DNI' => $dni,'NAME'=>$name,'LAST_NAME' => $last_name,'EMAIL'=>$email,'PHONE'=>$phone,'CELL'=>$cell);
        if($image!=null)
            $data['IMAGE']=$image;
        $wpdb->update($wpdb->prefix.'shuttle_driver',$data,array('%s','%s'));
    }
    $driver = $wpdb->get_row($wpdb->prepare("SELECT t.ID as tid,t.DNI as tdni, t.NAME as tnm, t.LAST_NAME as tlnm, t.EMAIL as temail,t.PHONE as tphone, t.CELL as tcell from ".$wpdb->prefix."shuttle_driver t where ID=%s",$id));

    ?>
    <link type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Editar taxista
            <a class="btn btn-success" href="<?php echo admin_url('admin.php?page=user_list')?>">&laquo; ir a taxistas</a>
        </h2>
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body">
                    <form role="form" id="tdForm" novalidate="novalidate" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label for="name">Nombre</label>
                                <input type="text" id="name" name="name" placeholder="nombre" class="form-control" required value="<?php echo $driver->tnm?>">
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="last_name">Apellidos</label>
                                <input type="text" id="last_name" placeholder="Apellidos" class="form-control" name="last_name" required value="<?php echo $driver->tlnm?>">
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="last_name">Dni</label>
                                <input type="text" id="dni" placeholder="dni" class="form-control" name="dni" required value="<?php echo $driver->tdni?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label for="number">Teléfono fijo</label>
                                <input type="tel" id="phone" placeholder="Teléfono" class="form-control" name="phone" required value="<?php echo $driver->tphone?>">
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="number">Teléfono celular</label>
                                <input type="tel" id="cell" placeholder="Teléfono" class="form-control" name="cell" required value="<?php echo $driver->tcell?>">
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="number">Correo electrónico</label>
                                <input type="email" id="email" placeholder="Correo electrónico" class="form-control" name="email" value="<?php echo $driver->temail?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label for="number">Foto</label>
                                <input type="file" id="image" class="form-control" name="image">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-10"></div>
                            <div class="form-group col-lg-2">
                                <button id="register_driver" class="btn btn-sm btn-primary m-t-n-xs" type="submit" name="update" value='Save'>
                                    <strong>Actualizar</strong>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <?php
}