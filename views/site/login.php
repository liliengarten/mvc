<h2>Авторизация</h2>
<h3><?= $message ?? ''; ?></h3>
<?php
if (!app()->auth::check()):
    ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= app()->auth::generateCSRF() ?>">
        <label>Имя</label>
        <input type="text" name="name">
        <label>Логин</label>
        <input type="text" name="login">
        <label>Пароль</label>
        <input type="text" name="password">
        <button>Войти</button>
    </form>
<?php endif;