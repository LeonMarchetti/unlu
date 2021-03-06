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
                "SUCCESS" => "El acta <b>{{titulo}}</b> fue agregado con éxito",
            ],
            "DATE" => [
                "@TRANSLATION" => "Fecha",
                "MISSING" => "Falta la fecha",
            ],
            "DELETE" => [
                "@TRANSLATION" => "Eliminar acta",
                "CONFIRM" => "¿Está seguro de eliminar el acta <b>{{ titulo }}</b>?",
                "SUCCESS" => "El acta <b>{{ titulo }}</b> fue eliminado con éxito"
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
                "SUCCESS" => "El archivo del acta <b>{{ titulo }}</b> fue reemplazado con éxito"
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
            "SUCCESS" => "El acta <b>{{titulo}}</b> fue asignado exitosamente a la vinculación <b>{{actividad}}</b>",
            "VINCULATION_ID" => [
                "MISSING" => "No se encontró la vinculación",
            ],
        ],
        "DEPENDENCY" => "Dependencia",
        "DESCRIPTION" => "Investigación. Docencia. Extensión. Universidad Nacional de Luján",
        "FORBIDDEN" => [
            "NOT_ADMIN_USER" => "Este usuario no es un usuario administrador del sistema de CIDETIC",
            "NOT_UNLU_USER" => "Este usuario no es un usuario del sistema de CIDETIC",
            "WRONG_USER_ACCESS" => "Este usuario no puede acceder a objetos de otros usuarios",
        ],
        "INSTITUTION" => "Institución",
        "PETITION" => [
            "@TRANSLATION" => "Peticiones",
            "ADDED" => "La petición <b>{{descripcion}}</b> fue solicitada exitosamente",
            "APPROVE" => [
                "@TRANSLATION" => "Aprobar",
                "CERTIFICATE_MISSING" => "No se puede aprobar esta acta sin asignar un acta",
                "CONFIRM" => "¿Está seguro que quiere aprobar esta petición?",
                "SUCCESS" => "Petición <b>{{descripcion}}</b> aprobada exitosamente",
            ],
            "APPROVE_PETITION" => "Aprobar petición",
            "APPROVED" => "Aprobada",
            "ASSIGN" => "Asignar",
            "CERTIFICATE" => [
                "@TRANSLATION" => "Acta",
                "ASSIGN" => [
                    "@TRANSLATION" => "Asignar acta",
                    "SUCCESS" => "El acta <b>{{archivo}}</b> fue asignado a la petición <b>{{descripcion}}</b> exitosamente",
                ],
                "FILE" => [
                    "@TRANSLATION" => "Archivo",
                    "ERROR" => "Error al subir el archivo",
                    "MISSING" => "Falta el archivo",
                ],
                "NEEDED" => "Este servicio requiere llenar un formulario para su aprobación",
                "PETITION_MISSING" => "Falta el id de la petición a asignar",
                "REASSIGN" => [
                    "@TRANSLATION" => "Reasignar acta",
                    "SUCCESS" => "La reasignación del acta fue exitosa",
                ],
            ],
            "DELETE_SUCCESSFUL" => "La petición <b>{{descripcion}}</b> fue borrada exitosamente",
            "DESCRIPTION" => [
                "@TRANSLATION" => "Descripción",
                "MISSING" => "Falta la descripción"
            ],
            "DOWN" => "Baja Solicitud",
            "EDIT" => "Editar",
            "EDIT_DISAPPROVED" => "La petición modificada <b>{{descripcion}}</b> perdió su condición de aprobada",
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
            "UPDATED" => "Petición <b>{{descripcion}}</b> actualizada",
            "VINCULATION" => [
                "@TRANSLATION" => "Vinculación",
                "HELP" => "No es obligatorio que esté vinculado a una vinculación",
                "MISSING" => "Falta la vinculación (el servicio seleccionado lo requiere).",
                "NEEDED" => "Este servicio requiere asignado a una vinculación para su solicitud",
                "NONE" => "Ninguna vinculación",
                "NOT_APPROVED" => "Esta vinculación no está aprobada",
            ],
        ],
        "PHONE" => [
            "@TRANSLATION" => "Teléfono",
            "MISSING" => "Falta asignar el número de teléfono en el perfil"
        ],
        "REPORT" => [
            "@TRANSLATION" => "Informes",
            "EXPIRED_PETITIONS" => [
                "@TRANSLATION" => "Peticiones vencidas",
                "DESCRIPTION" => "Peticiones vencidas entre <b>{{fecha_min}}</b> y <b>{{fecha_max}}</b>",
                "TITLE" => "Informe de peticiones vencidas",
            ],
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
            "ADDED" => "Vinculación <b>{{actividad}}</b> solicitada exitosamente",
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
            "REQUEST_DATE" => [
                "@TRANSLATION" => "Fecha solicitud",
                "MISSING" => "Falta la fecha de solicitud",
            ],
            "RESOLUTION" => "Resolución",
            "RESPONSABLE" => "Responsable",
            "UPDATED" => "La vinculación <b>{{actividad}}</b> fue actualizada satisfactoriamente",
            "USERTYPE" => [
                "@TRANSLATION" => "Tipo de usuario",
                "MISSING" => "Falta el tipo de usuario",
                "PLACEHOLDER" => "Seleccione el tipo de usuario"
            ],
        ]
    ]
];
