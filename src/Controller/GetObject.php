<?php

namespace UserFrosting\Sprinkle\Unlu\Controller;

use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema;

/**
 * Rasgo (trait) para otorgar el método que permite obtener un objeto de un
 * determinado tipo a través del mapeo de clases a los controladores.
 */
trait GetObject {
    protected function getObjectFromParams($params, $type) {
        $schema = new RequestSchema("schema://requests/get-by-id.yaml");

        // Whitelist and set parameter defaults
        $transformer = new RequestDataTransformer($schema);
        $data = $transformer->transform($params);

        /** @var \UserFrosting\Sprinkle\Core\Util\ClassMapper $classMapper */
        $classMapper = $this->ci->classMapper;

        $objeto = $classMapper->getClassMapping($type)::find($data["id"]);
        return $objeto;
    }
}