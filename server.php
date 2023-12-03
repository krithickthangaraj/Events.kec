<?php
include('db_connection.php'); // Assuming this file contains your database connection code

$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "mydbms";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $title = mysqli_real_escape_string($conn, $_POST["title"]); // Sanitize input to prevent SQL injection
    $photo = $_FILES["photo"]["name"]; // Assuming you want to store the filename
    $link = mysqli_real_escape_string($conn, $_POST["link"]); // Sanitize input to prevent SQL injection
    $description = mysqli_real_escape_string($conn, $_POST["description"]); // Sanitize input to prevent SQL injection
    $datetime = mysqli_real_escape_string($conn, $_POST["datetime"]); // Sanitize input to prevent SQL injection
    $venue = mysqli_real_escape_string($conn, $_POST["venue"]); // Sanitize input to prevent SQL injection
    $contact = mysqli_real_escape_string($conn, $_POST["contact"]); // Sanitize input to prevent SQL injection

    // File upload handling
    $targetDirectory = "uploads/";
    $targetFile = $targetDirectory . basename($_FILES["photo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Example query
   $sql = "SELECT id, title, description FROM events";
    $result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    die("Query failed: " . $conn->error);
}

// Fetch and display results
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row["id"] . " - Title: " . $row["title"] . " - Description: " . $row["description"] . "<br>";
}

    // Check if image file is a valid image
    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($targetFile)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["photo"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowedFileTypes = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowedFileTypes)) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Move uploaded file
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            // Insert data into the database
            $sql = "INSERT INTO events (title, photo, link, description, datetime, venue, contact) 
                    VALUES ('$title', '$photo', '$link', '$description', '$datetime', '$venue', '$contact')";

            if ($conn->query($sql) === TRUE) {
                echo "File uploaded successfully and data inserted into the database.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

$conn->close();
?>
