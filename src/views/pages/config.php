<?= $render('header', ['usuario' => $usuario]) ?>
<section class="container main">
    <?= $render('sidebar', ['active' => 'config']) ?>
    <h1>Configurações:</h1>
    <section class="feed mt10">
    <br>
        <form class="config-form" enctype="multipart/form-data" method="POST" action="<?= $base ?>/config/up">
            <?php if (!empty($_SESSION['flash'])): ?>
                <div class="flash">
                    <?= $_SESSION['flash'] ?>
                </div>
            <?php endif;
            $_SESSION['flash'] = '';
            ?>
            <label>
                Novo Avatar:<br/>
                <input type="file" name="avatar"><br/>
                <img class="image-edit" src="<?= $base ?>/media/avatars/<?= $usuario->avatar ?>">
            </label>
            <label>
                Nova capa:<br/>
                <input type="file" name="cover"><br/>
                <img class="image-edit" src="<?= $base ?>/media/covers/<?= $usuario->cover ?>">
            </label> 
            <hr/>
            <label>
                Nome completo:<br/>
                <input type="text" name="name" value="<?= $usuario->name ?>">
            </label>
            <label>
                Data de nascimento:<br/>
                <input type="date" name="dt_nascimento" value="<?= $usuario->birthdate ?>">
            </label>
            <label>
                Email:<br/>
                <input type="email" name="email" value="<?= $usuario->email?>">
            </label>
            <label>
                Cidade:<br/>
                <input type="text" name="cidade" value="<?= $usuario->city ?? ''?>">
            </label>
            <label>
                Trabalho:<br/>
                <input type="text" name="trabalho" value="<?= $usuario->work ?? ''?>">
            </label>
            <label>
                Nova Senha:<br/>
                <input type="password" name="senha">
            </label>
            <label>
                Confirmar Nova Senha:<br/>
                <input type="password" name="conf_senha">
            </label>
            <br/>
            <input class="button" type="submit" value="Salvar">
        </form>
    </section>
</section>
<?= $render('footer'); ?>