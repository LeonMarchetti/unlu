<?php

namespace UserFrosting\Sprinkle\Unlu\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Sprinkle\Core\Facades\Debug;

use UserFrosting\Sprinkle\Unlu\Database\Models\Peticion;
use UserFrosting\Sprinkle\Unlu\Database\Models\Servicio;
use UserFrosting\Sprinkle\Unlu\Database\Models\TipoUsuario;
use UserFrosting\Sprinkle\Unlu\Database\Models\UsuarioUnlu as Usuario;
use UserFrosting\Sprinkle\Unlu\Database\Models\Vinculacion;


class UnluModalController extends SimpleController {

    use GetObject;

    public function solicitarVinculacionModal(Request $request, Response $response, $args) {

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        // Valores por defecto de la vinculación que provienen de los datos del usuario actual.
        $vinculacion = [
            "responsable" => $currentUser->full_name,
            "cargo" => $currentUser->rol,
            "correo" => $currentUser->email,
            "telefono" => $currentUser->telefono,
        ];

        // Lista de tipos de usuario
        $tipos_de_usuario = TipoUsuario::all();
        $usuarios = Usuario::all();
        $usuarios_activos = Usuario::where("activo", true)->get();

        return $this->ci->view->render($response, 'modals/vinculacion.html.twig', [
            "vinculacion" => $vinculacion,
            "tipos_de_usuario" => $tipos_de_usuario,
            "usuarios" => $usuarios,
            "usuarios_activos" => $usuarios_activos,
            "form" => [
                "action" => "api/unlu",
                "method" => "POST",
                "submit_text" => "Solicitar"
            ]
        ]);
    }

    public function solicitarServicioModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        $servicios = Servicio::all();

        if ($authorizer->checkAccess($currentUser, 'admin_unlu')) {
            // Usuario administrador
            $vinculaciones = Vinculacion::all();

        } else {
            $vinculaciones = $currentUser->vinculaciones;
        }

        return $this->ci->view->render($response, 'modals/peticion.html.twig', [
            "servicios" => $servicios,
            "vinculaciones" => $vinculaciones,
            "form" => [
                "action" => "api/unlu/peticion",
                "method" => "POST",
                "submit_text" => "Solicitar"
            ]
        ]);
    }

    public function bajaSolicitudModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        $peticiones = Peticion::all();

        return $this->ci->view->render($response, 'modals/baja-solicitud.html.twig', [
            "peticiones" => $peticiones,
            "form" => [
                "action" => "api/unlu/baja-solicitud",
                "method" => "POST",
                "submit_text" => "Borrar"
            ]
        ]);
    }

    public function aprobarPeticionModal(Request $request, Response $response, $args) {
        // GET parameters
        $params = $request->getQueryParams();

        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Peticion $peticion */
        $peticion = $this->getObjectFromParams($params, "peticion");
        if (!$peticion) {
            throw new NotFoundException($request, $response);
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        return $this->ci->view->render($response, 'modals/aprobar-peticion.html.twig', [
            'peticion' => $peticion,
            'form' => [
                'action' => "api/unlu/p/{$peticion->id}",
            ],
        ]);
    }

    public function editarPeticionModal(Request $request, Response $response, $args) {
        // GET parameters
        $params = $request->getQueryParams();

        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Peticion $peticion */
        $peticion = $this->getObjectFromParams($params, "peticion");
        if (!$peticion) {
            throw new NotFoundException($request, $response);
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        /*  Como origen de datos del select creo una lista con solamente la
            vinculación y el servicio a las que está asignada la petición (ya
            que comparto el formulario con "Solicitar Servicio").
        */
        $servicios = [ $peticion->servicio ];

        if ($peticion->vinculacion) {
            $vinculaciones = [ $peticion->vinculacion ];
        }

        /*  edicion => true: Como comparto con el formulario de "Solicitar Ser-
            vicio", cuando quiero editar una petición y solo tener disponibles
            para editar la descripción y las observaciones con esta opción in-
            dico que se deshabiliten los demás controles.
        */
        return $this->ci->view->render($response, 'modals/editar-peticion.html.twig', [
            "edicion" => true,
            'peticion' => $peticion,
            "servicios" => $servicios,
            "vinculaciones" => $vinculaciones,
            'form' => [
                'action' => "api/unlu/p/{$peticion->id}",
                "method" => "POST",
                "submit_text" => "Actualizar"
            ],
        ]);
    }

    public function agregarServicioModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        return $this->ci->view->render($response, 'modals/servicio.html.twig', [
            "form" => [
                "action" => "api/unlu/s",
                "method" => "POST",
                "submit_text" => "Agregar"
            ]
        ]);
    }

    public function editarServicioModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Servicio $servicio */
        $servicio = $this->getObjectFromParams($request->getQueryParams(), "servicio");
        if (!$servicio) {
            throw new NotFoundException($request, $response);
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        return $this->ci->view->render($response, 'modals/servicio.html.twig', [
            "servicio" => $servicio,
            'form' => [
                'action' => "api/unlu/s/{$servicio->id}",
                "method" => "POST",
                "submit_text" => "Actualizar"
            ],
        ]);
    }

    public function eliminarServicioModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Servicio $servicio */
        $servicio = $this->getObjectFromParams($request->getQueryParams(), "servicio");

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        return $this->ci->view->render($response, 'modals/eliminar-servicio.html.twig', [
            'servicio' => $servicio,
            'form' => [
                'action' => "api/unlu/s/{$servicio->id}",
            ],
        ]);
    }

    public function editarVinculacionModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Vinculacion $vinculacion */
        $vinculacion = $this->getObjectFromParams($request->getQueryParams(), "vinculacion");
        if (!$vinculacion) {
            throw new NotFoundException($request, $response);
        }

        $tipos_de_usuario = TipoUsuario::all();
        $usuarios = Usuario::all();
        $usuarios_activos = Usuario::where("activo", true)->get();

        return $this->ci->view->render($response, 'modals/vinculacion.html.twig', [
            'vinculacion' => $vinculacion,
            "tipos_de_usuario" => $tipos_de_usuario,
            "usuarios" => $usuarios,
            "usuarios_activos" => $usuarios_activos,
            "edicion" => true,
            'form' => [
                'action' => "api/unlu/v/{$vinculacion->id}",
                "method" => "POST",
                "submit_text" => "Actualizar",
            ],
        ]);
    }

    public function agregarActaModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        return $this->ci->view->render($response, 'modals/acta.html.twig', [
            "form" => [
                "action" => "api/unlu/a",
                "method" => "POST",
                "submit_text" => "Agregar"
            ]
        ]);
    }

    public function asignarActaModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        return $this->ci->view->render($response, 'modals/asignar-acta.html.twig', [
            "form" => [
                "action" => "api/unlu/asignar-acta",
                "method" => "POST"
            ],
            "id_vinculacion" => $request->getQueryParams()["id"],
        ]);
    }
}