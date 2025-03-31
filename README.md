-- Task 1: Create Views
-- View: Get book from specific price range
CREATE VIEW BooksByPriceRange AS
SELECT * FROM books
WHERE price BETWEEN low_price AND high_price;

-- Task 2: Create Stored Procedures
-- Procedure: Adding new book
DELIMITER //
CREATE PROCEDURE AddNewBook(
    IN p_bookTitle VARCHAR(255),
    IN p_genre VARCHAR(100),
    IN p_price DECIMAL(10,2),
    IN p_publisher VARCHAR(100),
    IN p_publication_date DATE,
    IN p_quantity_in_stock INT,
    IN p_author_id INT,
    IN p_bookDesc VARCHAR(255)
)
BEGIN
    INSERT INTO books (bookTitle, genre, price, publisher, publication_date, quantity_in_stock, author_id, bookDesc)
    VALUES (p_bookTitle, p_genre, p_price, p_publisher, p_publication_date, p_quantity_in_stock, p_author_id, p_bookDesc);
END //
DELIMITER ;

-- Task 3: Updating inventory after saling
DELIMITER //
CREATE PROCEDURE UpdateInventoryAfterSale(
    IN p_book_id INT,
    IN p_quantity_sold INT
)
BEGIN
    DECLARE current_stock INT;
    
    START TRANSACTION;
    
    SELECT quantity_in_stock INTO current_stock FROM books WHERE bookId = p_book_id;
    
    IF current_stock >= p_quantity_sold THEN
        UPDATE books SET quantity_in_stock = quantity_in_stock - p_quantity_sold WHERE bookId = p_book_id;
        COMMIT;
    ELSE
        ROLLBACK;
    END IF;
END //
DELIMITER ;

-- Task 4: Create Triggers
-- Trigger: Alert when stock less than 10
DELIMITER //
CREATE TRIGGER LowStockAlert
AFTER UPDATE ON books
FOR EACH ROW
BEGIN
    IF NEW.quantity_in_stock < 10 THEN
        INSERT INTO notifications (message, created_at)
        VALUES (CONCAT('Book ID ', NEW.bookId, ' stock is low!'), NOW());
    END IF;
END //
DELIMITER ;

-- Task 5: User Defined Functions
-- Function: Get total value of inventory
DELIMITER //
CREATE FUNCTION GetInventoryValue(p_book_id INT) RETURNS DECIMAL(10,2) DETERMINISTIC
BEGIN
    DECLARE total_value DECIMAL(10,2);
    SELECT price * quantity_in_stock INTO total_value FROM books WHERE bookId = p_book_id;
    RETURN total_value;
END //
DELIMITER ;


-- Task 6: Create Events

-- Event: Checking inventory every day
DELIMITER //
CREATE EVENT DailyLowStockCheck
ON SCHEDULE EVERY 1 DAY
DO
BEGIN
    INSERT INTO notifications (message, created_at)
    SELECT CONCAT('Low stock warning: ', bookTitle) AS message, NOW()
    FROM books WHERE quantity_in_stock < 5;
END //
DELIMITER ;
