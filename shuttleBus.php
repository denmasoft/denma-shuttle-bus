<?php
/**

 *

 * @link              http://dnsempresas.com

 * @since             1.0.0

 * @package           ShuttleBus

 *

 * @wordpress-plugin

 * Plugin Name:       Shuttle Bus

 * Plugin URI:        http://dnsempresas.com

 * Description:       Shuttle Bus.

 * Version:           1.00

 * Author:            Dennis Piedra Yalint

 * Author URI:        http://denmasoft.com

 * License:           copyright

 * Text Domain:       shbs

 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
add_action( 'phpmailer_init', 'mailer_config', 10, 1);
function mailer_config(PHPMailer $mailer){

    global $wpdb;

    $conf = $wpdb->get_row($wpdb->prepare("select c.HOST as chost, c.PORT as cport, c.USERNAME as cusername,c.PASSWORD as cpassword FROM ".$wpdb->prefix."shuttle_conf c",''));

    $mailer->IsSMTP();

    $mailer->Host = $conf->chost;

    $mailer->Port = $conf->port;

    $mailer->SMTPAuth = true;

    $mailer->Username = $conf->cusername;

    $mailer->Password = $conf->cpassword;

    $mailer->SMTPAutoTLS = false;

    $mailer->SMTPDebug = 0;

    $mailer->CharSet  = 'utf-8';

}
register_activation_hook( __FILE__, 'shuttle_create_db' );

function shuttle_bus_load_textdomain(){
    load_plugin_textdomain( 'shuttle-bus', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    load_plugin_textdomain('SHUTTLE-BUS','shuttleBus/languages');
    load_plugin_textdomain('shbs','shuttleBus/languages');
    load_plugin_textdomain('wp-country', false, 'shuttleBus/languages/');
}
add_action( 'plugins_loaded', 'shuttle_bus_load_textdomain' );
add_action("init", 'shuttle_bus_load_textdomain');
add_action( 'admin_enqueue_scripts', 'register_admin_scripts' );
add_action( 'admin_enqueue_scripts', 'register_typeahead_scripts' );
add_action( 'admin_enqueue_scripts', 'register_jquery' );
add_action( 'admin_enqueue_scripts', 'register_style' );
function ajaxLoadScripts() {
    wp_localize_script( 'ajax-url', 'ajax_script_url', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
add_action('wp_print_scripts', 'ajaxLoadScripts');
/*function disable_google_maps_ob_start(){
    ob_start('disable_google_maps_ob_end');
}
function disable_google_maps_ob_end($html){
    $html = preg_replace('/<script[^<>]*\/\/maps.(googleapis|google|gstatic).com\/[^<>]*><\/script>/i', '<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&language=es"></script>', $html);
    return $html;
}*/
function my_js_variables(){ ?>
    <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url( "admin-ajax.php" ); ?>';
        var ajaxnonce = '<?php echo wp_create_nonce( "itr_ajax_nonce" ); ?>';
    </script><?php
}
add_action ( 'wp_head', 'my_js_variables');
function register_admin_scripts($hook)
{
    global $shuttle_bus_main;
    global $route_list;
    global $book_list;
    global $conf_list;
    global $new_route;
    global $update_route;
    if( $hook == $shuttle_bus_main || $hook== $route_list || $hook== $book_list || $hook==$conf_list || $hook==$new_route || $hook==$update_route)
    {
        wp_register_script('jquery-112', plugins_url('/assets/front/js/jquery-2.1.3.min.js', __FILE__), false,'',true);
        wp_enqueue_script('jquery-112');
        wp_register_script('slimscroll', plugins_url('/assets/admin/vendor/slimScroll/jquery.slimscroll.min.js', __FILE__), false, '',true);
        wp_enqueue_script('slimscroll');
        wp_register_script('bootstrap', plugins_url('/assets/admin/vendor/bootstrap/dist/js/bootstrap.min.js', __FILE__), false, '',true);
        wp_enqueue_script('bootstrap');
        wp_register_script('metisMenu', plugins_url('/assets/admin/vendor/metisMenu/dist/metisMenu.min.js', __FILE__), false, '',true);
        wp_enqueue_script('metisMenu');
        wp_register_script('icheck', plugins_url('/assets/admin/vendor/iCheck/icheck.min.js', __FILE__), false, '',true);
        wp_enqueue_script('icheck');
        wp_register_script('validate', plugins_url('/assets/admin/vendor/jquery-validation/jquery.validate.min.js', __FILE__), false, '',true);
        wp_enqueue_script('validate');
        wp_register_script('peity', plugins_url('/assets/admin/vendor/peity/jquery.peity.min.js', __FILE__), false, '',true);
        wp_enqueue_script('peity');
        wp_register_script('sweet-alert', plugins_url('/assets/admin/vendor/sweetalert/lib/sweet-alert.min.js', __FILE__), false, '',true);
        wp_enqueue_script('sweet-alert');
        wp_register_script('dataTables', plugins_url('/assets/admin/vendor/datatables/media/js/jquery.dataTables.min.js', __FILE__), false, '',true);
        wp_enqueue_script('dataTables');
        wp_register_script('dataTables_bootstrap', plugins_url('/assets/admin/vendor/datatables.net-bs/js/dataTables.bootstrap.min.js', __FILE__), false, '',true);
        wp_enqueue_script('dataTables_bootstrap');
        wp_register_script('datatables_buttons', plugins_url('/assets/admin/vendor/datatables.net-buttons/js/buttons.html5.min.js', __FILE__), false, '',true);
        wp_enqueue_script('datatables_buttons');
        wp_register_script('datatables_print', plugins_url('/assets/admin/vendor/datatables.net-buttons/js/buttons.print.min.js', __FILE__), false, '',true);
        wp_enqueue_script('datatables_print');
        wp_register_script('datatables_buttons_all', plugins_url('/assets/admin/vendor/datatables.net-buttons/js/dataTables.buttons.min.js', __FILE__), false, '',true);
        wp_enqueue_script('datatables_buttons_all');
        wp_register_script('buttons_bootstrap', plugins_url('/assets/admin/vendor/datatables.net-buttons-bs/js/buttons.bootstrap.min.js', __FILE__), false, '',true);
        wp_enqueue_script('buttons_bootstrap');
        wp_register_script('moment', plugins_url('/assets/admin/vendor/moment/moment.js', __FILE__), false, '',true);
        wp_enqueue_script('moment');
        wp_register_script('moment_locales', plugins_url('/assets/admin/vendor/moment-with-locales.js', __FILE__), false, '',true);
        wp_enqueue_script('moment_locales');
        wp_register_script('bootstrap-editable', plugins_url('/assets/admin/vendor/xeditable/bootstrap3-editable/js/bootstrap-editable.min.js', __FILE__), false, '',true);
        wp_enqueue_script('bootstrap-editable');
        wp_register_script('select2', plugins_url('/assets/admin/vendor/select2-3.5.2/select2.min.js', __FILE__), false, '',true);
        wp_enqueue_script('select2');
        wp_register_script('bootstrap-touchspin', plugins_url('/assets/admin/vendor/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js', __FILE__), false, '',true);
        wp_enqueue_script('bootstrap-touchspin');
        wp_register_script('bootstrap-datepicker', plugins_url('/assets/admin/vendor/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js', __FILE__), false, '',true);
        wp_enqueue_script('bootstrap-datepicker');
        wp_register_script('bootstrap-clockpicker', plugins_url('/assets/admin/vendor/clockpicker/dist/bootstrap-clockpicker.min.js', __FILE__), false, '',true);
        wp_enqueue_script('bootstrap-clockpicker');
        wp_register_script('bootstrap-datetimepicker', plugins_url('/assets/admin/vendor/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js', __FILE__), false, '',true);
        wp_enqueue_script('bootstrap-datetimepicker');
        wp_register_script('editable_select', plugins_url('/assets/admin/vendor/jquery.editable.select.js', __FILE__), false, '',true);
        wp_enqueue_script('editable_select');
        wp_register_script('summernote', plugins_url('/assets/admin/vendor/summernote/dist/summernote.min.js', __FILE__), false, '',true);
        wp_enqueue_script('summernote');
    }
}
function register_typeahead_scripts($hook){
    global $shuttle_bus_main;
    global $route_list;
    global $book_list;
    global $conf_list;
    global $new_route;
    global $update_route;
    if( $hook == $shuttle_bus_main || $hook== $route_list || $hook== $book_list || $hook==$conf_list || $hook==$new_route || $hook==$update_route)
    {
        wp_register_script('typeahead_bundle', plugins_url('/assets/front/js/typeahead.bundle.min.js', __FILE__), false, '',true);
        wp_enqueue_script('typeahead_bundle');
        /*wp_register_script('typeahead_addresspicker', 'http://cdnjs.cloudflare.com/ajax/libs/typeahead-addresspicker/0.1.4/typeahead-addresspicker.min.js', false, '',true);
        wp_enqueue_script('typeahead_addresspicker');*/
    }
}
function register_jquery($hook) {
    global $shuttle_bus_main;
    global $route_list;
    global $book_list;
    global $conf_list;
    global $new_route;
    global $update_route;
    if( $hook == $shuttle_bus_main || $hook== $route_list || $hook== $book_list || $hook==$conf_list || $hook==$new_route || $hook==$update_route)
    {
        wp_register_script('shuttle_bus', plugins_url('/assets/shuttlebus.js', __FILE__), false, '',true);
        wp_enqueue_script('shuttle_bus');
        wp_register_script('admin_shuttle_bus', plugins_url('/assets/admin_shuttlebus.js', __FILE__), false, '',true);
        wp_enqueue_script('admin_shuttle_bus');
        wp_register_script('route_shuttle_bus', plugins_url('/assets/route_shuttlebus.js', __FILE__), false, '',true);
        wp_enqueue_script('route_shuttle_bus');
        global $wpdb;
        $stops = $wpdb->get_results("SELECT s.ID as sid,s.ADDRESS as sadr from ".$wpdb->prefix."shuttle_stop s order by s.ADDRESS ASC");
        $options = null;
        foreach ($stops as $stop)
        {
            $options[]=array('sid'=>$stop->sid,'address'=>$stop->sadr);
        }
        wp_localize_script('admin_shuttle_bus', 'shuttle_bus_vars', array(
                'ajaxurl' => admin_url( "admin-ajax.php" ),
                'route_url'=>admin_url('admin.php?page=route_list'),
                'booking_url'=>admin_url('admin.php?page=book_list'),
                'routes'=>$options,
                'min_pax'=>__('Min. _pas_ Pas.','shbs'),
                'location'=>__('To or From location missing. When searching for a shuttle please select an address from the dropdown field.','shbs'),
                'invalid_phone'=>__('Please, Check your phone number.','shbs'),
                'invalid_card'=>__('Invalid card number.','shbs'),
                'invalid_card_exp'=>__('Please, Check your card expiration date.','shbs'),
                'invalid_card_cvv'=>__('Please, check your cvv number.','shbs'),
                'invalid_val'=>__('This value is invalid.','shbs'),
                'invalid_email'=>__('The email format is incorrect.','shbs'),
                'invalid_dni'=>__('The DNI is invalid.','shbs'),
                'blank_name'=>__('You must introduce your name.','shbs'),
                'blank_lnm'=>__('You must introduce your last names.','shbs'),
                'blank_phone'=>__('You must introduce your phone number.','shbs'),
                'update'=>false
            )
        );
    }
}
function register_style($hook){
    global $shuttle_bus_main;
    global $route_list;
    global $book_list;
    global $conf_list;
    global $new_route;
    global $update_route;
    if( $hook == $shuttle_bus_main || $hook== $route_list || $hook== $book_list || $hook==$conf_list || $hook==$new_route || $hook==$update_route)
    {
        wp_register_style('font-awesome', plugins_url('/assets/admin/vendor/fontawesome/css/font-awesome.css', __FILE__));
        wp_enqueue_style('font-awesome');
        wp_register_style('metisMenu', plugins_url('/assets/admin/vendor/metisMenu/dist/metisMenu.css', __FILE__));
        wp_enqueue_style('metisMenu');
        wp_register_style('bootstrap', plugins_url('/assets/admin/vendor/bootstrap/dist/css/bootstrap.css', __FILE__));
        wp_enqueue_style('bootstrap');
        wp_register_style('dataTables', plugins_url('/assets/admin/vendor/datatables.net-bs/css/dataTables.bootstrap.min.css', __FILE__));
        wp_enqueue_style('dataTables');
        wp_register_style('sweet-alert', plugins_url('/assets/admin/vendor/sweetalert/lib/sweet-alert.css', __FILE__));
        wp_enqueue_style('sweet-alert');
        wp_register_style('pe-icon-7-stroke', plugins_url('/assets/admin/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css', __FILE__));
        wp_enqueue_style('pe-icon-7-stroke');
        wp_register_style('pe-icon-7-stroke-helper', plugins_url('/assets/admin/fonts/pe-icon-7-stroke/css/helper.css', __FILE__));
        wp_enqueue_style('pe-icon-7-stroke-helper');
        wp_register_style('styles', plugins_url('/assets/admin/styles/style.css', __FILE__));
        wp_enqueue_style('styles');
        wp_register_style('bootstrap-editable', plugins_url('/assets/admin/vendor/xeditable/bootstrap3-editable/css/bootstrap-editable.css', __FILE__));
        wp_enqueue_style('bootstrap-editable');
        wp_register_style('select2', plugins_url('/assets/admin/vendor/select2-3.5.2/select2.css', __FILE__));
        wp_enqueue_style('select2');
        wp_register_style('select2-bootstrap', plugins_url('/assets/admin/vendor/select2-bootstrap/select2-bootstrap.css', __FILE__));
        wp_enqueue_style('select2-bootstrap');
        wp_register_style('touchspin', plugins_url('/assets/admin/vendor/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css', __FILE__));
        wp_enqueue_style('touchspin');
        wp_register_style('datepicker3', plugins_url('/assets/admin/vendor/bootstrap-datepicker-master/dist/css/bootstrap-datepicker3.min.css', __FILE__));
        wp_enqueue_style('datepicker3');
        wp_register_style('bootstrap-checkbox', plugins_url('/assets/admin/vendor/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css', __FILE__));
        wp_enqueue_style('bootstrap-checkbox');
        wp_register_style('bootstrap-clockpicker', plugins_url('/assets/admin/vendor/clockpicker/dist/bootstrap-clockpicker.min.css', __FILE__));
        wp_enqueue_style('bootstrap-clockpicker');
        wp_register_style('datetimepicker', plugins_url('/assets/admin/vendor/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css', __FILE__));
        wp_enqueue_style('datetimepicker');
        wp_register_style('summernote', plugins_url('/assets/admin/vendor/summernote/dist/summernote.css', __FILE__));
        wp_enqueue_style('summernote');
        wp_register_style('summernotebs3', plugins_url('/assets/admin/vendor/summernote/dist/summernote-bs3.css', __FILE__));
        wp_enqueue_style('summernotebs3');
    }

}
function search_routes() {
    $totalpassengers = $_REQUEST['totalpassengers'];
    $from = $_REQUEST['from'];
    $to = $_REQUEST['to'];
    $date = $_REQUEST['date'];
    die();
}
function routes(){
    /*echo json_encode($result);
    die();*/
}
function reserve_route(){
    global $wpdb;
    $route = $_REQUEST['route'];
    $from = $_REQUEST['from'];
    $to = $_REQUEST['to'];

    $total = $_REQUEST['total'];

    $date = $_REQUEST['date'];

    $client = $_REQUEST['client'];

    $client_data = array('NAME'=>$client['nm'],'LAST_NAME'=>$client['lnm'],'EMAIL'=>$client['email']);

    $wpdb->insert($wpdb->prefix.'shuttle_client',$client_data,array('%s','%s'));

    $client = $wpdb->insert_id;

    $price = 0;

    $route_from = $wpdb->get_row($wpdb->prepare("SELECT s.ID as sid,s.POS as pos,s.PRICE as price from ".$wpdb->prefix."shuttle_stop s where ID=%s",$from));

    $pos_from = 0;

    $pos_to = 0;

    if($route_from)

    {

        $price+=$route_from->price;

        $pos_from = $route_from->pos;

    }

    $route_to = $wpdb->get_row($wpdb->prepare("SELECT s.ID as sid,s.POS as pos from ".$wpdb->prefix."shuttle_stop s where ID=%s",$to));

    if($route_to)

    {

        $pos_to = $route_to->pos;

    }

    $stops = $wpdb->get_results($wpdb->prepare("SELECT s.ID as sid,s.POS as pos, s.PRICE as price from ".$wpdb->prefix."shuttle_stop s where ROUTE=%s AND POS BETWEEN %s AND %s",$route,$pos_from,$pos_to));

    if($stops)

    {

        foreach ($stops as $stop)

        {

            $price+=$stop->price;

        }

    }

    $data = array('NO_ORDER'=>'sb_'.mt_rand(),'CLIENT'=>$client,'ROUTE'=>$route,'START_DATE'=>$date['date'],'TOTAL'=>$total,'AMMOUNT'=>$price);

    $wpdb->insert($wpdb->prefix.'shuttle_order',$data,array('%s','%s'));

    die();

}

function confirm_shuttle(){

    global $wpdb;

    $route = $_POST['form_route'];

    $hrs = $_POST['form_hrs'];

    $from = $_POST['form_from'];

    $to = $_POST['form_to'];

    $total = $_POST['form_travelers'];

    $date = $_POST['form_date'];

    $price = $_POST['form_price'];

    //$card = $_POST['sb_cardnumber'];

    $clientName = $_POST['sb_name'];

    $clientLastName = $_POST['sb_lastname'];

    $clientEmail = $_POST['sb_email'];

    $clientPhone = $_POST['sb_phone'];

    $clientNotes = $_POST['sb_notes'];

    $drop_off = $_POST['drop_off'];

    $pick_up = $_POST['pick_up'];

    //$expDate = $_POST['sb_expdate'];

    //list($month,$year) = explode('/',$expDate);

    $price = $price*$total;

    $time = new \DateTime('now');

    $order_no = 'sb_'.mt_rand();

    $client_data = array('NAME'=>$clientName,'LAST_NAME'=>$clientLastName,'EMAIL'=>$clientEmail,'PHONE'=>$clientPhone);

    $data = array('NO_ORDER'=>'sb_'.mt_rand(),'ROUTE'=>$route,'START_DATE'=>$date,'TOTAL'=>$total,'AMMOUNT'=>$price,'START_POINT'=>$from,'END_POINT'=>$to,'HRS'=>$hrs,'NOTES'=>$clientNotes,'DROP_OFF'=>$drop_off,'PICK_UP'=>$pick_up,'CREATED_AT'=>$time->format('Y-m-d H:i:s'));

    $seg = (string)$time->format('s');

    $salt = md5(uniqid('shuttle_bus_',true).$seg);

    $pending = array('NO_ORDER'=>$order_no,'CLIENT_DATA'=>serialize($client_data),'ORDER_DATA'=>serialize($data),'VERIFIED'=>$salt);

    $wpdb->insert($wpdb->prefix.'shuttle_pending',$pending,array('%s','%s'));

    $location = get_site_url().'/checkout?token='.$salt;

    wp_redirect( $location, 301 );

    /*$result = 'success';

    \Stripe\Stripe::setApiKey('sk_test_xNuXtJNSkQJN6mlBGeBRQtWZ');

    $myCard = array('number' => $card, 'exp_month' => $month, 'exp_year' => $year);

    try{

        $charge = \Stripe\Charge::create(array('card' => $myCard, 'amount' => $price*100, 'currency' => 'eur'));

        if ($charge->card->cvc_check == 'fail') {

            throw new \Exception("cvc_check_invalid");

        }

    } catch(\Stripe\Error\Card $e) {

        $error = $e->getMessage();

        $result = 'declined';

    } catch (\Stripe\Error\InvalidRequest $e) {

        $result = 'declined';

    } catch (\Stripe\Error\Authentication $e) {

        $result = 'declined';

    } catch (\Stripe\Error\ApiConnection $e) {

        $result = 'declined';

    } catch (\Stripe\Error $e) {

        $result = 'declined';

    } catch (\Exception $e) {

        if ($e->getMessage() == 'cvc_check_invalid') {

            $result = 'declined';

        }

    }

    if($result=='success'){

        $client_data = array('NAME'=>$clientName,'LAST_NAME'=>$clientLastName,'EMAIL'=>$clientEmail,'PHONE'=>$clientPhone);

        $wpdb->insert($wpdb->prefix.'shuttle_client',$client_data,array('%s','%s'));

        $client = $wpdb->insert_id;

        $data = array('NO_ORDER'=>'sb_'.mt_rand(),'CLIENT'=>$client,'ROUTE'=>$route,'START_DATE'=>$date,'TOTAL'=>$total,'AMMOUNT'=>$price,'START_POINT'=>$from,'END_POINT'=>$to);

        $wpdb->insert($wpdb->prefix.'shuttle_order',$data,array('%s','%s'));

        //wp_mail($clientEmail,'Reserva de shuttle', '');

        //$location = get_site_url();

        //wp_redirect( $location, 301 );

        echo json_encode(array('success'=>true));

    }

    else {

        echo json_encode(array('success'=>false));

    }*/

    die();

}

function findRoutes($from,$to){

    global $wpdb;

    $routes=null;

    $rss = $wpdb->get_results($wpdb->prepare("SELECT r.id as rid,r.NAME as rnm,r.PEOPLE as travelers,av.DATA as rdata,sm.HRS, sm.PICK_UP as pickup from ".$wpdb->prefix."shuttle_route r LEFT JOIN ".$wpdb->prefix."shuttle_route_available av ON av.ROUTE=r.ID INNER JOIN ".$wpdb->prefix."shuttle_route_stop sm on r.ID=sm.ID_ROUTE INNER JOIN ".$wpdb->prefix."shuttle_route_stop sst on r.ID=sst.ID_ROUTE WHERE sm.ID_STOP=$from AND sst.ID_STOP=$to AND r.DRAFT=%s GROUP By r.ID",0));

    if($rss)

    {

        foreach ($rss as $rs)

        {

            $rft = $wpdb->get_row($wpdb->prepare("SELECT s.POS as sfrom,sp.POS as sto from ".$wpdb->prefix."shuttle_route_stop s INNER JOIN ".$wpdb->prefix."shuttle_route r ON s.ID_ROUTE=r.ID INNER JOIN ".$wpdb->prefix."shuttle_route_stop sp ON sp.ID_ROUTE=r.ID WHERE s.ID_STOP=$from AND sp.ID_STOP=$to AND r.ID=%s",$rs->rid));

            $posix=null;

            $boundary = $wpdb->get_row($wpdb->prepare("SELECT count(s.ID_ROUTE),(SELECT sa.POS as apos from ".$wpdb->prefix."shuttle_route_stop sa inner join ".$wpdb->prefix."shuttle_route rar ON sa.ID_ROUTE=rar.ID WHERE  rar.ID=%s order by sa.POS DESC LIMIT 1) as lpos,(SELECT sp.POS as pos from ".$wpdb->prefix."shuttle_route_stop sp inner join ".$wpdb->prefix."shuttle_route rr ON sp.ID_ROUTE=rr.ID WHERE  rr.ID=%s order by sp.POS ASC LIMIT 1) as fpos from ".$wpdb->prefix."shuttle_route_stop s INNER JOIN ".$wpdb->prefix."shuttle_route r ON s.ID_ROUTE=r.ID WHERE r.ID=%s",$rs->rid,$rs->rid,$rs->rid));

            $b = [$boundary->lpos,$boundary->fpos];

            if($rft->sfrom > $rft->sto){continue;}

            if(!in_array($rft->sfrom, $b) || !in_array($rft->sto, $b)){

                foreach (range($rft->sfrom, $rft->sto) as $number)

                {

                    $posix[] = $number;

                }

                unset($posix[0]);

                $posix = implode(',',$posix);

                $rp = $wpdb->get_row($wpdb->prepare("SELECT SUM(s.PRICE) as price from ".$wpdb->prefix."shuttle_route_stop s INNER JOIN ".$wpdb->prefix."shuttle_route r ON s.ID_ROUTE=r.ID WHERE s.POS IN ($posix) AND r.ID=%s",$rs->rid));

                $routes[]=array('rid'=>$rs->rid,'rnm'=>$rs->rnm,'travelers'=>$rs->travelers,'price'=>$rp->price,'rdata'=>$rs->rdata,'hrs'=>$rs->HRS,'pick_up'=>$rs->pickup);
            }
        }
    }
    return $routes;
}
/*function find($from,$to,$route){

    global $wpdb;

    $price = 0;

    $route_from = $wpdb->get_row($wpdb->prepare("SELECT s.ID as sid,s.POS as pos,s.PRICE as price,s.HRS as hours from ".$wpdb->prefix."shuttle_stop s where ID=%s",$from));

    $pos_from = 0;

    $pos_to = 0;

    $fhrs='';

    if($route_from)

    {

        $price+=$route_from->price;

        $pos_from = $route_from->pos;

        $fhrs = $route_from->hours;

    }

    $route_to = $wpdb->get_row($wpdb->prepare("SELECT s.ID as sid,s.POS as pos from ".$wpdb->prefix."shuttle_stop s where ID=%s",$to));

    if($route_to)

    {

        $pos_to = $route_to->pos;

    }

    $stops = $wpdb->get_results($wpdb->prepare("SELECT s.ID as sid,s.POS as pos, s.PRICE as price from ".$wpdb->prefix."shuttle_stop s where ROUTE=%s AND POS BETWEEN %s AND %s",$route,$pos_from,$pos_to));

    if($stops)

    {

        foreach ($stops as $stop)

        {

            if($stop->pos!=$pos_from)

            {

                $price+=$stop->price;

            }

        }

    }

    list($h,$m) = explode(':', $fhrs);

    return array('hrs'=>$m==0 ? $h.':00' : $fhrs,'price'=>$price);

}

function processRange($dy,$range,$period){

    $endDate = strtotime($range['to']->format('Y-m-d'));

    $startDate = strtotime($range['from']->format('Y-m-d'));

    $days=array('0'=>'None','1'=>'Monday','2' => 'Tuesday','3' => 'Wednesday','4'=>'Thursday','5' =>'Friday','6' => 'Saturday','7'=>'Sunday');

    $dates = array();

    for($i = strtotime($days[$dy], $startDate); $i <= $endDate; $i = strtotime('+1 '.$period, $i))

    {

        $dates[] = date('d/m/Y',$i);

    }

    return $dates;

}

function _processDay($dow,$dy,$range){

    $dates= array();

    if($dow==$dy)

    {

        $currentdate = new \DateTime();

        $diff_days = 0;

        $diff_hrs = 0;

        $days=array('0'=>'monday','1' => 'tuesday','2' => 'wednesday','3'=>'thursday','4' =>'friday','5' => 'saturday','6'=>'sunday');

        if($range['head']==true)

        {

            if($currentdate!=$range['pivot'])

            {

                $date = new \DateTime();

                $date->modify('next '.$days[$dy]);

                $diff = $date->diff($currentdate);

                $diff_days = $diff->days;

            }

            if($diff_days < $range['dahead'])

            {

                $dates[] = $range['pivot']->format('d/m/Y');

            }

        }

        else{

            if($currentdate!=$range['pivot'])

            {

                $date = new \DateTime();

                $date->modify('next '.$days[$dy]);

                $diff = $date->diff($currentdate);

                $diff_hrs = $diff->h;

            }

            if($diff_hrs < $range['hahead'])

            {

                $dates[] = $range['pivot']->format('d/m/Y');

            }

        }

    }

    return $dates;

}

function processDay($dy,$period,$range)

{

    $dates = array();

    $dow = date('w', strtotime($range['pivot']->format('Y-m-d')));

    switch ($period)

    {

        case 1:

            if($dow==$dy-1)

            {

                $dates[] = $range['pivot']->format('d/m/Y');

            }

            break;

        case 2:

            $m = $range['pivot']->format('m');

            $y = $range['pivot']->format('Y');

            $from = $range['from'];$from->setDate($y,$m,$range['from']->format('d'));

            $to = $range['to'];$to->setDate($y,$m,$range['to']->format('d'));

            if($range['pivot']>=$from && $range['pivot']<=$to)

            {

                if($dow==$dy-1)

                {

                    $dates[] = $range['pivot']->format('d/m/Y');

                }

            }

            break;

        case 3:

            $y = $range['pivot']->format('Y');

            $from = $range['from'];$from->setDate($y,$range['from']->format('m'),$range['from']->format('d'));

            $to = $range['to'];$to->setDate($y,$range['to']->format('m'),$range['to']->format('d'));

            if($range['pivot']>=$from && $range['pivot']<=$to)

            {

                if($dow==$dy-1)

                {

                    $dates[] = $range['pivot']->format('d/m/Y');

                }

            }

            break;

        default;

    }

    return $dates;

}

function processPeriod($range,$period,$dys)

{

    $dates = array();

    foreach ($dys as $dy)

    {

        $dates[] = processDay($dy,$period,$range);

    }

    return $dates;

}

function processAvailable($available,$input){

    if($available->bdays!=null)

    {

        $days = unserialize($available->bdays);

        $days = implode(',', $days);

        if(strpos($days, $input['fdate']))

        {

            return $days;

        }

    }

    if($available->dys!=null)

    {

        $dys = unserialize($available->dys);

        $dys = explode(',',$dys);

        $f = $available->od?:date('d/m/Y', strtotime('+5 years'));

        $t = $available->cld?:date('d/m/Y', strtotime('+6 years'));

        $desde = \DateTime::createFromFormat('d/m/Y', $f);

        $hasta = \DateTime::createFromFormat('d/m/Y', $t);

        $pivot = \DateTime::createFromFormat('d/m/Y', $input['_date']);

        return processPeriod(array('from'=>$desde,'to'=>$hasta,'pivot'=>$pivot,'input'=>$input),$available->period,$dys);

    }

    return true;

}

function processAvailables($availables,$fdate,$_date){

    $dates = array();

    if($availables)

    {

        $input=array('fdate'=>$fdate,'_date'=>$_date);

        foreach ($availables as $available)

        {

            $dates[] = processAvailable($available,$input);

        }

    }

    return $dates;

}

function processRouteByDate($route,$date){

    $_date = $date->format('Y-m-d');

    $fdate = $date->format('d/m/Y');

    global $wpdb;

    $availables = $wpdb->get_results("SELECT a.ID as aid,a.OPEN_DATE as od, a.CLOSE_DATE as cld,a.DAYS as dys,a.BLOCKED_DAYS as bdays,a.BLOCKED_MONTHS as bmonths.a.PERIOD as period from ".$wpdb->prefix."shuttle_route_available a WHERE a.ROUTE=$route AND (a.VERSION=NULL OR a.VERSION='')");

    $availables =processAvailables($availables,$fdate,$_date);

    if($availables)

    {

        return false;

    }

    return true;

}

function checkAhead($route,$date,$from,$to){

    global $wpdb;

    $conf = $wpdb->get_row($wpdb->prepare("select c.ID as cid,c.DAYS_AHEAD as dahead,c.HOURS_AHEAD as hahead,c.MIN_TRAVELERS as mintravelers FROM ".$wpdb->prefix."shuttle_conf c",''));

    $ruta = $wpdb->get_row($wpdb->prepare("SELECT  r.id as rid, r.PEOPLE as travelers,SUM(o.TOTAL) as total from ".$wpdb->prefix."shuttle_route r INNER JOIN ".$wpdb->prefix."shuttle_order o ON r.ID=o.ROUTE AND o.ROUTE=%s AND r.DRAFT=%s GROUP BY r.ID",$route,0));

    if(!$ruta)

    {

        $currentdate = new \DateTime();

        $diff = $date->diff($currentdate);

        if($diff->days < $conf->dahead)

        {

            return array('type'=>'dahead','msg'=>'Las reservas deben hacerse con '.$conf->dahead.' días de antelación');

        }

        $rut = $wpdb->get_row($wpdb->prepare("SELECT  r.id as rid, r.PEOPLE as travelers from ".$wpdb->prefix."shuttle_route r WHERE r.ID=%s AND r.DRAFT=%s GROUP BY r.ID",$route,0));

        $istop = find($from, $to, $route);

        return array('ruta'=>$rut->rid,'total'=>null,'travelers'=>$rut->travelers,'min'=>$conf->mintravelers,'hrs'=>$istop['hrs'],'price'=>$istop['price']);

    }

    $total = $ruta->travelers - $ruta->total;

    if($total>0)

    {

        if(!$ruta->total)

        {

            $currentdate = new \DateTime();

            $diff = $date->diff($currentdate);

            if($diff->days < $conf->dahead)

            {

                return array('type'=>'dahead','msg'=>'Las reservas deben hacerse con '.$conf->dahead.' días de antelación');

            }

        }

        else{

            $route_from = $wpdb->get_row($wpdb->prepare("SELECT s.ID as sid,s.HRS as hours from ".$wpdb->prefix."shuttle_stop s where ID=%s",$from));

            $temp = $date->format('Y-m-d');

            list($h,$m) = explode(':',$route_from->hours);

            $temp = $m==0 ? $temp.' '.$h.':00' : $temp.' '.$route_from->hours;

            $temp = \DateTime::createFromFormat('Y-m-d H:i', $temp);

            $currentdate = new \DateTime();

            $currentdate = \DateTime::createFromFormat('Y-m-d H:i', $currentdate->format('Y-m-d H:i'));

            $diff = $currentdate->diff($temp);

            $tdays = $diff->format('%a');

            $thrs = $tdays * 24;

            if($thrs < $conf->hahead)

            {

                return array('type'=>'hahead','msg'=>'Las reservas deben hacerse con '.$conf->hahead.' horas de antelación');

            }

        }

    }

    $istop = find($from, $to, $route);

    return array('ruta'=>$ruta->rid,'total'=>$ruta->total,'travelers'=>$ruta->travelers,'min'=>$conf->mintravelers,'hrs'=>$istop['hrs'],'price'=>$istop['price']);



}*/

function isDayBlocked($dy,$period,$range)

{

    $dow = date('w', strtotime($range['pivot']->format('Y-m-d')));

    switch ($period)

    {

        case 1:

            $from = $range['from'];

            $to = $range['to'];

            if($from!=null && $to!=null)

            {

                if($range['pivot']>=$from && $range['pivot']<=$to)

                {

                    if($dow==$dy)return true;

                }

            }

            else if($from==null && $to!=null)

            {

                if($range['pivot']<=$to)

                {

                    if($dow==$dy)return true;

                }

            }

            else if($from!=null && $to==null)

            {

                if($range['pivot']>=$from)

                {

                    if($dow==$dy)return true;

                }

            }

            break;

        case 2:

            $m = $range['pivot']->format('m');

            $y = $range['pivot']->format('Y');

            $from = $range['from'];

            $to = $range['to'];

            if($from!=null && $to!=null)

            {

                $from->setDate($y,$m,$range['from']->format('d'));

                $to->setDate($y,$m,$range['to']->format('d'));

                if($range['pivot']>=$from && $range['pivot']<=$to)

                {

                    if($dow==$dy)return true;

                }

            }

            else if($from==null && $to!=null)

            {

                $to->setDate($y,$m,$range['to']->format('d'));

                if($range['pivot']<=$to)

                {

                    if($dow==$dy)return true;

                }

            }

            else if($from!=null && $to==null)

            {

                $from->setDate($y,$m,$range['from']->format('d'));

                if($range['pivot']>=$from)

                {

                    if($dow==$dy)return true;

                }

            }

            break;

        case 3:

            $y = $range['pivot']->format('Y');

            $from = $range['from'];

            $to = $range['to'];

            if($from!=null && $to!=null)

            {

                $from->setDate($y,$range['from']->format('m'),$range['from']->format('d'));

                $to->setDate($y,$range['to']->format('m'),$range['to']->format('d'));

                if($range['pivot']>=$from && $range['pivot']<=$to)

                {

                    if($dow==$dy)return true;

                }

            }

            else if($from!=null && $to==null)

            {

                $from->setDate($y,$range['from']->format('m'),$range['from']->format('d'));

                if($range['pivot']>=$from)

                {

                    if($dow==$dy)return true;

                }

            }

            else if($from==null && $to!=null)

            {

                $to->setDate($y,$range['to']->format('m'),$range['to']->format('d'));

                if($range['pivot']<=$to)

                {

                    if($dow==$dy)return true;

                }

            }

            break;

        default;

    }

    return false;

}

function isRangeBlocked($range,$period,$dys)

{

    foreach ($dys as $dy)

    {

        if(isDayBlocked($dy,$period,$range))return true;

    }

    return false;

}

function isBlocked($data,$date)

{

    $_date = $date->format('d/m/Y');

    $data = unserialize($data);

    $days = $data['bdd'];

    if(in_array($_date, $days))

    {

        return true;

    }

    $ranges = $data['ranges'];

    foreach ($ranges as $range)

    {

        $op = $range['OPEN_DATE'];

        $cld = $range['CLOSE_DATE'];

        $dys = explode(',',$range['DAYS']);

        $period = $range['PERIOD'];

        $desde = isset($op)?\DateTime::createFromFormat('d/m/Y', $op):null;

        $hasta = isset($cld)?\DateTime::createFromFormat('d/m/Y', $cld):null;

        if(isRangeBlocked(array('from'=>$desde,'to'=>$hasta,'pivot'=>$date),$period,$dys)) return true;

    }

    return false;

}

function ordersByDate($rid,$date){

    global $wpdb;

    $rt = $wpdb->get_row($wpdb->prepare("SELECT SUM(o.TOTAL) as total from ".$wpdb->prefix."shuttle_route r LEFT JOIN ".$wpdb->prefix."shuttle_order o ON r.ID=o.ROUTE WHERE r.ID=%s AND o.START_DATE=%s AND o.CANCELLED=%s AND r.DRAFT=%s GROUP BY r.ID",$rid,$date->format('d/m/Y'),0,0));

    if($rt==null)return 0;

    return $rt->total;

}

function checkRoutesAhead($routes,$date,$dahead)

{

    $count = null;

    foreach($routes as $route)

    {

        $total_ordered = ordersByDate($route['rid'],$date);

        if($total_ordered==0)

        {

            $currentdate = new \DateTime();

            $diff = $date->diff($currentdate);

            if($diff->days < $dahead)

            {

                continue;

            }

        }

        $count[]=$route;

    }

    return $count;

}

function checkRoutesHour($iroutes,$date,$hahead)

{

    $iarray=null;

    foreach ($iroutes as $iroute)

    {

        $temp = $date->format('Y-m-d');

        list($h,$m) = explode(':',$iroute['hrs']);

        $temp = $m==0 ? $temp.' '.$h.':00' : $temp.' '.$iroute['hrs'];

        $temp = \DateTime::createFromFormat('Y-m-d H:i', $temp);

        $currentdate = new \DateTime();

        $currentdate = \DateTime::createFromFormat('Y-m-d H:i', $currentdate->format('Y-m-d H:i'));

        $diff = $currentdate->diff($temp);

        $tdays = $diff->format('%a');

        $thrs = $tdays * 24;

        if($thrs > $hahead)

        {

            $iarray[]=$iroute;

        }

    }

    return $iarray;

}

function check_available(){

    $from = $_REQUEST['from'];

    $to = $_REQUEST['to'];

    $date = $_REQUEST['date'];

    $results = null;

    $date = \DateTime::createFromFormat('d/m/Y', $date);

    global $wpdb;

    $conf = $wpdb->get_row($wpdb->prepare("select c.ID as cid,c.DAYS_AHEAD as dahead,c.HOURS_AHEAD as hahead,c.MIN_TRAVELERS as mintravelers FROM ".$wpdb->prefix."shuttle_conf c",''));

    $routes = findRoutes($from,$to);

    if($routes!=null)
    {
        $iroutes = checkRoutesAhead($routes,$date,$conf->dahead);
        if($iroutes!=null)
        {
            $irts = checkRoutesHour($iroutes, $date, $conf->hahead);
            if($irts!=null)
            {
                foreach($irts as $route)
                {
                    if($route['rdata']!=null && isBlocked($route['rdata'],$date))continue;
                    $total_ordered = ordersByDate($route['rid'],$date);
                    $travelers = $total_ordered==0?$route['travelers']:$route['travelers'] - $total_ordered;
                    //if($travelers==0)continue;
                    $min_travelers =  $total_ordered==0?$conf->mintravelers:1;
                    $min_defined =  $total_ordered==0?true:false;
                    list($h,$m) = explode(':',$route['hrs']);
                    $hrs = $m==0 ? $h.':00' :$route['hrs'];
                    $results[]=array('rid'=>$route['rid'],'rnm'=>$route['rnm'],'price'=>$route['price'],'travelers'=>$travelers,'min'=>$min_travelers,'hrs'=>$hrs,'min_defined'=>$min_defined,'pickup'=>$route['pick_up']);
                }
            }
            else
            {
                $msg = __('Reservations must be made _hours_ in advance.','shbs');
                $msg = str_replace('_hours_',$conf->hahead,$msg);
                echo json_encode(array('success'=>false,'msg'=>$msg));
                die();
            }
        }
        else
        {
            $msg = __('Reservations must be made _days_ in advance.','shbs');
            $msg = str_replace('_hours_',$conf->dahead,$msg);
            echo json_encode(array('success'=>false,'msg'=>$msg));
            die();
        }
        if($results)
        {
            $qb = $wpdb->get_row($wpdb->prepare("SELECT s.ADDRESS as saddr,sp.ADDRESS as spaddr from ".$wpdb->prefix."shuttle_stop s,".$wpdb->prefix."shuttle_stop sp WHERE s.ID=%s AND sp.ID=%s",$from,$to));
            foreach($results as &$result)
            {
                $result['start_point'] = $qb->saddr;
                $result['end_point'] = $qb->spaddr;
            }
            echo json_encode(array('success'=>true,'shuttles'=>$results));
            die();
        }
    }
    $msg = __('No routes availables for selected date.','shbs');
    echo json_encode(array('success'=>false,'msg'=>$msg));
    die();
}



function get_stops(){

    $stop = $_REQUEST['stop'];

    $origin = $_REQUEST['origin'];

    global $wpdb;

    $rows = null;
    $ssids =null;

    if($origin=='select_from'){
        $routes = $wpdb->get_results($wpdb->prepare("select r.ID as rid FROM ".$wpdb->prefix."shuttle_route r",""));
        foreach($routes as $route)
        {
            $sqlquery="select s.ID as sid, s.ADDRESS as address, rs.POS as pos 
                FROM ".$wpdb->prefix."shuttle_stop s 
                INNER JOIN ".$wpdb->prefix."shuttle_route_stop rs ON s.ID=rs.ID_STOP 
                inner join ".$wpdb->prefix."shuttle_route r on r.ID=rs.ID_ROUTE 
                WHERE s.ID<>r.end AND rs.POS < 
                (
                    select p.POS 
                    from ".$wpdb->prefix."shuttle_route_stop p
                    WHERE p.ID_STOP=%d AND p.ID_ROUTE=%d
                ) AND rs.ID_ROUTE=%d  
                GROUP BY s.ID order By s.ADDRESS ASC";
            $route_stops = $wpdb->get_results($wpdb->prepare($sqlquery,$stop,$route->rid,$route->rid));
            foreach($route_stops as $route_stop)
            {
                if(!in_array($route_stop->sid,$ssids))
                {
                    $ssids[] = $route_stop->sid;
                    $rows[] = array('sid'=>$route_stop->sid,'pos'=>$route_stop->pos,'address'=>$route_stop->address);
                }
            }
        }
        // $rows = $wpdb->get_results($wpdb->prepare("select s.ID as sid, s.ADDRESS as address FROM ".$wpdb->prefix."shuttle_stop s INNER JOIN ".$wpdb->prefix."shuttle_route_stop rs ON s.ID=rs.ID_STOP inner join ".$wpdb->prefix."shuttle_route r on r.ID=rs.ID_ROUTE WHERE s.ID<>%d and s.ID<>r.end GROUP BY s.ID order By s.ADDRESS ASC",$stop));
    }
    else{
        $routes = $wpdb->get_results($wpdb->prepare("select r.ID as rid FROM ".$wpdb->prefix."shuttle_route r",""));
        foreach($routes as $route)
        {
            $sqlquery="select s.ID as sid, s.ADDRESS as address, rs.POS as pos 
                FROM ".$wpdb->prefix."shuttle_stop s 
                INNER JOIN ".$wpdb->prefix."shuttle_route_stop rs ON s.ID=rs.ID_STOP 
                inner join ".$wpdb->prefix."shuttle_route r on r.ID=rs.ID_ROUTE 
                WHERE s.ID<>r.start AND rs.POS > 
                (
                    select p.POS 
                    from ".$wpdb->prefix."shuttle_route_stop p
                    WHERE p.ID_STOP=%d AND p.ID_ROUTE=%d
                ) AND rs.ID_ROUTE=%d  
                GROUP BY s.ID order By s.ADDRESS ASC";
            $route_stops = $wpdb->get_results($wpdb->prepare($sqlquery,$stop,$route->rid,$route->rid));
            foreach($route_stops as $route_stop)
            {
                if(!in_array($route_stop->sid,$ssids))
                {
                    $ssids[] = $route_stop->sid;
                    $rows[] = array('sid'=>$route_stop->sid,'pos'=>$route_stop->pos,'address'=>$route_stop->address);
                }
            }
        }
        /*$start_end = $wpdb->get_row($wpdb->prepare("SELECT rs.POS as pos FROM ".$wpdb->prefix."shuttle_route_stop rs inner join ".$wpdb->prefix."shuttle_stop s on rs.ID_STOP=s.ID WHERE s.ID=%d",$stop));*/
        //$rows = $wpdb->get_results($wpdb->prepare("select s.ID as sid, s.ADDRESS as address,rs.POS as pos FROM ".$wpdb->prefix."shuttle_stop s INNER JOIN ".$wpdb->prefix."shuttle_route_stop rs ON s.ID=rs.ID_STOP WHERE s.ID<>%d GROUP BY s.ID order By s.ADDRESS ASC",$stop));
    }
    //if($origin=='select_to')

    //{

        //$rows = $wpdb->get_results($wpdb->prepare("select r.ID as rid,s.ID as sid, s.ADDRESS as address FROM ".$wpdb->prefix."shuttle_stop s INNER JOIN ".$wpdb->prefix."shuttle_route_stop rs ON rs.ID_STOP=s.ID INNER JOIN ".$wpdb->prefix."shuttle_route r ON rs.ID_ROUTE=r.ID WHERE (rs.ID_STOP<>%s AND rs.ID_ROUTE=%s AND r.DRAFT=%s) order By s.POS ASC",$stop,$route,0));

    

    //}

    /*else{

        $rows = $wpdb->get_results($wpdb->prepare("select r.ID as rid,s.ID as sid, s.ADDRESS as address FROM ".$wpdb->prefix."shuttle_stop s INNER JOIN ".$wpdb->prefix."shuttle_route r ON s.ROUTE=r.ID WHERE s.ID<>%s order By s.POS ASC",$stop));

    }*/

    $html ="";

    if($rows!=null)

    {
        $msg = __('Select stop...','shbs');
        $html.='<option value="-1">'.$msg.'</option>';
        foreach ($rows as $row)
        {
            $html.="<option value='".$row['sid'].'_'.$row['pos']."'>".$row['address'].'</option>';
        }
    }
    echo json_encode(array('html'=>$html));
    die();
}

function fetchStops(){

    $stop = $_REQUEST['stop'];

    $route = $_REQUEST['route'];

    $pos = $_REQUEST['pos'];

    $orientation = $_REQUEST['orientation'];

    global $wpdb;

    $bearing = $orientation=='fwd'?" rs.POS > $pos ":" rs.POS < $pos ";

    $rows = null;

    $rows = $wpdb->get_results($wpdb->prepare("select s.ID as sid, s.ADDRESS as saddr, rs.POS as rspos FROM ".$wpdb->prefix."shuttle_stop s INNER JOIN ".$wpdb->prefix."shuttle_route_stop rs ON s.ID=rs.ID_STOP WHERE rs.ID_STOP<>%s AND rs.ID_ROUTE=%s AND ".$bearing." GROUP BY s.ID order By s.ADDRESS ASC",$stop,$route));

    $html ='';

    if($rows!=null)

    {

        foreach ($rows as $row)

        {

            $html.="<option value='".$row->sid.'_'.$row->rspos."'>".$row->saddr."</option>";

        }

    }

    echo json_encode(array('html'=>$html));

    die();

}

function remove_route () {

    global $wpdb;

    $id = $_REQUEST['route'];

    $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."shuttle_route_stop WHERE ID_ROUTE=%s",$id));

    $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."shuttle_route_available WHERE ROUTE=%s",$id));

    $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."shuttle_route WHERE ID=%s",$id));

    die();

}

function cancel_booking () {

    global $wpdb;

    $id = $_REQUEST['book'];

    $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."shuttle_order o  SET o.CANCELLED=%s WHERE o.ID=%s",1,$id));

    die();

}

add_action( 'wp_ajax_remove_route', 'remove_route' );

add_action( 'wp_ajax_nopriv_search_routes', 'search_routes' );

add_action( 'wp_ajax_search_routes', 'search_routes' );

add_action( 'wp_ajax_nopriv_routes', 'routes' );

add_action( 'wp_ajax_routes', 'routes' );



add_action( 'wp_ajax_nopriv_check_available', 'check_available' );

add_action( 'wp_ajax_check_available', 'check_available' );



add_action( 'wp_ajax_nopriv_reserve_route', 'reserve_route' );

add_action( 'wp_ajax_reserve_route', 'reserve_route' );



add_action( 'wp_ajax_nopriv_confirm_shuttle', 'confirm_shuttle' );

add_action( 'wp_ajax_confirm_shuttle', 'confirm_shuttle' );



add_action( 'wp_ajax_nopriv_get_stops', 'get_stops' );

add_action( 'wp_ajax_get_stops', 'get_stops' );



add_action( 'wp_ajax_nopriv_cancel_booking', 'cancel_booking' );

add_action( 'wp_ajax_cancel_booking', 'cancel_booking' );



add_action( 'wp_ajax_nopriv_fetchStops', 'fetchStops' );

add_action( 'wp_ajax_fetchStops', 'fetchStops' );

/*function createAddressTable(){

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'shuttle_address';



    $sql = "CREATE TABLE $table_name (

  ID int(11) NOT NULL AUTO_INCREMENT,

  LOCATION varchar(255) DEFAULT NULL,

  LAT decimal(10,6) DEFAULT NULL,

  LNG decimal(11,6) DEFAULT NULL,

  PRIMARY KEY (ID)) $charset_collate;";



    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );

}*/

function createConfiguration(){

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'shuttle_conf';



    $sql = "CREATE TABLE $table_name (

  ID int(11) NOT NULL AUTO_INCREMENT,
  DAYS_AHEAD int(11) DEFAULT NULL,
  HOURS_AHEAD int(11) DEFAULT NULL,
  MIN_TRAVELERS INT (11) DEFAULT NULL,
  EMAIL_ADDRESS VARCHAR (255) DEFAULT NULL,
  EMAIL_LOGO VARCHAR (255) DEFAULT NULL,
  HEADER_INFO TEXT DEFAULT NULL,
  CONTACT_INFO TEXT DEFAULT NULL,
  HOST VARCHAR (255) DEFAULT NULL,
  PORT INT(11) DEFAULT NULL,
  USERNAME VARCHAR (255) DEFAULT NULL,
  PASSWORD VARCHAR (255) DEFAULT NULL,
  LOGO_POS VARCHAR (255) DEFAULT NULL,
  BODY_INFO TEXT DEFAULT NULL,
  PRIMARY KEY (ID))$charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
function createRouteTable(){

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'shuttle_route';



    $sql = "CREATE TABLE $table_name (

  ID int(11) NOT NULL AUTO_INCREMENT,

  NAME varchar(255) DEFAULT NULL,

  START_POINT varchar(255) DEFAULT NULL,

  END_POINT varchar(255) DEFAULT NULL,  

  PEOPLE int(11) DEFAULT NULL,

  DRAFT INT(1) DEFAULT NULL,

  CREATED_AT datetime DEFAULT NULL,

  UPDATED_AT datetime DEFAULT NULL,

  start int(11) DEFAULT NULL,

  end int(11) DEFAULT NULL,

  PRIMARY KEY (ID))$charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );

}



function createStopTable(){

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'shuttle_stop';



    $sql = "CREATE TABLE $table_name (

      ID int(11) NOT NULL AUTO_INCREMENT,

      ADDRESS VARCHAR (255) DEFAULT NULL,

      CREATED_AT datetime DEFAULT NULL,

      UPDATED_AT datetime DEFAULT NULL,

      PRIMARY KEY (ID)

    )$charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );

}

function createRouteStopTable(){

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'shuttle_route_stop';



    $sql = "CREATE TABLE $table_name (

      ID_ROUTE int(11) NOT NULL,

      ID_STOP int(11) NOT NULL,

      POS int(11) DEFAULT NULL,

      PRICE float(10,2) NOT NULL,

      HRS VARCHAR (6)DEFAULT NULL,
      PICK_UP TEXT DEFAULT NULL,     

      PRIMARY KEY (ID_ROUTE,ID_STOP)

    )$charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );

}

function createTaxiTable(){

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'shuttle_taxi';



    $sql = "CREATE TABLE $table_name (

  ID int(11) NOT NULL AUTO_INCREMENT,

  NAME varchar(255) DEFAULT NULL,

  TYPE varchar(255) DEFAULT NULL,

  IMAGE varchar(255) DEFAULT NULL,

  DRIVER int(11) DEFAULT NULL,

  ROUTE int(11) DEFAULT NULL,

  PRIMARY KEY (ID)

)$charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );

}

function createDriverTable(){

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'shuttle_driver';



    $sql = "CREATE TABLE $table_name (

  ID int(11) NOT NULL AUTO_INCREMENT,

  NAME varchar(255) DEFAULT NULL,

  LAST_NAME varchar(255) DEFAULT NULL,

  EMAIL varchar(255) DEFAULT NULL,

  DNI varchar(255) DEFAULT NULL,

  PHONE varchar(255) DEFAULT NULL,

  CELL varchar(255) DEFAULT NULL,

  IMAGE varchar(255) DEFAULT NULL,  

  PRIMARY KEY (ID)

)$charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );

}



function createClientTable(){

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'shuttle_client';



    $sql = "CREATE TABLE $table_name (

  ID int(11) NOT NULL AUTO_INCREMENT,

  NAME varchar(255) DEFAULT NULL,

  LAST_NAME varchar(255) DEFAULT NULL,

  EMAIL varchar(255) DEFAULT NULL,

  DNI varchar(255) DEFAULT NULL,

  PHONE varchar(255) DEFAULT NULL,

  CELL varchar(255) DEFAULT NULL,  

  PRIMARY KEY (ID)

)$charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );

}



function createOrderTable(){

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'shuttle_order';



    $sql = "CREATE TABLE $table_name (

  ID int(11) NOT NULL AUTO_INCREMENT,

  NO_ORDER varchar(255) DEFAULT NULL,

  CLIENT INT(11) DEFAULT NULL,

  ROUTE INT(11) DEFAULT NULL,

  START_DATE VARCHAR (255) DEFAULT NULL,

  TOTAL INT(11) DEFAULT NULL,

  AMMOUNT FLOAT(10,2) DEFAULT NULL,

  START_POINT INT(11) DEFAULT NULL,

  END_POINT INT(11) DEFAULT NULL,

  CREATED_AT datetime DEFAULT NULL,

  UPDATED_AT datetime DEFAULT NULL,

  NOTES TEXT DEFAULT NULL,

  HRS VARCHAR (255) DEFAULT NULL,

  CANCELLED INT(1) DEFAULT NULL,

  DROP_OFF TEXT DEFAULT NULL,

  PICK_UP TEXT DEFAULT NULL,

  PRIMARY KEY (ID)) $charset_collate;";



    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );

}



function createAvailableTable(){

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'shuttle_route_available';



    $sql = "CREATE TABLE $table_name (

  ID int(11) NOT NULL AUTO_INCREMENT,

  ROUTE INT(11) NOT NULL,

  DATA TEXT DEFAULT NULL,

  CREATEDAT datetime DEFAULT NULL,

  UPDATEDAT datetime DEFAULT NULL,

  VERSION  INT(11) DEFAULT NULL,

  PRIMARY KEY (ID))$charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );

}

function shuttle_bus_upload_dir() {

    $wp_upload_dir = wp_upload_dir();

    $path = $wp_upload_dir['basedir'] . '/shuttlebus';

    if(!file_exists($path))

    {

        wp_mkdir_p( $path);

    }

    return apply_filters( 'shuttle_bus_upload_dir', $path );

}

function shuttle_bus_upload_taxis_dir() {

    $path = shuttle_bus_upload_dir() . '/taxis';

    if(!file_exists($path))

    {

        wp_mkdir_p( $path );

    }

    return apply_filters( 'shuttle_bus_upload_taxis_dir', $path );

}

function shuttle_bus_upload_email_dir() {

    $path = shuttle_bus_upload_dir() . '/email';

    if(!file_exists($path))

    {

        wp_mkdir_p( $path );

    }

    return apply_filters( 'shuttle_bus_upload_email_dir', $path );

}

function shuttle_bus_upload_user_dir() {

    $path = shuttle_bus_upload_dir() . '/users';

    if(!file_exists($path))

    {

        wp_mkdir_p( $path );

    }

    return apply_filters( 'shuttle_bus_upload_user_dir', $path );

}

function createPendingTable(){

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'shuttle_pending';

    $sql = "CREATE TABLE $table_name (

  ID int(11) NOT NULL AUTO_INCREMENT,

  NO_ORDER TEXT DEFAULT NULL,

  CLIENT_DATA TEXT DEFAULT NULL,

  ORDER_DATA TEXT DEFAULT NULL,

  VERIFIED TEXT DEFAULT NULL,

  PRIMARY KEY (ID)

)$charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );

}

function shuttle_create_db() {

    createRouteTable();

    createStopTable();

    createRouteStopTable();

    createTaxiTable();

    createClientTable();

    createDriverTable();

    createOrderTable();

    createPendingTable();

    createAvailableTable();

    createConfiguration();

    shuttle_bus_upload_dir();

    shuttle_bus_upload_taxis_dir();

    shuttle_bus_upload_user_dir();

    shuttle_bus_upload_email_dir();

}

add_action( 'admin_menu', 'shuttleBus');



//adding menu menu in wordpress admin panel



function shuttleBus() {

    global $shuttle_bus_main;
    global $route_list;
    global $book_list;
    global $conf_list;
    global $new_route;
    global $update_route;

    $shuttle_bus_main = add_menu_page( 'Shuttle Bus', 'Shuttle Bus','manage_options', 'shuttle_bus_main','shuttle_bus_main');



    $route_list = add_submenu_page( 'shuttle_bus_main', 'Rutas','Rutas', 'manage_options','route_list', 'route_list' );

    $book_list = add_submenu_page( 'shuttle_bus_main', 'Reservas','Reservas', 'manage_options','book_list', 'book_list' );

    $conf_list = add_submenu_page( 'shuttle_bus_main', 'Configuracion','Configuracion', 'manage_options','conf_list', 'conf_list' );

    //add_submenu_page( 'shuttle_bus_main', 'Taxistas','Taxistas', 'manage_options','user_list', 'user_list' );

    //add_submenu_page( 'shuttle_bus_main', 'Taxis','Taxis', 'manage_options','taxi_list', 'taxi_list' );

    //add_submenu_page( 'shuttle_bus_main', 'Paradas','Paradas', 'manage_options','stop_list', 'stop_list' );

    //add_submenu_page( 'shuttle_bus_main', 'Taxis','Taxis', 'manage_options','taxi_list', 'taxi_list' );

    $new_route = add_submenu_page(null, //parent slug

        'nueva ruta', //page title

        'nueva', //menu title

        'manage_options', //capability

        'new_route', //menu slug

        'new_route'); //function

    $update_route = add_submenu_page(null, //parent slug

        'actualizar ruta', //page title

        'actualizar', //menu title

        'manage_options', //capability

        'update_route', //menu slug

        'update_route'); //function

    add_submenu_page(null, //parent slug

        'eliminar ruta', //page title

        'eliminar', //menu title

        'manage_options', //capability

        'delete_route', //menu slug

        'delete_route'); //function

    add_submenu_page(null, //parent slug

        'actualizar reserva', //page title

        'actualizar', //menu title

        'manage_options', //capability

        'update_booking', //menu slug

        'update_booking'); //function

    /*add_submenu_page(null, //parent slug

        'nuevo taxista', //page title

        'nueva', //menu title

        'manage_options', //capability

        'new_taxi_driver', //menu slug

        'new_taxi_driver'); //function

    add_submenu_page(null, //parent slug

        'editar taxista', //page title

        'editar', //menu title

        'manage_options', //capability

        'update_user', //menu slug

        'update_user'); //function

    add_submenu_page(null, //parent slug

        'eliminar taxista', //page title

        'eliminar', //menu title

        'manage_options', //capability

        'delete_user', //menu slug

        'delete_user'); //function



    add_submenu_page(null, //parent slug

        'nuevo taxi', //page title

        'nuevo', //menu title

        'manage_options', //capability

        'new_taxi', //menu slug

        'new_taxi'); //function

    add_submenu_page(null, //parent slug

        'editar taxi', //page title

        'editar', //menu title

        'manage_options', //capability

        'update_taxi', //menu slug

        'update_taxi'); //function

    add_submenu_page(null, //parent slug

        'eliminar taxi', //page title

        'eliminar', //menu title

        'manage_options', //capability

        'delete_taxi', //menu slug

        'delete_taxi'); //function*/

}

//sample function to display after clicking menu

function shuttle_bus_main()

{}

require plugin_dir_path( __FILE__ ) . 'route/route-list.php';

require plugin_dir_path( __FILE__ ) . 'route/new-route.php';

require plugin_dir_path( __FILE__ ) . 'route/update-route.php';

require plugin_dir_path( __FILE__ ) . 'route/delete-route.php';

require plugin_dir_path( __FILE__ ) . 'route/conf-list.php';



require plugin_dir_path( __FILE__ ) . 'user/user-list.php';

require plugin_dir_path( __FILE__ ) . 'user/new-user.php';

require plugin_dir_path( __FILE__ ) . 'user/update-user.php';



require plugin_dir_path( __FILE__ ) . 'taxi/taxi-list.php';

require plugin_dir_path( __FILE__ ) . 'taxi/new-taxi.php';

require plugin_dir_path( __FILE__ ) . 'taxi/update-taxi.php';

require plugin_dir_path( __FILE__ ) . 'book/book-list.php';

require plugin_dir_path( __FILE__ ) . 'book/update-booking.php';

require plugin_dir_path( __FILE__ ) . 'shortcodes/booking.php';

require plugin_dir_path( __FILE__ ) . 'shortcodes/checkout.php';

require plugin_dir_path( __FILE__ ) . 'upload.php';

add_shortcode('shuttleBooking','shuttleBooking');

add_shortcode('shuttleCheckout','shuttleCheckout');



class WP_Country {



    private $all_countries = array(

        "AF" => "Afghanistan",

        "AX" => "Aland Islands",

        "AL" => "Albania",

        "DZ" => "Algeria",

        "AS" => "American Samoa",

        "AD" => "Andorra",

        "AO" => "Angola",

        "AI" => "Anguilla",

        "AQ" => "Antarctica",

        "AG" => "Antigua and Barbuda",

        "AR" => "Argentina",

        "AM" => "Armenia",

        "AW" => "Aruba",

        "AU" => "Australia",

        "AT" => "Austria",

        "AZ" => "Azerbaijan",

        "BS" => "Bahamas",

        "BH" => "Bahrain",

        "BD" => "Bangladesh",

        "BB" => "Barbados",

        "BY" => "Belarus",

        "BE" => "Belgium",

        "BZ" => "Belize",

        "BJ" => "Benin",

        "BM" => "Bermuda",

        "BT" => "Bhutan",

        "BO" => "Bolivia, Plurinational State of",

        "BQ" => "Bonaire, Sint Eustatius and Saba",

        "BA" => "Bosnia and Herzegovina",

        "BW" => "Botswana",

        "BV" => "Bouvet Island",

        "BR" => "Brazil",

        "IO" => "British Indian Ocean Territory",

        "BN" => "Brunei Darussalam",

        "BG" => "Bulgaria",

        "BF" => "Burkina Faso",

        "BI" => "Burundi",

        "KH" => "Cambodia",

        "CM" => "Cameroon",

        "CA" => "Canada",

        "CV" => "Cape Verde",

        "KY" => "Cayman Islands",

        "CF" => "Central African Republic",

        "TD" => "Chad",

        "CL" => "Chile",

        "CN" => "China",

        "CX" => "Christmas Island",

        "CC" => "Cocos (Keeling) Islands",

        "CO" => "Colombia",

        "KM" => "Comoros",

        "CG" => "Congo",

        "CD" => "Congo, the Democratic Republic of the",

        "CK" => "Cook Islands",

        "CR" => "Costa Rica",

        "CI" => "Cote d'Ivoire",

        "HR" => "Croatia",

        "CU" => "Cuba",

        "CW" => "Curacao",

        "CY" => "Cyprus",

        "CZ" => "Czech Republic",

        "DK" => "Denmark",

        "DJ" => "Djibouti",

        "DM" => "Dominica",

        "DO" => "Dominican Republic",

        "EC" => "Ecuador",

        "EG" => "Egypt",

        "SV" => "El Salvador",

        "GQ" => "Equatorial Guinea",

        "ER" => "Eritrea",

        "EE" => "Estonia",

        "ET" => "Ethiopia",

        "FK" => "Falkland Islands (Malvinas)",

        "FO" => "Faroe Islands",

        "FJ" => "Fiji",

        "FI" => "Finland",

        "FR" => "France",

        "GF" => "French Guiana",

        "PF" => "French Polynesia",

        "TF" => "French Southern Territories",

        "GA" => "Gabon",

        "GM" => "Gambia",

        "GE" => "Georgia",

        "DE" => "Germany",

        "GH" => "Ghana",

        "GI" => "Gibraltar",

        "GR" => "Greece",

        "GL" => "Greenland",

        "GD" => "Grenada",

        "GP" => "Guadeloupe",

        "GU" => "Guam",

        "GT" => "Guatemala",

        "GG" => "Guernsey",

        "GN" => "Guinea",

        "GW" => "Guinea-Bissau",

        "GY" => "Guyana",

        "HT" => "Haiti",

        "HM" => "Heard Island and McDonald Islands",

        "VA" => "Holy See (Vatican City State)",

        "HN" => "Honduras",

        "HK" => "Hong Kong",

        "HU" => "Hungary",

        "IS" => "Iceland",

        "IN" => "India",

        "ID" => "Indonesia",

        "IR" => "Iran, Islamic Republic of",

        "IQ" => "Iraq",

        "IE" => "Ireland",

        "IM" => "Isle of Man",

        "IL" => "Israel",

        "IT" => "Italy",

        "JM" => "Jamaica",

        "JP" => "Japan",

        "JE" => "Jersey",

        "JO" => "Jordan",

        "KZ" => "Kazakhstan",

        "KE" => "Kenya",

        "KI" => "Kiribati",

        "KP" => "Korea, Democratic People's Republic",

        "KR" => "Korea, Republic of",

        "KW" => "Kuwait",

        "KG" => "Kyrgyzstan",

        "LA" => "Lao People's Democratic Republic",

        "LV" => "Latvia",

        "LB" => "Lebanon",

        "LS" => "Lesotho",

        "LR" => "Liberia",

        "LY" => "Libya",

        "LI" => "Liechtenstein",

        "LT" => "Lithuania",

        "LU" => "Luxembourg",

        "MO" => "Macao",

        "MK" => "Macedonia",

        "MG" => "Madagascar",

        "MW" => "Malawi",

        "MY" => "Malaysia",

        "MV" => "Maldives",

        "ML" => "Mali",

        "MT" => "Malta",

        "MH" => "Marshall Islands",

        "MQ" => "Martinique",

        "MR" => "Mauritania",

        "MU" => "Mauritius",

        "YT" => "Mayotte",

        "MX" => "Mexico",

        "FM" => "Micronesia, Federated States of",

        "MD" => "Moldova",

        "MC" => "Monaco",

        "MN" => "Mongolia",

        "ME" => "Montenegro",

        "MS" => "Montserrat",

        "MA" => "Morocco",

        "MZ" => "Mozambique",

        "MM" => "Myanmar",

        "NA" => "Namibia",

        "NR" => "Nauru",

        "NP" => "Nepal",

        "NL" => "Netherlands",

        "NC" => "New Caledonia",

        "NZ" => "New Zealand",

        "NI" => "Nicaragua",

        "NE" => "Niger",

        "NG" => "Nigeria",

        "NU" => "Niue",

        "NF" => "Norfolk Island",

        "MP" => "Northern Mariana Islands",

        "NO" => "Norway",

        "OM" => "Oman",

        "PK" => "Pakistan",

        "PW" => "Palau",

        "PS" => "Palestine, State of",

        "PA" => "Panama",

        "PG" => "Papua New Guinea",

        "PY" => "Paraguay",

        "PE" => "Peru",

        "PH" => "Philippines",

        "PN" => "Pitcairn",

        "PL" => "Poland",

        "PT" => "Portugal",

        "PR" => "Puerto Rico",

        "QA" => "Qatar",

        "RE" => "Reunion",

        "RO" => "Romania",

        "RU" => "Russian Federation",

        "RW" => "Rwanda",

        "BL" => "Saint-Barthelemy",

        "SH" => "Saint Helena, Ascension and Tristan da Cunha",

        "KN" => "Saint Kitts and Nevis",

        "LC" => "Saint Lucia",

        "MF" => "Saint Martin (French part)",

        "PM" => "Saint Pierre and Miquelon",

        "VC" => "Saint Vincent and the Grenadines",

        "WS" => "Samoa",

        "SM" => "San Marino",

        "ST" => "Sao Tome and Principe",

        "SA" => "Saudi Arabia",

        "SN" => "Senegal",

        "RS" => "Serbia",

        "SC" => "Seychelles",

        "SL" => "Sierra Leone",

        "SG" => "Singapore",

        "SX" => "Sint Maarten (Dutch part)",

        "SK" => "Slovakia",

        "SI" => "Slovenia",

        "SB" => "Solomon Islands",

        "SO" => "Somalia",

        "ZA" => "South Africa",

        "GS" => "South Georgia and the South Sandwich Islands",

        "SS" => "South Sudan",

        "ES" => "Spain",

        "LK" => "Sri Lanka",

        "SD" => "Sudan",

        "SR" => "Suriname",

        "SJ" => "Svalbard and Jan Mayen",

        "SZ" => "Swaziland",

        "SE" => "Sweden",

        "CH" => "Switzerland",

        "SY" => "Syrian Arab Republic",

        "TW" => "Taiwan, Province of China",

        "TJ" => "Tajikistan",

        "TZ" => "Tanzania, United Republic of",

        "TH" => "Thailand",

        "TL" => "Timor-Leste",

        "TG" => "Togo",

        "TK" => "Tokelau",

        "TO" => "Tonga",

        "TT" => "Trinidad and Tobago",

        "TN" => "Tunisia",

        "TR" => "Turkey",

        "TM" => "Turkmenistan",

        "TC" => "Turks and Caicos Islands",

        "TV" => "Tuvalu",

        "UG" => "Uganda",

        "UA" => "Ukraine",

        "AE" => "United Arab Emirates",

        "GB" => "United Kingdom",

        "US" => "United States",

        "UM" => "United States Minor Outlying Islands",

        "UY" => "Uruguay",

        "UZ" => "Uzbekistan",

        "VU" => "Vanuatu",

        "VE" => "Venezuela",

        "VN" => "Viet Nam",

        "VG" => "Virgin Islands, British",

        "VI" => "Virgin Islands, U.S.",

        "WF" => "Wallis and Futuna",

        "EH" => "Western Sahara",

        "YE" => "Yemen",

        "ZM" => "Zambia",

        "ZW" => "Zimbabwe",

    );



    public function name($alpha2) {

        if ( empty($this->all_countries[$alpha2]) ) {

            return '';

        }

        return __($this->all_countries[$alpha2], 'wp-country');

    }



    public function countries_list($blank = '') {

        $arr = array();

        if ( $blank ) {

            $arr[''] = $blank;

        }

        return array_merge($arr, $this->all_countries);

    }



    public function dropdown($blank = '', $echo = true, $args = array()) {

        $default_args = array(

            'include' => array(),

            'exclude' => array(),

            'name' => 'country',

            'id' => '',

            'class' => '',

            'selected' => array(),

            'multiple' => false,

        );



        $args = array_merge($default_args, $args);



        foreach ( $args as $key => $value ) {

            if ( !array_key_exists($key, $default_args) ) {

                unset($args[$key]);

            }

        }

        $args = array_merge($default_args, $args);

        $args = apply_filters('wp_country_args', $args);

        extract($args);



        $out = '';

        $arr = array();

        if ( $blank ) {

            $arr[''] = $blank;

        }



        foreach ($this->all_countries as $alpha2 => $value) {

            if ( $include ) {

                if ( in_array($alpha2, $include) ) {

                    $arr[$alpha2] = __($value, 'wp-country');

                }

            }

            elseif ( $exclude ) {

                if ( !in_array($alpha2, $exclude) ) {

                    $arr[$alpha2] = __($value, 'wp-country');

                }

            }

            else {

                $arr[$alpha2] = __($value, 'wp-country');

            }

        }



        if ($arr) {

            $out .= '<select name="' . $name . ($multiple? '[]" multiple ': '"')

                . ($id ? ' id="' . $id . '" ' : '') . ($class ? ' class="' . $class . '"' : '') . '>';

            foreach ($arr as $key => $value) {

                $out .= '<option value="' . $key. '"' . (in_array($key, $selected) ? ' selected' : '') . '>' . $value . '</option>';

            }

            $out .= '</select>';

        }

        if ($echo) {

            echo $out;

        }

        else {

            return $out;

        }

    }

}



$wp_country = new WP_Country();