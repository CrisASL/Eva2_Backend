<?php
class Usuario {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerUsuarios() {
        $query = "SELECT id, nombre, apellido, email, rol, estado FROM Usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearUsuario($nombre, $apellido, $email, $contraseña, $fecha_nacimiento, $telefono, $direccion, $rol) {
        $query = "INSERT INTO Usuario (nombre, apellido, email, contraseña, fecha_nacimiento, telefono, direccion, rol) 
                  VALUES (:nombre, :apellido, :email, :contrasena, :fecha_nacimiento, :telefono, :direccion, :rol)";
        
        $stmt = $this->conn->prepare($query);
        $hashedPassword = password_hash($contraseña, PASSWORD_DEFAULT);

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contrasena', $hashedPassword);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':rol', $rol);
    
        try {
            $stmt->execute();
            return array("message" => "Usuario creado exitosamente.");
        } catch (PDOException $e) {
            return array("error" => "Error en la consulta: " . $e->getMessage());
        }
    }
    
    public function actualizarUsuario($id, $data) {
        $sql = "UPDATE Usuario SET 
                nombre = :nombre,
                apellido = :apellido,
                email = :email,
                telefono = :telefono,
                direccion = :direccion,
                rol = :rol,
                fecha_nacimiento = :fecha_nacimiento,
                estado = :estado 
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':nombre', $data->nombre);
        $stmt->bindValue(':apellido', $data->apellido);
        $stmt->bindValue(':email', $data->email);
        $stmt->bindValue(':telefono', $data->telefono);
        $stmt->bindValue(':direccion', $data->direccion);
        $stmt->bindValue(':rol', $data->rol);
        $stmt->bindValue(':fecha_nacimiento', $data->fecha_nacimiento);
        $stmt->bindValue(':estado', $data->estado);

        try {
            if ($stmt->execute()) {
                return ["message" => "Usuario actualizado exitosamente."];
            } else {
                return ["error" => "Error al actualizar el usuario."];
            }
        } catch (PDOException $e) {
            return ["error" => "Error en la consulta: " . $e->getMessage()];
        }
    }
    
    public function eliminarUsuario($id) {
        $query = "DELETE FROM Usuario WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        try {
            $stmt->execute();
            return ["message" => "Usuario eliminado exitosamente."];
        } catch (PDOException $e) {
            return ["error" => "Error al eliminar el usuario: " . $e->getMessage()];
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