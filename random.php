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

$max = 12;
$datas = $db->rand("t_photos", '*', ['baseUrl[!]' => 'DELETED', 'LIMIT' => $max]);
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
        @media (max-width: 767.98px) {
            .card-columns {
                column-count: 2;
            }
        }
        @media (max-width: 991.98px) {
            .card-columns {
                column-count: 4;
            }
         }
    </style>
</head>

<body>
    <a href="random.php"><h1>Google Photos random</h1></a>
    <div class="card-columns" id="konten">
        <?php
}//not isset delete
        foreach ($datas as $data) {
        ?><div class="card">
                <a href="<?= $data['baseUrl'] ?>" data-toggle="lightbox" data-gallery="google-gallery">
                    <img src="<?= $data['baseUrl'] ?>" class="card-img-top img-fluid" alt="">
                </a>
                <a href="javascript:hapus('<?= $data['hash'] ?>')" class="close float-left" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>
                <div class="card-block d-none d-md-block">
                    <p class="card-text"><small class="text-muted"><?= $data['filename'] ?></small></p>
                </div>
            </div>
        <?php }

if (!isset($_GET['delete'])) {
?>
    </div>
    <hr>
    <br><br>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>
    <script>
        function hapus(hash) {
            $("#konten").load("/random.php?delete="+hash,);
        }

        $(document).on('click', '[data-toggle="lightbox"]', function(event) {
            event.preventDefault();
            $(this).ekkoLightbox();
        });
    </script>
</body>

</html>
<?php }