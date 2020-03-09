<?php

namespace UserFrosting\Sprinkle\Unlu\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Support\Exception\ForbiddenException;
use UserFrosting\Sprinkle\Core\Facades\Debug;

use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema;
use Illuminate\Database\Capsule\Manager as Capsule;

class UnluController extends SimpleController {

    use GetObject;

    public function page(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        // Access-controlled page
        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_UNLU_USER"));
        }

        return $this->ci->view->render($response, 'pages/unlu.html.twig');
    }

    public function solicitarVinculacion(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_UNLU_USER"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $schema      = new RequestSchema('schema://requests/unlu/vinculacion.yaml');
        $transformer = new RequestDataTransformer($schema);
        $data        = $transformer->transform($params);

        $error = false;

        if (!isset($data['fecha_solicitud']) || $data['fecha_solicitud'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.VINCULATION.REQUEST_DATE.MISSING', $data);
            $error = true;
        }

        if (!isset($data['fecha_fin']) || $data['fecha_fin'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.VINCULATION.END_DATE.MISSING', $data);
            $error = true;
        }

        /*  Asigno el id del usuario actual como id del solicitante de la vin-
            culación. Un usuario administrador puede solicitar una vinculación
            en lugar de otro usuario. */
        if (!isset($data["id_solicitante"])) {
            $data["id_solicitante"] = $currentUser->id;
        }

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

        if (!isset($data['descripcion']) || $data['descripcion'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.VINCULATION.DESCRIPTION.MISSING', $data);
            $error = true;
        }

        $integrantes = $data["integrantes"];

        if ($error) {
            return $response->withJson([], 400);
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        Capsule::transaction(function () use ($classMapper, $config, $currentUser, $data, $integrantes, $ms) {
            $vinculacion = $classMapper->createInstance("vinculacion", $data);
            $vinculacion->save();
            /*  $vinculacion->id tiene el id generado para esta instancia, si
                es autoincrement. */

            foreach ($integrantes as $i) {
                if (is_numeric($i)) {
                    /*  Si $i es un número entonces se trata de un id de usua-
                        rio, y busco el nombre de la base de datos. */
                    $data_integrantes = [
                        "id_usuario"     => $i,
                        "id_vinculacion" => $vinculacion->id,
                        "nombre"         => $this->getObjectFromParams(["id" => $i], "user")->full_name
                    ];

                } else {
                    if ($i === "") continue;

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

            $this->ci->userActivityLogger->info("{$currentUser->user_name} solicitó la vinculación {$vinculacion->id} ({$vinculacion->actividad}).", [
                'type'    => $config["actividad.tipo.vinculacion.solicitud"],
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'UNLU.VINCULATION.ADDED', $data);
        });

        return $response->withJson([], 200);
    }

    public function solicitarServicio(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_UNLU_USER"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $schema      = new RequestSchema('schema://requests/unlu/peticion.yaml');
        $transformer = new RequestDataTransformer($schema);
        $data        = $transformer->transform($params);

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

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

        } else {
            $servicio = $this->getObjectFromParams([ "id" => $data["id_servicio"] ], "servicio");
            if ($servicio->necesita_vinculacion && (!isset($data["vinculacion"]) || $data["vinculacion"] === "")) {
                $ms->addMessageTranslated('danger', 'UNLU.PETITION.VINCULATION.MISSING', $data);
                $error = true;
            }
        }

        if (!isset($data["descripcion"]) || $data["descripcion"] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.PETITION.DESCRIPTION.MISSING', $data);
            $error = true;
        }

        /*  Borro la instancia de id_vinculacion si viene vacía del formulario
            (o sea, que la petición no está vinculada a ninguna vinculación): */
        if ($data["id_vinculacion"] === "") {
            unset($data["id_vinculacion"]);
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        Capsule::transaction(function () use ($classMapper, $config, $currentUser, $data, $ms) {
            $peticion = $classMapper->createInstance("peticion", $data);
            $peticion->save();

            $this->ci->userActivityLogger->info("{$currentUser->user_name} solicitó la petición {$peticion->id} ({$peticion->descripcion}).", [
                'type'    => $config["actividad.tipo.peticion.solicitud"],
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'UNLU.PETITION.ADDED', $data);
        });

        return $response->withJson([], 200);
    }

    public function bajaSolicitud(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();

        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Peticion $peticion */
        $peticion = $this->getObjectFromParams($params, "peticion");
        if (!$peticion) {
            throw new NotFoundException($request, $response);
        }

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        // Begin transaction - DB will be rolled back if an exception occurs
        Capsule::transaction(function () use ($config, $currentUser, $ms, $peticion) {
            $id_peticion = $peticion->id;
            $descripcion = $peticion->descripcion;

            $peticion->delete();
            unset($peticion);

            $this->ci->userActivityLogger->info("{$currentUser->user_name} bajó la solicitud de servicio {$id_peticion} ({$descripcion}).", [
                'type'    => $config["actividad.tipo.peticion.baja"],
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'UNLU.PETITION.DELETE_SUCCESSFUL', [
                "descripcion" => $descripcion
            ]);
        });

        return $response->withJson([], 200);
    }

    public function editarPeticion(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Peticion $peticion */
        $peticion = $this->getObjectFromParams($args, "peticion");
        if (!$peticion) {
            throw new NotFoundException($request, $response);
        }

        $params = $request->getParsedBody();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_UNLU_USER"));

        } else if (!$authorizer->checkAccess($currentUser, 'admin_unlu') && ($peticion->id_usuario != $currentUser->id)) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.WRONG_USER_ACCESS"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $schema = new RequestSchema('schema://requests/unlu/peticion.yaml');
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        $error = false;

        if (isset($data["aprobada"])) {
            if ($peticion->servicio->necesita_acta && !$peticion->acta) {
                $ms->addMessageTranslated('danger', 'UNLU.PETITION.APPROVE.CERTIFICATE_MISSING', $data);
                $error = true;
            }
        }

        if ((!isset($data["descripcion"]) || $data["descripcion"] === "") && !isset($data["aprobada"])) {
            // Verifico que la descripción no esté vacía. En el caso de que
            // quiera solamente aprobar una petición entonces evito esta
            // verificación.
            $ms->addMessageTranslated('danger', 'UNLU.PETITION.DESCRIPTION.MISSING', $data);
            $error = true;
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        Capsule::transaction(function () use ($classMapper, $config, $currentUser, $data, $ms, $peticion) {
            if (isset($data["aprobada"])) {
                $peticion->aprobada = $data["aprobada"];

            } else {
                $peticion->descripcion = $data["descripcion"];
                $peticion->observaciones = $data["observaciones"];
            }

            /*  Si cambia la fecha de finalización entonces se le quita el
                estado de aprobada a la petición */
            if (isset($data["fecha_fin"])) {
                if ($data["fecha_fin"] != $peticion->fecha_fin) {
                    $peticion->fecha_fin = $data["fecha_fin"];
                    if ($peticion->aprobada) {
                        $ms->addMessageTranslated('success', 'UNLU.PETITION.EDIT_DISAPPROVED', $data);
                        $peticion->aprobada = false;
                    }
                }
            }

            $peticion->save();

            $this->ci->userActivityLogger->info("{$currentUser->user_name} editó la petition {$peticion->id} ({$peticion->descripcion}).", [
                'type'    => $config["actividad.tipo.peticion.editar"],
                'user_id' => $currentUser->id,
            ]);

            if (isset($data["aprobada"])) {
                $ms->addMessageTranslated('success', 'UNLU.PETITION.APPROVE.SUCCESS', [
                    "descripcion" => $peticion->descripcion
                ]);
            } else {
                $ms->addMessageTranslated('success', 'UNLU.PETITION.UPDATED', $data);
            }

        });

        return $response->withJson([], 200);
    }

    public function agregarServicio(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $schema      = new RequestSchema('schema://requests/unlu/servicio.yaml');
        $transformer = new RequestDataTransformer($schema);
        $data        = $transformer->transform($params);

        $error = false;

        if (!isset($data['denominacion']) || $data['denominacion'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.SERVICE.DENOMINATION.MISSING', $data);
            $error = true;
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        Capsule::transaction(function () use ($classMapper, $config, $currentUser, $data, $ms) {
            $servicio = $classMapper->createInstance("servicio", $data);
            $servicio->save();

            $this->ci->userActivityLogger->info("{$currentUser->user_name} agregó el servicio {$servicio->id} ({$servicio->denominacion}).", [
                'type'    => $config["actividad.tipo.servicio.agregar"],
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'UNLU.SERVICE.ADD.SUCCESS', $data);
        });

        return $response->withJson([], 200);
    }

    public function editarServicio(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Servicio $servicio */
        $servicio = $this->getObjectFromParams($args, "servicio");
        if (!$servicio) {
            throw new NotFoundException($request, $response);
        }

        $params = $request->getParsedBody();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $schema      = new RequestSchema('schema://requests/unlu/servicio.yaml');
        $transformer = new RequestDataTransformer($schema);
        $data        = $transformer->transform($params);

        $error = false;

        if (!isset($data["denominacion"]) || $data["denominacion"] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.SERVICE.DENOMINATION.MISSING', $data);
            $error = true;
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        Capsule::transaction(function () use ($classMapper, $config, $currentUser, $data, $ms, $servicio) {
            $servicio->denominacion         = $data["denominacion"];
            $servicio->necesita_acta        = $data["necesita_acta"];
            $servicio->necesita_vinculacion = $data["necesita_vinculacion"];
            $servicio->observaciones        = $data["observaciones"];
            $servicio->save();

            $this->ci->userActivityLogger->info("{$currentUser->user_name} editó el servicio {$servicio->id} ({$servicio->denominacion}).", [
                'type'    => $config["actividad.tipo.servicio.editar"],
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'UNLU.SERVICE.UPDATED', [
                'denominacion' => $servicio->denominacion,
            ]);
        });

        return $response->withJson([], 200);
    }

    public function eliminarServicio(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Servicio $servicio */
        $servicio = $this->getObjectFromParams($args, "servicio");
        if (!$servicio) {
            throw new NotFoundException($request, $response);
        }

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        Capsule::transaction(function () use ($config, $currentUser, $ms, $servicio) {
            $id           = $servicio->id;
            $denominacion = $servicio->denominacion;

            $servicio->delete();
            unset($servicio);

            $this->ci->userActivityLogger->info("{$currentUser->user_name} eliminó el servicio {$id} ({$denominacion}).", [
                'type'    => $config["actividad.tipo.servicio.eliminar"],
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'UNLU.SERVICE.DELETE.SUCCESS', [
                'denominacion' => $denominacion,
            ]);
        });

        return $response->withJson([], 200);
    }

    public function listarActas(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        $sprunje = $classMapper->createInstance('acta_sprunje', $classMapper, $params);
        return $sprunje->toResponse($response);
    }

    public function listarPeticiones(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_UNLU_USER"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        /** @var \UserFrosting\Sprinkle\Unlu\Sprunje\PeticionSprunje $sprunje */
        $sprunje = $classMapper->createInstance('peticion_sprunje', $classMapper, $params);

        /* Extiendo la consulta del sprunje para que solo traiga las peticiones del usuario (si no es administrador). */
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            $sprunje->extendQuery(function($query) use ($currentUser) {
                return $query->where('id_usuario', $currentUser->id);
            });
        }

        $sprunje->extendQuery(function($query) {
            $fecha_hoy = date("d-m-Y", time());
            return $query->where('fecha_fin', '>=', $fecha_hoy);
        });

        return $sprunje->toResponse($response);
    }

    public function listarServicios(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        /** @var \UserFrosting\Sprinkle\Unlu\Sprunje\ServicioSprunje $sprunje */
        $sprunje = $classMapper->createInstance('servicio_sprunje', $classMapper, $params);

        return $sprunje->toResponse($response);
    }

    public function listarVinculaciones(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_UNLU_USER"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        /** @var \UserFrosting\Sprinkle\Unlu\Sprunje\VinculacionSprunje $sprunje */
        $sprunje = $classMapper->createInstance('vinculacion_sprunje', $classMapper, $params);

        // Extiendo la consulta del sprunje para que solo traiga las vinculaciones del usuario (si no es administrador).
        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            $sprunje->extendQuery(function($query) use ($currentUser) {
                return $query->where('id_solicitante', $currentUser->id);
            });
        }

        return $sprunje->toResponse($response);
    }

    public function listarPeticionesVencidas(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        /** @var \UserFrosting\Sprinkle\Unlu\Sprunje\PeticionSprunje $sprunje */
        $sprunje = $classMapper->createInstance('peticion_sprunje', $classMapper, $params);
        $sprunje->extendQuery(function ($query) use ($params) {
            return $query->whereBetween("fecha_fin", [$params["fecha_min"], $params["fecha_max"]]);
        });
        return $sprunje->toResponse($response);
    }

    public function getActa(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Acta $acta */
        $acta = $this->getObjectFromParams($args, "acta");
        if (!$acta) {
            throw new NotFoundException($request, $response);
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_UNLU_USER"));
        }

        return $response->write($this->ci->filesystem->get("actas/$acta->ubicacion"))
                        ->withHeader('Content-type', 'application/pdf')
                        ->withStatus(200);
    }

    public function editarVinculacion(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Vinculacion $vinculacion */
        $vinculacion = $this->getObjectFromParams($args, "vinculacion");
        if (!$vinculacion) {
            throw new NotFoundException($request, $response);
        }

        $params = $request->getParsedBody();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_UNLU_USER"));

        } else if (!$authorizer->checkAccess($currentUser, 'admin_unlu') && ($vinculacion->id_solicitante != $currentUser->id)) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.WRONG_USER_ACCESS"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $schema      = new RequestSchema('schema://requests/unlu/vinculacion.yaml');
        $transformer = new RequestDataTransformer($schema);
        $data        = $transformer->transform($params);

        $error = false;

        if (!isset($data['fecha_solicitud']) || $data['fecha_solicitud'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.VINCULATION.REQUEST_DATE.MISSING', $data);
            $error = true;
        }

        if (!isset($data['fecha_fin']) || $data['fecha_fin'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.VINCULATION.END_DATE.MISSING', $data);
            $error = true;
        }

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

        if (!isset($data['descripcion']) || $data['descripcion'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.VINCULATION.DESCRIPTION.MISSING', $data);
            $error = true;
        }

        // Compruebo si tengo que editar los integrantes de la vinculación:
        $editar_integrantes = false;

        if (count($data["integrantes"]) != count($vinculacion->integrantes)) {
            $editar_integrantes = true;
        } else {
            for ($i = 1; $i < count($data["integrantes"]); $i++) {
                if (is_numeric($data["integrantes"][$i])) {
                    if ($data["integrantes"][$i] != $vinculacion->integrantes[$i]->id_usuario) {
                        $editar_integrantes = true;
                        break;
                    }
                } else {
                    if ($data["integrantes"][$i] != $vinculacion->integrantes[$i]->nombre) {
                        $editar_integrantes = true;
                        break;
                    }
                }
            }
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        Capsule::transaction(function () use ($classMapper, $config, $currentUser, $data, $editar_integrantes, $ms, $vinculacion) {
            $vinculacion->fecha_solicitud = $data["fecha_solicitud"];
            $vinculacion->fecha_fin       = $data["fecha_fin"];
            $vinculacion->id_solicitante  = $data["id_solicitante"];
            $vinculacion->responsable     = $data["responsable"];
            $vinculacion->cargo           = $data["cargo"];
            $vinculacion->tipo_de_usuario = $data["tipo_de_usuario"];
            $vinculacion->actividad       = $data["actividad"];
            $vinculacion->telefono        = $data["telefono"];
            $vinculacion->descripcion     = $data["descripcion"];
            $vinculacion->resolucion      = $data["resolucion"];
            $vinculacion->save();

            // Actualizar integrantes de la vinculación:
            if ($editar_integrantes) {
                $classMapper->getClassMapping("integrante")::where('id_vinculacion', $vinculacion->id)->delete();

                foreach ($data["integrantes"] as $i) {
                    if (is_numeric($i)) {
                        // Si $i es un número entonces se trata de un id de
                        // usuario, y busco el nombre en la base de datos.
                        $data_integrante = [
                            "id_usuario"     => $i,
                            "id_vinculacion" => $vinculacion->id,
                            "nombre"         => $this->getObjectFromParams(["id" => $i], "user")->full_name
                        ];

                    } else {
                        // Si $i es una cadena de texto entonces no se trata de
                        // un usuario del sistema y entonces ingreso el
                        // integrante sin id de usuario.
                        $data_integrante = [
                            "id_vinculacion" => $vinculacion->id,
                            "nombre"         => $i
                        ];
                    }

                    $integrante = $classMapper->createInstance("integrante", $data_integrante);
                    $integrante->save();
                }
            }

            $this->ci->userActivityLogger->info("{$currentUser->user_name} editó la vinculación {$vinculacion->id} ({$vinculacion->actividad}).", [
                'type'    => $config["actividad.tipo.vinculacion.editar"],
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'UNLU.VINCULATION.UPDATED', $data);
        });

        return $response->withJson([], 200);
    }

    public function agregarActa(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();
        $archivos = $request->getUploadedFiles();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $schema      = new RequestSchema('schema://requests/unlu/acta.yaml');
        $transformer = new RequestDataTransformer($schema);
        $data        = $transformer->transform($params);

        $error = false;

        if (!isset($data['titulo']) || $data['titulo'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.CERTIFICATE.TITLE.MISSING', $data);
            $error = true;
        }

        if (!isset($data['fecha']) || $data['fecha'] === "") {
            $ms->addMessageTranslated('danger', 'UNLU.CERTIFICATE.DATE.MISSING', $data);
            $error = true;
        }

        if (!isset($archivos['archivo'])) {
            $ms->addMessageTranslated('danger', 'UNLU.CERTIFICATE.FILE.MISSING', $data);
            $error = true;

        } else {
            // Armo el nombre del archivo con el título y la fecha.
            // Reemplazo los espacios en el título con guión bajo.
            // Reemplazo las barras en la fecha con guiones.
            $titulo = str_replace(" ", "_", $data["titulo"]);
            $data["ubicacion"] = $titulo."_".$data["fecha"].".pdf";

            $archivo = $archivos['archivo'];
            if ($archivo->getError() !== UPLOAD_ERR_OK) {
                $ms->addMessageTranslated('danger', 'UNLU.CERTIFICATE.FILE.ERROR', $data);
                $error = true;
            }
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        /** @var \UserFrosting\Sprinkle\Core\Filesystem\FilesystemManager $filesystem */
        $filesystem = $this->ci->filesystem;

        Capsule::transaction(function () use ($archivo, $classMapper, $config, $currentUser, $data, $filesystem, $ms) {
            $filesystem->put("actas/$data[ubicacion]", $archivo->getStream()->getContents());

            $acta = $classMapper->createInstance("acta", $data);
            $acta->save();

            $this->ci->userActivityLogger->info("{$currentUser->user_name} agregó el certificado ${$acta->id} ({$acta->titulo}).", [
                'type'    => $config["actividad.tipo.acta.agregar"],
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'UNLU.CERTIFICATE.ADD.SUCCESS', $data);
        });

        return $response->withJson([], 200);
    }

    public function reemplazarActa(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();
        $archivos = $request->getUploadedFiles();

        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Vinculacion $vinculacion */
        $acta = $this->getObjectFromParams($args, "acta");
        if (!$acta) {
            throw new NotFoundException($request, $response);
        }

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $schema      = new RequestSchema('schema://requests/unlu/acta.yaml');
        $transformer = new RequestDataTransformer($schema);
        $data        = $transformer->transform($params);

        $error = false;

        if (!isset($archivos['archivo'])) {
            $ms->addMessageTranslated('danger', 'UNLU.CERTIFICATE.FILE.MISSING', $data);
            $error = true;

        } else {
            $archivo = $archivos['archivo'];
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        /** @var \UserFrosting\Sprinkle\Core\Filesystem\FilesystemManager $filesystem */
        $filesystem = $this->ci->filesystem;

        Capsule::transaction(function () use ($acta, $archivo, $config, $currentUser, $filesystem, $ms) {
            $ubicacion = $acta->ubicacion;
            $filesystem->put("actas/$ubicacion", $archivo->getStream()->getContents());

            $this->ci->userActivityLogger->info("{$currentUser->user_name} reemplazó el archivo del acta {$acta->id} ({$acta->titulo}).", [
                'type'    => $config["actividad.tipo.acta.reemplazar"],
                'user_id' => $currentUser->id
            ]);

            $ms->addMessageTranslated('success', 'UNLU.CERTIFICATE.REPLACE.SUCCESS', [
                "titulo" => $acta->titulo
            ]);
        });

        return $response->withJson([], 200);
    }

    public function eliminarActa(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Servicio $acta */
        $acta = $this->getObjectFromParams($args, "acta");
        if (!$acta) {
            throw new NotFoundException($request, $response);
        }

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        /** @var \UserFrosting\Sprinkle\Core\Filesystem\FilesystemManager $filesystem */
        $filesystem = $this->ci->filesystem;

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        Capsule::transaction(function () use ($acta, $config, $currentUser, $filesystem, $ms) {
            $id        = $acta->id;
            $titulo    = $acta->titulo;
            $ubicacion = $acta->ubicacion;

            $acta->delete();
            unset($acta);

            $filesystem->delete("actas/$ubicacion");

            $this->ci->userActivityLogger->info("{$currentUser->user_name} eliminó el acta {$id} ({$titulo}).", [
                'type'    => $config["actividad.tipo.acta.eliminar"],
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'UNLU.CERTIFICATE.DELETE.SUCCESS', [
                'titulo' => $titulo
            ]);
        });

        return $response->withJson([], 200);
    }

    public function asignarActa(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $schema      = new RequestSchema('schema://requests/unlu/asignar-acta.yaml');
        $transformer = new RequestDataTransformer($schema);
        $data        = $transformer->transform($params);

        $error = false;

        if (!isset($data['id_acta'])) {
            $ms->addMessageTranslated('danger', 'UNLU.CERTIFICATE_ASSIGN.CERTIFICATE_ID.MISSING', $data);
            $error = true;

        } else {
            /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Acta $acta */
            $acta = $this->getObjectFromParams(['id' => $data["id_acta"]], "acta");
            if (!$acta) {
                throw new NotFoundException($request, $response);
            }
        }

        if (!isset($data['id_vinculacion'])) {
            $ms->addMessageTranslated('danger', 'UNLU.CERTIFICATE_ASSIGN.VINCULATION_ID.MISSING', $data);
            $error = true;

        } else {
            /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Vinculacion $vinculacion */
            $vinculacion = $this->getObjectFromParams(['id' => $data["id_vinculacion"]], "vinculacion");
            if (!$vinculacion) {
                throw new NotFoundException($request, $response);
            }
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        Capsule::transaction(function () use ($acta, $classMapper, $config, $currentUser, $data, $ms, $vinculacion) {
            $vinculacion->id_acta = $acta->id;
            $vinculacion->save();

            $this->ci->userActivityLogger->info("{$currentUser->user_name} asignó el acta {$acta->id} ({$acta->titulo}) a la vinculación {$vinculacion->id} ({$vinculacion->actividad}).", [
                'type'    => $config["actividad.tipo.acta.asignar"],
                'user_id' => $currentUser->id,
            ]);

            $ms->addMessageTranslated('success', 'UNLU.CERTIFICATE_ASSIGN.SUCCESS', [
                "titulo"    => $acta->titulo,
                "actividad" => $vinculacion->actividad
            ]);
        });

        return $response->withJson([], 200);
    }

    public function getActaServicio(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Peticion $peticion */
        $peticion = $this->getObjectFromParams($args, "peticion");
        if (!$peticion) {
            throw new NotFoundException($request, $response);
        }

        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_UNLU_USER"));

        } else if (!$authorizer->checkAccess($currentUser, 'admin_unlu') && ($peticion->id_usuario != $currentUser->id)) {
            $this->ci->translator->translate("UNLU.FORBIDDEN.WRONG_USER_ACCESS");
        }

        return $response->write($this->ci->filesystem->get("actas-peticiones/$peticion->acta"))
                        ->withHeader('Content-type', 'application/pdf')
                        ->withStatus(200);
    }

    public function asignarActaPeticion(Request $request, Response $response, $args) {
        $params     = $request->getParsedBody();
        $archivos   = $request->getUploadedFiles();

        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\Peticion $peticion */
        $peticion = $this->getObjectFromParams($params, "peticion");
        if (!$peticion) {
            throw new NotFoundException($request, $response);
        }

        /** @var \UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager $authorizer */
        $authorizer = $this->ci->authorizer;

        /** @var \UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'admin_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_ADMIN_USER"));
        }

        /** @var \UserFrosting\Sprinkle\Core\Filesystem\FilesystemManager $filesystem */
        $filesystem = $this->ci->filesystem;
        $directorio = "actas-peticiones";

        $schema      = new RequestSchema('schema://requests/unlu/asignar-acta-peticion.yaml');
        $transformer = new RequestDataTransformer($schema);
        $data        = $transformer->transform($params);

        /** @var \UserFrosting\Sprinkle\Core\Alert\AlertStream $ms */
        $ms = $this->ci->alerts;

        $error = false;

        if (!isset($archivos['archivo'])) {
            $ms->addMessageTranslated('danger', 'UNLU.PETITION.CERTIFICATE.FILE.MISSING', $data);
            $error = true;

        } else {
            $archivo = $archivos['archivo'];
            if ($archivo->getError() !== UPLOAD_ERR_OK) {
                $ms->addMessageTranslated('danger', 'UNLU.PETITION.CERTIFICATE.FILE.ERROR', $data);
                $error = true;
            }

            // Determino el nombre del archivo para almacenarlo en le servidor.
            // Si ya existe un archivo con ese nombre entonces lo modifico
            // agregándole un número atrás.
            $nombre_base = pathinfo($archivo->getClientFilename(), PATHINFO_FILENAME);
            $ext = pathinfo($archivo->getClientFilename(), PATHINFO_EXTENSION);
            $nombre_archivo = $nombre_base.".pdf";
            $i = 1;
            while ($filesystem->exists("$directorio/$nombre_archivo")) {
                $indice = sprintf("%04d", $i++);
                $nombre_archivo = $nombre_base.".".$indice.".".$ext;
            }
        }

        if ($error) {
            return $response->withJson([], 400);
        }

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        Capsule::transaction(function () use ($archivo, $config, $currentUser, $filesystem, $ms, $nombre_archivo, $peticion) {
            $filesystem->put("actas-peticiones/$nombre_archivo", $archivo->getStream()->getContents());
            if ($peticion->acta) {
                $filesystem->delete("actas-peticiones/$peticion->acta");
            }

            $peticion->acta = $nombre_archivo;
            if ($peticion->aprobada) {
                $ms->addMessageTranslated('warning', 'UNLU.PETITION.EDIT_DISAPPROVED', [ "descripcion" => $peticion->descripcion ]);
                $peticion->aprobada = false;
            }
            $peticion->save();

            $this->ci->userActivityLogger->info("{$currentUser->user_name} asignó el acta {$peticion->acta} a la petición {$peticion->id} ({$peticion->descripcion}).", [
                'type'    => $config["actividad.tipo.peticion.asignar_acta"],
                'user_id' => $currentUser->id
            ]);

            $ms->addMessageTranslated('success', 'UNLU.PETITION.CERTIFICATE.ASSIGN.SUCCESS', [
                "archivo"     => $nombre_archivo,
                "descripcion" => $peticion->descripcion
            ]);
        });

        return $response->withJson([], 200);
    }

    /**
     * Comprueba si un usuario, según su nombre de usuario, no está ingresado en la base de datos.
     *
     * Devuelve "false" si ya existe.
     *
     * Para usar en la validación de nombre de usuario no existente, que necesita obtener un "false" para mostrar un error.
     */
    public function existeUsuario(Request $request, Response $response, $args) {
        $user_name = $request->getQueryParams()["user_name"];
        $email = $request->getQueryParams()["email"];

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        $usuario = $classMapper->getClassMapping("user")
                               ::where("user_name", $user_name)
                               ->orWhere("email", $email)
                               ->first();

        $resultado = empty($usuario);

        return $response->withJSON($resultado)
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(200);
    }

    public function getUsuario(Request $request, Response $response, $args) {
        /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
        $authorizer = $this->ci->authorizer;

        /** @var UserFrosting\Sprinkle\Account\Database\Models\User $currentUser */
        $currentUser = $this->ci->currentUser;

        if (!$authorizer->checkAccess($currentUser, 'usuario_unlu')) {
            throw new ForbiddenException($this->ci->translator->translate("UNLU.FORBIDDEN.NOT_UNLU_USER"));
        }

        /** @var UserFrosting\Sprinkle\Unlu\Database\Models\UsuarioUnlu $usuario */
        $usuario = $this->getObjectFromParams($args, "user");
        if (!$usuario) {
            throw new NotFoundException($request, $response);
        }

        return $response->withJSON($usuario)
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(200);
    }
}
