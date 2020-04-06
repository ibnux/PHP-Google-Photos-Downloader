<?php
require 'vendor/autoload.php';
include "config.php";
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
    <div class="card-columns">
        <?php
        $max = 50;
        $page = $_GET['p']*1;
        $pageNow = $page*$max;
        $datas = $db->select("t_photos",'*',['LIMIT'=>[$pageNow,$max],'ORDER'=>['date_added'=>'DESC']]);
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
<nav aria-label="Page navigation">
    <ul class="pagination">
        <?php if($page>0){?>
        <li class="page-item"><a class="page-link" href="?p=<?=$page-1?>">Previous</a></li>
        <?php } ?>
        <li class="page-item"><a class="page-link" href="?p=<?=$page+1?>">Next</a></li>
    </ul>
</nav>
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