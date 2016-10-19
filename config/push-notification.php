<?php

return array(

    'appNameIOS'     => array(
        'environment' =>'devolopment',
        'certificate' =>'/var/www/html/habbis/ckipad.pem',
        'passPhrase'  =>'12345678',
        'service'     =>'apns'
    ),
    'appNameAndroid' => array(
        'environment' =>'production',
        'apiKey'      =>'yourAPIKey',
        'service'     =>'gcm'
    )

);
