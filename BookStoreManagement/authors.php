<?php
    include 'C:/xampp/htdocs/BookStoreManagement/class/Book.php';
    require_once 'C:/xampp/htdocs/BookStoreManagement/class/Author.php';
    
    $db = new DBConnection();
    $author = new Author();
    $authors = $author->getAllAuthors();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BookStore Management System</title>
    <!-- style links-->
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/custom.css" rel="stylesheet" type="text/css">
    <!-- script links-->
    <script src="https://kit.fontawesome.com/01dd5a93f4.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"
            integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
            crossorigin="anonymous"></script>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#"><i class="fas fa-book"></i> BookStore Management</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <input type="text" id="searchBookId" class="form-control d-inline" style="width: 200px;" placeholder="Enter Book ID">
                </li>
                <li class="nav-item">
                    <button class="btn btn-info d-inline" id="searchBookBtn"><i class="fa-solid fa-search"></i> Search Book</button>
                </li>
                <li class="nav-item">
                    <button class="btn btn-success d-inline" data-bs-toggle="modal" data-bs-target="#addBook"><i class="fa-solid fa-book"></i> Add Book</button>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid mt-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2">
                <div class="list-group">
                    <a href="index.php" class="list-group-item list-group-item-action">Books</a>
                    <a href="authors.php" class="list-group-item list-group-item-action">Authors</a>
                    <a href="orders.php" class="list-group-item list-group-item-action">Orders</a>
                </div>
            </div>
            <!-- Main Content -->
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user"></i> Author List
                        <button class="btn btn-success float-right" data-bs-toggle="modal" data-bs-target="#addAuthor"><i class="fa-solid fa-user-plus"></i> Add Author</button>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="10%">Author ID</th>
                                    <th width="70%">Name</th>
                                    <th width="20%">Manage Author</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($authors as $author): ?>
                                <tr>
                                    <td><?php echo $author['author_id']; ?></td>
                                    <td><?php echo $author['name']; ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm editAuthorBtn" data-bs-toggle="modal" data-bs-target="#editAuthor" id="<?php echo $author['author_id']; ?>"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                                        <button class="btn btn-danger btn-sm deleteAuthorBtn" id="<?php echo $author['author_id']; ?>"><i class="fa-solid fa-trash"></i> Delete</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for adding a new book -->
    <div class="modal fade" id="addBook" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Book</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addBookForm">
                        <div class="form-group">
                            <label for="bookTitle">Book Title</label>
                            <input type="text" name="bookTitle" class="form-control" required placeholder="Enter book title">
                        </div>
                        <div class="form-group">
                            <label for="bookDesc">Book Description</label>
                            <input type="text" name="bookDesc" class="form-control" required placeholder="Enter book description">
                        </div>
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" name="author" class="form-control" required placeholder="Enter book author">
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" name="price" class="form-control" required placeholder="Enter book price">
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" name="quantity" class="form-control" required placeholder="Enter quantity in stock">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="addBookBtn">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for editing book details -->
    <div class="modal fade" id="editBook" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Book Details</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editBookForm">
                        <div class="form-group">
                            <label for="bookTitle">Book Title</label>
                            <input type="text" name="updateBookTitle" id="editBookTitle" class="form-control" required>
                            <input type="hidden" name="bookId" id="bookId">
                        </div>
                        <div class="form-group">
                            <label for="bookDesc">Book Description</label>
                            <input type="text" name="bookDesc" id="bookDesc" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" name="author" id="author" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" name="price" id="price" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="updateBook" name="updatebtn">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for alert -->
    <div class="modal fade" id="alert" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Alert!</h1>
                </div>
                <div class="modal-body">
                    <div class="alert"></div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $('#addBookBtn').on('click',function() {
                $.post('class/Book.php', $('form#addBookForm').serialize(), function(data){
                    var data = JSON.parse(data);
                    if(data.type == 'success'){
                        $('#addBook').modal('hide');
                        $('#alert').modal('show');
                        $('#alert .alert').addClass('alert-success').append(data.message).delay(1500).fadeOut('slow',function(){
                            location.reload();
                        });
                    }
                });
            });

            $('.editBookBtn').on('click', function(e) {
                $('#editBook').modal('show');
                var editId = $(this).attr('id');
                $.post('class/Book.php', {editId: editId}, function(data) {
                    var data = JSON.parse(data);
                    $('#editBookTitle').val(data.bookTitle);
                    $('#bookDesc').val(data.bookDesc);
                    $('#author').val(data.author);
                    $('#price').val(data.price);
                    $('#quantity').val(data.quantity_in_stock);
                    $('#bookId').val(data.bookId); 
                });
            });

            $('#updateBook').on('click', function() {
                var formData = $('form#editBookForm').serializeArray();
                $.post('class/Book.php', $('form#editBookForm').serialize(), function(data) {
                    var data = JSON.parse(data);
                    if (data.type == 'success') {
                        $('#editBook').modal('hide');
                        $('#alert').modal('show');
                        $('#alert .alert').addClass('alert-success').append(data.message).delay(1500).fadeOut('slow', function() {
                            location.reload();
                        });
                    } else {
                        console.error(data.message); // Log the error message
                    }
                });
            });

            $('.deleteButton').on('click',function(e){
                var confirmDelete = confirm('Are you sure you want to delete this book?');
                if(confirmDelete){
                    $.post('class/Book.php', {deleteId: e.target.id}, function(data){
                        var data = JSON.parse(data);
                        if (data.type == 'success') {
                            $('#alert').modal('show');
                            $('#alert .alert').addClass('alert-success').append(data.message).delay(15000).fadeOut('slow', function() {
                                location.reload();
                            });
                        } else {
                            console.error(data.message); // Log the error message
                        }
                    });
                }
            });

            $('#searchBookBtn').on('click', function() {
                var searchBookId = $('#searchBookId').val();
                if (searchBookId) {
                    $.post('class/Book.php', {searchId: searchBookId}, function(data) {
                        var data = JSON.parse(data);
                        if (data.bookId) {
                            var bookRow = `
                                <tr>
                                    <td>${data.bookId}</td>
                                    <td><h4>${data.bookTitle}</h4></td>
                                    <td><h4>${data.author}</h4></td>
                                    <td>${data.price}</td>
                                    <td>${data.quantity_in_stock}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm editBookBtn" data-bs-toggle="modal" data-bs-target="#editBook" id="${data.bookId}"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                                        <button class="btn btn-danger btn-sm deleteButton" id="${data.bookId}"><i class="fa-solid fa-trash"></i> Delete</button>
                                    </td>
                                </tr>
                            `;
                            $('#bookTableBody').html(bookRow);
                        } else {
                            alert('Book not found');
                        }
                    });
                } else {
                    alert('Please enter a Book ID');
                }
            });
        });
    </script>

    <!-- Modal for adding a new author -->
    <div class="modal fade" id="addAuthor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Author</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAuthorForm">
                        <div class="form-group">
                            <label for="authorName">Author Name</label>
                            <input type="text" name="authorName" class="form-control" required placeholder="Enter author name">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="addAuthorBtn">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for editing author details -->
    <div class="modal fade" id="editAuthor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Author Details</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editAuthorForm">
                        <div class="form-group">
                            <label for="authorName">Author Name</label>
                            <input type="text" name="updateAuthorName" id="editAuthorName" class="form-control" required>
                            <input type="hidden" name="authorId" id="authorId">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="updateAuthorBtn">Update</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $('#addAuthorBtn').on('click', function() {
                $.post('class/Author.php', $('form#addAuthorForm').serialize(), function(data) {
                    var data = JSON.parse(data);
                    if (data.type == 'success') {
                        $('#addAuthor').modal('hide');
                        location.reload();
                    } else {
                        console.error(data.message); // Log the error message
                    }
                });
            });

            $('.editAuthorBtn').on('click', function(e) {
                $('#editAuthor').modal('show');
                var editId = $(this).attr('id');
                $.post('class/Author.php', {editId: editId}, function(data) {
                    var data = JSON.parse(data);
                    $('#editAuthorName').val(data.name);
                    $('#authorId').val(data.author_id);
                });
            });

            $('#updateAuthorBtn').on('click', function() {
                $.post('class/Author.php', $('form#editAuthorForm').serialize(), function(data) {
                    var data = JSON.parse(data);
                    if (data.type == 'success') {
                        $('#editAuthor').modal('hide');
                        location.reload();
                    } else {
                        console.error(data.message); // Log the error message
                    }
                });
            });

            $('.deleteAuthorBtn').on('click', function(e) {
                var confirmDelete = confirm('Are you sure you want to delete this author?');
                if (confirmDelete) {
                    $.post('class/Author.php', {deleteId: e.target.id}, function(data) {
                        var data = JSON.parse(data);
                        if (data.type == 'success') {
                            location.reload();
                        } else {
                            console.error(data.message); // Log the error message
                        }
                    });
                }
            });
        });
    </script>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 BookStore Management System. All Rights Reserved.</p>
    </footer>
</body>
</html>