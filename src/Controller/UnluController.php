<?php

namespace UserFrosting\Sprinkle\Unlu\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Sprinkle\Core\Facades\Debug;

use UserFrosting\Sprinkle\Unlu\Database\Models\Acta;
use UserFrosting\Sprinkle\Unlu\Database\Models\Peticion;
use UserFrosting\Sprinkle\Unlu\Database\Models\Servicio;
use UserFrosting\Sprinkle\Unlu\Database\Models\Vinculacion;
use UserFrosting\Sprinkle\Unlu\Database\Models\UsuarioUnlu as Usuario;

use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema;
use Illuminate\Database\Capsule\Manager as Capsule;

class UnluController extends SimpleController {

    public function page(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        return $this->ci->view->render($response, 'pages/unlu.html.twig');
    }

    public function solicitarVinculacion(Request $request, Response $response, $args) {
        // Get POST parameters: user_name, first_name, last_name, email, locale, (group)
        $params = $request->getParsedBody();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $schema = new RequestSchema('schema://requests/unlu/solicitar_vinculacion.yaml');

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        $error = false;

        /*  Asigno el id del usuario actual como id del solicitante de la vin-
            culación. Un usuario administrador puede solicitar una vinculación
            en lugar de otro usuario. */
        if (!isset($data["id_solicitante"])) {
            $data["id_solicitante"] = $currentUser->id;
        }

        // Asigno la fecha actual como fecha de solicitud de la vinculación
        $data["fecha_solicitud"] = date("d-m-Y", time());

        if (!isset($data["responsable"]) || $data["responsable"] === "") {
            $data["responsable"] = $currentUser->full_name;
        }

        if (!isset($data["cargo"]) || $data["cargo"] === "") {

            if ($currentUser->rol === "") {
                $ms->addMessageTranslated('danger', 'UNLU.ROLE.MISSING', $data);
                $error = true;

            } else {
                $data["cargo"] = $currentUser->rol;
            }
        }

        if (!isset($data['tipo_de_usuario']) || $data['tipo_de_usuario'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.VINCULATION.USERTYPE.MISSING', $data);
            $error = true;
        }

        if (!isset($data['actividad']) || $data['actividad'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.VINCULATION.ACTIVITY.MISSING', $data);
            $error = true;
        }

        if (!isset($data["telefono"]) || $data["telefono"] === "") {
            if ($currentUser->telefono === "") {
                $ms->addMessageTranslated('danger', 'UNLU.PHONE.MISSING', $data);
                $error = true;

            } else {
                $data["telefono"] = $currentUser->telefono;
            }
        }

        if (!isset($data['fecha_fin']) || $data['fecha_fin'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.VINCULATION.END_DATE.MISSING', $data);
            $error = true;

        } else {
            // Comprobar que la fecha de finalización no sea anterior a la fecha de solicitud.
            if (strtotime($data["fecha_fin"]) < strtotime($data["fecha_solicitud"])) {
                $ms->addMessageTranslated('danger', 'UNLU.VINCULATION.END_DATE.BEFORE', $data);
                $error = true;
            }
        }

        if (!isset($data['descripcion']) || $data['descripcion'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.VINCULATION.DESCRIPTION.MISSING', $data);
            $error = true;
        }

        // Integrantes
        $integrantes = $data["integrantes"];
        $integrantes[] = $data["id_solicitante"]; // Agregar usuario solicitante como integrante

        if ($error) {
            return $response->withJson([], 400);
        }

        $classMapper = $this->ci->classMapper;

        Capsule::transaction(function () use ($classMapper, $data, $ms, $config, $currentUser, $integrantes) {

            $vinculacion = $classMapper->createInstance("vinculacion", $data);
            $vinculacion->save();
            /*  $vinculacion->id tiene el id generado para esta instancia, si
                es autoincrement. */

            foreach ($integrantes as $i) {
                if (is_numeric($i)) {
                    /*  Si $i es un número entonces se trata de un id de usua-
                        rio, y busco el nombre de la base de datos. */
                    $data_integrantes = [
                        "id_usuario" => $i,
                        "id_vinculacion" => $vinculacion->id,
                        "nombre" => Usuario::find($i)->full_name
                    ];

                } else {
                    /*  Si $i es una cadena de texto entonces no se trata de un
                        usuario del sistema y entonces ingreso el integrante
                        sin id de usuario. */
                    $data_integrantes = [
                        "id_vinculacion" => $vinculacion->id,
                        "nombre" => $i
                    ];
                }

                $integrante = $classMapper->createInstance("integrante", $data_integrantes);
                $integrante->save();
            }

            // Create activity record
            $this->ci->userActivityLogger->info("User {$currentUser->user_name} created a new vinculation {$vinculacion->id}.", [
                'type'    => 'pastry_create',
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'UNLU.VINCULATION.ADDED', $data);
        });

        return $response->withJson([], 200);
    }

    public function solicitarServicio(Request $request, Response $response, $args) {
        /*  Get POST parameters: user_name, first_name, last_name, email,
            locale, (group)
        */
        $params = $request->getParsedBody();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $schema = new RequestSchema('schema://requests/unlu/peticion.yaml');

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        $error = false;

        // Asigno el id del usuario actual como id del solicitante de la petición
        $data["id_usuario"] = $currentUser->id;

        if (!isset($data['fecha_inicio']) || $data['fecha_inicio'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.PETITION.START_DATE.MISSING', $data);
            $error = true;

        } else {
            // Comprobar que la fecha de inicio no sea anterior a la fecha actual:
            $fecha_hoy = strtotime(date("d-m-Y", time()));
            if (strtotime($data["fecha_inicio"]) < $fecha_hoy) {
                $ms->addMessageTranslated('danger', 'UNLU.PETITION.START_DATE.BEFORE', $data);
                $error = true;
            }
        }

        if (!isset($data['fecha_fin']) || $data['fecha_fin'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.PETITION.END_DATE.MISSING', $data);
            $error = true;

        } else {
            // Comprobar que la fecha de finalización no sea anterior a la fecha de solicitud:
            if (strtotime($data["fecha_fin"]) < strtotime($data["fecha_inicio"])) {
                $ms->addMessageTranslated('danger', 'UNLU.PETITION.END_DATE.BEFORE', $data);
                $error = true;
            }
        }

        if (!isset($data["id_servicio"]) || $data["id_servicio"] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.PETITION.SERVICE.MISSING', $data);
            $error = true;
        }

        if (!isset($data["descripcion"]) || $data["descripcion"] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.PETITION.DESCRIPTION.MISSING', $data);
            $error = true;
        }

        /*  Borro la instancia de id_vinculacion si viene vacía del formulario
            (o sea, que la petición no está vinculada a ninguna vinculación):
        */
        if ($data["id_vinculacion"] === "") {
            unset($data["id_vinculacion"]);
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        $classMapper = $this->ci->classMapper;

        Capsule::transaction(function () use ($classMapper, $data, $ms, $config, $currentUser) {

            try {
                $peticion = $classMapper->createInstance("peticion", $data);
                $peticion->save();

            } catch (\Exception $e) {
                $msg = $e->getMessage();
                Debug::debug("Error: $msg");
            }

            // Create activity record
            $this->ci->userActivityLogger->info("User {$currentUser->user_name} solicitó una petición {$data->descripcion}.", [
                'type'    => 'pastry_create',
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'UNLU.PETITION.ADDED', $data);
        });

        return $response->withJson([], 200);
    }

    public function bajaSolicitud(Request $request, Response $response, $args) {

        $params = $request->getParsedBody();

        $peticion = $this->getPeticionFromParams($params);

        if (!$peticion) {
            throw new NotFoundException($request, $response);
        }

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($peticion, $currentUser) {
            $peticion->delete();
            unset($peticion);

            // Create activity record
            $this->ci->userActivityLogger->info("Usuario {$currentUser->user_name} borró la petición {$peticion->id}.", [
                'type'    => 'pastry_delete',
                'user_id' => $currentUser->id,
            ]);
        });

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $ms->addMessageTranslated('success', 'UNLU.PETITION.DELETE_SUCCESSFUL', [
            'user_name' => $peticon->id,
        ]);

        return $response->withJson([], 200);
    }

    public function editarPeticion(Request $request, Response $response, $args) {

        $peticion = $this->getPeticionFromParams($args);
        if (!$peticion) {
            throw new NotFoundException($request, $response);
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        // Get PUT parameters
        $params = $request->getParsedBody();

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        // Load the request schema
        $schema = new RequestSchema('schema://requests/unlu/peticion.yaml');

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        $error = false;

        if ((!isset($data["descripcion"]) || $data["descripcion"] === "") && !isset($data["aprobada"])) {
            /*  Verifico que la descripción no esté vacía. En el caso de que
                quiera solamente aprobar una petición entonces evito esta veri-
                ficación.
            */

            $ms->addMessageTranslated('danger', 'UNLU.PETITION.DESCRIPTION.MISSING', $data);
            $error = true;
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        $classMapper = $this->ci->classMapper;

        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($classMapper, $data, $peticion, $currentUser) {

            if (isset($data["aprobada"])) {
                $peticion->aprobada = $data["aprobada"];

            } else {
                $peticion->descripcion = $data["descripcion"];
                $peticion->observaciones = $data["observaciones"];
            }

            /*  Si cambia la fecha de finalización entonces se le quita el es-
                tado de aprobada a la petición */
            if (isset($data["fecha_fin"])) {
                if ($data["fecha_fin"] != $peticion->fecha_fin) {
                    $peticion->fecha_fin = $data["fecha_fin"];
                    $peticion->aprobada = false;
                }
            }

            $peticion->save();

            // Create activity record
            $this->ci->userActivityLogger->info("User {$currentUser->user_name} updated info for petition {$peticion->id}.", [
                'type'    => 'account_update_info',
                'user_id' => $currentUser->id,
            ]);
        });

        $ms->addMessageTranslated('success', 'UNLU.PETITION.UPDATED', [
            'user_name' => $peticion->id,
        ]);

        return $response->withJson([], 200);
    }

    public function agregarServicio(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $schema = new RequestSchema('schema://requests/unlu/servicio.yaml');

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        $error = false;

        if (!isset($data['denominacion']) || $data['denominacion'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.SERVICE.DENOMINATION.MISSING', $data);
            $error = true;
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        $classMapper = $this->ci->classMapper;

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        Capsule::transaction(function () use ($classMapper, $data, $ms, $config, $currentUser) {
            $servicio = $classMapper->createInstance("servicio", $data);
            $servicio->save();

            // Create activity record
            $this->ci->userActivityLogger->info("User {$currentUser->user_name} created a new service {$servicio->denominacion}.", [
                'type'    => 'service_create',
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'UNLU.SERVICE.ADD.SUCCESS', $data);
        });

        return $response->withJson([], 200);
    }

    public function editarServicio(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Servicio $servicio */
        $servicio = $this->getServicioFromParams($args);
        if (!$servicio) {
            throw new NotFoundException($request, $response);
        }

        // Get PUT parameters
        $params = $request->getParsedBody();

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        // Load the request schema
        $schema = new RequestSchema('schema://requests/unlu/servicio.yaml');

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException();
        }

        $error = false;

        if (!isset($data["denominacion"]) || $data["denominacion"] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.SERVICE.DENOMINATION.MISSING', $data);
            $error = true;
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        $classMapper = $this->ci->classMapper;

        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($classMapper, $data, $servicio, $currentUser) {
            $servicio->denominacion = $data["denominacion"];
            $servicio->observaciones = $data["observaciones"];
            $servicio->save();

            // Create activity record
            $this->ci->userActivityLogger->info("User {$currentUser->user_name} updated info for service {$servicio->id}.", [
                'type'    => 'account_update_info',
                'user_id' => $currentUser->id,
            ]);
        });

        $ms->addMessageTranslated('success', 'UNLU.SERVICE.UPDATED', [
            'user_name' => $servicio->id,
        ]);

        return $response->withJson([], 200);
    }

    public function eliminarServicio(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Servicio $servicio */
        $servicio = $this->getServicioFromParams($args);
        if (!$servicio) {
            throw new NotFoundException($request, $response);
        }

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        $denominacion = $servicio->denominacion;

        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($servicio, $currentUser) {
            $servicio->delete();
            unset($servicio);

            // Create activity record
            $this->ci->userActivityLogger->info("User {$currentUser->user_name} deleted the service {$denominacion}.", [
                'type'    => 'pastry_delete',
                'user_id' => $currentUser->id,
            ]);
        });

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;
        $ms->addMessageTranslated('success', 'UNLU.SERVICE.DELETE.SUCCESS', [
            'denominacion' => $servicio->denominacion,
        ]);

        return $response->withJson([], 200);
    }

    public function listarPeticiones(Request $request, Response $response, $args) {
        // GET parameters
        $params = $request->getQueryParams();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        $sprunje = $classMapper->createInstance('peticion_sprunje', $classMapper, $params);

        /*  Extiendo la consulta del sprunje para que solo traiga las peticiones del usuario (si no es administrador). */
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            $sprunje->extendQuery(function($query) use ($currentUser) {
                return $query->where('id_usuario', $currentUser->id);
            });
        }

        // Be careful how you consume this data - it has not been escaped and contains untrusted user-supplied content.
        // For example, if you plan to insert it into an HTML DOM, you must escape it on the client side (or use client-side templating).
        return $sprunje->toResponse($response);
    }

    public function listarServicios(Request $request, Response $response, $args) {
        // GET parameters
        $params = $request->getQueryParams();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        $sprunje = $classMapper->createInstance('servicio_sprunje', $classMapper, $params);

        // Be careful how you consume this data - it has not been escaped and contains untrusted user-supplied content.
        // For example, if you plan to insert it into an HTML DOM, you must escape it on the client side (or use client-side templating).
        return $sprunje->toResponse($response);
    }

    public function listarVinculaciones(Request $request, Response $response, $args) {
        // GET parameters
        $params = $request->getQueryParams();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        $sprunje = $classMapper->createInstance('vinculacion_sprunje', $classMapper, $params);
        /*  Extiendo la consulta del sprunje para que solo traiga las vinculaciones del usuario (si no es administrador). */
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            $sprunje->extendQuery(function($query) use ($currentUser) {
                return $query->where('id_solicitante', $currentUser->id);
            });
        }

        // Be careful how you consume this data - it has not been escaped and contains untrusted user-supplied content.
        // For example, if you plan to insert it into an HTML DOM, you must escape it on the client side (or use client-side templating).
        return $sprunje->toResponse($response);
    }

    public function getActa(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException();
        }

        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Acta $acta */
        $acta = $this->getActaFromParams($args);
        if (!$acta) {
            throw new NotFoundException($request, $response);
        }

        return $response->write($this->ci->filesystem->get("actas/$acta->ubicacion"))
                        ->withHeader('Content-type', 'application/pdf')
                        ->withStatus(200);
    }

    protected function getActaFromParams($params) {
        $schema = new RequestSchema("schema://requests/get-by-id.yaml");

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        $acta = Acta::find($data["id"]);
        return $acta;
    }

    protected function getPeticionFromParams($params) {
        $schema = new RequestSchema("schema://requests/get-by-id.yaml");

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        $peticion = Peticion::find($data["id"]);

        return $peticion;
    }

    protected function getServicioFromParams($params) {
        $schema = new RequestSchema("schema://requests/get-by-id.yaml");

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        $servicio = Servicio::find($data["id"]);

        return $servicio;
    }
}