<?php

return [
    "UNLU" => [
        "@TRANSLATION" => "UNLu",
        "ACTIONS" => [
            "DELETE_REQUEST" => "Delete Request",
            "DENIED" => "You are not enabled yet to perform actions in the system. Check with the administrator.",
            "REQUEST_SERVICE" => "Request Service",
            "REQUEST_VINCULATION" => "Request Vinculation",
        ],
        "ACTIVE" => "Active",
        "CERTIFICATE" => "Certificate",
        "DEPENDENCY" => "Dependency",
        "INSTITUTION" => "Institution",
        "PETITION" => [
            "@TRANSLATION" => "Petitions",
            "ADDED" => "The petition was requested succesfully",
            "APPROVE" => "Approve",
            "APPROVED" => "Approved",
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
                "MISSING" => "Without vinculation"
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
            "DESCRIPTION" => [
                "@TRANSLATION" => "Description",
                "MISSING" => "Description is missing"
            ],
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
                    The applicant is necessarily a member of the vinculation requested (the system appends it automatically).",
                "MISSING" => "Members are missing",
                "REMOVE" => "Remove",
                "REPEATED" => "There are repeated members",
                "SELECT_PLACEHOLDER" => "Select user"
            ],
            "PLURAL" => "Vinculations",
            "POSITION" => "Position",
            "REQUEST_DATE" => "Request date",
            "RESPONSABLE" => "Responsable",
            "USERTYPE" => [
                "@TRANSLATION" => "User type",
                "MISSING" => "User type is missing",
                "PLACEHOLDER" => "Select an user type"
            ],
        ],
    ]
];