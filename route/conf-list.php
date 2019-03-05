<?php

function conf_list () {
    global $wpdb;
    if(isset($_POST['insert']))
    {
        $dahead = $_POST['dahead'];
        $hahead = $_POST['hahead'];
        $idahead = $_POST['idahead'];
        $mintravelers = $_POST['mintravelers'];
        $headertext = $_POST['headertext'];
        $contacttext = $_POST['contacttext'];
        $host = $_POST['host'];
        $port = $_POST['port'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $logopos = $_POST['logopos'];
        $bodytext = $_POST['bodytext'];
        $image = null;
        if(isset($_FILES['logo']))
        {
            $img = uploadFile($_FILES['logo'],shuttle_bus_upload_email_dir(),'');
            if($img!=1 && $img!=2)
            {
                $image = $img;
            }
        }
        if($_POST['idahead'] > 0)
        {
            if(isset($_FILES['logo']['name']) && $_FILES['logo']['name']!='')
            {
                $wpdb->update($wpdb->prefix.'shuttle_conf',array('DAYS_AHEAD'=>$dahead,'HOURS_AHEAD'=>$hahead,'MIN_TRAVELERS'=>$mintravelers,'EMAIL_LOGO'=>$image,'EMAIL_ADDRESS'=>$email,'HEADER_INFO'=>$headertext,'CONTACT_INFO'=>$contacttext,'HOST'=>$host,'PORT'=>$port,'USERNAME'=>$username,'PASSWORD'=>$password,'LOGO_POS'=>$logopos,'BODY_INFO'=>$bodytext),array('ID'=>$idahead),array('%s','%s'));
            }
            else{
                $wpdb->update($wpdb->prefix.'shuttle_conf',array('DAYS_AHEAD'=>$dahead,'HOURS_AHEAD'=>$hahead,'MIN_TRAVELERS'=>$mintravelers,'EMAIL_ADDRESS'=>$email,'HEADER_INFO'=>$headertext,'CONTACT_INFO'=>$contacttext,'HOST'=>$host,'PORT'=>$port,'USERNAME'=>$username,'PASSWORD'=>$password,'LOGO_POS'=>$logopos,'BODY_INFO'=>$bodytext),array('ID'=>$idahead),array('%s','%s'));
            }
        }
        else{
            $wpdb->insert($wpdb->prefix.'shuttle_conf',array('DAYS_AHEAD'=>$dahead,'HOURS_AHEAD'=>$hahead,'MIN_TRAVELERS'=>$mintravelers,'EMAIL_LOGO'=>$image,'EMAIL_ADDRESS'=>$email,'HEADER_INFO'=>$headertext,'CONTACT_INFO'=>$contacttext,'HOST'=>$host,'PORT'=>$port,'USERNAME'=>$username,'PASSWORD'=>$password,'LOGO_POS'=>$logopos,'BODY_INFO'=>$bodytext),array('%s','%s'));
        }
    }
    $conf = $wpdb->get_row($wpdb->prepare("select c.ID as cid, c.HOURS_AHEAD as hahead, c.DAYS_AHEAD as dahead,c.MIN_TRAVELERS as mintravelers, c.EMAIL_ADDRESS as addr, c.EMAIL_LOGO as logo, c.HEADER_INFO as iheader,c.CONTACT_INFO as icontact,c.HOST as chost, c.PORT as cport, c.USERNAME as cusername,c.PASSWORD as cpassword,c.LOGO_POS as logopos,c.BODY_INFO as cbody FROM ".$wpdb->prefix."shuttle_conf c",''));
    ?>
    <link type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .btn-toolbar {
            margin-left: 0 !important;
        }
    </style>
    <div class="wrap">
        <h2>Configuración</h2>
        <?php if (isset($message)): ?><div class="updated"><p><?php echo $message;?></p></div><?php endif;?>
        <form class="form" id="aheadForm" role="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-header">
                    Ruta
                </div>
                <div class="panel-body">
                        <input type="hidden" name="idahead" id="idahead" value="<?php echo $conf->cid;?>">
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label for="from" class="control-label" style="line-height: 35px;">Días de antelación para reservar en rutas cerradas:</label>
                                <div class="col-sm-12">
                                    <input type="text" class="route-input form-control" name="dahead" id="dahead" value="<?php echo $conf->dahead;?>">
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="from" class="control-label" style="line-height: 35px;">Horas de antelación para reservar en rutas abiertas:</label>
                                <div class="col-sm-12">
                                    <input type="text" class="route-input form-control" name="hahead" id="hahead" value="<?php echo $conf->hahead;?>">
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="from" class="control-label" style="line-height: 35px;">Número de viajeros mínimo:</label>
                                <div class="col-sm-12">
                                    <input type="text" class="route-input form-control" name="mintravelers" id="mintravelers" value="<?php echo $conf->mintravelers;?>">
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-header">
                    Correo Electronico
                </div>
                <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label for="from" class="control-label" style="line-height: 35px;">Correo Electronico:</label>
                                <div class="col-sm-12">
                                    <input type="email" class="route-input form-control" name="email" id="email" value="<?php echo $conf->addr;?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label for="from" class="control-label" style="line-height: 35px;">Logo:</label>
                                <div class="col-sm-12">
                                    <input type="file" class="route-input form-control" name="logo" id="headerlogo">
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <div class="col-sm-12">
                                    <?php if($conf->logo){ ?>
                                    <img style="width:50%;max-width: 100%;height: auto;" src="<?php echo '/wp-content/uploads/shuttlebus/email/'.$conf->logo;?>">
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="from" class="control-label" style="line-height: 35px;">Posición del Logo:</label>
                                <div class="col-sm-12">
                                    <select name="logopos" class="form-control">
                                        <option <?php echo ($conf->logopos=='left')?'selected':'' ;?> value="left">Izquierda</option>
                                        <option <?php echo ($conf->logopos=='center')?'selected':'' ;?>  value="center">Centro</option>
                                        <option <?php echo ($conf->logopos=='right')?'selected':'' ;?>  value="right">Derecha</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label for="from" class="control-label" style="line-height: 35px;">Cabecera:</label>
                                <div class="summernote"><?php echo $conf->iheader;?></div>
                                <textarea style="display: none;" id="headertext" name="headertext"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label for="from" class="control-label" style="line-height: 35px;">Cuerpo:</label>
                                <pre>Puede usar los siguientes placeholders:<br> _clientName_,_clientLastName_,_clientPhone_,_noOrder_,_Route_,_date_,_passengers_,_price_,_from_,_to_,_notes_,_hrs_</pre>
                                <div class="body_text"><?php echo $conf->cbody;?></div>

                                <textarea style="display: none;" id="bodytext" name="bodytext"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label for="from" class="control-label" style="line-height: 35px;">Contacto:</label>
                                <div class="contact"><?php echo $conf->icontact;?></div>
                                <textarea style="display: none;" id="contacttext" name="contacttext"></textarea>
                            </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-header">
                    Servidor de Correo
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="from" class="control-label" style="line-height: 35px;">Huésped:</label>
                            <div class="col-sm-12">
                                <input type="text" class="route-input form-control" name="host" id="host" value="<?php echo $conf->chost;?>">
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="from" class="control-label" style="line-height: 35px;">Puerto:</label>
                            <div class="col-sm-12">
                                <input type="text" class="route-input form-control" name="port" id="port" value="<?php echo $conf->cport;?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="from" class="control-label" style="line-height: 35px;">Usuario:</label>
                            <div class="col-sm-12">
                                <input type="text" class="route-input form-control" name="username" id="username" value="<?php echo $conf->cusername;?>">
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="from" class="control-label" style="line-height: 35px;">Contraseña:</label>
                            <div class="col-sm-12">
                                <input type="password" class="route-input form-control" name="password" id="password" value="<?php echo $conf->cpassword;?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="row">
                <div class="col-lg-11"></div>
                <div class="col-lg-1">
                    <input type="hidden" name="insert" />
                    <a id="save_conf" class="btn btn-sm btn-primary m-t-n-xs" >Guardar</a>
                </div>
            </div>
        </form>
    </div>
    <?php
}