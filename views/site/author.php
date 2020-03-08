<?php
$this->title = "Михаил Русаков";

$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Об авторе блога Русакове Михаиле Юрьевиче.'
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'об авторе, михаил русаков, михаил юрьевич русаков'
])
?>

<div id="custom">
    <h2>Об авторе</h2>
    <hr />
    <?php include "likes.php"; ?>
    <div class="post_text">
        <p class="center">
            <img src="/web/images/author.png" alt="Об авторе" />
        </p>
        <p>Я автор</p>
    </div>
</div>