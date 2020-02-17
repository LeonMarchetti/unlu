<?php

return [
    "UNLU" => [
        "@TRANSLATION" => "CIDETIC",
        "ACTIONS" => [
            "ADD" => "Agregar",
            "DELETE_REQUEST" => "Baja Solicitud",
            "DENIED" => "No estás habilitado todavía para realizar acciones en el sistema. Consulte con el administrador.",
            "REQUEST" => "Solicitar",
            "REQUEST_SERVICE" => "Solicitar Servicio",
            "REQUEST_VINCULATION" => "Solicitar Vinculación",
        ],
        "ACTIVE" => "Activo",
        "CERTIFICATE" => [
            "@TRANSLATION" => "Acta",
            "ADD" => [
                "@TRANSLATION" => "Agregar acta",
                "SUCCESS" => "El acta fue agregado con éxito",
            ],
            "DATE" => [
                "@TRANSLATION" => "Fecha",
                "MISSING" => "Falta la fecha",
            ],
            "DELETE" => [
                "@TRANSLATION" => "Eliminar acta",
                "CONFIRM" => "¿Está seguro de eliminar el acta <b>{{ titulo }}</b>?",
                "SUCCESS" => "El acta \"{{ titulo }}\" fue eliminado con éxito"
            ],
            "FILE" => [
                "@TRANSLATION" => "Archivo",
                "ERROR" => "Error al subir el archivo",
                "MISSING" => "Falta el archivo",
                "PDF_ONLY" => "Solo se permite achivos .PDF"
            ],
            "PLURAL" => "Actas",
            "REPLACE" => [
                "@TRANSLATION" => "Reemplazar archivo",
                "SUCCESS" => "El archivo del acta \"{{ titulo }}\" fue reemplazado con éxito"
            ],
            "TITLE" => [
                "@TRANSLATION" => "Título",
                "MISSING" => "Falta el título",
            ],
        ],
        "CERTIFICATE_ASSIGN" => [
            "CERTIFICATE_ID" => [
                "MISSING" => "No se encontró el acta",
            ],
            "SUCCESS" => "El acta fue asignado exitosamente",
            "VINCULATION_ID" => [
                "MISSING" => "No se encontró la vinculación",
            ],
        ],
        "DEPENDENCY" => "Dependencia",
        "DESCRIPTION" => "Investigación. Docencia. Extensión. Universidad Nacional de Luján",
        "INSTITUTION" => "Institución",
        "PETITION" => [
            "@TRANSLATION" => "Peticiones",
            "ADDED" => "La petición fue solicitada exitosamente",
            "APPROVE" => [
                "@TRANSLATION" => "Aprobar",
                "CERTIFICATE_MISSING" => "No se puede aprobar esta acta sin asignar un acta",
                "CONFIRM" => "¿Está seguro que quiere aprobar esta petición?"
            ],
            "APPROVE_PETITION" => "Aprobar petición",
            "APPROVED" => "Aprobada",
            "ASSIGN" => "Asignar",
            "CERTIFICATE" => [
                "@TRANSLATION" => "Acta",
                "ASSIGN" => [
                    "@TRANSLATION" => "Asignar acta",
                    "SUCCESS" => "El acta fue asignado a la petición exitosamente",
                ],
                "FILE" => [
                    "@TRANSLATION" => "Archivo",
                    "ERROR" => "Error al subir el archivo",
                    "MISSING" => "Falta el archivo",
                ],
                "NEEDED" => "Este servicio requiere llenar un formulario para su aprobación",
                "PETITION_MISSING" => "Falta el id de la petición a asignar",
            ],
            "DELETE_SUCCESSFUL" => "La petición fue borrada exitosamente",
            "DESCRIPTION" => [
                "@TRANSLATION" => "Descripción",
                "MISSING" => "Falta la descripción"
            ],
            "DOWN" => "Baja Solicitud",
            "EDIT" => "Editar",
            "END_DATE" => [
                "@TRANSLATION" => "Fecha de finalización",
                "BEFORE" => "La fecha de finalización no puede ser anterior a la fecha de inicio",
                "MISSING" => "Falta la fecha de finalización"
            ],
            "HELP" => "<i class=\"fa fa-times\"></i> en la columna Acta significa que la petición no necesita un acta.",
            "OBSERVATIONS" => "Observaciones",
            "SERVICE" => [
                "@TRANSLATION" => "Servicio",
                "MISSING" => "Falta el servicio"
            ],
            "START_DATE" => [
                "@TRANSLATION" => "Fecha de inicio",
                "BEFORE" => "La fecha de inicio no puede ser anterior a hoy",
                "MISSING" => "Falta la fecha de inicio"
            ],
            "UPDATED" => "Petición actualizada",
            "VINCULATION" => [
                "@TRANSLATION" => "Vinculación",
                "HELP" => "No es obligatorio que esté vinculado a una vinculación",
                "MISSING" => "Sin vinculación",
                "NONE" => "Ninguna",
                "NOT_APPROVED" => "Esta vinculación no está aprobada",
            ],
        ],
        "PHONE" => [
            "@TRANSLATION" => "Teléfono",
            "MISSING" => "Falta asignar el número de teléfono en el perfil"
        ],
        "ROLE" => [
            "@TRANSLATION" => "Rol",
            "MISSING" => "Falta asignar el rol en el perfil de usuario"
        ],
        "SERVICE" => [
            "@TRANSLATION" => "Servicio",
            "ADD" => [
                "@TRANSLATION" => "Agregar servicio",
                "SUCCESS" => "El servicio <b>{{denominacion}}</b> fue agregado exitosamente",
            ],
            "CERTIFICATE_NEEDED" => [
                "@TRANSLATION" => "¿Necesita acta?",
                "HELP" => "Si realizar una petición de este servicio requiere de adjuntar algún acta",
            ],
            "DELETE" => [
                "@TRANSLATION" => "Eliminar",
                "CONFIRM" => "¿Está seguro de eliminar el servicio <b>{{denominacion}}</b>?",
                "SUCCESS" => "El servicio <b>{{denominacion}}</b> fue eliminado exitosamente",
            ],
            "DENOMINATION" => [
                "@TRANSLATION" => "Denominación",
                "MISSING" => "Falta la denominación",
            ],
            "EDIT" => "Editar",
            "OBSERVATIONS" => "Observaciones",
            "PLURAL" => "Servicios",
            "UPDATED" => "El servicio <b>{{denominacion}}</b> fue actualizado exitosamente",
            "VINCULATION_NEEDED" => [
                "@TRANSLATION" => "¿Necesita una vinculación?",
                "HELP" => "Si realizar una petición de este servicio requiere de estar necesariamente asignado a una vinculación",
            ],
        ],
        "VINCULATION" => [
            "@TRANSLATION" => "Vinculación",
            "ACTIVITY" => [
                "@TRANSLATION" => "Actividad",
                "MISSING" => "Falta la actividad"
            ],
            "ADDED" => "Vinculación solicitada",
            "APPLICANT" => "Usuario solicitante",
            "ASSIGN" => "Asignar",
            "CERTIFICATE" => "Acta",
            "DESCRIPTION" => [
                "@TRANSLATION" => "Descripción",
                "MISSING" => "Falta la descripción"
            ],
            "EDIT" => "Editar",
            "END_DATE" => [
                "@TRANSLATION" => "Fecha de finalización",
                "BEFORE" => "La fecha de finalización no puede ser anterior a la fecha de solicitud",
                "MISSING" => "Falta la fecha de finalización"
            ],
            "MEMBERS" => [
                "@TRANSLATION" => "Integrantes",
                "ADD_NOT_REGISTERED" => "Agregar integrante no registrado",
                "ADD_REGISTERED" => "Agregar usuario registrado",
                "HELP" =>
                    "\"Agregar usuario registrado\" agrega una lista desplegable para seleccionar un usuario registrado en el sistema.<br>
                    \"Agregar usuario no registrado\" agrega un cuadro de texto para ingresar el nombre de un integrante si no está registrado en el sistema.<br>
                    El solicitante es obligatoriamente un integrante de la vinculación que solicite, no hay que agregarlo.",
                "MISSING" => "Faltan los integrantes",
                "REMOVE" => "Quitar",
                "REPEATED" => "Hay integrantes repetidos",
                "SELECT_PLACEHOLDER" => "Seleccione un usuario"
            ],
            "PLURAL" => "Vinculaciones",
            "POSITION" => "Cargo",
            "REQUEST_DATE" => "Fecha solicitud",
            "RESPONSABLE" => "Responsable",
            "UPDATED" => "La vinculación fue actualizada satisfactoriamente",
            "USERTYPE" => [
                "@TRANSLATION" => "Tipo de usuario",
                "MISSING" => "Falta el tipo de usuario",
                "PLACEHOLDER" => "Seleccione el tipo de usuario"
            ],
        ]
    ]
];