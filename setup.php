#!/usr/bin/env php
<?php

$skipEnv = false;

if (file_exists("./.env")) {
    echo "Detected existing ENV file.  Setup has occured before.  Only doing updates.";
    $skipENV = true;
}
$emailfrom = null;
$emailname = null;

if (!$skipENV) {

    $a = '0123456789abcdef';
    $secret = '';
    for ($i = 0; $i < 32; $i++) {
        $secret .= $a[rand(0, 15)];
    }

    $dbuser=prompt("Database User");
    $dbpass=prompt("Database Password", null, true);
    $dbhost=prompt("Database Host", "localhost");
    $dbport=prompt("Database Port", "3306");
    $dbbase=prompt('Database Name', 'hotbox');
    echo "\n";
    $smtptype=prompt("SMTP Service", "gmail");
    $smtpuser=urlencode(prompt("SMTP User"));
    $smtppass=urlencode(prompt("SMTP Password", null, true));
    echo "\n";
    $emailfrom=prompt("Email address");
    $emailname=prompt("From name");
    echo "\n";
    echo "Writing .env file";

    $out = "# This is an auto-generated file.  
    DATABASE_URL=\"mysql://{$dbuser}:{$dbpass}@{$dbhost}:{$dbport}/{$dbbase}?serverVersion=10.11.2-MariaDB&charset=utf8mb4\"
    
    APP_ENV=prod
    APP_SECRET={$secret}
    
    MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
    
    MAILER_DSN=\"{$smtptype}+smtp://{$smtpuser}:{$smtppass}@default\"
    MAILER_FROMADDR=\"{$emailfrom}\"
    MAILERFROMNAME=\"{$emailname}\"
    
    APP_TIMEZONE=America/New_York
    ";

    file_put_contents("./.env", $out);
}



@exec("composer install --no-scripts", $return, $retval);

if ($retval !==0 ) {
    die("Composer did not run properly.  Please run composer install");
}

if ($skipENV) {
    echo "Updating database\n";
} else {
    echo "Installing database\n";
}

@exec("./bin/console doctrine:migrations:migrate -n", $return, $retval);

if ($retval !==0 ) {
    die("Database did not install correctly.\n");
}

if (!$skipENV) {
    echo "Creating initial user account.\n";

    $ownemail = prompt("Owner email");
    $ownpass = prompt('Owner password', null, true);

    @exec("./bin/console hotbox:new-user {$ownemail} {$ownpass} OWNER,ADMINISTRATOR,CREATOR", $return, $retval);

    if ($retval !==0 ) {
        die("Error.  Did not create initial user.\n");
    }

    $servername = prompt("What is the name/title of this server? e.g. Fred's Hotbox");
    $hostname = prompt("Server URL (please include http(s):// in your url)");

    @exec("./bin/console hotbox:set-config server_name $servername");
    @exec("./bin/console hotbox:set-config server_url $hostname");
    @exec("./bin/console hotbox:set-config email_from_name $emailname");
    @exec("./bin/console hotbox:set-config email_from_address $emailfrom");

}

echo "\nUpdating permissions on storage and cache directories\n";

// TODO - Permissions need to be writable for the Webserver (apache/nginx/etc)  Best method to do this is?
// Option 1 - Change entire root to be owned by webserver
// Option 2 - Change perms for specific directories (storage/caching) to be writable
// Going with option 2 until a better idea comes up.

if (!is_dir("./var/cache")) {
    mkdir("./var/cache", 0777, true);
}
@exec("chmod -R 777 ./var", $return, $retval);

if (!is_dir("./storage")) {
    mkdir("./storage", 0777, true);
}
@exec("chmod -R 777 ./storage", $return, $retval);


echo "\nSetup is complete\n";



function prompt($message = 'prompt: ', $default = null, $hidden = false) {
    if (PHP_SAPI !== 'cli') {
        return false;
    }
    $display = $message;
    if (!empty($default)) {
        $display .= " [{$default}]";
    }
    $display .= ": ";
    echo $display;
    $ret =
        $hidden
            ? exec(
            PHP_OS === 'WINNT' || PHP_OS === 'WIN32'
                ? __DIR__ . '\prompt_win.bat'
                : 'read -s PW; echo $PW'
        )
            : rtrim(fgets(STDIN), PHP_EOL)
    ;
    if ($hidden) {
        echo PHP_EOL;
    }
    if (empty($ret) && !empty($default)) {
        $ret = $default;
    }
    return $ret;
}