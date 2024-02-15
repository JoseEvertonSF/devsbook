<?= $render('header', ['usuario' => $usuario]) ?>
<section class="container main">
    <?= $render('sidebar', ['active' => 'search']) ?>
    <section class="feed">
        <br>
        <h1>Você pesquisou por: <?= $searchTerm ?></h1>
        <div class="full-friend-list">
            <?php foreach($users as $user):?>
                <div class="friend-icon">
                    <a href="<?= $base ?>/perfil/<?= $user->id ?>">
                        <div class="friend-icon-avatar">
                            <img src="<?= $base ?>/media/avatars/<?= $user->avatar ?>" />
                        </div>
                        <div class="friend-icon-name">
                            <?= $user->name ?>
                        </div>
                    </a>
                </div>
            <?php endforeach;?>
        </div>
                            

    </section>
</section>
</section>
<?= $render('footer'); ?>