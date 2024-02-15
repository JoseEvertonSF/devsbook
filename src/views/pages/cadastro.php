<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Cadastro</title>
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1"/>
    <link rel="stylesheet" href="<?=$base?>/assets/css/login.css" />
</head>
<body>
    <header>
        <div class="container">
            <a href=""><img src="<?=$base?>/assets/images/devsbook_logo.png" /></a>
        </div>
    </header>
    <section class="container main">
        <form method="POST" action="<?= $base ?>/cadastro">
            <?php if(!empty($flash)):?>
                <div id="flash" class="flash">
                    <?= $flash ?>
                </div>
            <?php endif;?>
            <input placeholder="Digite seu nome completo" class="input" type="text" name="name" />

            <input placeholder="Digite seu e-mail" class="input" type="email" name="email" />

            <input placeholder="Digite sua senha" class="input" type="password" name="password" />

            <input id="dt_nascimento" placeholder="Digite sua data de nascimento" class="input" type="text" name="dt_nascimento" />

            <input class="button" type="submit" value="Cadastrar" />

            <a href="<?= $base ?>/login">Já tem conta? Faça o login</a>
        </form>
    </section>
</body>
<script src="https://unpkg.com/imask"></script>
<script>
    IMask(
        document.getElementById("dt_nascimento"),
        {
            mask: '00/00/0000'
        }
    );
    var flash = document.getElementById("flash");
    setTimeout(function(){
        flash.style.display = "none";
        console.log('jesus');
    }, 6000)
</script>
</html>