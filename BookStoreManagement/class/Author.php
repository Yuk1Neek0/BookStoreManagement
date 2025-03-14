<?php
require_once 'C:/xampp/htdocs/BookStoreManagement/utility/DBConnection.php';

class Author {
    public $conn;

    public function __construct() {
        $db = DBConnection::getInstance(); // Use Singleton instance
        $this->conn = $db->getConnection();
    }

    public function saveAuthor($post) {
        $authorName = $this->conn->real_escape_string($post['authorName']);

        $sql = "INSERT INTO authors (name) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $authorName);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return json_encode(array('type' => 'success', 'message' => 'Author added successfully'));
        } else {
            return json_encode(array('type' => 'error', 'message' => 'Failed to add author: ' . $this->conn->error));
        }
    }

    public function getAllAuthors() {
        $sql = "SELECT * FROM authors";
        $result = $this->conn->query($sql);
        $authors = array();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $authors[] = $row;
            }
        }

        return $authors;
    }

    public function editAuthor($editId) {
        $editId = intval($editId);
        $sql = "SELECT * FROM authors WHERE author_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $editId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data = [
                'author_id' => $row['author_id'],
                'name' => $row['name']
            ];
            $stmt->close();
            return json_encode($data);
        }
        $stmt->close();
    }

    public function updateAuthor($post) {
        $authorId = intval($post['authorId']);
        $authorName = $this->conn->real_escape_string($post['updateAuthorName']);

        $sql = "UPDATE authors SET name = ? WHERE author_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $authorName, $authorId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return json_encode(array('type' => 'success', 'message' => 'Author updated successfully'));
        } else {
            return json_encode(array('type' => 'error', 'message' => 'Failed to update author: ' . $this->conn->error));
        }
    }

    public function deleteAuthor($deleteId) {
        $deleteId = intval($deleteId);
        $sql = "DELETE FROM authors WHERE author_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $deleteId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return json_encode(array('type' => 'success', 'message' => 'Author deleted successfully'));
        } else {
            return json_encode(array('type' => 'error', 'message' => 'Failed to delete author: ' . $this->conn->error));
        }
    }

    public function searchAuthor($searchId) {
        $searchId = intval($searchId);
        $sql = "SELECT * FROM authors WHERE author_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $searchId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data = [
                'author_id' => $row['author_id'],
                'name' => $row['name']
            ];
            $stmt->close();
            return json_encode($data);
        } else {
            $stmt->close();
            return json_encode(array('type' => 'error', 'message' => 'Author not found'));
        }
    }
}

$author = new Author();

if (isset($_POST['authorName'])) {
    echo $author->saveAuthor($_POST);
}

if (isset($_POST['editId'])) {
    echo $author->editAuthor($_POST['editId']);
}

if (isset($_POST['authorId'])) {
    echo $author->updateAuthor($_POST);
}

if (isset($_POST['deleteId'])) {
    echo $author->deleteAuthor($_POST['deleteId']);
}

if (isset($_POST['searchId'])) {
    echo $author->searchAuthor($_POST['searchId']);
}
?>