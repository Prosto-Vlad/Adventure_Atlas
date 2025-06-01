<!DOCTYPE html>
<html lang="uk">

<?php
$isLoggedIn = session()->get('logged_in');
$userName = $isLoggedIn ? session()->get('username') : 'Увійти або зареєструватись';
?>
<head>
    <meta charset="UTF-8">
    <title>Adventure Atlas</title>
    <meta name="description" content="Adventure Atlas - твій найкращий помічник у створенню світів.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('style/home.css'); ?>">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/@panzoom/panzoom@4/dist/panzoom.min.js"></script>
    <script src="<?= base_url('scripts/mapgenerators.js'); ?>"></script>
</head>

<body class="d-flex flex-column h-100">

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <a class="navbar-brand" href="/">Adventure Atlas</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if ($isLoggedIn): ?> 
                    <li class="nav-item">
                        <a class="nav-link" href="/gallery" id="gallery">Галерея</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#userModal">
                        <?php echo $userName; ?>
                        <span class="user-icon"></span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>

<div id="userModal" class="modal fade" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="userModalLabel"><?php $isLoggedIn ? 'Профіль' : 'Увійти' ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if ($isLoggedIn): ?>
                    <p>Вітаємо, <?php echo session()->get('username'); ?>!</p>
                    <button class="btn btn-danger" id="logoutButton">Вийти</button>
                <?php else: ?>
                    <p>Будь ласка, увійдіть або зареєструйтесь, щоб отримати доступ до всіх функцій.</p>
                    <form id="authForm">
                        <div class="form-group">
                            <input type="text" class="form-control" id="username" placeholder="Ім'я користувача" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" id="password" placeholder="Пароль" required>
                        </div>
                        <div class="form-group" id="emailField" style="display:none;">
                            <input type="email" class="form-control" placeholder="Електронна пошта">
                        </div>
                        <button type="button" class="btn btn-primary" id="authButton">Увійти</button>
                        <button type="button" class="btn btn-link" id="regButton">Реєстрація</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>