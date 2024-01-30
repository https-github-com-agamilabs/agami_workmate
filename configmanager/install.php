<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Config Settings</title>

	<link href='../css/main.css' rel='stylesheet'>
</head>

<body>

	<?php

	$domain_config_access_key = null;
	$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "localhost";

	$mandatory_fields = array(
		"orgname",
		"orgname_bn",
		"orgnamecapital",
		"orglocation",
		"orglocation_bn",
		"orglogourl",
		"fullorglogourl",
		"firstslider",
		"orgheadsignurl",
		"orglogo_text",
		"org_lp_email",
		"org_noreply_from_email",
		"org_lp_contact",
		"org_socialmedia_facebook_url",
		"org_socialmedia_linkedin_url",
		"org_socialmedia_twitter_url",
		"org_socialmedia_youtubechannel_url",
		"org_copyright",
		"developer_name",
		"developer_web_url",
		"DB_NAME",
		"DB_USER",
		"DB_PASS",
		"DB_HOST"
	);

	$optional_fields = array(
		"org_sub_title",
		"first-nav-back",
		"second-nav-back",
		"first-footer-back",
		"second-footer-back",
		"org_theme",
		"org_id_contact",
		"org_eiin",
		"admission_open",
		"admission_notfound_msg",
		"admission_deadline_msg"
	);

	$combined_fields = array_merge($mandatory_fields, $optional_fields);

	$response = array(); // init as empty for safety

	$config_file = dirname(__FILE__) . "/org_" . $host . "_configuration.php";


	// check if in post

	$missing = array_diff_key(array_flip($combined_fields), $_POST);

	if (count($missing) == 0) {
		// save changes was called, now save to php

		$phpstr = "<?php ";

		for ($i = 0; $i < count($combined_fields); $i++) {
			// code...
			$phpstr .= "\$response['" . $combined_fields[$i] . "']='" . $_POST[$combined_fields[$i]] . "'; ";
		}

		$phpstr .= "?>";

		$phpstr = implode('$', explode('\$', $phpstr));

		file_put_contents($config_file, $phpstr);
	}

	if (!isset($_POST['domain_config_access_key'])) {
		// show input for domain config access key
		echo "<div class='container py-5'>";
		echo "<form action='install.php' method='post'>";
		echo "<div class='position-relative row form-group'>";
		echo "<label for='domain_config_access_key' class='col-sm-2'>License Key for <b>'$host'</b></label>";
		echo "<div class='col-sm-10'>";
		echo "<input name='domain_config_access_key' id='domain_config_access_key' placeholder='Enter License Key...' type='text' class='form-control form-control-lg shadow-sm' required>";
		echo "</div>";
		echo "</div>";
		echo "<div style='text-align:center'>";
		echo "<button class='btn btn-primary btn-lg px-5 shadow' type='submit'>Edit Config</button>";
		echo "</div>";
		echo "</form>";
		echo "</div>";
		exit();
	} else {
		// check access key validity
		$domain_config_access_key = strip_tags($_POST['domain_config_access_key']);

		if (strcasecmp($domain_config_access_key, base64_encode($host)) !== 0) {
			echo "<div class='container py-5'>";
			echo "<div class='h6 alert alert-danger shadow-sm'>Invalid Access Key for <b>'$host'</b>. Get Access Key from AGAMiLabs Ltd.</div>";
			echo "</div>";
			exit();
		}
		echo '<div class="h6 alert alert-primary shadow-sm text-center mx-auto mt-2" style="width: max-content;">';
		echo "<h5 class='mb-0'> <b>'$host'</b> config file is in edit mode.</h5>";
		echo '</div>';
	}



	if (file_exists($config_file)) {
		// echo $config_file;
		// echo "<br/>";
		// echo "config_file exists";
		// exit();

		require $config_file; // this file should contain a $response variable containing mandatory fields
	}

	echo "<div class='container pb-5'>";
	echo "<h3><u style='text-underline-position: under;'>Configuration Manager</u></h3>";
	echo "<form method='post' action='install.php'>";
	for ($i = 0; $i < count($combined_fields); $i++) {
		$val = isset($response[$combined_fields[$i]]) ? $response[$combined_fields[$i]] : "";
		echo '<div class="position-relative row form-group">';
		echo '<label class="col-md-5 col-lg-4 col-form-label"><div class="d-flex flex-wrap justify-content-md-between font-weight-bold"><div class="mr-2">'
			. ($i + 1) . '.</div><div>'
			. $combined_fields[$i] . '</div></div></label>';
		echo '<div class="col-md-7 col-lg-8">';
		echo '<input name="' . $combined_fields[$i] . '" value="' . $val . '" placeholder="' . strtoupper($combined_fields[$i]) . '" type="text" class="form-control shadow-sm" ' . (in_array($combined_fields[$i], $mandatory_fields) ? 'required' : '') . '>';
		echo '</div>';
		echo '</div>';
	}

	echo "<div style='text-align:center'>";
	echo "<button class='btn btn-primary btn-lg px-5 shadow' type='submit'>Save Changes</button>";
	echo "</div>";
	echo "</form>";
	echo "</div>";
	?>
</body>

</html>