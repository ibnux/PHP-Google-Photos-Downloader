<?php
require 'vendor/autoload.php';
include "config.php";

$max = 50;
$page = $_GET['p']*1;
$pageNow = $page*$max;
$datas = $db->select("t_photos",'*',['LIMIT'=>[$pageNow,$max],'ORDER'=>['date_added'=>'DESC']]);
$total = $db->count("t_photos");
$pages = ($total/$max);
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
<body>
    <h1>Google Photos</h1>
    <div>
        <?php ob_start(); ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php if($page>0){?>
                <li class="page-item"><a class="page-link" href="?p=<?=$page-1?>">Previous</a></li>
                <?php } ?>
                <li class="page-item">
                    <form>
                    <select name="p" class="form-control" onchange="this.form.submit()">
                <?php
                for($n=0;$n<$pages;$n++){
                    if($n==$page)
                    echo '<option selected value="'.$n.'">'.($n+1).'</option>';
                    else
                    echo '<option value="'.$n.'">'.($n+1).'</option>';
                }
                ?>
                </select>
                </form>
                <li class="page-item"><a class="page-link" href="?p=<?=$page+1?>">Next</a></li>
            </ul>
        </nav>
        <?php
        $pagination = ob_get_contents();
        ob_end_flush();
        ?>
    </div>
    <hr>
    <div class="card-columns">
        <?php
        foreach($datas as $data){
        ?><div class="card">
            <a href="<?=$data['baseUrl']?>" data-toggle="lightbox" data-gallery="google-gallery">
            <img src="<?=$data['baseUrl']?>" class="card-img-top img-fluid" alt="">
            </a>
            <div class="card-block d-none d-md-block">
                <p class="card-text"><small class="text-muted"><?=$data['filename']?></small></p>
            </div>
        </div>
        <?php } ?>
    </div>
    <hr>
    <div>
        <?=$pagination?>
    </div>
    <hr>
    <br><br>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>
<script>
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });
</script>
</body>
</html>