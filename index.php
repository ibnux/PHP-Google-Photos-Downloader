<?php
require 'vendor/autoload.php';
include "config.php";
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $baseUrl = $db->get('t_photos', 'baseUrl', ['hash' => $_GET['delete']]);
    if ($baseUrl == 'DELETED') {
        $db->update('t_photos', ['baseUrl' => ''], ['hash' => $_GET['delete']]);
    } else if (file_exists($baseUrl)) {
        unlink($baseUrl);
        if (!file_exists($baseUrl)) {
            $db->update('t_photos', ['baseUrl' => 'DELETED'], ['hash' => $_GET['delete']]);
        }
    }
}

if (isset($_GET['fav']) && !empty($_GET['fav'])) {
    $fav = $db->get('t_photos', 'favorite', ['hash' => $_GET['fav']]);
    $db->update('t_photos', ['favorite' => ($fav==1)?0:1 ], ['hash' => $_GET['fav']]);
    $_GET['delete'] = 'fav';
}

$tipe = $_GET['tipe'];

$max = 48;
$page = $_GET['p'] * 1;
$pageNow = $page * $max;
if($tipe=='rand'){
    $max = 12;
    $datas = $db->rand("t_photos", '*', ['baseUrl[!]' => 'DELETED', 'LIMIT' => $max]);
    $total = 0;
}else if($tipe=='fav'){
    $datas = $db->select("t_photos", '*', ['AND'=>['baseUrl[!]' => 'DELETED','favorite' => 1], 'LIMIT' => [$pageNow, $max], 'ORDER' => ['date_added' => 'DESC']]);
    $total = $db->count("t_photos",['AND'=>['baseUrl[!]' => 'DELETED','favorite' => 1]]);
}else{
    $datas = $db->select("t_photos", '*', ['baseUrl[!]' => 'DELETED', 'LIMIT' => [$pageNow, $max], 'ORDER' => ['date_added' => 'DESC']]);
    $total = $db->count("t_photos",['baseUrl[!]' => 'DELETED']);
}
$pages = ($total / $max);
if (!isset($_GET['delete'])) {
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Google Photos</title>
    <style>
        .card-columns {
            column-count: 6;
        }
    </style>
</head>

<body class="pr-3">
    <h1>Google Photos</h1>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link <?php if($tipe=='') echo 'active'?>" href="/">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if($tipe=='rand') echo 'active'?>" href="/?tipe=rand">Random</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if($tipe=='fav') echo 'active'?>" href="/?tipe=fav">Favorite</a>
            </li>
        </ul>
    <div id="konten">
<?php     }//not isset delete
        if($total>0){
            ob_start(); ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 0) { ?>
                        <li class="page-item"><a class="page-link" href="?p=<?= $page - 1 ?>&tipe=<?=$tipe?>">Previous</a></li>
                    <?php } ?>
                    <li class="page-item">
                        <form>
                            <select name="p" class="form-control" onchange="this.form.submit()">
                                <?php
                                for ($n = 0; $n < $pages; $n++) {
                                    if ($n == $page)
                                        echo '<option selected value="' . $n . '">' . ($n + 1) . '</option>';
                                    else
                                        echo '<option value="' . $n . '">' . ($n + 1) . '</option>';
                                }
                                ?>
                            </select>
                            <input type="hidden" name="tipe" value="<?=$tipe?>">
                        </form>
                    <li class="page-item"><a class="page-link" href="?p=<?= $page + 1 ?>&tipe=<?=$tipe?>">Next</a></li>
                </ul>
            </nav>
            <?php
            $pagination = ob_get_contents();
            ob_end_flush();
        }
        ?>
    <hr>
    <div class="card-columns">
        <?php
        foreach ($datas as $data) {
        ?><div class="card">
                <?php if(strpos($data['mimeType'],'video')===false){ ?>
                <a href="<?= $data['baseUrl'] ?>" data-toggle="lightbox" data-gallery="google-gallery">
                    <img src="<?= $data['baseUrl'] ?>" class="card-img-top img-fluid" alt="">
                </a>
                <?php }else{ ?>
                    <video class="card-img-top img-fluid" controls>
                        <source src="<?= $data['baseUrl'] ?>" type="<?=$data['mimeType']?>">
                    </video>
                <?php } ?>
                <a href="javascript:hapus('<?= $data['hash'] ?>')" class="close float-left" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>
                <a href="javascript:fav('<?= $data['hash'] ?>')" class="close" aria-label="Close">
                    <span class="true" title="Favorite"><?=($data['favorite']==1)?'x':'+'?></span>
                </a>
                <div class="card-block d-none d-md-block">
                    <p class="card-text"><small class="text-muted"><?= $data['filename'] ?></small></p>
                </div>
            </div>
        <?php } ?>
    </div>
    <hr>
    <div>
        <?= ($total>0)?$pagination:'' ?>
    </div>

    <hr>
    <br><br>
</div>
<?php if (!isset($_GET['delete'])) { ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>
    <script>
        function hapus(hash) {
            $("#konten").load("/?p=<?= $page ?>&tipe=<?=$tipe?>&delete="+hash,);
        }
        function fav(hash) {
            $("#konten").load("/?p=<?= $page ?>&tipe=<?=$tipe?>&fav="+hash,);
        }

        $(document).on('click', '[data-toggle="lightbox"]', function(event) {
            event.preventDefault();
            $(this).ekkoLightbox();
        });
    </script>
</body>

</html>
<?php }