<?php

/*
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2019 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\Unlu\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserFrosting\Fortress\Adapter\JqueryValidationAdapter;
use UserFrosting\Fortress\RequestSchema;
use UserFrosting\Support\Exception\NotFoundException;

use UserFrosting\Sprinkle\Account\Controller\AccountController;

class UnluAccountController extends AccountController {

    /**
     * {@inheritDoc}
     */
    public function pageRegister(Request $request, Response $response, $args) {
        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        /** @var \UserFrosting\I18n\LocalePathBuilder */
        $localePathBuilder = $this->ci->localePathBuilder;

        if (!$config['site.registration.enabled']) {
            throw new NotFoundException();
        }

        /** @var \UserFrosting\Sprinkle\Account\Authenticate\Authenticator $authenticator */
        $authenticator = $this->ci->authenticator;

        // Redirect if user is already logged in
        if ($authenticator->check()) {
            $redirect = $this->ci->get('redirect.onAlreadyLoggedIn');

            return $redirect($request, $response, $args);
        }

        // Load validation rules
        $schema = new RequestSchema('schema://requests/register.yaml');
        $schema->set('password.validators.length.min', $config['site.password.length.min']);
        $schema->set('password.validators.length.max', $config['site.password.length.max']);
        $schema->set('passwordc.validators.length.min', $config['site.password.length.min']);
        $schema->set('passwordc.validators.length.max', $config['site.password.length.max']);
        $validatorRegister = new JqueryValidationAdapter($schema, $this->ci->translator);

        // Get locale information
        $currentLocales = $localePathBuilder->getLocales();

        // Hide the locale field if there is only 1 locale available
        $fields = [
            'hidden'   => [],
            'disabled' => [],
        ];
        if (count($config->getDefined('site.locales.available')) <= 1) {
            $fields['hidden'][] = 'locale';
        }

        /*  Establezco los valores por defectos de usuario_unlu para el usuario
            en el formulario. */
        $user = [];
        $user['telefono'] = $config['site.registration.user_defaults.telefono'];
        $user['institucion'] = $config['site.registration.user_defaults.institucion'];
        $user['dependencia'] = $config['site.registration.user_defaults.dependencia'];
        $user['rol'] = $config['site.registration.user_defaults.rol'];

        return $this->ci->view->render($response, 'pages/register.html.twig', [
            'page' => [
                'validators' => [
                    'register' => $validatorRegister->rules('json', false),
                ],
            ],
            'fields'  => $fields,
            'locales' => [
                'available' => $config['site.locales.available'],
                'current'   => end($currentLocales),
            ],
            'user' => $user,
        ]);
    }
}