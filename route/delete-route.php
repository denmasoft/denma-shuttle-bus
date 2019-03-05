<?php
function delete_route () {
    global $wpdb;
    $id = $_GET['id'];
    $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."shuttle_stop WHERE ROUTE=%s",$id));
    $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."shuttle_route_availability WHERE ROUTE=%s",$id));
    $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."shuttle_route WHERE ID=%s",$id));
    ?>
    <link type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
<div class="wrap">
    <h2><a class="btn btn-success" href="<?php echo admin_url('admin.php?page=route_list')?>">&laquo; ir a rutas</a>
    </h2>
    </div>
<?php
}