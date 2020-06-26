<?php
/**
 * REST API DOC
 * https://developers.google.com/photos/library/guides/get-started
 *
 * run on command line more better
 */
require 'vendor/autoload.php';
require 'config.php';

// to get detail item in case i need it
//GET https://photoslibrary.googleapis.com/v1/mediaItems/{mediaItemId}

$jsoncredential = json_decode(file_get_contents('data/credentials.txt'),true);

/**
 * Retrieves the user's albums, and renders them in a grid.
 */

if(!isCLI()){
    echo "<pre>";
}

//delete content to restart from beginning
$pageToken = file_get_contents("data/page.token");
ulang:

//cek sudah expired belum?
$sisa = time()-filemtime("data/credentials.txt");
echo "\nbaru $sisa detik dari ".($jsoncredential['expires_in']-300)." detik\n\n";
if($sisa>$jsoncredential['expires_in']-300){
    updateToken();
}
$fotos = getData($pageSize,$pageToken);
if(!isset($fotos['mediaItems'])){
    print_r($fotos);
    sleep(5*50);
    goto ulang;
}

echo "pageToken: ".$pageToken."\n";
foreach ($fotos['mediaItems'] as $data) {
    if(!$db->has('t_photos',['id'=>$data['id']])){
        //insert
        unset($data['contributorInfo']);
        echo $data['mimeType']."  ".$data['filename']."\n";
        $data['mediaMetadata'] = (is_array($data['mediaMetadata']))? json_encode($data['mediaMetadata']) : $data['mediaMetadata'];
        echo "wget ".$data['filename']."\n";
        $baseURL = $data['baseUrl'];
        $filename = sha1($data['id']);
        $a = 'images/'.substr($filename,0,1);
        $b = substr($filename,1,1);
        if(strpos($data['mimeType'],"video")===false){
            $ext = "jpg";
            $param = "=d";
        }else{
            $ext = "mp4";
            $param = "=dv";
        }
        if(!file_exists($a)) mkdir($a);
        if(!file_exists("$a/$b")) mkdir("$a/$b");
        $filename = "$a/$b/".$filename.".$ext";
        $data['baseUrl'] = $filename;
        $count = 0;
        $data['hash'] = downloadImage($baseURL.$param,$filename);
        echo "hash ".$data['hash']."\n";
        echo "size ".filesize($filename)."\n";
        if(!isCLI()){
            echo "<img src=\"$filename\" width=\"256\">\n";
        }
        echo "\n";
        //check duplicate who has same hash
        $hash = $db->get('t_photos','id',['AND'=>['hash'=>$data['hash'],'id[!]'=>$data['id']]]);
        $db->insert('t_photos',$data);
    }else{
        //if not deleted
        if(!($db->get('t_photos','baseUrl',['id'=>$data['id']])=='DELETED')){
            //update cek size
            unset($data['contributorInfo']);
            echo $data['mimeType']."  ".$data['filename']."\n";
            $baseURL = $data['baseUrl'];
            $filename = sha1($data['id']);
            $a = 'images/'.substr($filename,0,1);
            $b = substr($filename,1,1);
            if(strpos($data['mimeType'],"video")===false){
                $ext = "jpg";
                $param = "=d";
            }else{
                $ext = "mp4";
                $param = "=dv";
            }
            if(!file_exists($a)) mkdir($a);
            if(!file_exists("$a/$b")) mkdir("$a/$b");
            $filename = "$a/$b/".$filename.".$ext";
            if(!file_exists($filename) || filesize($filename)<10000){
                if(file_exists($filename))
                    echo ".filesize ".filesize($filename)."\n";
                else echo "not exists\n";
                $count = 0;
                $data['hash'] = downloadImage($baseURL.$param,$filename);
                echo ".hash ".$data['hash']."\n";
                echo ".size ".filesize($filename)."\n";
                if(!isCLI()){
                    echo "<img src=\"$filename\" width=\"256\">\n";
                }
                echo "\n";
                //check duplicate who has same hash
                $hash = $db->get('t_photos','id',['AND'=>['hash'=>$data['hash'],'id[!]'=>$data['id']]]);
                $db->update('t_photos',['baseUrl'=>$filename,'root_id'=>$hash,'hash'=>$sha1],['id'=>$data['id']]);
            }else{
                $data['hash'] = sha1_file($filename);
                $db->update('t_photos',['hash'=>$data['hash']],['id'=>$data['id']]);
                echo "exists\n";
            }
        }else{
            echo "DELETED\n";
        }
    }
}
echo "\n";
if(isset($fotos['nextPageToken']) && !empty($fotos['nextPageToken'])){
    $pageToken = $fotos['nextPageToken'];
    file_put_contents("data/page.token",$pageToken);
    if(!isCLI()){
        echo '<meta http-equiv="refresh" content="3">';
    }else{
        sleep(rand(1,10));
        goto ulang;
    }
}else{
    file_put_contents("data/page.token",'');
    echo "FINISH\n\n";
}

//if token expired, we need to update new token
function updateToken(){
    global $client_id ,$client_sc,$jsoncredential;
    echo "update Token\n";
    $refresh_token = file_get_contents('data/refresh.token');
    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        json_encode([
                'client_id' => $client_id,
                'client_secret' => $client_sc,
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token'
            ]));
    $authToken = curl_exec($ch);
    curl_close($ch);
    $jsoncredential = json_decode($authToken,true);
    if(isset($jsoncredential['access_token'])){
        file_put_contents("data/credentials.txt", $authToken);
    }
}

$count = 0;

function downloadImage($url,$name){
    global $count;
    echo "wget ".$name."\n";
    set_time_limit(0);
    //This is the file where we save the    information
    $fp = fopen ($name, 'w+');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:3128');
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // get curl response
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    //below 10kb is fail
    if(filesize($name)<10000){
        if($count<5){
            $count++;
            echo "retry $count\n";
            return downloadImage($url,$name);
        }else{
            echo "FAILED $url\n";
            unlink($name);
            return "";
        }
    }
    return sha1_file($name);
}

function getData($pageSize=10,$pageToken=null){
    global $jsoncredential;
    $url = 'https://photoslibrary.googleapis.com/v1/mediaItems?pageSize='.$pageSize.'&pageToken='.$pageToken ;
    echo "url: ".$url."\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:3128');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization: Bearer '.$jsoncredential['access_token']));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result,true);
}

function isCLI()
{
    return (php_sapi_name() === 'cli');
}