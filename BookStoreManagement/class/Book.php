<?php
    include 'C:/xampp/htdocs/BookStoreManagement/utility/DBConnection.php';

    class Book{
        public $conn;

        public function __construct() {
            $db = new DBConnection();
            $this->conn = $db->conn;
        }

        public function saveBook($post) {
            $bookTitle = $post['bookTitle'];
            $bookDesc = $post['bookDesc'];
            $author = $post['author'];
            
            $sql = "INSERT INTO books (bookTitle, bookDesc, author) VALUES ('$bookTitle', '$bookDesc', '$author')";
            $result = $this->conn->query($sql);

            if($result){
                return json_encode(array('type' => 'success', 'message' => 'Book added successfully'));
            }
            else{
                return json_encode(array('type' => 'error', 'message' => 'Failed to add book'));
            }
        }

        public function getAllBooks(){
            $sql = "SELECT * FROM books";
            $result = $this->conn->query($sql);
            $books = array();

            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $books[] = $row;
                }
            }

            return $books;
        }

        public function editBook($editId){
            $sql= "SELECT * FROM books WHERE bookId = '$editId'";
            $result = $this->conn->query($sql);

            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $data['bookId'] = $row['bookId'];
                    $data['bookTitle'] = $row['bookTitle'];
                    $data['bookDesc'] = $row['bookDesc'];
                    $data['author'] = $row['author'];
                }

                return json_encode($data);
            }
        }

        public function updateBook($post){
            $bookId = $post['bookId'];
            $bookTitle = $post['updateBookTitle'];
            $bookDesc = $post['bookDesc'];
            $author = $post['author'];

            $sql = "UPDATE books SET bookTitle = '$bookTitle', bookDesc = '$bookDesc', author = '$author' WHERE bookId = '$bookId'";
            $result = $this->conn->query($sql);

            if($result){
                return json_encode(array('type' => 'success', 'message' => 'Book updated successfully'));
            }
            else {
                return json_encode(array('type' => 'error', 'message' => 'Failed to update book'));
            }
        }

        public function deleteBook($deleteId){
            $sql = "DELETE FROM books WHERE bookId = '$deleteId'";
            $result = $this->conn->query($sql);

            if($result){
                return json_encode(array('type' => 'success', 'message' => 'Book deleted successfully'));
            }
            else {
                return json_encode(array('type' => 'error', 'message' => 'Failed to delete book'));
            }
        }
    }

    $book = new Book();

    if(isset($_POST['bookTitle'])) {
        $saveBook = $book->saveBook($_POST);
        echo $saveBook;
    }

    if(isset($_POST['editId'])){
        $editBook = $book->editBook($_POST['editId']);
        echo $editBook;
    }

    if(isset($_POST['bookId'])){
        $updateBook = $book->updateBook($_POST);
        echo $updateBook;
    }

    if(isset($_POST['deleteId'])){
        $deleteBook = $book->deleteBook($_POST['deleteId']);
        echo $deleteBook;
    }
?>