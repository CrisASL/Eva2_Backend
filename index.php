<?php

// CORS y headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Incluir dependencias
require_once 'config/database.php';
require_once 'controllers/UsuarioController.php';

// Crear conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Crear instancia del controlador
$usuarioController = new UsuarioController($db);

// Obtener el método HTTP de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $respuesta = $usuarioController->obtenerUsuarios();
        echo json_encode($respuesta);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->nombre, $data->apellido, $data->email, $data->contraseña, $data->fecha_nacimiento, $data->telefono, $data->direccion, $data->rol)) {
            $respuesta = $usuarioController->crearUsuario($data);
            echo json_encode($respuesta);
        } else {
            echo json_encode(array("message" => "Datos incompletos."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id)) {
            $userId = $data->id;
            echo json_encode($usuarioController->actualizarUsuario($userId, $data));
        } else {
            echo json_encode(["error" => "ID del usuario es requerido para actualizar."]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id)) {
            $respuesta = $usuarioController->eliminarUsuario($data->id);
            echo json_encode($respuesta);
        } else {
            echo json_encode(array("message" => "ID no proporcionado."));
        }
        break;

    case 'PATCH':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id)) {
            $respuesta = $usuarioController->actualizarUsuarioParcial($data->id, $data);
            echo json_encode($respuesta);
        } else {
            echo json_encode(array("message" => "ID no proporcionado."));
        }
        break;

    default:
        echo json_encode(array("message" => "Método no permitido."));
        break;
}

?>