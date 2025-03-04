<?php
    include 'C:/xampp/htdocs/BookStoreManagement/class/Book.php';
    
    $db = new DBConnection();
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
<!-- script links-->
    <script src="https://kit.fontawesome.com/01dd5a93f4.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"
            integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
            crossorigin="anonymous"></script>


</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                         <i class="fas fa-book"></i>
                        BookStore Management System
                        <button class="btn btn-success float-right" style="float: right;" data-bs-toggle="modal" data-bs-target="#addBook"><i class="fa-solid fa-book"></i> Add book</button>
                    </div>

                    <div class="card-body">
                            <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="15%">No.</th>
                                    <th width="50%">Book Title</th>
                                    <th width="30%">Manage Book</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $book = new Book();
                                    $books = $book->getAllBooks();
                                    $no = 0;
                                    foreach($books as $book):
                                        $no++;
                                ?>
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td>
                                        
                                        <h4><?php echo $book['bookTitle'] ; ?></h4>
                                        <small>- By <?php echo $book['author']?></small>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm editBookBtn" data-bs-toggle="modal" data-bs-target="#editBook" id="<?php echo $book['bookId']; ?>"><i class="fa-solid fa-pen-to-square"></i>Edit Book</button>
                                        <button class="btn btn-danger btn-sm deleteButton" id= "<?php echo $book['bookId']; ?>"><i class="fa-solid fa-trash"></i>Delete Book</button>
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
    <!-- Button trigger modal -->

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
                <label for="author">author</label>
                <input type="text" name="author" class="form-control" required placeholder="Enter book author">
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
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="updateBook" name="updatebtn">Update</button>
      </div>
      </form>
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

<script type="text/javascript" >
    $(document).ready(function(){
        $('#addBookBtn').on('click',function()
        {
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
        })
    }
        
    );
</script>
</body>
</html>