<?php

function gen_oauth_creds() {
	// Get a whole bunch of random characters from the OS
	$fp = fopen('/dev/urandom','rb');
	$entropy = fread($fp, 32);
	fclose($fp);

	// Takes our binary entropy, and concatenates a string which represents the current time to the microsecond
	$entropy .= uniqid(mt_rand(), true);

	// Hash the binary entropy
	$hash = hash('sha512', $entropy);

	// Base62 Encode the hash, resulting in an 86 or 85 character string
	$hash = gmp_strval(gmp_init($hash, 16), 62);

	// Chop and send the first 80 characters back to the client
	return array(
		'consumer_key' => substr($hash, 0, 16),
		'shared_secret' => substr($hash, 32, 48)
	);
}

$credentials = gen_oauth_creds();
print_r($credentials);

print_r("Consumer Key Length: ".strlen($credentials['consumer_key']));
print_r("Secret Key Length: ".strlen($credentials['shared_secret']));



?>