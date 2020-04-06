<?php
include 'vendor/autoload.php';
include 'config.php';
/**
 * Oauth2 login for google
 * its simple, wo i don't use any library
 */



function connectWithGooglePhotos($scopes, $redirectURI)
{
    global $client_id ,$client_sc;

    // The authorization URI will, upon redirecting, return a parameter called code.
    // =http%3A%2F%2Fphotos.ibnux.org%2Flogin.php&state&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fphotoslibrary
    if (!isset($_GET['code'])) {
        header("Location: https://accounts.google.com/o/oauth2/v2/auth?response_type=code&prompt=consent&access_type=offline&client_id=".$client_id.
                "&redirect_uri=".urlencode($redirectURI)."&scope=".urlencode($scopes));
    } else {
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            json_encode([
                    'code' => urldecode($_GET['code']),
                    'client_id' => $client_id,
                    'client_secret' => $client_sc,
                    'redirect_uri' => $redirectURI,
                    'grant_type' => 'authorization_code'
                ]));
        $authToken = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($authToken,true);
        if(isset($json['access_token'])){
            //save refresh token
            file_put_contents("data/refresh.token",$json['refresh_token']);
            //save real token
            file_put_contents("data/credentials.txt",$authToken);
        }else{
            echo "gagal";
        }
        echo $authToken;
    }
}

connectWithGooglePhotos(
    $scopes,
    $redirect
);