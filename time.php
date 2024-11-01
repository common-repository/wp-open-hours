<?php
date_default_timezone_set('Europe/London');
//date_default_timezone_set('Asia/Kolkata');
$info = getdate();
$min = $info['minutes'];
$sec = $info['seconds'];
$am = date('a');
$hours = date('H');
$mint = date('i');
?>
<script language="text/javascript">
	var d = new Date();
	var offset = 0;
	utc = d.getTime() + (d.getTimezoneOffset() * 60000);
	nd = new Date(utc + (3600000*offset));
	var todayh = nd.getHours();
	var todaymin = nd.getMinutes();
	document.getElementById('mod-fwopenhours-date-time').innerHTML=todayh+":"+todaymin;
</script>