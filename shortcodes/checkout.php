<?php
function shuttleCheckout(){
    require_once plugin_dir_path( __FILE__ ) . '../stripe-php/init.php';
    $stripe = array(
        "secret_key"      => "sk_test_xNuXtJNSkQJN6mlBGeBRQtWZ",
        "publishable_key" => "pk_test_1JNSCoG3WlSFTuXERkR6MPx1"
    );
    \Stripe\Stripe::setApiKey($stripe['secret_key']);
    if(isset($_POST['stripeToken']))
    {
        global $wpdb;
        $token = $_GET['token'];
        $row = $wpdb->get_row($wpdb->prepare("select p.CLIENT_DATA as client,p.ORDER_DATA as dorder FROM ".$wpdb->prefix."shuttle_pending p where VERIFIED=%s",$token));
        $result='success';
        $client = unserialize($row->client);
        $order =unserialize($row->dorder);
        $stoken = $_POST['stripeToken'];
        $customer = \Stripe\Customer::create(array(
            'email' => $client['EMAIL'],
            'source'  => $stoken
        ));
        try{
            $charge = \Stripe\Charge::create(array(
                'customer' => $customer->id,
                'amount'   => $order['AMMOUNT']*100,
                'currency' => 'eur'
            ));
            if ($charge->card->cvc_check == 'fail') {
                throw new \Exception("cvc_check_invalid");
            }
        }catch(\Stripe\Error\Card $e) {
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
        if($result=='success')
        {
            $client_data = array('NAME'=>$client['NAME'],'LAST_NAME'=>$client['LAST_NAME'],'EMAIL'=>$client['EMAIL'],'PHONE'=>$client['PHONE']);
            $wpdb->insert($wpdb->prefix.'shuttle_client',$client_data,array('%s','%s'));
            $client = $wpdb->insert_id;
            $data = array('NO_ORDER'=>$order['NO_ORDER'],'CLIENT'=>$client,'ROUTE'=>$order['ROUTE'],'START_DATE'=>$order['START_DATE'],'TOTAL'=>$order['TOTAL'],'AMMOUNT'=>$order['AMMOUNT'],'START_POINT'=>$order['START_POINT'],'END_POINT'=>$order['END_POINT'],'NOTES'=>$order['NOTES'],'DROP_OFF'=>$order['DROP_OFF'],'PICK_UP'=>$order['PICK_UP'],'CREATED_AT'=>$order['CREATED_AT'],'HRS'=>$order['HRS']);
            $wpdb->insert($wpdb->prefix.'shuttle_order',$data,array('%s','%s'));
            $wpdb->delete($wpdb->prefix.'shuttle_pending', array('VERIFIED'=>$token),array('%s','%s'));
            $conf = $wpdb->get_row($wpdb->prepare("select c.ID as cid, c.HOURS_AHEAD as hahead, c.DAYS_AHEAD as dahead,c.MIN_TRAVELERS as mintravelers, c.EMAIL_ADDRESS as addr, c.EMAIL_LOGO as logo, c.HEADER_INFO as iheader,c.CONTACT_INFO as icontact FROM ".$wpdb->prefix."shuttle_conf c",''));
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $domainName = $_SERVER['HTTP_HOST'].'/';
            $url = $protocol.$domainName;
            $to = $client_data['EMAIL'];
            $subject = __('Shuttle Booking','shbs');
            $body =  file_get_contents($url.'wp-content/plugins/shuttleBus/template/email.php?order='.$order['NO_ORDER'].'&site='.urlencode(get_home_url()));
            $headers = array('Content-Type: text/html; charset=UTF-8');
            wp_mail( $to, $subject, $body, $headers );

            $to = $conf->addr;
            $subject = __('Shuttle Booking','shbs');
            $body =  file_get_contents($url.'wp-content/plugins/shuttleBus/template/email.php?order='.$order['NO_ORDER'].'&site='.urlencode(get_home_url()));
            $headers = array('Content-Type: text/html; charset=UTF-8');
            wp_mail( $to, $subject, $body, $headers );
            ?>
            <p><?php echo __('Thanks for using our booking system.','shbs');?> </p><?php
            return;
        }
        else{?>
            <p><?php echo __('Payment failed.Please, check your data.','shbs');?></p><?php
            return;
        }
    }
    global $wpdb;
    $data=  null;
    $token = $_GET['token'];
    $row = $wpdb->get_row($wpdb->prepare("select p.CLIENT_DATA as client,p.ORDER_DATA as dorder FROM ".$wpdb->prefix."shuttle_pending p where VERIFIED=%s",$token));
    if(!$row)
    {
        echo __('Oh oh. Something went wrong','shbs');
    }
    else{
            $client = unserialize($row->client);
            $order =unserialize($row->dorder);
            $client_name = $client['NAME'];
            $client_email = $client['EMAIL'];
            $passengers = $order['TOTAL'];
            $price = $order['AMMOUNT']*100;
            $route = $wpdb->get_row($wpdb->prepare("select r.NAME as rnm FROM ".$wpdb->prefix."shuttle_route r WHERE r.ID=%s",$order['ROUTE']));
            $desc = $route->rnm;
            $hrs = $order['HRS'];
            $date = $order['START_DATE'];
            $from = $wpdb->get_row($wpdb->prepare("select s.ADDRESS as saddr FROM ".$wpdb->prefix."shuttle_stop s WHERE s.ID=%s",$order['START_POINT']));
            $from = $from->saddr;
            $to = $wpdb->get_row($wpdb->prepare("select s.ADDRESS as saddr FROM ".$wpdb->prefix."shuttle_stop s WHERE s.ID=%s",$order['END_POINT']));
            $to = $to->saddr;
    ?>
    <link href="<?php echo WP_PLUGIN_URL; ?>/shuttleBus/assets/front/css/metro.css" rel="stylesheet">
    <link href="<?php echo WP_PLUGIN_URL; ?>/shuttleBus/assets/front/css/metro-icons.css" rel="stylesheet">
    <link href="<?php echo WP_PLUGIN_URL; ?>/shuttleBus/assets/front/css/metro-responsive.css" rel="stylesheet">
    <link href="<?php echo WP_PLUGIN_URL; ?>/shuttleBus/assets/front/css/metro-schemes.css" rel="stylesheet">
    <link href="<?php echo WP_PLUGIN_URL; ?>/shuttleBus/assets/front/css/addressPicker.css" rel="stylesheet">

    <script src="<?php echo WP_PLUGIN_URL; ?>/shuttleBus/assets/front/js/jquery-2.1.3.min.js"></script>
    <script src="<?php echo WP_PLUGIN_URL; ?>/shuttleBus/assets/front/js/metro.js"></script>
    <script src="<?php echo WP_PLUGIN_URL; ?>/shuttleBus/assets/front/js/docs.js"></script>
    <script src="<?php echo WP_PLUGIN_URL; ?>/shuttleBus/assets/front/js/prettify/run_prettify.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&language=es"></script>
    <script src="<?php echo WP_PLUGIN_URL; ?>/shuttleBus/assets/front/js/typeahead.bundle.min.js"></script>

    <script src="<?php echo WP_PLUGIN_URL; ?>/shuttleBus/assets/admin/vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="<?php echo WP_PLUGIN_URL; ?>/shuttleBus/assets/front/js/select2.min.js"></script>
    <script>var shuttle_params = {"ajax_url":"<?php echo admin_url( 'admin-ajax.php' ) ?>"};</script>
    <script src="<?php echo WP_PLUGIN_URL; ?>/shuttleBus/assets/front/js/shuttlebus.js"></script>
    <style>
        .book-hidden{
            display: none;
        }
        .stepper > ul li{
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .input-control.modern input{
            background-color: transparent !important;
            border-color: transparent !important;
            color: black !important;
        }
        .input-control.modern input:focus{
            box-shadow: none !important;
            color: black !important;
        }
        .input-control.text .prepend-icon ~ input, .input-control.select .prepend-icon ~ input, .input-control.file .prepend-icon ~ input, .input-control.password .prepend-icon ~ input, .input-control.number .prepend-icon ~ input, .input-control.email .prepend-icon ~ input, .input-control.tel .prepend-icon ~ input {
            padding-left: 30px !important;
        }
        label.error, label span.error {
            color: #e74c3c !important;
            font-weight: 400 !important;
            margin: 5px 0 0 !important;
            position: relative !important;
            top: -1rem !important;
        }
        label.error::before {
            content: "";
            font-family: "FontAwesome";
            margin: 0 4px;
        }
        .panel > .content {
            background-color: transparent !important;
            font-size: 0.875rem;
            z-index: 1;
        }
        .input-control {
            margin: 0.325rem 0 30px !important;
        }

        .input-control.text, .input-control.select, .input-control.file, .input-control.password, .input-control.number, .input-control.email, .input-control.tel {
            width: 15rem;
        }
        .locate{
            background-color: #1b6eae !important;
            color: white !important;
        }
        .twitter-typeahead{
            position: unset !important;
        }
        .typeahead,
        .tt-query,
        .tt-hint {
            width: 396px;
            height: 30px;
            padding: 8px 12px;
            font-size: 24px;
            line-height: 30px;
            border: 2px solid #ccc;
            -webkit-border-radius: 8px;
            -moz-border-radius: 8px;
            border-radius: 8px;
            outline: none;
        }

        .typeahead {
            background-color: #fff;
        }

        .typeahead:focus {
            border: 2px solid #0097cf;
        }

        .tt-query {
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        }

        .tt-hint {
            color: #999
        }

        .tt-menu {
            width: 422px;
            margin: 12px 0;
            padding: 8px 0;
            background-color: #fff;
            border: 1px solid #ccc;
            border: 1px solid rgba(0, 0, 0, 0.2);
            -webkit-border-radius: 8px;
            -moz-border-radius: 8px;
            border-radius: 8px;
            -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
            -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
            box-shadow: 0 5px 10px rgba(0,0,0,.2);
        }

        .tt-suggestion {
            padding: 3px 20px;
            font-size: 18px;
            line-height: 24px;
        }

        .tt-suggestion:hover {
            cursor: pointer;
            color: #fff;
            background-color: #0097cf;
        }

        .tt-suggestion.tt-cursor {
            color: #fff;
            background-color: #0097cf;

        }

        .tt-suggestion p {
            margin: 0;
        }

        .gist {
            font-size: 14px;
        }
        .booking {
            background-color: #1F7BDC;
            padding-top: 1.5em;
            margin-bottom: 0;
            border: 1px #ccc !important;
        }
        .booking label {
            font-size: 1.25em;
            padding-top: 0;
            font-weight: 400;
            height: 2em;
            line-height: 2em;
            width: 100%;
            color: #F3F9FF;
        }
        .booking select,.booking input, .select2-results__option{
            color: #333 !important;
        }
        .booking #start,.booking #confirm {
            height: auto;
            font-size: 2em;
            line-height: 2em;
            border-radius: 0;
            padding: 0;
            /*margin-bottom: -50px;*/
            width: 100%;
            background-color: #FFA700;
        }
        .booking .select2-container {
            width: 100% !important;
        }
        .bform{
            padding: .625rem 1.825rem .625rem 2.5rem !important;
        }
        .popover {
            z-index: 1;
        }
        hr {
            border: 0;
            height: 2px;
            background-color: transparent !important;
        }
        .example{
            border: transparent;
        }
        #booking-passengers:focus{
            border:transparent !important;
        }
        @-webkit-keyframes rotate-forever{0%{-webkit-transform:rotate(0deg);-moz-transform:rotate(0deg);-ms-transform:rotate(0deg);-o-transform:rotate(0deg);transform:rotate(0deg)}100%{-webkit-transform:rotate(360deg);-moz-transform:rotate(360deg);-ms-transform:rotate(360deg);-o-transform:rotate(360deg);transform:rotate(360deg)}}@-moz-keyframes rotate-forever{0%{-webkit-transform:rotate(0deg);-moz-transform:rotate(0deg);-ms-transform:rotate(0deg);-o-transform:rotate(0deg);transform:rotate(0deg)}100%{-webkit-transform:rotate(360deg);-moz-transform:rotate(360deg);-ms-transform:rotate(360deg);-o-transform:rotate(360deg);transform:rotate(360deg)}}@keyframes rotate-forever{0%{-webkit-transform:rotate(0deg);-moz-transform:rotate(0deg);-ms-transform:rotate(0deg);-o-transform:rotate(0deg);transform:rotate(0deg)}100%{-webkit-transform:rotate(360deg);-moz-transform:rotate(360deg);-ms-transform:rotate(360deg);-o-transform:rotate(360deg);transform:rotate(360deg)}}
        .loading-indicator {
            position: absolute;
            z-index: 9999;
            top: 50% !important;
            left: 50% !important;
            margin: -15px 0 0 -15px;
            -webkit-animation-duration: .75s;
            -moz-animation-duration: .75s;
            animation-duration: .75s;
            -webkit-animation-iteration-count: infinite;
            -moz-animation-iteration-count: infinite;
            animation-iteration-count: infinite;
            -webkit-animation-name: rotate-forever;
            -moz-animation-name: rotate-forever;
            animation-name: rotate-forever;
            -webkit-animation-timing-function: linear;
            -moz-animation-timing-function: linear;
            animation-timing-function: linear;
            height: 30px;
            width: 30px;
            border: 5px solid rgba(0,0,0,0.7) !important;
            border-right-color: transparent !important;
            border-radius: 50% !important;
            display: inline-block;
            border-top: 5px solid rgba(0,0,0,0.25) !important;
            border-right: 5px solid rgba(0,0,0,.75) !important;
            border-bottom: 5px solid rgba(0,0,0,.75) !important;
            border-left: 5px solid rgba(0,0,0,.75) !important;
            background-position: center center !important;
            background: none !important;
        }

        .loading-indicator-overlay {
            background-color: #FFFFFF !important;
            top: 0 !important;
            left: 0 !important;
            opacity: 1 !important;
            position: absolute !important;
            min-height: 50px !important;
            min-width: 50px !important;
            z-index: 9999 !important;
            width: 100% !important;
            height: 100% !important;
            background-color: rgba(255,255,255,.85) !important;
            transition: opacity 500ms ease-in !important;
        }
        #start{
            position: relative;
            /*top: 45px;*/
        }
        .lio{
            display: none;
        }
        .booking-progress-bar {
            z-index: 0;
            margin-bottom: 10px;
        }
        .progressBar {
            width: 100%;
            list-style: none;
            margin: 15px 0;
            padding: 0 0 40px;
        }
        li, ul.tripTypeButtons {
            list-style-type: none;
        }
        .progressBar li {
            float: left;
            text-align: center;
            position: relative;
            z-index: 2;
            margin-left: 0 !important;
            padding: 0 !important;
        }
        ol.progressBar[data-steps="2"] li {
            width: 24%;
        }
        .progressBar .name {
            font-size: 1.2em;
            display: block;
            vertical-align: bottom;
            text-align: center;
            color: #b1b1b1;
        }
        .progressBar .step {
            color: #0090ff;
            border: 2px solid #d2d2d2;
            background-color: #d2d2d2;
            border-radius: 50%;
            line-height: .7em;
            width: 1.1em;
            height: 1.1em;
            display: inline-block;
            z-index: 2;
        }
        .progressBar .active .step, .progressBar .active .step:before, .progressBar .done .step, .progressBar .done .step:after, .progressBar .done .step:before {
            background-color: #0090FF;
        }
        .progressBar .active .step, .progressBar .done .step {
            border: 2px solid #0090FF;
        }
        .progressBar .step span {
            opacity: 0;
        }
        .progressBar .step:after {
            right: 0;
        }
        .progressBar .step:after, .progressBar .step:before {
            content: "";
            display: block;
            background-color: #d2d2d2;
            height: .4em;
            width: 50%;
            position: absolute;
            bottom: .4em;
            z-index: -1;
        }
        .progressBar .step:before {
            left: 0;
        }
        .progressBar .active .step, .progressBar .active .step:before, .progressBar .done .step, .progressBar .done .step:after, .progressBar .done .step:before {
            background-color: #0090FF;
        }
        .quotes {
            background-color: transparent;
        }
        .row.carTypeButtons {
            margin: 0;
        }
        .carTypeButtons>div {
            display: inline;
            white-space: nowrap;
            background-color: #1F7BDC;
        }
        .carTypeButtons>div .inner {
            padding-top: 15px;
            padding-bottom: 15px;
        }
        .carTypeButtons .fa, .carTypeButtons a {
            color: #fff!important;
        }
        .carTypeButtons a, .carTypeButtons a:hover {
            text-decoration: none;
        }
        .quoteHeaderTitle {
            font-size: 1.3em;
        }
        .non-wide-show, .popover-filters .checkbox.hidden {
            display: none;
        }
        .found-trips, ul.tripTypeButtons li {
            display: inline;
        }
        .found-trips, .smart-input label {
            white-space: nowrap;
            overflow: hidden;
        }
        .found-trips {
            color: #fff;
            text-overflow: ellipsis;
            padding-left: 10px;
        }
        .list-group {
            padding-left: 0;
            margin-bottom: 20px;
            margin-left: 0rem !important;
        }
        .row.results_card {
            background-color: #FFF;
            background-image: url(https://d3prcfwi67dnm2.cloudfront.net/img/img/result_card_line-2.svg);
            background-position-y: 137px;
            padding-top: 7px;
            margin: 0;
            border-bottom: 2px solid #CCC;
        }
        .row.results_card {
            background-image: none;
            padding-top: 22px;
            border: 1px solid #CCC;
            border-top: 0;
        }
        .row.results_card section {
            padding-left: 0;
            padding-right: 0;
        }
        .row.results_card .pho_information, .row.results_card .price_container {
            height: 65px;
            margin-bottom: 10px;
        }
        .row.results_card h4 {
            font-size: 16px;
            font-weight: 600;
            color: #1F7BDC;
            margin-top: 0;
            margin-bottom: 8px;
        }
        .row.results_card p {
            font-size: 14px;
            margin-top: 0;
        }
        .pho-location {
            font-size: 10px;
            color: grey;
            margin-bottom: 0;
        }
        .row.results_card .continue_button, .row.results_card .filters {
            height: 40px;
            margin-bottom: 8px;
        }
        .row.results_card .pho_information, .row.results_card .price_container {
            height: 65px;
            margin-bottom: 10px;
        }
        .row.results_card .price_container {
            padding-right: 20px;
        }
        .row.results_card .final_price, .row.results_card .price {
            font-size: 20px !important;
            font-weight: 600 !important;
            line-height: 1.5 !important;
            margin-bottom: 3px !important;
            text-align: right !important;
        }
        .row.results_card .price {
            color: #000 !important;
            display:block !important;
        }
        .row.results_card .payment-type {
            font-size: 10px;
            text-align: right;
            color: grey;
            margin-bottom: 0;
        }
        .btn-primary__orange {
            background: #FFA700;
            color: #fff;
            border: none;
            border-radius: 0;
        }
        .quotes .btn {
            display: inline-block;
            padding: 6px 12px;
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.42857143;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -ms-touch-action: manipulation;
            touch-action: manipulation;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            background-image: none;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .row.results_card .btn {
            float: none;
            width: 100%;
            border-radius: 0;
            color: #fff;
            text-decoration: none;
        }
        #booking-shuttle .container {
            margin-left: auto;
            margin-right: auto;
            padding-right: 15px;
            padding-left: 15px;
        }

        #booking-shuttle .container::after {
            content: "";
            display: table;
            clear: both;
        }

        @media (min-width: 576px) {
            #booking-shuttle .container {
                padding-right: 15px;
                padding-left: 15px;
            }
        }

        @media (min-width: 768px) {
            #booking-shuttle .container {
                padding-right: 15px;
                padding-left: 15px;
            }
        }

        @media (min-width: 992px) {
            #booking-shuttle .container {
                padding-right: 15px;
                padding-left: 15px;
            }
        }

        @media (min-width: 1200px) {
            #booking-shuttle .container {
                padding-right: 15px;
                padding-left: 15px;
            }
        }

        @media (min-width: 576px) {
            #booking-shuttle .container {
                width: 540px;
                max-width: 100%;
            }
        }

        @media (min-width: 768px) {
            #booking-shuttle .container {
                width: 720px;
                max-width: 100%;
            }
        }

        @media (min-width: 992px) {
            #booking-shuttle .container {
                width: 960px;
                max-width: 100%;
            }
        }

        @media (min-width: 1200px) {
            #booking-shuttle .container {
                width: 1140px;
                max-width: 100%;
            }
        }

        #booking-shuttle .container-fluid {
            margin-left: auto;
            margin-right: auto;
            padding-right: 15px;
            padding-left: 15px;
        }

        #booking-shuttle .container-fluid::after {
            content: "";
            display: table;
            clear: both;
        }

        @media (min-width: 576px) {
            #booking-shuttle .container-fluid {
                padding-right: 15px;
                padding-left: 15px;
            }
        }

        @media (min-width: 768px) {
            #booking-shuttle .container-fluid {
                padding-right: 15px;
                padding-left: 15px;
            }
        }

        @media (min-width: 992px) {
            #booking-shuttle .container-fluid {
                padding-right: 15px;
                padding-left: 15px;
            }
        }

        @media (min-width: 1200px) {
            #booking-shuttle .container-fluid {
                padding-right: 15px;
                padding-left: 15px;
            }
        }

        /*#booking-shuttle .row {
            margin-right: -15px;
            margin-left: -15px;
        }*/

        #booking-shuttle .row::after {
            content: "";
            display: table;
            clear: both;
        }

        @media (min-width: 576px) {
            /*#booking-shuttle .row {
                margin-right: -15px;
                margin-left: -15px;
            }*/
        }

        @media (min-width: 768px) {
            /* #booking-shuttle .row {
                 margin-right: -15px;
                 margin-left: -15px;
             }*/
        }

        @media (min-width: 992px) {
            /*#booking-shuttle .row {
                margin-right: -15px;
                margin-left: -15px;
            }*/
        }

        @media (min-width: 1200px) {
            /*#booking-shuttle .row {
                margin-right: -15px;
                margin-left: -15px;
            }*/
        }

        #booking-shuttle .col-1, #booking-shuttle .col-2, #booking-shuttle .col-3, #booking-shuttle .col-4, #booking-shuttle .col-5, #booking-shuttle .col-6, #booking-shuttle .col-7, #booking-shuttle .col-8, #booking-shuttle .col-9, #booking-shuttle .col-10, #booking-shuttle .col-11, #booking-shuttle .col-12, #booking-shuttle .col-sm, #booking-shuttle .col-sm-1, #booking-shuttle .col-sm-2, #booking-shuttle .col-sm-3, #booking-shuttle .col-sm-4, #booking-shuttle .col-sm-5, #booking-shuttle .col-sm-6, #booking-shuttle .col-sm-7, #booking-shuttle .col-sm-8, #booking-shuttle .col-sm-9, #booking-shuttle .col-sm-10, #booking-shuttle .col-sm-11, #booking-shuttle .col-sm-12, #booking-shuttle .col-md, #booking-shuttle .col-md-1, #booking-shuttle .col-md-2, #booking-shuttle .col-md-3, #booking-shuttle .col-md-4, #booking-shuttle .col-md-5, #booking-shuttle .col-md-6, #booking-shuttle .col-md-7, #booking-shuttle .col-md-8, #booking-shuttle .col-md-9, #booking-shuttle .col-md-10, #booking-shuttle .col-md-11, #booking-shuttle .col-md-12, #booking-shuttle .col-lg, #booking-shuttle .col-lg-1, #booking-shuttle .col-lg-2, #booking-shuttle .col-lg-3, #booking-shuttle .col-lg-4, #booking-shuttle .col-lg-5, #booking-shuttle .col-lg-6, #booking-shuttle .col-lg-7, #booking-shuttle .col-lg-8, #booking-shuttle .col-lg-9, #booking-shuttle .col-lg-10, #booking-shuttle .col-lg-11, #booking-shuttle .col-lg-12, #booking-shuttle .col-xl, #booking-shuttle .col-xl-1, #booking-shuttle .col-xl-2, #booking-shuttle .col-xl-3, #booking-shuttle .col-xl-4, #booking-shuttle .col-xl-5, #booking-shuttle .col-xl-6, #booking-shuttle .col-xl-7, #booking-shuttle .col-xl-8, #booking-shuttle .col-xl-9, #booking-shuttle .col-xl-10, #booking-shuttle .col-xl-11, #booking-shuttle .col-xl-12 {
            position: relative;
            min-height: 1px;
            padding-right: 15px;
            padding-left: 15px;
        }

        @media (min-width: 576px) {
            #booking-shuttle .col-1, #booking-shuttle .col-2, #booking-shuttle .col-3, #booking-shuttle .col-4, #booking-shuttle .col-5, #booking-shuttle .col-6, #booking-shuttle .col-7, #booking-shuttle .col-8, #booking-shuttle .col-9, #booking-shuttle .col-10, #booking-shuttle .col-11, #booking-shuttle .col-12, #booking-shuttle .col-sm, #booking-shuttle .col-sm-1, #booking-shuttle .col-sm-2, #booking-shuttle .col-sm-3, #booking-shuttle .col-sm-4, #booking-shuttle .col-sm-5, #booking-shuttle .col-sm-6, #booking-shuttle .col-sm-7, #booking-shuttle .col-sm-8, #booking-shuttle .col-sm-9, #booking-shuttle .col-sm-10, #booking-shuttle .col-sm-11, #booking-shuttle .col-sm-12, #booking-shuttle .col-md, #booking-shuttle .col-md-1, #booking-shuttle .col-md-2, #booking-shuttle .col-md-3, #booking-shuttle .col-md-4, #booking-shuttle .col-md-5, #booking-shuttle .col-md-6, #booking-shuttle .col-md-7, #booking-shuttle .col-md-8, #booking-shuttle .col-md-9, #booking-shuttle .col-md-10, #booking-shuttle .col-md-11, #booking-shuttle .col-md-12, #booking-shuttle .col-lg, #booking-shuttle .col-lg-1, #booking-shuttle .col-lg-2, #booking-shuttle .col-lg-3, #booking-shuttle .col-lg-4, #booking-shuttle .col-lg-5, #booking-shuttle .col-lg-6, #booking-shuttle .col-lg-7, #booking-shuttle .col-lg-8, #booking-shuttle .col-lg-9, #booking-shuttle .col-lg-10, #booking-shuttle .col-lg-11, #booking-shuttle .col-lg-12, #booking-shuttle .col-xl, #booking-shuttle .col-xl-1, #booking-shuttle .col-xl-2, #booking-shuttle .col-xl-3, #booking-shuttle .col-xl-4, #booking-shuttle .col-xl-5, #booking-shuttle .col-xl-6, #booking-shuttle .col-xl-7, #booking-shuttle .col-xl-8, #booking-shuttle .col-xl-9, #booking-shuttle .col-xl-10, #booking-shuttle .col-xl-11, #booking-shuttle .col-xl-12 {
                padding-right: 15px;
                padding-left: 15px;
            }
        }

        @media (min-width: 768px) {
            #booking-shuttle .col-1, #booking-shuttle .col-2, #booking-shuttle .col-3, #booking-shuttle .col-4, #booking-shuttle .col-5, #booking-shuttle .col-6, #booking-shuttle .col-7, #booking-shuttle .col-8, #booking-shuttle .col-9, #booking-shuttle .col-10, #booking-shuttle .col-11, #booking-shuttle .col-12, #booking-shuttle .col-sm, #booking-shuttle .col-sm-1, #booking-shuttle .col-sm-2, #booking-shuttle .col-sm-3, #booking-shuttle .col-sm-4, #booking-shuttle .col-sm-5, #booking-shuttle .col-sm-6, #booking-shuttle .col-sm-7, #booking-shuttle .col-sm-8, #booking-shuttle .col-sm-9, #booking-shuttle .col-sm-10, #booking-shuttle .col-sm-11, #booking-shuttle .col-sm-12, #booking-shuttle .col-md, #booking-shuttle .col-md-1, #booking-shuttle .col-md-2, #booking-shuttle .col-md-3, #booking-shuttle .col-md-4, #booking-shuttle .col-md-5, #booking-shuttle .col-md-6, #booking-shuttle .col-md-7, #booking-shuttle .col-md-8, #booking-shuttle .col-md-9, #booking-shuttle .col-md-10, #booking-shuttle .col-md-11, #booking-shuttle .col-md-12, #booking-shuttle .col-lg, #booking-shuttle .col-lg-1, #booking-shuttle .col-lg-2, #booking-shuttle .col-lg-3, #booking-shuttle .col-lg-4, #booking-shuttle .col-lg-5, #booking-shuttle .col-lg-6, #booking-shuttle .col-lg-7, #booking-shuttle .col-lg-8, #booking-shuttle .col-lg-9, #booking-shuttle .col-lg-10, #booking-shuttle .col-lg-11, #booking-shuttle .col-lg-12, #booking-shuttle .col-xl, #booking-shuttle .col-xl-1, #booking-shuttle .col-xl-2, #booking-shuttle .col-xl-3, #booking-shuttle .col-xl-4, #booking-shuttle .col-xl-5, #booking-shuttle .col-xl-6, #booking-shuttle .col-xl-7, #booking-shuttle .col-xl-8, #booking-shuttle .col-xl-9, #booking-shuttle .col-xl-10, #booking-shuttle .col-xl-11, #booking-shuttle .col-xl-12 {
                padding-right: 15px;
                padding-left: 15px;
            }
        }

        @media (min-width: 992px) {
            #booking-shuttle .col-1, #booking-shuttle .col-2, #booking-shuttle .col-3, #booking-shuttle .col-4, #booking-shuttle .col-5, #booking-shuttle .col-6, #booking-shuttle .col-7, #booking-shuttle .col-8, #booking-shuttle .col-9, #booking-shuttle .col-10, #booking-shuttle .col-11, #booking-shuttle .col-12, #booking-shuttle .col-sm, #booking-shuttle .col-sm-1, #booking-shuttle .col-sm-2, #booking-shuttle .col-sm-3, #booking-shuttle .col-sm-4, #booking-shuttle .col-sm-5, #booking-shuttle .col-sm-6, #booking-shuttle .col-sm-7, #booking-shuttle .col-sm-8, #booking-shuttle .col-sm-9, #booking-shuttle .col-sm-10, #booking-shuttle .col-sm-11, #booking-shuttle .col-sm-12, #booking-shuttle .col-md, #booking-shuttle .col-md-1, #booking-shuttle .col-md-2, #booking-shuttle .col-md-3, #booking-shuttle .col-md-4, #booking-shuttle .col-md-5, #booking-shuttle .col-md-6, #booking-shuttle .col-md-7, #booking-shuttle .col-md-8, #booking-shuttle .col-md-9, #booking-shuttle .col-md-10, #booking-shuttle .col-md-11, #booking-shuttle .col-md-12, #booking-shuttle .col-lg, #booking-shuttle .col-lg-1, #booking-shuttle .col-lg-2, #booking-shuttle .col-lg-3, #booking-shuttle .col-lg-4, #booking-shuttle .col-lg-5, #booking-shuttle .col-lg-6, #booking-shuttle .col-lg-7, #booking-shuttle .col-lg-8, #booking-shuttle .col-lg-9, #booking-shuttle .col-lg-10, #booking-shuttle .col-lg-11, #booking-shuttle .col-lg-12, #booking-shuttle .col-xl, #booking-shuttle .col-xl-1, #booking-shuttle .col-xl-2, #booking-shuttle .col-xl-3, #booking-shuttle .col-xl-4, #booking-shuttle .col-xl-5, #booking-shuttle .col-xl-6, #booking-shuttle .col-xl-7, #booking-shuttle .col-xl-8, #booking-shuttle .col-xl-9, #booking-shuttle .col-xl-10, #booking-shuttle .col-xl-11, #booking-shuttle .col-xl-12 {
                padding-right: 15px;
                padding-left: 15px;
            }
        }

        @media (min-width: 1200px) {
            #booking-shuttle .col-1, #booking-shuttle .col-2, #booking-shuttle .col-3, #booking-shuttle .col-4, #booking-shuttle .col-5, #booking-shuttle .col-6, #booking-shuttle .col-7, #booking-shuttle .col-8, #booking-shuttle .col-9, #booking-shuttle .col-10, #booking-shuttle .col-11, #booking-shuttle .col-12, #booking-shuttle .col-sm, #booking-shuttle .col-sm-1, #booking-shuttle .col-sm-2, #booking-shuttle .col-sm-3, #booking-shuttle .col-sm-4, #booking-shuttle .col-sm-5, #booking-shuttle .col-sm-6, #booking-shuttle .col-sm-7, #booking-shuttle .col-sm-8, #booking-shuttle .col-sm-9, #booking-shuttle .col-sm-10, #booking-shuttle .col-sm-11, #booking-shuttle .col-sm-12, #booking-shuttle .col-md, #booking-shuttle .col-md-1, #booking-shuttle .col-md-2, #booking-shuttle .col-md-3, #booking-shuttle .col-md-4, #booking-shuttle .col-md-5, #booking-shuttle .col-md-6, #booking-shuttle .col-md-7, #booking-shuttle .col-md-8, #booking-shuttle .col-md-9, #booking-shuttle .col-md-10, #booking-shuttle .col-md-11, #booking-shuttle .col-md-12, #booking-shuttle .col-lg, #booking-shuttle .col-lg-1, #booking-shuttle .col-lg-2, #booking-shuttle .col-lg-3, #booking-shuttle .col-lg-4, #booking-shuttle .col-lg-5, #booking-shuttle .col-lg-6, #booking-shuttle .col-lg-7, #booking-shuttle .col-lg-8, #booking-shuttle .col-lg-9, #booking-shuttle .col-lg-10, #booking-shuttle .col-lg-11, #booking-shuttle .col-lg-12, #booking-shuttle .col-xl, #booking-shuttle .col-xl-1, #booking-shuttle .col-xl-2, #booking-shuttle .col-xl-3, #booking-shuttle .col-xl-4, #booking-shuttle .col-xl-5, #booking-shuttle .col-xl-6, #booking-shuttle .col-xl-7, #booking-shuttle .col-xl-8, #booking-shuttle .col-xl-9, #booking-shuttle .col-xl-10, #booking-shuttle .col-xl-11, #booking-shuttle .col-xl-12 {
                padding-right: 15px;
                padding-left: 15px;
            }
        }

        #booking-shuttle .col-1 {
            float: left;
            width: 8.333333%;
        }

        #booking-shuttle .col-2 {
            float: left;
            width: 16.666667%;
        }

        #booking-shuttle .col-3 {
            float: left;
            width: 25%;
        }

        #booking-shuttle .col-4 {
            float: left;
            width: 33.333333%;
        }

        #booking-shuttle .col-5 {
            float: left;
            width: 41.666667%;
        }

        #booking-shuttle .col-6 {
            float: left;
            width: 50%;
        }

        #booking-shuttle .col-7 {
            float: left;
            width: 58.333333%;
        }

        #booking-shuttle .col-8 {
            float: left;
            width: 66.666667%;
        }

        #booking-shuttle .col-9 {
            float: left;
            width: 75%;
        }

        #booking-shuttle .col-10 {
            float: left;
            width: 83.333333%;
        }

        #booking-shuttle .col-11 {
            float: left;
            width: 91.666667%;
        }

        #booking-shuttle .col-12 {
            float: left;
            width: 100%;
        }

        #booking-shuttle .pull-0 {
            right: auto;
        }

        #booking-shuttle .pull-1 {
            right: 8.333333%;
        }

        #booking-shuttle .pull-2 {
            right: 16.666667%;
        }

        #booking-shuttle .pull-3 {
            right: 25%;
        }

        #booking-shuttle .pull-4 {
            right: 33.333333%;
        }

        #booking-shuttle .pull-5 {
            right: 41.666667%;
        }

        #booking-shuttle .pull-6 {
            right: 50%;
        }

        #booking-shuttle .pull-7 {
            right: 58.333333%;
        }

        #booking-shuttle .pull-8 {
            right: 66.666667%;
        }

        #booking-shuttle .pull-9 {
            right: 75%;
        }

        #booking-shuttle .pull-10 {
            right: 83.333333%;
        }

        #booking-shuttle .pull-11 {
            right: 91.666667%;
        }

        #booking-shuttle .pull-12 {
            right: 100%;
        }

        #booking-shuttle .push-0 {
            left: auto;
        }

        #booking-shuttle .push-1 {
            left: 8.333333%;
        }

        #booking-shuttle .push-2 {
            left: 16.666667%;
        }

        #booking-shuttle .push-3 {
            left: 25%;
        }

        #booking-shuttle .push-4 {
            left: 33.333333%;
        }

        #booking-shuttle .push-5 {
            left: 41.666667%;
        }

        #booking-shuttle .push-6 {
            left: 50%;
        }

        #booking-shuttle .push-7 {
            left: 58.333333%;
        }

        #booking-shuttle .push-8 {
            left: 66.666667%;
        }

        #booking-shuttle .push-9 {
            left: 75%;
        }

        #booking-shuttle .push-10 {
            left: 83.333333%;
        }

        #booking-shuttle .push-11 {
            left: 91.666667%;
        }

        #booking-shuttle .push-12 {
            left: 100%;
        }

        #booking-shuttle .offset-1 {
            margin-left: 8.333333%;
        }

        #booking-shuttle .offset-2 {
            margin-left: 16.666667%;
        }

        #booking-shuttle .offset-3 {
            margin-left: 25%;
        }

        #booking-shuttle .offset-4 {
            margin-left: 33.333333%;
        }

        #booking-shuttle .offset-5 {
            margin-left: 41.666667%;
        }

        #booking-shuttle .offset-6 {
            margin-left: 50%;
        }

        #booking-shuttle .offset-7 {
            margin-left: 58.333333%;
        }

        #booking-shuttle .offset-8 {
            margin-left: 66.666667%;
        }

        #booking-shuttle .offset-9 {
            margin-left: 75%;
        }

        #booking-shuttle .offset-10 {
            margin-left: 83.333333%;
        }

        #booking-shuttle .offset-11 {
            margin-left: 91.666667%;
        }

        @media (min-width: 576px) {
            #booking-shuttle .col-sm-1 {
                float: left;
                width: 8.333333%;
            }
            #booking-shuttle .col-sm-2 {
                float: left;
                width: 16.666667%;
            }
            #booking-shuttle .col-sm-3 {
                float: left;
                width: 25%;
            }
            #booking-shuttle .col-sm-4 {
                float: left;
                width: 33.333333%;
            }
            #booking-shuttle .col-sm-5 {
                float: left;
                width: 41.666667%;
            }
            #booking-shuttle .col-sm-6 {
                float: left;
                width: 50%;
            }
            #booking-shuttle .col-sm-7 {
                float: left;
                width: 58.333333%;
            }
            #booking-shuttle .col-sm-8 {
                float: left;
                width: 66.666667%;
            }
            #booking-shuttle .col-sm-9 {
                float: left;
                width: 75%;
            }
            #booking-shuttle .col-sm-10 {
                float: left;
                width: 83.333333%;
            }
            #booking-shuttle .col-sm-11 {
                float: left;
                width: 91.666667%;
            }
            #booking-shuttle .col-sm-12 {
                float: left;
                width: 100%;
            }
            #booking-shuttle .pull-sm-0 {
                right: auto;
            }
            #booking-shuttle .pull-sm-1 {
                right: 8.333333%;
            }
            #booking-shuttle .pull-sm-2 {
                right: 16.666667%;
            }
            #booking-shuttle .pull-sm-3 {
                right: 25%;
            }
            #booking-shuttle .pull-sm-4 {
                right: 33.333333%;
            }
            #booking-shuttle .pull-sm-5 {
                right: 41.666667%;
            }
            #booking-shuttle .pull-sm-6 {
                right: 50%;
            }
            #booking-shuttle .pull-sm-7 {
                right: 58.333333%;
            }
            #booking-shuttle .pull-sm-8 {
                right: 66.666667%;
            }
            #booking-shuttle .pull-sm-9 {
                right: 75%;
            }
            #booking-shuttle .pull-sm-10 {
                right: 83.333333%;
            }
            #booking-shuttle .pull-sm-11 {
                right: 91.666667%;
            }
            #booking-shuttle .pull-sm-12 {
                right: 100%;
            }
            #booking-shuttle .push-sm-0 {
                left: auto;
            }
            #booking-shuttle .push-sm-1 {
                left: 8.333333%;
            }
            #booking-shuttle .push-sm-2 {
                left: 16.666667%;
            }
            #booking-shuttle .push-sm-3 {
                left: 25%;
            }
            #booking-shuttle .push-sm-4 {
                left: 33.333333%;
            }
            #booking-shuttle .push-sm-5 {
                left: 41.666667%;
            }
            #booking-shuttle .push-sm-6 {
                left: 50%;
            }
            #booking-shuttle .push-sm-7 {
                left: 58.333333%;
            }
            #booking-shuttle .push-sm-8 {
                left: 66.666667%;
            }
            #booking-shuttle .push-sm-9 {
                left: 75%;
            }
            #booking-shuttle .push-sm-10 {
                left: 83.333333%;
            }
            #booking-shuttle .push-sm-11 {
                left: 91.666667%;
            }
            #booking-shuttle .push-sm-12 {
                left: 100%;
            }
            #booking-shuttle .offset-sm-0 {
                margin-left: 0%;
            }
            #booking-shuttle .offset-sm-1 {
                margin-left: 8.333333%;
            }
            #booking-shuttle .offset-sm-2 {
                margin-left: 16.666667%;
            }
            #booking-shuttle .offset-sm-3 {
                margin-left: 25%;
            }
            #booking-shuttle .offset-sm-4 {
                margin-left: 33.333333%;
            }
            #booking-shuttle .offset-sm-5 {
                margin-left: 41.666667%;
            }
            #booking-shuttle .offset-sm-6 {
                margin-left: 50%;
            }
            #booking-shuttle .offset-sm-7 {
                margin-left: 58.333333%;
            }
            #booking-shuttle .offset-sm-8 {
                margin-left: 66.666667%;
            }
            #booking-shuttle .offset-sm-9 {
                margin-left: 75%;
            }
            #booking-shuttle .offset-sm-10 {
                margin-left: 83.333333%;
            }
            #booking-shuttle .offset-sm-11 {
                margin-left: 91.666667%;
            }
        }

        @media (min-width: 768px) {
            #booking-shuttle .col-md-1 {
                float: left;
                width: 8.333333%;
            }
            #booking-shuttle .col-md-2 {
                float: left;
                width: 16.666667%;
            }
            #booking-shuttle .col-md-3 {
                float: left;
                width: 25%;
            }
            #booking-shuttle .col-md-4 {
                float: left;
                width: 33.333333%;
            }
            #booking-shuttle .col-md-5 {
                float: left;
                width: 41.666667%;
            }
            #booking-shuttle .col-md-6 {
                float: left;
                width: 50%;
            }
            #booking-shuttle .col-md-7 {
                float: left;
                width: 58.333333%;
            }
            #booking-shuttle .col-md-8 {
                float: left;
                width: 66.666667%;
            }
            #booking-shuttle .col-md-9 {
                float: left;
                width: 75%;
            }
            #booking-shuttle .col-md-10 {
                float: left;
                width: 83.333333%;
            }
            #booking-shuttle .col-md-11 {
                float: left;
                width: 91.666667%;
            }
            #booking-shuttle .col-md-12 {
                float: left;
                width: 100%;
            }
            #booking-shuttle .pull-md-0 {
                right: auto;
            }
            #booking-shuttle .pull-md-1 {
                right: 8.333333%;
            }
            #booking-shuttle .pull-md-2 {
                right: 16.666667%;
            }
            #booking-shuttle .pull-md-3 {
                right: 25%;
            }
            #booking-shuttle .pull-md-4 {
                right: 33.333333%;
            }
            #booking-shuttle .pull-md-5 {
                right: 41.666667%;
            }
            #booking-shuttle .pull-md-6 {
                right: 50%;
            }
            #booking-shuttle .pull-md-7 {
                right: 58.333333%;
            }
            #booking-shuttle .pull-md-8 {
                right: 66.666667%;
            }
            #booking-shuttle .pull-md-9 {
                right: 75%;
            }
            #booking-shuttle .pull-md-10 {
                right: 83.333333%;
            }
            #booking-shuttle .pull-md-11 {
                right: 91.666667%;
            }
            #booking-shuttle .pull-md-12 {
                right: 100%;
            }
            #booking-shuttle .push-md-0 {
                left: auto;
            }
            #booking-shuttle .push-md-1 {
                left: 8.333333%;
            }
            #booking-shuttle .push-md-2 {
                left: 16.666667%;
            }
            #booking-shuttle .push-md-3 {
                left: 25%;
            }
            #booking-shuttle .push-md-4 {
                left: 33.333333%;
            }
            #booking-shuttle .push-md-5 {
                left: 41.666667%;
            }
            #booking-shuttle .push-md-6 {
                left: 50%;
            }
            #booking-shuttle .push-md-7 {
                left: 58.333333%;
            }
            #booking-shuttle .push-md-8 {
                left: 66.666667%;
            }
            #booking-shuttle .push-md-9 {
                left: 75%;
            }
            #booking-shuttle .push-md-10 {
                left: 83.333333%;
            }
            #booking-shuttle .push-md-11 {
                left: 91.666667%;
            }
            #booking-shuttle .push-md-12 {
                left: 100%;
            }
            #booking-shuttle .offset-md-0 {
                margin-left: 0%;
            }
            #booking-shuttle .offset-md-1 {
                margin-left: 8.333333%;
            }
            #booking-shuttle .offset-md-2 {
                margin-left: 16.666667%;
            }
            #booking-shuttle .offset-md-3 {
                margin-left: 25%;
            }
            #booking-shuttle .offset-md-4 {
                margin-left: 33.333333%;
            }
            #booking-shuttle .offset-md-5 {
                margin-left: 41.666667%;
            }
            #booking-shuttle .offset-md-6 {
                margin-left: 50%;
            }
            #booking-shuttle .offset-md-7 {
                margin-left: 58.333333%;
            }
            #booking-shuttle .offset-md-8 {
                margin-left: 66.666667%;
            }
            #booking-shuttle .offset-md-9 {
                margin-left: 75%;
            }
            #booking-shuttle .offset-md-10 {
                margin-left: 83.333333%;
            }
            #booking-shuttle .offset-md-11 {
                margin-left: 91.666667%;
            }
        }

        @media (min-width: 992px) {
            #booking-shuttle .col-lg-1 {
                float: left;
                width: 8.333333%;
            }
            #booking-shuttle .col-lg-2 {
                float: left;
                width: 16.666667%;
            }
            #booking-shuttle .col-lg-3 {
                float: left;
                width: 25%;
            }
            #booking-shuttle .col-lg-4 {
                float: left;
                width: 33.333333%;
            }
            #booking-shuttle .col-lg-5 {
                float: left;
                width: 41.666667%;
            }
            #booking-shuttle .col-lg-6 {
                float: left;
                width: 50%;
            }
            #booking-shuttle .col-lg-7 {
                float: left;
                width: 58.333333%;
            }
            #booking-shuttle .col-lg-8 {
                float: left;
                width: 66.666667%;
            }
            #booking-shuttle .col-lg-9 {
                float: left;
                width: 75%;
            }
            #booking-shuttle .col-lg-10 {
                float: left;
                width: 83.333333%;
            }
            #booking-shuttle .col-lg-11 {
                float: left;
                width: 91.666667%;
            }
            #booking-shuttle .col-lg-12 {
                float: left;
                width: 100%;
            }
            #booking-shuttle .pull-lg-0 {
                right: auto;
            }
            #booking-shuttle .pull-lg-1 {
                right: 8.333333%;
            }
            #booking-shuttle .pull-lg-2 {
                right: 16.666667%;
            }
            #booking-shuttle .pull-lg-3 {
                right: 25%;
            }
            #booking-shuttle .pull-lg-4 {
                right: 33.333333%;
            }
            #booking-shuttle .pull-lg-5 {
                right: 41.666667%;
            }
            #booking-shuttle .pull-lg-6 {
                right: 50%;
            }
            #booking-shuttle .pull-lg-7 {
                right: 58.333333%;
            }
            #booking-shuttle .pull-lg-8 {
                right: 66.666667%;
            }
            #booking-shuttle .pull-lg-9 {
                right: 75%;
            }
            #booking-shuttle .pull-lg-10 {
                right: 83.333333%;
            }
            #booking-shuttle .pull-lg-11 {
                right: 91.666667%;
            }
            #booking-shuttle .pull-lg-12 {
                right: 100%;
            }
            #booking-shuttle .push-lg-0 {
                left: auto;
            }
            #booking-shuttle .push-lg-1 {
                left: 8.333333%;
            }
            #booking-shuttle .push-lg-2 {
                left: 16.666667%;
            }
            #booking-shuttle .push-lg-3 {
                left: 25%;
            }
            #booking-shuttle .push-lg-4 {
                left: 33.333333%;
            }
            #booking-shuttle .push-lg-5 {
                left: 41.666667%;
            }
            #booking-shuttle .push-lg-6 {
                left: 50%;
            }
            #booking-shuttle .push-lg-7 {
                left: 58.333333%;
            }
            #booking-shuttle .push-lg-8 {
                left: 66.666667%;
            }
            #booking-shuttle .push-lg-9 {
                left: 75%;
            }
            #booking-shuttle .push-lg-10 {
                left: 83.333333%;
            }
            #booking-shuttle .push-lg-11 {
                left: 91.666667%;
            }
            #booking-shuttle .push-lg-12 {
                left: 100%;
            }
            #booking-shuttle .offset-lg-0 {
                margin-left: 0%;
            }
            #booking-shuttle .offset-lg-1 {
                margin-left: 8.333333%;
            }
            #booking-shuttle .offset-lg-2 {
                margin-left: 16.666667%;
            }
            #booking-shuttle .offset-lg-3 {
                margin-left: 25%;
            }
            #booking-shuttle .offset-lg-4 {
                margin-left: 33.333333%;
            }
            #booking-shuttle .offset-lg-5 {
                margin-left: 41.666667%;
            }
            #booking-shuttle .offset-lg-6 {
                margin-left: 50%;
            }
            #booking-shuttle .offset-lg-7 {
                margin-left: 58.333333%;
            }
            #booking-shuttle .offset-lg-8 {
                margin-left: 66.666667%;
            }
            #booking-shuttle .offset-lg-9 {
                margin-left: 75%;
            }
            #booking-shuttle .offset-lg-10 {
                margin-left: 83.333333%;
            }
            #booking-shuttle .offset-lg-11 {
                margin-left: 91.666667%;
            }
        }

        @media (min-width: 1200px) {
            #booking-shuttle .col-xl-1 {
                float: left;
                width: 8.333333%;
            }
            #booking-shuttle .col-xl-2 {
                float: left;
                width: 16.666667%;
            }
            #booking-shuttle .col-xl-3 {
                float: left;
                width: 25%;
            }
            #booking-shuttle .col-xl-4 {
                float: left;
                width: 33.333333%;
            }
            #booking-shuttle .col-xl-5 {
                float: left;
                width: 41.666667%;
            }
            #booking-shuttle .col-xl-6 {
                float: left;
                width: 50%;
            }
            #booking-shuttle .col-xl-7 {
                float: left;
                width: 58.333333%;
            }
            #booking-shuttle .col-xl-8 {
                float: left;
                width: 66.666667%;
            }
            #booking-shuttle .col-xl-9 {
                float: left;
                width: 75%;
            }
            #booking-shuttle .col-xl-10 {
                float: left;
                width: 83.333333%;
            }
            #booking-shuttle .col-xl-11 {
                float: left;
                width: 91.666667%;
            }
            #booking-shuttle .col-xl-12 {
                float: left;
                width: 100%;
            }
            #booking-shuttle .pull-xl-0 {
                right: auto;
            }
            #booking-shuttle .pull-xl-1 {
                right: 8.333333%;
            }
            #booking-shuttle .pull-xl-2 {
                right: 16.666667%;
            }
            #booking-shuttle .pull-xl-3 {
                right: 25%;
            }
            #booking-shuttle .pull-xl-4 {
                right: 33.333333%;
            }
            #booking-shuttle .pull-xl-5 {
                right: 41.666667%;
            }
            #booking-shuttle .pull-xl-6 {
                right: 50%;
            }
            #booking-shuttle .pull-xl-7 {
                right: 58.333333%;
            }
            #booking-shuttle .pull-xl-8 {
                right: 66.666667%;
            }
            #booking-shuttle .pull-xl-9 {
                right: 75%;
            }
            #booking-shuttle .pull-xl-10 {
                right: 83.333333%;
            }
            #booking-shuttle .pull-xl-11 {
                right: 91.666667%;
            }
            #booking-shuttle .pull-xl-12 {
                right: 100%;
            }
            #booking-shuttle .push-xl-0 {
                left: auto;
            }
            #booking-shuttle .push-xl-1 {
                left: 8.333333%;
            }
            #booking-shuttle .push-xl-2 {
                left: 16.666667%;
            }
            #booking-shuttle .push-xl-3 {
                left: 25%;
            }
            #booking-shuttle .push-xl-4 {
                left: 33.333333%;
            }
            #booking-shuttle .push-xl-5 {
                left: 41.666667%;
            }
            #booking-shuttle .push-xl-6 {
                left: 50%;
            }
            #booking-shuttle .push-xl-7 {
                left: 58.333333%;
            }
            #booking-shuttle .push-xl-8 {
                left: 66.666667%;
            }
            #booking-shuttle .push-xl-9 {
                left: 75%;
            }
            #booking-shuttle .push-xl-10 {
                left: 83.333333%;
            }
            #booking-shuttle .push-xl-11 {
                left: 91.666667%;
            }
            #booking-shuttle .push-xl-12 {
                left: 100%;
            }
            #booking-shuttle .offset-xl-0 {
                margin-left: 0%;
            }
            #booking-shuttle .offset-xl-1 {
                margin-left: 8.333333%;
            }
            #booking-shuttle .offset-xl-2 {
                margin-left: 16.666667%;
            }
            #booking-shuttle .offset-xl-3 {
                margin-left: 25%;
            }
            #booking-shuttle .offset-xl-4 {
                margin-left: 33.333333%;
            }
            #booking-shuttle .offset-xl-5 {
                margin-left: 41.666667%;
            }
            #booking-shuttle .offset-xl-6 {
                margin-left: 50%;
            }
            #booking-shuttle .offset-xl-7 {
                margin-left: 58.333333%;
            }
            #booking-shuttle .offset-xl-8 {
                margin-left: 66.666667%;
            }
            #booking-shuttle .offset-xl-9 {
                margin-left: 75%;
            }
            #booking-shuttle .offset-xl-10 {
                margin-left: 83.333333%;
            }
            #booking-shuttle .offset-xl-11 {
                margin-left: 91.666667%;
            }
        }
        .trip-details {
            font-size: 14px;
            max-width: 500px;
        }
        .additional-info .row, .passenger-details .row, .payment-details .row, .trip-details .row {
            margin-bottom: 0;
        }
        .quote-summary--header, .quote-summary--header__mobile {
            background-color: #1F7BDC;
            color: #FFF;
            padding: 20px;
            height: 70px;
        }
        .additional-info .row, .passenger-details .row, .payment-details .row, .trip-details .row {
            margin-bottom: 0;
        }
        .trip-details .border .row {
            padding-bottom: 10px;
        }
        .trip-details .border .row:first-child {
            padding-top: 10px;
        }
        .pull-left {
            float: left!important;
        }
        .quote-summary--body__text-body {
            text-align: right;
            word-wrap: break-word;
        }
        .quote-summary--body__text-header {
            white-space: nowrap;
            color: #0090FF;
        }
        .trip-details .heading{
            font: 500 1.125rem/1.1 "Segoe UI", "Open Sans", sans-serif, serif;
            color: #ffffff;
            cursor: default;
            vertical-align: middle;
            z-index: 2;
            height: 2.625rem;
        }
        .stripe-button-el{float:right;}
    </style>
    <div class="row" id="booking-shuttle">

        <section class="trip-details col-md-12 col-xs-12 pull-right">
            <div>
                <div class="row">
                    <section class="col-md-12 pull-left col-xs-12 quote-summary--header__mobile">
                        <div class="heading">
                            <span class="icon mif-bus"></span>
                            <span class="title"><?php echo __('Shuttle details','shbs');?></span>
                        </div>
                    </section>
                </div>
                <div class="row border quote-summary--section">
                    <div class="row quote-summary--body__information">
                        <section class="col-md-2 pull-left col-xs-5">
                            <span class="quote-summary--body__text-header"><?php echo __('From','shbs');?>:</span>
                        </section>
                        <section class="col-md-10 pull-left col-xs-7 quote-summary--body__text-body" id="going_out_from_address"><?php echo $from;?></section>
                    </div>
                    <div class="row quote-summary--body__information">
                        <section class="col-md-2 pull-left col-xs-5">
                            <span class="quote-summary--body__text-header"><?php echo __('To','shbs');?>:</span>
                        </section>
                        <section class="col-md-10 pull-left col-xs-7 quote-summary--body__text-body" id="going_out_to_address"><?php echo $to;?></section>
                    </div>
                    <div class="row quote-summary--body__information">
                        <section class="col-md-3 pull-left col-xs-6">
                            <span class="quote-summary--body__text-header"><?php echo __('Pick up','shbs');?>:</span>
                        </section>
                        <section class="col-md-9 pull-left col-xs-6 quote-summary--body__text-body" id="going_out_pick_up"><?php echo $date;?></section>
                    </div>
                    <div class="row quote-summary--body__information">
                        <section class="col-md-3 pull-left col-xs-6">
                            <span class="quote-summary--body__text-header"><?php echo __('Time','shbs');?>:</span>
                        </section>
                        <section class="col-md-9 pull-left col-xs-6 quote-summary--body__text-body" id="going_out_pick_up"><?php echo $hrs;?></section>
                    </div>
                    <div class="row quote-summary--body__information">
                        <section class="col-md-6 pull-left col-xs-7">
                            <span class="quote-summary--body__text-header"> <?php echo __('Passengers','shbs');?>:</span>
                        </section>
                        <section class="col-md-6 pull-left col-xs-5 quote-summary--body__text-body" id="going_out_passengers"><?php echo $passengers;?></section>
                    </div>
                    <div class="row quote-summary--body__information">
                        <section class="col-md-6 pull-left col-xs-7">
                            <span class="quote-summary--body__text-header"> <?php echo __('Total','shbs');?>:</span>
                        </section>
                        <section class="col-md-6 pull-left col-xs-5 quote-summary--body__text-body" id="going_out_passengers">&euro;<?php echo money_format('%(#1n', $order['AMMOUNT']);?></section>
                    </div>
                </div>
            </div>
            <form action="" method="post">
                <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                        data-key="<?php echo $stripe['publishable_key']; ?>"
                        data-amount="<?php echo $price;?>"
                        data-card="454545545454545455454"
                        data-locale="es"
                        data-email="<?php echo $client_email;?>"
                        data-description="<?php echo $desc;?>"></script>
            </form>
        </section>

    </div>
<?php } } ?>