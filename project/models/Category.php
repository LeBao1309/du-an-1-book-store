<?php


class Category {
    public static function find($id) {
        global $conn;
        $sql = "SELECT * FROM categories WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
