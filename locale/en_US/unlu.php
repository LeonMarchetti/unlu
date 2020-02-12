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
                "SUCCESS" => "The certificate was added successfully",
            ],
            "DATE" => [
                "@TRANSLATION" => "Date",
                "MISSING" => "Date is missing",
            ],
            "DELETE" => [
                "@TRANSLATION" => "Delete",
                "CONFIRM" => "Are you sure of deleting the certificate <b>{{ titulo }}</b>?",
                "SUCCESS" => "The certificate {{ titulo }} was deleted successfully"
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
                "SUCCESS" => "The certificate \"{{ titulo }}\"'s file was replaced successfully"
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
            "SUCCESS" => "Certificate was assigned successfully",
            "VINCULATION_ID" => [
                "MISSING" => "Vinculation not found",
            ],
        ],
        "DEPENDENCY" => "Dependency",
        "DESCRIPTION" => "Research. Teaching. Extension. National University of Luján",
        "INSTITUTION" => "Institution",
        "PETITION" => [
            "@TRANSLATION" => "Petitions",
            "ADDED" => "The petition was requested succesfully",
            "APPROVE" => "Approve",
            "APPROVE_PETITION" => "Approve petition",
            "APPROVED" => "Approved",
            "ASSIGN" => "Assign",
            "CERTIFICATE" => [
                "@TRANSLATION" => "Certificate",
                "ASSIGN" => [
                    "@TRANSLATION" => "Assign certificate",
                    "SUCCESS" => "The certificate was assigned to the petition successfully",
                ],
                "FILE" => [
                    "@TRANSLATION" => "File",
                    "ERROR" => "Error uploading the file",
                    "MISSING" => "File is missing",
                ],
                "NEEDED" => "This service requires filling a form for the petition to be approved.",
                "PETITION_MISSING" => "Petition's id to assign is missing",
            ],
            "DELETE_SUCCESSFUL" => "The petition was deleted successfully",
            "DESCRIPTION" => [
                "@TRANSLATION" => "Description",
                "MISSING" => "Description is missing"
            ],
            "DOWN" => "Remove Request",
            "EDIT" => "Edit",
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
            "UPDATED" => "Petition updated",
            "VINCULATION" => [
                "@TRANSLATION" => "Vinculation",
                "HELP" => "It's not required to be linked to a vinculation",
                "MISSING" => "Without vinculation",
                "NONE" => "None",
            ],
        ],
        "PHONE" => [
            "@TRANSLATION" => "Phone",
            "MISSING" => "Phone number is not assigned in user profile"
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
                "MISSING" => "Indicating if certificate is needed is missing",
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
            "UPDATED" => "The service was updated successfully",
        ],
        "VINCULATION" => [
            "@TRANSLATION" => "Vinculation",
            "ACTIVITY" => [
                "@TRANSLATION" => "Activity",
                "MISSING" => "Activity is missing"
            ],
            "ADDED" => "Vinculation requested",
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
            "REQUEST_DATE" => "Request date",
            "RESPONSABLE" => "Responsable",
            "UPDATED" => "The vinculation was updated successfully",
            "USERTYPE" => [
                "@TRANSLATION" => "User type",
                "MISSING" => "User type is missing",
                "PLACEHOLDER" => "Select an user type"
            ],
        ],
    ]
];