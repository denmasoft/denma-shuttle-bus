<?php
function update_taxi () {
    global $wpdb;
    $id = $_GET["id"];    
    if(isset($_POST['update']))
    {
        $type = $_POST["type"];
        $driver = $_POST["driver"];
        global $wpdb;
        $image = null;
        if(isset($_FILES['image']))
        {
            $img = uploadFile($_FILES['image'],shuttle_bus_upload_taxis_dir(),$type);
            if($img!=1 && $img!=2)
            {
                $image = $img;
            }
        }
        $data = array('TYPE' => $type,'DRIVER'=>$driver);
        if($image!=null)
            $data['IMAGE']=$image;
        $wpdb->update($wpdb->prefix.'shuttle_user',$data,array('%s','%s'));
    }
    $taxi = $wpdb->get_row($wpdb->prepare("select t.ID as tid, t.TYPE as tname,t.IMAGE as timg,d.ID as did, d.NAME as dnm, d.LAST_NAME as dlnm from ".$wpdb->prefix."shuttle_taxi t INNER JOIN ".$wpdb->prefix."shuttle_driver d ON t.DRIVER=d.ID where ID=%s",$id));

    ?>
    <style>
        .custom-control{
            float: left;
            margin-left: 5px;
            margin-right: 5px;
            width: 40%;
            margin-bottom: 9px;
            padding: 6px 12px !important;
            height:auto !important;
        }
        .cm-control{
            float: left;
            width: 45%;
            padding: 6px 1px !important;
            height:auto !important;
        }
        .dot{
            float: left; line-height: 25px;
        }
        .cm_dot{
            line-height: 25px;
        }

        body.modal-open {
            position: relative !important;
        }

    </style>
    <div class="wrap">
        <h2>Editar taxi
            <a class="btn btn-success" href="<?php echo admin_url('admin.php?page=taxi_list')?>">&laquo; ir a taxis</a>
        </h2>
        <?php if (isset($message)): ?><div class="updated"><p><?php echo $message;?></p></div><?php endif;?>
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body">
                    <form role="form" id="taxiForm" novalidate="novalidate" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
                        <input type="hidden" name="update" value='Update'>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label for="name">Tipo</label>
                                <input type="text" id="type" name="type" placeholder="tipo" class="form-control" value="<?php echo $taxi->tname;?>" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label for="last_name">Taxista</label>
                                <select class="form-control" id="driver" name="driver">
                                    <?php
                                    global $wpdb;
                                    $drivers = $wpdb->get_results("SELECT d.ID,d.NAME,d.LAST_NAME from ".$wpdb->prefix."shuttle_driver d WHERE d.ID NOT IN (SELECT t.DRIVER FROM ".$wpdb->prefix."shuttle_taxi t)");
                                    foreach($drivers as $driver){
                                        ?>
                                        <option <?php echo $driver->ID==$taxi->did ? 'selected': '' ?> value="<?php echo $driver->ID; ?>"><?php echo $driver->NAME.' '.$driver->LAST_NAME; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label for="number">Foto</label>
                                <img src="<?php echo shuttle_bus_upload_taxis_dir().'/'.$taxi->timg; ?>">
                                <input type="file" id="image" class="form-control" name="image">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-10"></div>
                            <div class="form-group col-lg-2">
                                <a  id="register_taxi" class="btn btn-sm btn-primary m-t-n-xs">
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