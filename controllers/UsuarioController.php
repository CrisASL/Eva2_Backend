<?php
require_once 'models/Usuario.php';

class UsuarioController {
    private $usuario;
    private $conn;

    public function __construct($db) {
        $this->usuario = new Usuario($db);
        $this->conn = $db;
    }

    public function obtenerUsuarios() {
        try {
            $usuarios = $this->usuario->obtenerUsuarios();
            return array("data" => $usuarios);
        } catch (Exception $e) {
            http_response_code(500);
            return array("error" => "Ocurrió un error al obtener los usuarios.", "details" => $e->getMessage());
        }
    }

    public function crearUsuario($data) {
        try {
            $this->usuario->crearUsuario(
                $data->nombre,
                $data->apellido,
                $data->email,
                $data->contraseña,
                $data->fecha_nacimiento,
                $data->telefono,
                $data->direccion,
                $data->rol
            );

            http_response_code(201);
            return array("message" => "Usuario creado exitosamente.");
        } catch (Exception $e) {
            http_response_code(500);
            return array("error" => "Ocurrió un error al crear el usuario.", "details" => $e->getMessage());
        }
    }

    public function actualizarUsuario($id, $data) {
        try {
            error_log("Datos recibidos para actualizar: " . print_r($data, true));

            $campos = [
                "nombre" => $data->nombre ?? null,
                "apellido" => $data->apellido ?? null,
                "email" => $data->email ?? null,
                "telefono" => $data->telefono ?? null,
                "direccion" => $data->direccion ?? null,
                "rol" => $data->rol ?? null,
                "fecha_nacimiento" => $data->fecha_nacimiento ?? null,
                "estado" => $data->estado ?? null,
            ];

            if (empty(array_filter($campos, function($v) {
                return !is_null($v) && $v !== '';
            }))) {
                return ["error" => "No se proporcionaron datos válidos para actualizar."];
            }

            $setParts = [];
            foreach ($campos as $campo => $valor) {
                if (!is_null($valor)) {
                    $setParts[] = "$campo = :$campo";
                }
            }
            $setClause = implode(", ", $setParts);

            $sql = "UPDATE Usuario SET $setClause WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);

            foreach ($campos as $campo => $valor) {
                if (!is_null($valor)) {
                    $stmt->bindValue(":$campo", $valor);
                }
            }

            if ($stmt->execute()) {
                return ["message" => "Usuario actualizado exitosamente."];
            } else {
                return ["error" => "No se pudo actualizar el usuario."];
            }
        } catch (PDOException $e) {
            http_response_code(500);
            return ["error" => "Error al actualizar el usuario.", "details" => $e->getMessage()];
        }
    }

    public function eliminarUsuario($id) {
        try {
            $this->usuario->eliminarUsuario($id);
            return array("message" => "Usuario eliminado exitosamente.");
        } catch (Exception $e) {
            http_response_code(500);
            return array("error" => "Ocurrió un error al eliminar el usuario.", "details" => $e->getMessage());
        }
    }

    public function actualizarUsuarioParcial($id, $data) {
        $fields = [];
        $params = [];
        
        if (isset($data->nombre)) {
            $fields[] = "nombre = :nombre";
            $params[':nombre'] = $data->nombre;
        }
        if (isset($data->apellido)) {
            $fields[] = "apellido = :apellido";
            $params[':apellido'] = $data->apellido;
        }
        if (isset($data->email)) {
            $fields[] = "email = :email";
            $params[':email'] = $data->email;
        }
        if (isset($data->telefono)) {
            $fields[] = "telefono = :telefono";
            $params[':telefono'] = $data->telefono;
        }
        if (isset($data->direccion)) {
            $fields[] = "direccion = :direccion";
            $params[':direccion'] = $data->direccion;
        }
        if (isset($data->fecha_nacimiento)) {
            $fields[] = "fecha_nacimiento = :fecha_nacimiento";
            $params[':fecha_nacimiento'] = $data->fecha_nacimiento;
        }
        if (isset($data->estado)) {
            $fields[] = "estado = :estado";
            $params[':estado'] = $data->estado;
        }
        if (isset($data->rol)) {
            $fields[] = "rol = :rol";
            $params[':rol'] = $data->rol;
        }

        if (empty($fields)) {
            return ["error" => "No hay nada que actualizar."];
        }

        $query = "UPDATE Usuario SET " . implode(", ", $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $params[':id'] = $id;

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        try {
            if ($stmt->execute()) {
                return ["message" => "Usuario actualizado parcialmente exitosamente."];
            } else {
                return ["error" => "Error al actualizar el usuario."];
            }
        } catch (PDOException $e) {
            return ["error" => "Error en la consulta: " . $e->getMessage()];
        }
    }
}
?>