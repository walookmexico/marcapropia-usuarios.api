<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="API de Gestión de accesos y usuarios",
 *      description="API para la gestión de accesos y usuarios, que permite crear, modificar y eliminar cuentas de usuario, así como gestionar roles y permisos de seguridad",
 *      @OA\Contact(
 *          email="jborges@walook.com.mx"
 *      ),
 *      @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 */
class Controller extends BaseController
{
    //
}
