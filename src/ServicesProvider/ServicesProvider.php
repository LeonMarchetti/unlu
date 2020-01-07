<?php

namespace UserFrosting\Sprinkle\Unlu\ServicesProvider;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserFrosting\Sprinkle\Core\Facades\Debug;

class ServicesProvider {
    /**
     * Register extended user fields services.
     *
     * @param Container $container A DI container implementing ArrayAccess and container-interop.
     */
    public function register($container) {
        /**
         * Extend the 'classMapper' service to register model classes.
         *
         * Mappings added: Member
         */
        $container->extend('classMapper', function ($classMapper, $c) {
            $classMapper->setClassMapping("user", "UserFrosting\Sprinkle\Unlu\Database\Models\UsuarioUnlu");

            $classMapper->setClassMapping("acta", "UserFrosting\Sprinkle\Unlu\Database\Models\Acta");
            $classMapper->setClassMapping("integrante", "UserFrosting\Sprinkle\Unlu\Database\Models\IntegrantesVinculacion");
            $classMapper->setClassMapping("peticion", "UserFrosting\Sprinkle\Unlu\Database\Models\Peticion");
            $classMapper->setClassMapping("servicio", "UserFrosting\Sprinkle\Unlu\Database\Models\Servicio");
            $classMapper->setClassMapping("tipo_de_usuario", "UserFrosting\Sprinkle\Unlu\Database\Models\TipoUsuario");
            $classMapper->setClassMapping('vinculacion', 'UserFrosting\Sprinkle\Unlu\Database\Models\Vinculacion');

            $classMapper->setClassMapping('acta_sprunje', 'UserFrosting\Sprinkle\Unlu\Sprunje\ActaSprunje');
            $classMapper->setClassMapping('peticion_sprunje', 'UserFrosting\Sprinkle\Unlu\Sprunje\PeticionSprunje');
            $classMapper->setClassMapping('servicio_sprunje', 'UserFrosting\Sprinkle\Unlu\Sprunje\ServicioSprunje');
            $classMapper->setClassMapping('vinculacion_sprunje', 'UserFrosting\Sprinkle\Unlu\Sprunje\VinculacionSprunje');

            return $classMapper;
        });

        /** *
         * Returns a callback that handles setting the `UF-Redirect` header after a successful login.
         *
         * Overrides the service definition in the account Sprinkle.
         */
        $container['redirect.onLogin'] = function ($c) {
            /**
             * This method is invoked when a user completes the login process.
             *
             * Returns a callback that handles setting the `UF-Redirect` header after a successful login.
             * @param \Psr\Http\Message\ServerRequestInterface $request
             * @param \Psr\Http\Message\ResponseInterface      $response
             * @param array $args
             * @return \Psr\Http\Message\ResponseInterface
             */
            return function (Request $request, Response $response, array $args) use ($c) {
                // Backwards compatibility for the deprecated determineRedirectOnLogin service
                if ($c->has('determineRedirectOnLogin')) {
                    $determineRedirectOnLogin = $c->determineRedirectOnLogin;

                    return $determineRedirectOnLogin($response)->withStatus(200);
                }

                /** @var UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager */
                $authorizer = $c->authorizer;

                $currentUser = $c->authenticator->user();

                if ($authorizer->checkAccess($currentUser, 'usuario_unlu')) {
                    return $response->withHeader('UF-Redirect', $c->router->pathFor('unlu'));
                } elseif ($authorizer->checkAccess($currentUser, 'uri_dashboard')) {
                    return $response->withHeader('UF-Redirect', $c->router->pathFor('dashboard'));
                } elseif ($authorizer->checkAccess($currentUser, 'uri_account_settings')) {
                    return $response->withHeader('UF-Redirect', $c->router->pathFor('settings'));
                } else {
                    return $response->withHeader('UF-Redirect', $c->router->pathFor('index'));
                }

                return $response->withHeader('UF-Redirect', $c->router->pathFor('index'));
            };
        };
    }
}