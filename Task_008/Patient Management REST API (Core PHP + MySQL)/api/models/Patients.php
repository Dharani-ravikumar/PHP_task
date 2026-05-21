<?php

class Patient {

    // GET ALL
    public static function getAll($conn) {

        $result = mysqli_query($conn, "SELECT * FROM patients");

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // GET BY ID (bind_param)
    public static function getById($conn, $id) {

        $stmt = mysqli_prepare($conn, "SELECT * FROM patients WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    // CREATE (bind_param)
    public static function create($conn, $data) {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO patients(name, age, gender, phone) VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "siss",
            $data['name'],
            $data['age'],
            $data['gender'],
            $data['phone']
        );

        return mysqli_stmt_execute($stmt);
    }

    // UPDATE (bind_param)
    public static function update($conn, $id, $data) {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE patients SET name=?, age=?, gender=?, phone=? WHERE id=?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sissi",
            $data['name'],
            $data['age'],
            $data['gender'],
            $data['phone'],
            $id
        );

        return mysqli_stmt_execute($stmt);
    }

    // DELETE (bind_param)
    public static function delete($conn, $id) {

        $stmt = mysqli_prepare($conn, "DELETE FROM patients WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);

        return mysqli_stmt_execute($stmt);
    }
}

?>