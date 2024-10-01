<?php
// Database Connection Class
class Database {
    private $host = "localhost";
    private $db_name = "auth_system_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// Contact Form Class
class ContactForm {
    private $conn;
    public $name;
    public $email;
    public $message;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Validate and save contact data
    public function submitForm($name, $email, $message) {
        if (empty($name) || empty($email) || empty($message)) {
            return "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
        } else {
            $query = "INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)";
            $stmt = $this->conn->prepare($query);

            // Binding parameters
            $stmt->bindParam(':name', htmlspecialchars(strip_tags($name)));
            $stmt->bindParam(':email', htmlspecialchars(strip_tags($email)));
            $stmt->bindParam(':message', htmlspecialchars(strip_tags($message)));

            // Executing the query
            if ($stmt->execute()) {
                return "Message sent successfully!";
            } else {
                return "Failed to send message.";
            }
        }
    }
}

// Database connection
$database = new Database();
$db = $database->getConnection();

// Initialize form
$contactForm = new ContactForm($db);

// Initialize message
$message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $messageText = $_POST['message'];

    // Validate and submit the form
    $message = $contactForm->submitForm($name, $email, $messageText);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group textarea {
            height: 100px;
            resize: none;
        }
        .btn {
            width: 100%;
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 10px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #4cae4c;
        }
        .message {
            margin-top: 10px;
            text-align: center;
            font-weight: bold;
            color: #ff0000;
        }
        .success {
            color: #28a745;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Contact Us</h2>
<!-- Display message -->
<?php if ($message): ?>
            <div class="message <?= strpos($message, 'successfully') !== false ? 'success' : '' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
    
    <form method="post" action="">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name">
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
        </div>
        <div class="form-group">
            <label for="message">Message:</label>
            <textarea id="message" name="message"></textarea>
        </div>
        <button type="submit" class="btn">Send</button>

        </form>
</div>

</body>
</html>
