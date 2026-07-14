<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(version: "1.0.0", description: "Documentación de la API para el MVP de Phoenix Orders", title: "Phoenix Orders API")]
#[OA\Server(url: "http://127.0.0.1:8000", description: "Servidor Local")]
abstract class Controller
{
}
