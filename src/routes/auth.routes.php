<?php
// Módulo auth: registro, login y logout.
$app->get('/auth/register', [$authController, 'mostrarRegistro']);
$app->post('/auth/register', [$authController, 'registrar']);
$app->get('/auth/login', [$authController, 'mostrarLogin']);
$app->post('/auth/login', [$authController, 'login']);
$app->get('/auth/logout', [$authController, 'logout']);
$app->post('/auth/logout', [$authController, 'logout']);
