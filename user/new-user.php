<?php

function new_taxi_driver () {
    $dni = $_POST["dni"];
    $name = $_POST["name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $cell = $_POST["cell"];
    if(isset($_POST['insert']))
    {
        global $wpdb;
        $image = null;
        if(isset($_FILES['image']) && $_FILES['image']['name']!='')
        {
            $img = uploadFile($_FILES['image'],shuttle_bus_upload_user_dir(),$dni);
            if($img!=1 && $img!=2)
            {
                $image = $img;
            }
        }
        $data = array('DNI' => $dni,'NAME'=>$name,'LAST_NAME' => $last_name,'EMAIL'=>$email,'PHONE'=>$phone,'CELL'=>$cell);
        if($image!=null)
            $data['IMAGE']=$image;
        $wpdb->insert($wpdb->prefix.'shuttle_driver',$data,array('%s','%s'));
    }
    ?>
    <div class="wrap">
        <h2>Nuevo taxista
            <a class="btn btn-success" href="<?php echo admin_url('admin.php?page=user_list')?>">&laquo; ir a taxistas</a>
        </h2>
        <?php if (isset($message)): ?><div class="updated"><p><?php echo $message;?></p></div><?php endif;?>
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body">
                    <form role="form" id="tdForm" novalidate="novalidate" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label for="name">Nombre</label>
                                <input type="text" id="name" name="name" placeholder="nombre" class="form-control" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="last_name">Apellidos</label>
                                <input type="text" id="last_name" placeholder="Apellidos" class="form-control" name="last_name" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="last_name">Dni</label>
                                <input type="text" id="dni" placeholder="dni" class="form-control" name="dni" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label for="number">Teléfono fijo</label>
                                <input type="tel" id="phone" placeholder="Teléfono" class="form-control" name="phone" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="number">Teléfono celular</label>
                                <input type="tel" id="cell" placeholder="Teléfono" class="form-control" name="cell" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="number">Correo electrónico</label>
                                <input type="email" id="email" placeholder="Correo electrónico" class="form-control" name="email">
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
                                <input type="hidden" name="insert" value='Save'>
                                <a id="register_driver" class="btn btn-sm btn-primary m-t-n-xs">
                                    <strong>Registrar</strong>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}