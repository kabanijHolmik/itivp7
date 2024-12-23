<?php
require_once __DIR__ . "/databaseConnection.php";

function getOffices()
{
    $connection = connectToDatabase();

    $officesArray = [];

    $sql = "SELECT id, name, coordinates FROM offices";
    $stmt = $connection->prepare($sql);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $officesArray[] = $row;
        }
    }

    $stmt->close();

    disconnectFromDatabase($connection);

    return $officesArray;
}

function getCoordinates($name)
{
    $connection = connectToDatabase();

    $coordinates = null;

    $sql = "SELECT * FROM offices WHERE name = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $name);

    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();
    $coordinates = $row["coordinates"];

    $stmt->close();

    disconnectFromDatabase($connection);

    return $coordinates;
}
