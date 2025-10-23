<?php
$CI =& get_instance(); 
$clean_cert = trim($lotw_cert_info->cert);
$cert1 = str_replace("-----BEGIN CERTIFICATE-----", "", $clean_cert);
$cert2 = str_replace("-----END CERTIFICATE-----", "", $cert1);
?>
<TQSL_IDENT:54>TQSL V2.8.2 Lib: V2.6 Config: V11.34 AllowDupes: false

<Rec_Type:5>tCERT
<CERT_UID:1>1
<CERTIFICATE:<?php echo strlen(trim($cert2)) + 1; ?>><?php echo trim($cert2); ?>

<eor>

<Rec_Type:8>tSTATION
<STATION_UID:1>1
<CERT_UID:1>1
<?php
print "<CALL:".strlen($lotw_cert_info->callsign).">".$lotw_cert_info->callsign."\n";
print "<DXCC:".strlen($lotw_cert_info->cert_dxcc_id).">".$lotw_cert_info->cert_dxcc_id."\n";
if($station_profile->station_gridsquare) {
	print "<GRIDSQUARE:".strlen($station_profile->station_gridsquare).">".$station_profile->station_gridsquare."\n";
}
if($station_profile->station_itu) {
	print "<ITUZ:".strlen($station_profile->station_itu).">".$station_profile->station_itu."\n";
}
if($station_profile->station_cq) {
	print "<CQZ:".strlen($station_profile->station_cq).">".$station_profile->station_cq."\n";
}
if($station_profile->station_iota) {
	print "<IOTA:".strlen($station_profile->station_iota).">".$station_profile->station_iota."\n";
}

switch ($lotw_cert_info->cert_dxcc_id) {
	case 6:       // Alaska
	case 110:     // Hawaii
	case 291:     // Cont US
		if($station_profile->state != "") {
			print "<US_STATE:".strlen($station_profile->state).">".$station_profile->state."\n";
		}
		if($station_profile->station_cnty != "") {
			print "<US_COUNTY:".strlen($station_profile->station_cnty).">".$station_profile->station_cnty."\n";
		}
		break;
	case 1:       // Canada
		if($station_profile->state != "") {
			print "<CA_PROVINCE:".strlen($CI->lotw_ca_province_map($station_profile->state)).">".$CI->lotw_ca_province_map($station_profile->state)."\n";
		}
		break;
	case 15:      // Asiatic Russia
	case 54:      // European Russia
	case 61:      // FJL
	case 125:     // Juan Fernandez
	case 151:     // Malyj Vysotskij
		if($station_profile->state != "") {
			print "<RU_OBLAST:".strlen($CI->lotw_ru_oblast_map($station_profile->state)).">".$CI->lotw_ru_oblast_map($station_profile->state)."\n";
		}
		break;
	case 318:     // China
		if($station_profile->state != "") {
			print "<CN_PROVINCE:".strlen($station_profile->state).">".$station_profile->state."\n";
		}
		break;
	case 150:     // Australia
		if($station_profile->state != "") {
			print "<AU_STATE:".strlen($station_profile->state).">".$station_profile->state."\n";
		}
		break;
	case 339:     // Japan
		if($station_profile->state != "") {
			print "<JA_PREFECTURE:".strlen($station_profile->state).">".$station_profile->state."\n";
		}
		if($station_profile->station_cnty != "") {
			print "<JA_CITY_GUN_KU:".strlen($station_profile->station_cnty).">".$station_profile->station_cnty."\n";
		}
		break;
	case 5:       // Aland Island
	case 224:     // Finland
		if($station_profile->state != "") {
			print "<FI_KUNTA:".strlen($station_profile->state).">".$station_profile->state."\n";
		}
		break;
}
?>
<eor>

<?php foreach ($qsos->result() as $qso) { 
	unset($freq_in_mhz);
	unset($freq_in_mhz_rx);
?>
<Rec_Type:8>tCONTACT
<STATION_UID:1>1
<?php
print "<CALL:".strlen($qso->COL_CALL).">".$qso->COL_CALL."\n";
print "<BAND:".strlen($qso->COL_BAND).">".strtoupper($qso->COL_BAND)."\n";
print "<MODE:".strlen($CI->mode_map($qso->COL_MODE, $qso->COL_SUBMODE)).">".strtoupper($CI->mode_map(($qso->COL_MODE == null ? '' : strtoupper($qso->COL_MODE)), ($qso->COL_SUBMODE == null ? '' : strtoupper($qso->COL_SUBMODE))))."\n";
if($qso->COL_FREQ != "" && $qso->COL_FREQ != "0") {
	$freq_in_mhz = $qso->COL_FREQ / 1000000;
	print "<FREQ:".strlen($freq_in_mhz).">".$freq_in_mhz."\n";
}
if($qso->COL_FREQ_RX != "" && $qso->COL_FREQ_RX != "0") {
	$freq_in_mhz_rx = $qso->COL_FREQ_RX / 1000000;
	print "<FREQ_RX:".strlen($freq_in_mhz_rx).">".$freq_in_mhz_rx."\n";
}
if($qso->COL_PROP_MODE) {
	print "<PROP_MODE:".strlen($qso->COL_PROP_MODE).">".strtoupper($qso->COL_PROP_MODE)."\n";
}
if($qso->COL_SAT_NAME) {
	print "<SAT_NAME:".strlen($qso->COL_SAT_NAME).">".strtoupper($qso->COL_SAT_NAME)."\n";
}
if($qso->COL_BAND_RX) {
	print "<BAND_RX:".strlen($qso->COL_BAND_RX).">".strtoupper($qso->COL_BAND_RX)."\n";
}
$date_on = strtotime($qso->COL_TIME_ON);
$new_date = date('Y-m-d', $date_on);
print "<QSO_DATE:".strlen($new_date).">".$new_date."\n";
$time_on = strtotime($qso->COL_TIME_ON);
$new_on = date('H:i:s', $time_on);
print "<QSO_TIME:".strlen($new_on."Z").">".$new_on."Z\n";

$sign_string = "";

if ($lotw_cert_info->cert_dxcc_id == 150) { // If state is Australia add AU state
	if($station_profile->state != "") {
		$sign_string .= $station_profile->state;
	}
}

if ($lotw_cert_info->cert_dxcc_id == 1) { // If state is Canada add CA province
	if($station_profile->state != "") {
		$sign_string .= $CI->lotw_ca_province_map($station_profile->state);
	}
}

if ($lotw_cert_info->cert_dxcc_id == 318) { // If state is China add CN province
	if($station_profile->state != "") {
		$sign_string .= $station_profile->state;
	}
}

// Add CQ Zone
if($station_profile->station_cq) {
	$sign_string .= $station_profile->station_cq;
}

if (in_array($lotw_cert_info->cert_dxcc_id, array(5, 224))) { // If state is Finland add FI kunta
	if($station_profile->state != "") {
		$sign_string .= $station_profile->state;
	}
}

// Add Gridsquare
if($station_profile->station_gridsquare) {
	$sign_string .= $station_profile->station_gridsquare;
}

if($station_profile->station_iota) {
	$sign_string .= $station_profile->station_iota;
}

if($station_profile->station_itu) {
	$sign_string .= $station_profile->station_itu;
}

if ($lotw_cert_info->cert_dxcc_id == 339) { // If state is Japan add JA city/gun/ku and prefecture
	if($station_profile->station_cnty != "") {
		$sign_string .= $station_profile->station_cnty;
	}
	if($station_profile->state != "") {
		$sign_string .= $station_profile->state;
	}
}

if (in_array($lotw_cert_info->cert_dxcc_id, array(15, 54, 61, 125, 151))) { // If state is Russia add RU oblast
	if($station_profile->state != "") {
		$sign_string .= $CI->lotw_ru_oblast_map($station_profile->state);
	}
}

if (in_array($lotw_cert_info->cert_dxcc_id, array(6, 110, 291))) { // If state is US, Alaska or Hawaii add US cnty and state
	if($station_profile->station_cnty != "") {
		$sign_string .= $station_profile->station_cnty;
	}
	if($station_profile->state != "") {
		$sign_string .= $station_profile->state;
	}
}

if($qso->COL_BAND) {
	$sign_string .= $qso->COL_BAND;
}

if($qso->COL_BAND_RX) {
	$sign_string .= $qso->COL_BAND_RX;
}

if($qso->COL_CALL) {
	$sign_string .= $qso->COL_CALL;
}

if(isset($freq_in_mhz)) {
	$sign_string .= $freq_in_mhz;
}

if(isset($freq_in_mhz_rx)) {
	$sign_string .= $freq_in_mhz_rx;
}

if($qso->COL_MODE) {
	$sign_string .= $CI->mode_map($qso->COL_MODE, $qso->COL_SUBMODE);
}

if($qso->COL_PROP_MODE) {
	$sign_string .= $qso->COL_PROP_MODE;
}

$sign_string .= $new_date;
$sign_string .= $new_on."Z";

if($qso->COL_SAT_NAME) {
	$sign_string .= $qso->COL_SAT_NAME;
}

$sign_string = strtoupper($sign_string);

$signed_item = trim($CI->signlog($lotw_cert_info->cert_key, $sign_string));
print "<SIGN_LOTW_V2.0:".(strlen($signed_item)+intdiv(strlen($signed_item),64)+1).":6>";
for ($i=0; $i<strlen($signed_item); $i+=64) {
	print substr($signed_item, $i, 64)."\n";
}
print "<SIGNDATA:".strlen($sign_string).">".$sign_string."\n";
?>
<eor>

<?php } ?>
