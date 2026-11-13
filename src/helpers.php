<?php
// Funciones chicas que usan las vistas (se cargan con el autoload de Composer).
// Escapa texto para mostrarlo en HTML de forma segura (evita XSS).
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// Explicación simple de qué significa cada nivel de riesgo.
function explicarRiesgo(string $riesgo): string
{
    return match ($riesgo) {
        'alto'  => 'Cerralo si no lo usás: es un servicio muy atacado o que viaja sin cifrar.',
        'medio' => 'Revisá que tenga contraseña fuerte y que no esté expuesto a Internet.',
        default => 'Normal en muchos equipos, pero mantené el software actualizado.',
    };
}
