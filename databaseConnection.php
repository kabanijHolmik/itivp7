<?php
function connectToDatabase()
{
    try {
        $conn = new mysqli("localhost", "root", "", "mail_db");
        return $conn;
    } catch (Exception $e) {
        die("Error: " . "ошибка при подключении к бд");
    }
}

function disconnectFromDatabase($conn)
{
    $conn->close();
}
