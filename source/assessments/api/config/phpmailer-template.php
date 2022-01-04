<?php
    $mailAccount = '';
    $mailPassword = '';
    $mailAddress = '';
    $mailSMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mailSMTPHost = '';
    $mailSMTPPort = 587;