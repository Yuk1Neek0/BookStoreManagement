<?php
    include 'C:/xampp/htdocs/BookStoreManagement/class/Order.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Items Management</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/custom.css" rel="stylesheet" type="text/css">
    <script src="https://kit.fontawesome.com/01dd5a93f4.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"
            integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
            crossorigin="anonymous"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#"><i class="fas fa-shopping-cart"></i> Order Items Management</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <button class="btn btn-success d-inline" data-bs-toggle="modal" data-bs-target="#addOrderItem"><i class="fa-solid fa-plus"></i> Add Order Item</button>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid mt-5">
        <div class="row">
            <div class="col-md-2">
                <div class="list-group">
                    <a href="index.php" class="list-group-item list-group-item-action">Books</a>
                    <a href="authors.php" class="list-group-item list-group-item-action">Authors</a>
                    <a href="orders.php" class="list-group-item list-group-item-action active">Order Items</a>
                </div>
            </div>
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-list"></i> Order Items List
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Order Item ID</th>
                                    <th>Order ID</th>
                                    <th>Book ID</th>
                                    <th>Price at Time of Order</th>
                                    <th>Quantity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="orderItemsTableBody">
                                <?php
                                    $order = new Order();
                                    $orderItems = json_decode($order->getAllOrderItems(), true);
                                    foreach ($orderItems as $item):
                                ?>
                                <tr>
                                    <td><?php echo $item['order_item_id']; ?></td>
                                    <td><?php echo $item['order_id']; ?></td>
                                    <td><?php echo $item['book_id']; ?></td>
                                    <td><?php echo $item['price_at_time_of_order']; ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm editOrderItemBtn" data-bs-toggle="modal" data-bs-target="#editOrderItem" id="<?php echo $item['order_item_id']; ?>"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                                        <button class="btn btn-danger btn-sm deleteOrderItemBtn" id="<?php echo $item['order_item_id']; ?>"><i class="fa-solid fa-trash"></i> Delete</button>
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

    <!-- Modal for adding a new order item -->
    <div class="modal fade" id="addOrderItem" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Order Item</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addOrderItemForm">
                        <div class="form-group">
                            <label for="order_id">Order ID</label>
                            <input type="number" name="order_id" class="form-control" required placeholder="Enter order ID">
                        </div>
                        <div class="form-group">
                            <label for="book_id">Book ID</label>
                            <input type="number" name="book_id" class="form-control" required placeholder="Enter book ID">
                        </div>
                        <div class="form-group">
                            <label for="price_at_time_of_order">Price at Time of Order</label>
                            <input type="number" step="0.01" name="price_at_time_of_order" class="form-control" required placeholder="Enter price">
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" name="quantity" class="form-control" required placeholder="Enter quantity">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="addOrderItemBtn">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#addOrderItemBtn').on('click', function() {
    $.post('class/Order.php', $('form#addOrderItemForm').serialize(), function(data) {
        console.log(data); // Log the server response
        try {
            var parsedData = JSON.parse(data);
            if (parsedData.type === 'success') {
                $('#addOrderItem').modal('hide');
                location.reload();
            } else {
                alert(parsedData.message);
            }
        } catch (e) {
            console.error('Invalid JSON response:', data);
            alert('An error occurred. Please try again.');
        }
    });
});

            $('.editOrderItemBtn').on('click', function() {
                var orderItemId = $(this).attr('id');
                $.post('class/Order.php', {editOrderItemId: orderItemId}, function(data) {
                    var parsedData = JSON.parse(data);
                    $('#editOrderItemId').val(parsedData.order_item_id);
                    $('#editOrderId').val(parsedData.order_id);
                    $('#editBookId').val(parsedData.book_id);
                    $('#editPriceAtTimeOfOrder').val(parsedData.price_at_time_of_order);
                    $('#editQuantity').val(parsedData.quantity);
                });
            });

            $('#updateOrderItemBtn').on('click', function() {
                $.post('class/Order.php', $('form#editOrderItemForm').serialize(), function(data) {
                    var parsedData = JSON.parse(data);
                    if (parsedData.type === 'success') {
                        $('#editOrderItem').modal('hide');
                        location.reload();
                    } else {
                        alert(parsedData.message);
                    }
                });
            });

            $('.deleteOrderItemBtn').on('click', function() {
                var confirmDelete = confirm('Are you sure you want to delete this order item?');
                if (confirmDelete) {
                    var orderItemId = $(this).attr('id');
                    $.post('class/Order.php', {deleteOrderItemId: orderItemId}, function(data) {
                        var parsedData = JSON.parse(data);
                        if (parsedData.type === 'success') {
                            location.reload();
                        } else {
                            alert(parsedData.message);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>