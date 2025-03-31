<?php
require_once 'C:/xampp/htdocs/BookStoreManagement/utility/DBConnection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Order {
    public $conn;

    public function __construct() {
        $db = DBConnection::getInstance(); // Use Singleton instance
        $this->conn = $db->getConnection();
    }

    public function createOrder($post) {
        $customerId = intval($post['customer_id']);
        $orderDate = $this->conn->real_escape_string($post['order_date']);
        $shippingAddress = $this->conn->real_escape_string($post['shipping_address']);
        $status = $this->conn->real_escape_string($post['status']);
        $totalAmount = floatval($post['total_amount']);
        $orderItems = $post['order_items']; // Array of items

        // Insert into orders table
        $sql = "INSERT INTO orders (customer_id, order_date, shipping_address, status, total_amount) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isssd", $customerId, $orderDate, $shippingAddress, $status, $totalAmount);
        $stmt->execute();
        $orderId = $this->conn->insert_id;
        $stmt->close();

        // Insert into order_items table
        foreach ($orderItems as $item) {
            $bookId = intval($item['book_id']);
            $quantity = intval($item['quantity']);
            $priceAtTimeOfOrder = floatval($item['price_at_time_of_order']);

            $itemSql = "INSERT INTO order_items (order_id, book_id, quantity, price_at_time_of_order) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($itemSql);
            $stmt->bind_param("iiid", $orderId, $bookId, $quantity, $priceAtTimeOfOrder);
            $stmt->execute();
            $stmt->close();
        }

        return json_encode(array('type' => 'success', 'message' => 'Order created successfully'));
    }

    public function getAllOrders() {
        $sql = "SELECT o.*, GROUP_CONCAT(CONCAT(oi.book_id, ':', oi.quantity) SEPARATOR ', ') AS items 
                FROM orders o 
                LEFT JOIN order_items oi ON o.order_id = oi.order_id 
                GROUP BY o.order_id";
        $result = $this->conn->query($sql);
        $orders = array();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
        }

        return json_encode($orders);
    }

    public function editOrder($orderId) {
        $orderId = intval($orderId);
        $sql = "SELECT * FROM orders WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $order = $result->fetch_assoc();
            $stmt->close();

            // Fetch order items
            $itemSql = "SELECT * FROM order_items WHERE order_id = ?";
            $stmt = $this->conn->prepare($itemSql);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $itemResult = $stmt->get_result();
            $orderItems = array();

            while ($itemRow = $itemResult->fetch_assoc()) {
                $orderItems[] = $itemRow;
            }

            $order['items'] = $orderItems;
            $stmt->close();

            return json_encode($order);
        }

        $stmt->close();
        return json_encode(array('type' => 'error', 'message' => 'Order not found'));
    }

    public function updateOrder($post) {
        $orderId = intval($post['order_id']);
        $customerId = intval($post['customer_id']);
        $orderDate = $this->conn->real_escape_string($post['order_date']);
        $shippingAddress = $this->conn->real_escape_string($post['shipping_address']);
        $status = $this->conn->real_escape_string($post['status']);
        $totalAmount = floatval($post['total_amount']);
        $orderItems = $post['order_items']; // Array of items

        // Update orders table
        $sql = "UPDATE orders SET customer_id = ?, order_date = ?, shipping_address = ?, status = ?, total_amount = ? WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isssdi", $customerId, $orderDate, $shippingAddress, $status, $totalAmount, $orderId);
        $stmt->execute();
        $stmt->close();

        // Delete existing order items
        $deleteSql = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = $this->conn->prepare($deleteSql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $stmt->close();

        // Insert updated order items
        foreach ($orderItems as $item) {
            $bookId = intval($item['book_id']);
            $quantity = intval($item['quantity']);
            $priceAtTimeOfOrder = floatval($item['price_at_time_of_order']);

            $itemSql = "INSERT INTO order_items (order_id, book_id, quantity, price_at_time_of_order) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($itemSql);
            $stmt->bind_param("iiid", $orderId, $bookId, $quantity, $priceAtTimeOfOrder);
            $stmt->execute();
            $stmt->close();
        }

        return json_encode(array('type' => 'success', 'message' => 'Order updated successfully'));
    }

    public function deleteOrder($orderId) {
        $orderId = intval($orderId);

        // Delete order items
        $itemSql = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = $this->conn->prepare($itemSql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $stmt->close();

        // Delete order
        $sql = "DELETE FROM orders WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return json_encode(array('type' => 'success', 'message' => 'Order deleted successfully'));
        } else {
            return json_encode(array('type' => 'error', 'message' => 'Failed to delete order: ' . $this->conn->error));
        }
    }

    public function getAllOrderItems() {
        $sql = "SELECT * FROM order_items";
        $result = $this->conn->query($sql);
        $orderItems = array();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $orderItems[] = $row;
            }
        }

        return json_encode($orderItems);
    }

    public function createOrderItem($post) {
        $orderId = intval($post['order_id']);
        $bookId = intval($post['book_id']);
        $priceAtTimeOfOrder = floatval($post['price_at_time_of_order']);
        $quantity = intval($post['quantity']);

        $sql = "INSERT INTO order_items (order_id, book_id, price_at_time_of_order, quantity) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iidi", $orderId, $bookId, $priceAtTimeOfOrder, $quantity);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return json_encode(array('type' => 'success', 'message' => 'Order item created successfully.'));
        } else {
            return json_encode(array('type' => 'error', 'message' => 'Failed to create order item: ' . $this->conn->error));
        }
    }

    public function editOrderItem($orderItemId) {
        $orderItemId = intval($orderItemId);
        $sql = "SELECT * FROM order_items WHERE order_item_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $orderItemId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $orderItem = $result->fetch_assoc();
            $stmt->close();
            return json_encode($orderItem);
        } else {
            $stmt->close();
            return json_encode(array('type' => 'error', 'message' => 'Order item not found.'));
        }
    }

    public function updateOrderItem($post) {
        $orderItemId = intval($post['order_item_id']);
        $orderId = intval($post['order_id']);
        $bookId = intval($post['book_id']);
        $priceAtTimeOfOrder = floatval($post['price_at_time_of_order']);
        $quantity = intval($post['quantity']);

        $sql = "UPDATE order_items SET order_id = ?, book_id = ?, price_at_time_of_order = ?, quantity = ? WHERE order_item_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iidii", $orderId, $bookId, $priceAtTimeOfOrder, $quantity, $orderItemId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return json_encode(array('type' => 'success', 'message' => 'Order item updated successfully.'));
        } else {
            return json_encode(array('type' => 'error', 'message' => 'Failed to update order item: ' . $this->conn->error));
        }
    }

    public function deleteOrderItem($orderItemId) {
        $orderItemId = intval($orderItemId);

        $sql = "DELETE FROM order_items WHERE order_item_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $orderItemId);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return json_encode(array('type' => 'success', 'message' => 'Order item deleted successfully.'));
        } else {
            return json_encode(array('type' => 'error', 'message' => 'Failed to delete order item: ' . $this->conn->error));
        }
    }
}

$order = new Order();

if (isset($_POST['createOrder'])) {
    echo $order->createOrder($_POST);
}

if (isset($_POST['editOrderId'])) {
    echo $order->editOrder($_POST['editOrderId']);
}

if (isset($_POST['updateOrder'])) {
    echo $order->updateOrder($_POST);
}

if (isset($_POST['deleteOrderId'])) {
    echo $order->deleteOrder($_POST['deleteOrderId']);
}

if (isset($_POST['getAllOrders'])) {
    echo $order->getAllOrders();
}

if (isset($_POST['getAllOrderItems'])) {
    echo $order->getAllOrderItems();
}

if (isset($_POST['createOrderItem'])) {
    echo $order->createOrderItem($_POST);
}

if (isset($_POST['editOrderItemId'])) {
    echo $order->editOrderItem($_POST['editOrderItemId']);
}

if (isset($_POST['updateOrderItem'])) {
    echo $order->updateOrderItem($_POST);
}

if (isset($_POST['deleteOrderItemId'])) {
    echo $order->deleteOrderItem($_POST['deleteOrderItemId']);
}