<?php
require_once 'C:/xampp/htdocs/BookStoreManagement/utility/DBConnection.php';

class Book {
    public $conn;

    public function __construct() {
        $db = DBConnection::getInstance(); // Use Singleton instance
        $this->conn = $db->getConnection();
    }

    public function saveBook($post) {
        $bookTitle = $this->conn->real_escape_string($post['bookTitle']);
        $bookDesc = $this->conn->real_escape_string($post['bookDesc']);
        $authorName = $this->conn->real_escape_string($post['author']);
        $price = floatval($post['price']);
        $quantity = intval($post['quantity']);

        // Insert author into authors table if not exists
        $authorSql = "INSERT INTO authors (name) VALUES (?) ON DUPLICATE KEY UPDATE author_id=LAST_INSERT_ID(author_id)";
        $stmt = $this->conn->prepare($authorSql);
        $stmt->bind_param("s", $authorName);
        $stmt->execute();
        $authorId = $this->conn->insert_id;
        $stmt->close();

        // Insert book into books table
        $sql = "INSERT INTO books (bookTitle, bookDesc, author_id, price, quantity_in_stock) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssidi", $bookTitle, $bookDesc, $authorId, $price, $quantity);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return json_encode(array('type' => 'success', 'message' => 'Book added successfully'));
        } else {
            return json_encode(array('type' => 'error', 'message' => 'Failed to add book: ' . $this->conn->error));
        }
    }

    public function getAllBooks() {
        $sql = "SELECT books.*, authors.name as author FROM books JOIN authors ON books.author_id = authors.author_id";
        $result = $this->conn->query($sql);
        $books = array();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
        }

        return $books;
    }

    public function editBook($editId) {
        $editId = intval($editId);
        $sql = "SELECT books.*, authors.name as author FROM books JOIN authors ON books.author_id = authors.author_id WHERE books.bookId = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $editId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data = [
                'bookId' => $row['bookId'],
                'bookTitle' => $row['bookTitle'],
                'bookDesc' => $row['bookDesc'],
                'author' => $row['author'],
                'price' => $row['price'],
                'quantity_in_stock' => $row['quantity_in_stock']
            ];
            $stmt->close();
            return json_encode($data);
        }
        $stmt->close();
    }

    public function updateBook($post) {
        $bookId = intval($post['bookId']);
        $bookTitle = $this->conn->real_escape_string($post['updateBookTitle']);
        $bookDesc = $this->conn->real_escape_string($post['bookDesc']);
        $authorName = $this->conn->real_escape_string($post['author']);
        $price = floatval($post['price']);
        $quantity = intval($post['quantity']);

        // Insert author into authors table if not exists
        $authorSql = "INSERT INTO authors (name) VALUES (?) ON DUPLICATE KEY UPDATE author_id=LAST_INSERT_ID(author_id)";
        $stmt = $this->conn->prepare($authorSql);
        $stmt->bind_param("s", $authorName);
        $stmt->execute();
        $authorId = $this->conn->insert_id;
        $stmt->close();

        // Update book in books table
        $sql = "UPDATE books SET bookTitle = ?, bookDesc = ?, author_id = ?, price = ?, quantity_in_stock = ? WHERE bookId = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssidii", $bookTitle, $bookDesc, $authorId, $price, $quantity, $bookId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return json_encode(array('type' => 'success', 'message' => 'Book updated successfully'));
        } else {
            return json_encode(array('type' => 'error', 'message' => 'Failed to update book: ' . $this->conn->error));
        }
    }

    public function deleteBook($deleteId) {
        $deleteId = intval($deleteId);
        $sql = "DELETE FROM books WHERE bookId = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $deleteId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return json_encode(array('type' => 'success', 'message' => 'Book deleted successfully'));
        } else {
            return json_encode(array('type' => 'error', 'message' => 'Failed to delete book: ' . $this->conn->error));
        }
    }

    public function searchBook($searchId) {
        $searchId = intval($searchId);
        $sql = "SELECT books.*, authors.name as author FROM books JOIN authors ON books.author_id = authors.author_id WHERE books.bookId = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $searchId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data = [
                'bookId' => $row['bookId'],
                'bookTitle' => $row['bookTitle'],
                'bookDesc' => $row['bookDesc'],
                'author' => $row['author'],
                'price' => $row['price'],
                'quantity_in_stock' => $row['quantity_in_stock']
            ];
            $stmt->close();
            return json_encode($data);
        } else {
            $stmt->close();
            return json_encode(array('type' => 'error', 'message' => 'Book not found'));
        }
    }
}

$book = new Book();

if (isset($_POST['bookTitle'])) {
    echo $book->saveBook($_POST);
}

if (isset($_POST['editId'])) {
    echo $book->editBook($_POST['editId']);
}

if (isset($_POST['bookId'])) {
    echo $book->updateBook($_POST);
}

if (isset($_POST['deleteId'])) {
    echo $book->deleteBook($_POST['deleteId']);
}

if (isset($_POST['searchId'])) {
    echo $book->searchBook($_POST['searchId']);
}