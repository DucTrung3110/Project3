<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'personal_blog';

// Kết nối đến database
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Xử lý lấy danh sách bài viết 
if (isset($_GET['get_posts'])) {
    $sql = "SELECT * FROM posts ORDER BY created_at DESC";
    $result = $conn->query($sql);
    $posts = [];

    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }

    echo json_encode($posts);
    exit;
}

// Xử lý thêm bài viết
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['title'], $_POST['content'])) {
    $name = trim($_POST['name']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($name) && !empty($title) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO posts (name, title, content) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $title, $content);
        if ($stmt->execute()) {
            echo json_encode(["message" => "Post added successfully"]);
        } else {
            echo json_encode(["error" => "Error adding post"]);
        }
        $stmt->close();
    }
    exit;
}

// Xử lý xóa bài viết
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    echo json_encode(["message" => "Post deleted successfully"]);
    $stmt->close();
    exit;
}

// Xử lý sửa bài viết
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'], $_POST['title'], $_POST['content'])) {
    $id = intval($_POST['update']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param('ssi', $title, $content, $id);
        if ($stmt->execute()) {
            echo json_encode(["message" => "Post updated successfully"]);
        } else {
            echo json_encode(["error" => "Error updating post"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Title and content cannot be empty"]);
    }
    exit;
}


$conn->close();
