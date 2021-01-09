<?php
define('APPNAME', "dhru-rest-api-framework");

define('VERSION_MAJOR', '1'); //<MAJOR>
define('VERSION_MINOR', '0'); //<MINOR>
define('VERSION_REVISION', '0');//<REVISION>

define('DEVELOPMENT_STAGE', 'alpha'); /*alpha,beta,rc,final */
define("ENV", "dev"); //production


define('DB_CONFIG_PATH', ''); // Database connection.  "../../config/config.php"

define('API_URL_DEVELOPMENT', "http://localhost:8080/");
define('API_URL_BETA', "https://beta-testing-url.com/");
define('API_URL_PRODUCTION', "https://final-prod-url.com/");
define('PUBLIC_API_FOLDER_NAME', "");


/*
 * Auto Sync with POSTMAN.
 */
define("POSTMAN_API_KEY", ""); // POSTMAN API Key
define("COLLECTION_UID", ""); // POSTMAN Collection uid
define("ENV_UID_PRODUCTION", ""); // POSTMAN Environment uid for prod.
define("ENV_UID_BETA", ""); // POSTMAN Environment uid for beta.
define("ENV_UID_DEVELOPMENT", ""); // POSTMAN Environment uid for development.


/*
 * JWT Token setting
 */
define('TOKEN_EXPIRE', 18000); // in sec 2 min expiry
define('TOKEN_IP_VALIDATION', false); // in sec


/*
 * Google Recaptcha v3
 */
define('GOOGLE_RECAPTCHA_SECRET', '');


/*
 * Define private/public KEY for JWT Tokens.
 */
$privateKey = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
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
-----END RSA PRIVATE KEY-----
EOD;

$publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA8C+T9emDwnXAZ5qMTf/f
/cXFUo3dXktpN6INTPiFVNnyKVtmA3saMsoRZwlF6qAhDNHBNcWSIsYUFkhdAyjO
00s0AeFQ4224fSRzf823bqM1f+PPQjRRXAkxbRqiHhyxyRSLej1/QYseAv/3wXB7
vRAfnU9gIy6QCWsvDa50dm4puJ1cH/3y2exMhKE77QUFbMWZFUnggu1jJuEGyaO4
Um9dpwYP9GHMpS5RxjX+PjynA/LU2IARkqb0iQB37U+Zqsu1aaAv1NFKQ3jZawKe
rxa5XLjiL1USGwQTLC7gbL/oEC2KXEhnyYSFY5Y8CvXt2Lggb5Ioi7owm8wkVjlr
K/htCQjjIT6PyBtyglF22Dxqfz8rLtm8uPrFAnhfOOfkhh+6U6NAB2YQpNCpXPJm
RskG0wP0JEi//HQIus/JP+5zPtJDTjB8YiuObqCDdqyA6rpkGi0okRh6ZRawNC3+
9E5aQ57685vFOqKbGsHsS0a1oF9eXUkIxcaG3qVIEKLxwDYXxHIlJSGyA8sSymAA
HW46OmNwkER9PvVMiGF0X87bFoUcRvfAnztwf08wFcF25KuQZtg25EFHjcFvUGSN
hMLCqy3vFrihpkHh1leNGjMk01nEs8AIL7E8TxP8+bJ5291LYAm+UiWBH8PAjltd
CfoRMWW/joq6P6DO4C6VI70CAwEAAQ==
-----END PUBLIC KEY-----
EOD;

