<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require( $parse_uri[0] . 'wp-load.php' );
$order = $_GET['order'];
$home = $_GET['site'];
global $wpdb;

$order = $wpdb->get_row($wpdb->prepare("select o.ID as rid, o.NO_ORDER as no_order,o.CLIENT as client,o.ROUTE as route,o.START_DATE as sdate,o.TOTAL as passengers, o.AMMOUNT as price,o.START_POINT as sfrom, o.END_POINT as sto,o.NOTES as onotes,o.HRS as hrs FROM ".$wpdb->prefix."shuttle_order o WHERE o.NO_ORDER=%s",$order));
$client = $wpdb->get_row($wpdb->prepare("select c.ID as cid, c.NAME as cnm,c.LAST_NAME as clnm,c.PHONE as cphone FROM ".$wpdb->prefix."shuttle_client c WHERE c.ID=%s",$order->client));
$route = $wpdb->get_row($wpdb->prepare("select r.ID as rid, r.NAME as rnm FROM ".$wpdb->prefix."shuttle_route r WHERE r.ID=%s",$order->route));
$from = $wpdb->get_row($wpdb->prepare("select f.ID as fid, f.ADDRESS as faddr FROM ".$wpdb->prefix."shuttle_stop f WHERE f.ID=%s",$order->sfrom));
$to = $wpdb->get_row($wpdb->prepare("select t.ID as tid, t.ADDRESS as taddr FROM ".$wpdb->prefix."shuttle_stop t WHERE t.ID=%s",$order->sto));
$conf = $wpdb->get_row($wpdb->prepare("select c.EMAIL_LOGO as logo, c.HEADER_INFO as iheader,c.CONTACT_INFO as icontact,c.LOGO_POS as logopos,c.BODY_INFO as cbody FROM ".$wpdb->prefix."shuttle_conf c",''));
$cbody = $conf->cbody;
$cbody = str_replace('_clientName_',$client->cnm, $cbody);
$cbody = str_replace('_clientLastName_',$client->clnm, $cbody);
$cbody = str_replace('_clientPhone_',$client->cphone, $cbody);
$cbody = str_replace('_noOrder_',$order->no_order, $cbody);
$cbody = str_replace('_Route_',$route->rnm, $cbody);
$cbody = str_replace('_date_',$order->sdate, $cbody);
$cbody = str_replace('_passengers_',$order->passengers, $cbody);
$cbody = str_replace('_price_',$order->price, $cbody);
$cbody = str_replace('_from_',$from->faddr, $cbody);
$cbody = str_replace('_to_',$to->taddr, $cbody);
$cbody = str_replace('_hrs_',$order->hrs, $cbody);
if($order->onotes!='' && $order->onotes!=null)
{
    $cbody = str_replace('_notes_',$order->onotes, $cbody);
}
?>
<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>ShuttleBus</title>
    <style>
        * {
            font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
            font-size: 100%;
            line-height: 1.6em;
            margin: 0;
            padding: 0;
        }

        img {
            max-width: 600px;
            width: 100%;
        }

        body {
            -webkit-font-smoothing: antialiased;
            height: 100%;
            -webkit-text-size-adjust: none;
            width: 100% !important;
        }

        a {
            color: #348eda;
        }

        .btn-primary {
            Margin-bottom: 10px;
            width: auto !important;
        }

        .btn-primary td {
            background-color: #62cb31;
            border-radius: 3px;
            font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
            font-size: 14px;
            text-align: center;
            vertical-align: top;
        }

        .btn-primary td a {
            background-color: #62cb31;
            border: solid 1px #62cb31;
            border-radius: 3px;
            border-width: 4px 20px;
            display: inline-block;
            color: #ffffff;
            cursor: pointer;
            font-weight: bold;
            line-height: 2;
            text-decoration: none;
        }

        .last {
            margin-bottom: 0;
        }

        .first {
            margin-top: 0;
        }

        .padding {
            padding: 10px 0;
        }

        table.body-wrap {
            padding: 20px;
            width: 100%;
        }

        table.body-wrap .container {
            border: 1px solid #e4e5e7;
        }

        table.footer-wrap {
            clear: both !important;
            width: 100%;
        }

        .footer-wrap .container p {
            color: #666666;
            font-size: 12px;

        }

        table.footer-wrap a {
            color: #999999;
        }

        h1,
        h2,
        h3 {
            color: #111111;
            font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
            font-weight: 200;
            line-height: 1.2em;
            margin: 40px 0 10px;
        }

        h1 {
            font-size: 36px;
        }
        h2 {
            font-size: 28px;
        }
        h3 {
            font-size: 22px;
        }

        p,
        ul,
        ol {
            font-size: 14px;
            font-weight: normal;
            margin-bottom: 10px;
        }

        ul li,
        ol li {
            margin-left: 5px;
            list-style-position: inside;
        }

        /* ---------------------------------------------------
            RESPONSIVENESS
        ------------------------------------------------------ */

        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
            clear: both !important;
            display: block !important;
            Margin: 0 auto !important;
            max-width: 600px !important;
        }

        /* Set the padding on the td rather than the div for Outlook compatibility */
        .body-wrap .container {
            padding: 40px;
        }

        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            display: block;
            margin: 0 auto;
            max-width: 600px;
        }

        /* Let's make sure tables in the content area are 100% wide */
        .content table {
            width: 100%;
        }

    </style>
</head>

<body bgcolor="#f7f9fa">
<table class="body-wrap" bgcolor="#f7f9fa">
    <tr>
        <td></td>
        <td class="container" bgcolor="#FFFFFF">

            <div class="content">
                <table>
                    <tr>
                        <td>
                            <?php
                            $pos = '';
                            if($conf->logopos=='left')$pos = 'float:left;';
                            if($conf->logopos=='center')$pos = 'margin-right:25%;margin-left:25%';
                            if($conf->logopos=='right')$pos = 'float:right;';
                            ?>
                            <img style="width:50%;max-width: 100%;height: auto; <?php echo $pos;?>" src="<?php
                            echo $home.'/wp-content/uploads/shuttlebus/email/'.$conf->logo;

                            ?>" />
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td>
                            <?php echo $conf->iheader;?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="content">
                <table>
                    <tr>
                        <td>
                            <?php echo $cbody;?>
                        </td>
                    </tr>
                </table>
            </div>

        </td>
        <td></td>
    </tr>
</table>

<table class="footer-wrap">
    <tr>
        <td></td>
        <td class="container">
            <div class="content">
                <table>
                    <tr>
                        <td align="center">
                            <?php echo $conf->icontact;?>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        <td></td>
    </tr>
</table>
</body>
</html>