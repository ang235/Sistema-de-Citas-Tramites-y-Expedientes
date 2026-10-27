<?php

declare(strict_types=1);

use App\Http\Middleware\CheckRole;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

test('check role middleware blocks unauthenticated user with 401 on json', function () {
    $middleware = new CheckRole;
    $request = Request::create('/api/admin', 'GET');
    $request->headers->set('Accept', 'application/json');

    $response = $middleware->handle($request, function () {
        return new Response('ok');
    }, 'Administrador');

    expect($response->getStatusCode())->toBe(401);
});

test('check role middleware rejects user without the required role', function () {
    $middleware = new CheckRole;
    $usuario = new Usuario;
    $usuario->ID_Usuarios = 1;
    $usuario->Nombre = 'Juan Perez';
    $usuario->Email = 'juan@setc.gob';

    $rolCiudadano = new Rol;
    $rolCiudadano->ID_Rol = 1;
    $rolCiudadano->Nombre = 'Ciudadano';

    $usuario->setRelation('roles', collect([$rolCiudadano]));

    $request = Request::create('/panel-supervisor', 'GET');
    $request->setUserResolver(fn () => $usuario);
    $request->headers->set('Accept', 'application/json');

    $response = $middleware->handle($request, function () {
        return new Response('ok');
    }, 'Supervisor', 'Administrador');

    expect($response->getStatusCode())->toBe(403);
});

test('check role middleware allows user with the required role', function () {
    $middleware = new CheckRole;
    $usuario = new Usuario;
    $usuario->ID_Usuarios = 2;
    $usuario->Nombre = 'Admin SETC';
    $usuario->Email = 'admin@setc.gob';

    $rolAdmin = new Rol;
    $rolAdmin->ID_Rol = 2;
    $rolAdmin->Nombre = 'Administrador';

    $usuario->setRelation('roles', collect([$rolAdmin]));

    $request = Request::create('/panel-admin', 'GET');
    $request->setUserResolver(fn () => $usuario);

    $response = $middleware->handle($request, function () {
        return new Response('acceso concedido', 200);
    }, 'Administrador');

    expect($response->getStatusCode())->toBe(200);
    expect($response->getContent())->toBe('acceso concedido');
});
