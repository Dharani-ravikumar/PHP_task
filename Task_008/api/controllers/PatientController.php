<?php

require_once __DIR__ . "/../models/Patients.php";
require_once __DIR__ . "/../helpers/Response.php";

class PatientController {

    public static function handle($conn, $method, $id = null) {

        // GET
        if ($method == "GET") {

            if ($id) {

                $data = Patient::getById($conn, $id);

                if ($data) {
                    http_response_code(200);
                    Response::send(true, "Patient found", $data);
                } else {
                    http_response_code(404);
                    Response::send(false, "Patient not found");
                }

            } else {

                $data = Patient::getAll($conn);
                Response::send(true, "All patients", $data);
            }
        }

        // POST
        elseif ($method == "POST") {

            $data = json_decode(file_get_contents("php://input"), true);

            if (empty($data['name']) || empty($data['phone'])) {
                http_response_code(400);
                Response::send(false, "Name and phone required");
                return;
            }

            Patient::create($conn, $data);
            http_response_code(201);
            Response::send(true, "Patient created");
        }

        // PUT
        elseif ($method == "PUT") {

            $data = json_decode(file_get_contents("php://input"), true);

            Patient::update($conn, $id, $data);

            Response::send(true, "Patient updated");
        }

        // DELETE
        elseif ($method == "DELETE") {

            Patient::delete($conn, $id);

            Response::send(true, "Patient deleted");
        }

        else {
            http_response_code(405);
            Response::send(false, "Invalid method");
        }
    }
}

?>