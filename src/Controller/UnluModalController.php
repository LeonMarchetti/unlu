<?php

namespace UserFrosting\Sprinkle\Unlu\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Sprinkle\Core\Facades\Debug;

use UserFrosting\Fortress\Adapter\JqueryValidationAdapter;
use UserFrosting\Fortress\RequestSchema;

class UnluModalController extends SimpleController {

    use GetObject;

    public function solicitarVinculacionModal(Request $request, Response $response, $args) {

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_UNLU_USER"));
        }

        // Valores por defecto de la vinculación que provienen de los datos del usuario actual.
        $vinculacion = [
            "responsable" => $currentUser->full_name,
            "cargo" => $currentUser->rol,
            "correo" => $currentUser->email,
            "telefono" => $currentUser->telefono,
        ];

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;
        $tipos_de_usuario = $classMapper->getClassMapping("tipo_de_usuario")::all();
        $usuarios         = $classMapper->getClassMapping("user")::all();
        $usuarios_activos = $classMapper->getClassMapping("user")::where("activo", true)->get();

        $schema = new RequestSchema('schema://requests/unlu/vinculacion.yaml');
        $validator = new JqueryValidationAdapter($schema, $this->ci->translator);
        $rules = $validator->rules('json', false);

        return $this->ci->view->render($response, 'modals/modal.html.twig', [
            "vinculacion" => $vinculacion,
            "tipos_de_usuario" => $tipos_de_usuario,
            "usuarios" => $usuarios,
            "usuarios_activos" => $usuarios_activos,
            "form" => [
                "action" => "api/unlu",
                "method" => "POST",
                "submit_text" => $this->ci->translator->translate("UNLU.ACTIONS.REQUEST")
            ],
            "modal" => [
                "title" => $this->ci->translator->translate("UNLU.VINCULATION"),
                "form" => "vinculacion.html.twig"
            ],
            'page' => [ 'validators' => $rules ]
        ]);
    }

    public function solicitarServicioModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_UNLU_USER"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        $servicios = $classMapper->getClassMapping("servicio")::all();

        $vinculaciones_map = $classMapper->getClassMapping("vinculacion")
            ::where("fecha_fin", ">", "Now()");
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            $vinculaciones_map->where("id_solicitante", $currentUser->id);
        }
        $vinculaciones = $vinculaciones_map->get();

        // Última vinculación vigente
        $id_vinculacion = $classMapper->getClassMapping("vinculacion")
            ::where([
                ["id_solicitante", $currentUser->id],
                ["fecha_fin", ">", "Now()"]
            ])
            ->orderBy("fecha_solicitud", "desc")
            ->first()
            ->id;

        $schema = new RequestSchema('schema://requests/unlu/peticion.yaml');
        $validator = new JqueryValidationAdapter($schema, $this->ci->translator);
        $rules = $validator->rules('json', false);

        return $this->ci->view->render($response, 'modals/modal.html.twig', [
            "peticion" => [
                "id_vinculacion" => $id_vinculacion
            ],
            "servicios" => $servicios,
            "vinculaciones" => $vinculaciones,
            "form" => [
                "action" => "api/unlu/peticion",
                "method" => "POST",
                "submit_text" => $this->ci->translator->translate("UNLU.ACTIONS.REQUEST")
            ],
            "modal" => [
                "title" => $this->ci->translator->translate("UNLU.PETITION"),
                "form" => "peticion.html.twig"
            ],
            'page' => [ 'validators' => $rules ]
        ]);
    }

    public function bajaSolicitudModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;
        $peticiones = $classMapper->getClassMapping("peticion")::all();

        return $this->ci->view->render($response, 'modals/modal.html.twig', [
            "peticiones" => $peticiones,
            "form" => [
                "action" => "api/unlu/baja-solicitud",
                "method" => "POST",
                "submit_text" => $this->ci->translator->translate("DELETE")
            ],
            "modal" => [
                "title" => $this->ci->translator->translate("UNLU.PETITION.DOWN"),
                "form" => "baja-solicitud.html.twig"
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

        return $this->ci->view->render($response, 'modals/modal.html.twig', [
            'peticion' => $peticion,
            'form' => [
                'action' => "api/unlu/p/{$peticion->id}",
                "submit_text" => $this->ci->translator->translate("UNLU.PETITION.APPROVE")
            ],
            "modal" => [
                "title" => $this->ci->translator->translate("UNLU.PETITION.APPROVE_PETITION"),
                "form" => "aprobar-peticion.html.twig"
            ]
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
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_UNLU_USER"));

        } else if (!$authorizer->checkAccess($currentUser, 'admin_unlu') && ($peticion->id_usuario != $currentUser->id)) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.WRONG_USER_ACCESS"));
        }

        /*  Como origen de datos del select creo una lista con solamente la
            vinculación y el servicio a las que está asignada la petición (ya
            que comparto el formulario con "Solicitar Servicio").
        */
        $servicios = [ $peticion->servicio ];

        if ($peticion->vinculacion) {
            $vinculaciones = [ $peticion->vinculacion ];
        }

        $schema = new RequestSchema('schema://requests/unlu/peticion.yaml');
        $validator = new JqueryValidationAdapter($schema, $this->ci->translator);
        $rules = $validator->rules('json', false);

        /*  edicion => true: Como comparto con el formulario de "Solicitar Ser-
            vicio", cuando quiero editar una petición y solo tener disponibles
            para editar la descripción y las observaciones con esta opción in-
            dico que se deshabiliten los demás controles.
        */
        return $this->ci->view->render($response, 'modals/modal.html.twig', [
            "edicion" => true,
            'peticion' => $peticion,
            "servicios" => $servicios,
            "vinculaciones" => $vinculaciones,
            'form' => [
                'action' => "api/unlu/p/{$peticion->id}",
                "method" => "POST",
                "submit_text" => $this->ci->translator->translate("UPDATE")
            ],
            "modal" => [
                "title" => $this->ci->translator->translate("UNLU.PETITION"),
                "form" => "peticion.html.twig"
            ],
            'page' => [ 'validators' => $rules ]
        ]);
    }

    public function agregarServicioModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        $schema     = new RequestSchema('schema://requests/unlu/servicio.yaml');
        $validator  = new JqueryValidationAdapter($schema, $this->ci->translator);
        $rules      = $validator->rules('json', false);

        return $this->ci->view->render($response, 'modals/modal.html.twig', [
            "form" => [
                "action" => "api/unlu/s",
                "method" => "POST",
                "submit_text" => $this->ci->translator->translate("UNLU.ACTIONS.ADD")
            ],
            "modal" => [
                "title" => $this->ci->translator->translate("UNLU.SERVICE"),
                "form" => "servicio.html.twig"
            ],
            'page' => [ 'validators' => $rules ]
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
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        $schema     = new RequestSchema('schema://requests/unlu/servicio.yaml');
        $validator  = new JqueryValidationAdapter($schema, $this->ci->translator);
        $rules      = $validator->rules('json', false);

        return $this->ci->view->render($response, 'modals/modal.html.twig', [
            "servicio" => $servicio,
            'form' => [
                'action' => "api/unlu/s/{$servicio->id}",
                "method" => "POST",
                "submit_text" => $this->ci->translator->translate("UPDATE")
            ],
            "modal" => [
                "title" => $this->ci->translator->translate("UNLU.SERVICE"),
                "form" => "servicio.html.twig"
            ],
            'page' => [ 'validators' => $rules ]
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
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        return $this->ci->view->render($response, 'modals/modal.html.twig', [
            'servicio' => $servicio,
            'form' => [
                'action' => "api/unlu/s/{$servicio->id}",
                'method' => "DELETE",
                'submit_text' => $this->ci->translator->translate("DELETE")
            ],
            "modal" => [
                "title" => $this->ci->translator->translate("UNLU.SERVICE.DELETE"),
                "form" => "eliminar-servicio.html.twig"
            ]
        ]);
    }

    public function editarVinculacionModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Vinculacion $vinculacion */
        $vinculacion = $this->getObjectFromParams($request->getQueryParams(), "vinculacion");
        if (!$vinculacion) {
            throw new NotFoundException($request, $response);
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_UNLU_USER"));

        } else if (!$authorizer->checkAccess($currentUser, 'admin_unlu') && ($vinculacion->id_solicitante != $currentUser->id)) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.WRONG_USER_ACCESS"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;
        $tipos_de_usuario = $classMapper->getClassMapping("tipo_de_usuario")::all();
        $usuarios         = $classMapper->getClassMapping("user")::all();
        $usuarios_activos = $classMapper->getClassMapping("user")::where("activo", true)->get();

        $schema     = new RequestSchema('schema://requests/unlu/vinculacion.yaml');
        $validator  = new JqueryValidationAdapter($schema, $this->ci->translator);
        $rules      = $validator->rules('json', false);

        return $this->ci->view->render($response, 'modals/modal.html.twig', [
            'vinculacion' => $vinculacion,
            "tipos_de_usuario" => $tipos_de_usuario,
            "usuarios" => $usuarios,
            "usuarios_activos" => $usuarios_activos,
            "edicion" => true,
            'form' => [
                'action' => "api/unlu/v/{$vinculacion->id}",
                "method" => "POST",
                "submit_text" => $this->ci->translator->translate("UPDATE")
            ],
            "modal" => [
                "title" => $this->ci->translator->translate("UNLU.VINCULATION"),
                "form" => "vinculacion.html.twig"
            ],
            'page' => [ 'validators' => $rules ]
        ]);
    }

    public function agregarActaModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        $schema = new RequestSchema('schema://requests/unlu/acta.yaml');
        $validator = new JqueryValidationAdapter($schema, $this->ci->translator);
        $rules = $validator->rules('json', false);

        return $this->ci->view->render($response, 'modals/modal.html.twig', [
            "form" => [
                "action" => "api/unlu/a",
                "method" => "POST",
                "submit_text" => $this->ci->translator->translate("UNLU.ACTIONS.ADD")
            ],
            "modal" => [
                "title" => $this->ci->translator->translate("UNLU.CERTIFICATE"),
                "form" => "acta.html.twig"
            ],
            'page' => [ 'validators' => $rules ]
        ]);
    }

    public function reemplazarActaModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Acta $acta */
        $acta = $this->getObjectFromParams($request->getQueryParams(), "acta");
        if (!$acta) {
            throw new NotFoundException($request, $response);
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        $schema = new RequestSchema('schema://requests/unlu/acta.yaml');
        $validator = new JqueryValidationAdapter($schema, $this->ci->translator);
        $rules = $validator->rules('json', false);

        return $this->ci->view->render($response, 'modals/modal.html.twig', [
            "acta" => $acta,
            'form' => [
                'action' => "api/unlu/a-reemplazar/{$acta->id}",
                "method" => "POST",
                "submit_text" => $this->ci->translator->translate("UPDATE")
            ],
            "modal" => [
                "title" => $this->ci->translator->translate("UNLU.CERTIFICATE.REPLACE",
                    [ "acta" => $acta->titulo ]),
                "form" => "reemplazar-acta.html.twig"
            ],
            'page' => [ 'validators' => $rules ]
        ]);
    }

    public function eliminarActaModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Acta $acta */
        $acta = $this->getObjectFromParams($request->getQueryParams(), "acta");
        if (!$acta) {
            throw new NotFoundException($request, $response);
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        $schema = new RequestSchema('schema://requests/unlu/acta.yaml');
        $validator = new JqueryValidationAdapter($schema, $this->ci->translator);
        $rules = $validator->rules('json', false);

        return $this->ci->view->render($response, 'modals/modal.html.twig', [
            'acta' => $acta,
            'form' => [
                'action' => "api/unlu/a-eliminar/{$acta->id}",
                'method' => "GET",
                'submit_text' => $this->ci->translator->translate("DELETE")
            ],
            "modal" => [
                "title" => $this->ci->translator->translate("UNLU.CERTIFICATE.DELETE"),
                "form" => "eliminar-acta.html.twig"
            ],
            "page" => [ "validators" => $rules ]
        ]);
    }

    public function asignarActaModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        return $this->ci->view->render($response, 'modals/modal.html.twig', [
            "id_vinculacion" => $request->getQueryParams()["id"],
            "form" => [
                "action" => "api/unlu/asignar-acta",
                "method" => "POST"
            ],
            "modal" => [
                "title" => $this->ci->translator->translate("UNLU.CERTIFICATE.PLURAL"),
                "form" => "asignar-acta.html.twig"
            ]
        ]);
    }

    public function asignarActaPeticionModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        $schema    = new RequestSchema('schema://requests/unlu/asignar-acta-peticion.yaml');
        $validator = new JqueryValidationAdapter($schema, $this->ci->translator);
        $rules     = $validator->rules('json', false);

        return $this->ci->view->render($response, 'modals/modal.html.twig', [
            "id_peticion" => $request->getQueryParams()["id"],
            "form" => [
                "action" => "api/unlu/as",
                "method" => "POST",
                "submit_text" => $this->ci->translator->translate("UNLU.PETITION.ASSIGN")
            ],
            "modal" => [
                "title" => $this->ci->translator->translate("UNLU.PETITION.CERTIFICATE.ASSIGN"),
                "form" => "asignar-acta-peticion.html.twig"
            ],
            "page" => [ "validators" => $rules ]
        ]);
    }

    public function peticionesVencidasModal(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        $params = $request->getQueryParams();

        return $this->ci->view->render($response, 'modals/peticiones-vencidas.html.twig', [
            "fecha_min" => $params["fecha_min"],
            "fecha_max" => $params["fecha_max"],
            "modal" => [
                "title" => $this->ci->translator->translate("UNLU.REPORT.EXPIRED_PETITIONS.TITLE")
            ]
        ]);
    }
}
