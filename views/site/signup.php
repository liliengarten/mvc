<h2>Регистрация</h2>
<h3><?= $message ?? ''; ?></h3>
<form method="post">
    <label>Имя</label>
    <input type="text" name="name">
    <label>Логин</label>
    <input type="text" name="login">
    <label>Пароль</label>
    <input type="text" name="password">
    <button>Зарегистрироваться</button>
</form>