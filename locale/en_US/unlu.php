<?php

return [
    "UNLU" => [
        "@TRANSLATION" => "CIDETIC",
        "ACTIONS" => [
            "ADD" => "Add",
            "DELETE_REQUEST" => "Delete Request",
            "DENIED" => "You are not enabled yet to perform actions in the system. Check with the administrator.",
            "REQUEST" => "Solicitar",
            "REQUEST_SERVICE" => "Request Service",
            "REQUEST_VINCULATION" => "Request Vinculation",
        ],
        "ACTIVE" => "Active",
        "CERTIFICATE" => [
            "@TRANSLATION" => "Certificate",
            "ADD" => [
                "@TRANSLATION" => "Add certificate",
                "SUCCESS" => "The certificate <b>{{titulo}}</b> was added successfully",
            ],
            "DATE" => [
                "@TRANSLATION" => "Date",
                "MISSING" => "Date is missing",
            ],
            "DELETE" => [
                "@TRANSLATION" => "Delete",
                "CONFIRM" => "Are you sure of deleting the certificate <b>{{ titulo }}</b>?",
                "SUCCESS" => "The certificate <b>{{ titulo }}</b> was deleted successfully"
            ],
            "FILE" => [
                "@TRANSLATION" => "File",
                "ERROR" => "Error uploading the file",
                "MISSING" => "File is missing",
                "PDF_ONLY" => "Only .PDF files permitted"
            ],
            "PLURAL" => "Certificates",
            "REPLACE" => [
                "@TRANSLATION" => "Replace file",
                "SUCCESS" => "The certificate <b>{{ titulo }}</b>'s file was replaced successfully"
            ],
            "TITLE" => [
                "@TRANSLATION" => "Title",
                "MISSING" => "Title is missing",
            ],
        ],
        "CERTIFICATE_ASSIGN" => [
            "CERTIFICATE_ID" => [
                "MISSING" => "Certificate not found",
            ],
            "SUCCESS" => "Certificate <b>{{titulo}}</b> was assigned successfully to vinculation <b>{{actividad}}</b>",
            "VINCULATION_ID" => [
                "MISSING" => "Vinculation not found",
            ],
        ],
        "DEPENDENCY" => "Dependency",
        "DESCRIPTION" => "Research. Teaching. Extension. National University of Luján",
        "FORBIDDEN" => [
            "NOT_ADMIN_USER" => "This user is not a CIDETIC system administrator user",
            "NOT_UNLU_USER" => "This user is not a CIDETIC system user",
            "WRONG_USER_ACCESS" => "This user can't access other users' objects",
        ],
        "INSTITUTION" => "Institution",
        "PETITION" => [
            "@TRANSLATION" => "Petitions",
            "ADDED" => "The petition <b>{{descripcion}}</b> was requested succesfully",
            "APPROVE" => [
                "@TRANSLATION" => "Approve",
                "CERTIFICATE_MISSING" => "You can't approve this petition without it's assigned certificate",
                "CONFIRM" => "Are you sure you want to approve this petition?",
                "SUCCESS" => "Petition <b>{{descripcion}}</b> approved successfully",
            ],
            "APPROVE_PETITION" => "Approve petition",
            "APPROVED" => "Approved",
            "ASSIGN" => "Assign",
            "CERTIFICATE" => [
                "@TRANSLATION" => "Certificate",
                "ASSIGN" => [
                    "@TRANSLATION" => "Assign certificate",
                    "SUCCESS" => "The certificate <b>{{archivo}}</b> was assigned to the petition <b>{{descripcion}}</b> successfully",
                ],
                "FILE" => [
                    "@TRANSLATION" => "File",
                    "ERROR" => "Error uploading the file",
                    "MISSING" => "File is missing",
                ],
                "NEEDED" => "This service requires filling a form for the petition to be approved.",
                "PETITION_MISSING" => "Petition's id to assign is missing",
                "REASSIGN" => [
                    "@TRANSLATION" => "Reassign certificate",
                    "SUCCESS" => "The certificate reassignation was successful",
                ],
            ],
            "DELETE_SUCCESSFUL" => "The petition <b>{{descripcion}}</b> was deleted successfully",
            "DESCRIPTION" => [
                "@TRANSLATION" => "Description",
                "MISSING" => "Description is missing"
            ],
            "DOWN" => "Remove Request",
            "EDIT" => "Edit",
            "EDIT_DISAPPROVED" => "The modified petition <b>{{descripcion}}</b> lost its approved status",
            "END_DATE" => [
                "@TRANSLATION" => "End date",
                "BEFORE" => "The end date occurs before the start date",
                "MISSING" => "End date is missing"
            ],
            "HELP" => "<i class=\"fa fa-times\"></i> in Certificate column means the petition doesn't need a certificate.",
            "OBSERVATIONS" => "Observations",
            "SERVICE" => [
                "@TRANSLATION" => "Service",
                "MISSING" => "Service is missing"
            ],
            "START_DATE" => [
                "@TRANSLATION" => "Start date",
                "BEFORE" => "Start date occurs before today",
                "MISSING" => "Start date is missing"
            ],
            "UPDATED" => "Petition <b>{{descripcion}}</b> updated",
            "VINCULATION" => [
                "@TRANSLATION" => "Vinculation",
                "HELP" => "It's not required to be linked to a vinculation",
                "MISSING" => "Missing vinculation (the selected service requires it)",
                "NEEDED" => "This service requires to be assigned to a vinculation for the petition to be requested.",
                "NONE" => "No vinculation",
                "NOT_APPROVED" => "This vinculation is not approved",
            ],
        ],
        "PHONE" => [
            "@TRANSLATION" => "Phone",
            "MISSING" => "Phone number is not assigned in user profile"
        ],
        "REPORT" => [
            "@TRANSLATION" => "Reports",
            "EXPIRED_PETITIONS" => [
                "@TRANSLATION" => "Expired Petitions",
                "DESCRIPTION" => "Expired petitions between <b>{{fecha_min}}</b> and <b>{{fecha_max}}</b>",
                "TITLE" => "Expired Petitions Report",
            ],
        ],
        "ROLE" => [
            "@TRANSLATION" => "Role",
            "MISSING" => "Role is not assigned in user profile"
        ],
        "SERVICE" => [
            "@TRANSLATION" => "Service",
            "ADD" => [
                "@TRANSLATION" => "Add service",
                "SUCCESS" => "The service <b>{{denominacion}}</b> was added successfully",
            ],
            "CERTIFICATE_NEEDED" => [
                "@TRANSLATION" => "Certificate needed?",
                "HELP" => "If requesting a petition of this service needs a certificate",
            ],
            "DELETE" => [
                "@TRANSLATION" => "Delete",
                "CONFIRM" => "Are you sure you want to delete the service <b>{{denominacion}}</b>?",
                "SUCCESS" => "The service <b>{{denominacion}}</b> was deleted successfully",
            ],
            "DENOMINATION" => [
                "@TRANSLATION" => "Denomination",
                "MISSING" => "Denomination is missing",
            ],
            "EDIT" => "Edit",
            "OBSERVATIONS" => "Observations",
            "PLURAL" => "Services",
            "UPDATED" => "The service <b>{{denominacion}}</b> was updated successfully",
            "VINCULATION_NEEDED" => [
                "@TRANSLATION" => "Vinculation needed?",
                "HELP" => "If to request a petition of this service needs to be assigned to a vinculation",
            ],
        ],
        "VINCULATION" => [
            "@TRANSLATION" => "Vinculation",
            "ACTIVITY" => [
                "@TRANSLATION" => "Activity",
                "MISSING" => "Activity is missing"
            ],
            "ADDED" => "Vinculation <b>{{actividad}}</b> requested successfully",
            "APPLICANT" => "Applicant user",
            "ASSIGN" => "Asignar",
            "CERTIFICATE" => "Certificate",
            "DESCRIPTION" => [
                "@TRANSLATION" => "Description",
                "MISSING" => "Description is missing"
            ],
            "EDIT" => "Edit",
            "END_DATE" => [
                "@TRANSLATION" => "End date",
                "BEFORE" => "End date can't be before than request date",
                "MISSING" => "End date is missing"
            ],
            "MEMBERS" => [
                "@TRANSLATION" => "Members",
                "ADD_NOT_REGISTERED" => "Add non-registered member",
                "ADD_REGISTERED" => "Add registered user",
                "HELP" =>
                    "\"Add registered member\" adds a dropdown menu to select a user registered in the system.<br>
                    \"Add non-registered member\" adds a text box to input the name of a member not registered in the system.<br>
                    The applicant is necessarily a member of the vinculation requested, you don't have to add them.",
                "MISSING" => "Members are missing",
                "REMOVE" => "Remove",
                "REPEATED" => "There are repeated members",
                "SELECT_PLACEHOLDER" => "Select user"
            ],
            "PLURAL" => "Vinculations",
            "POSITION" => "Position",
            "REQUEST_DATE" => [
                "@TRANSLATION" => "Request date",
                "MISSING" => "Request date is missing",
            ],
            "RESOLUTION" => "Resolution",
            "RESPONSABLE" => "Responsable",
            "UPDATED" => "The vinculation <b>{{actividad}}</b> was updated successfully",
            "USERTYPE" => [
                "@TRANSLATION" => "User type",
                "MISSING" => "User type is missing",
                "PLACEHOLDER" => "Select an user type"
            ],
        ],
    ]
];
