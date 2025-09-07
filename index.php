<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookstore_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add new book
    if (isset($_POST['add_book'])) {
        $title = $conn->real_escape_string($_POST['title']);
        $author = $conn->real_escape_string($_POST['author']);
        $price = $conn->real_escape_string($_POST['price']);
        $category_id = $conn->real_escape_string($_POST['category_id']);
        $description = $conn->real_escape_string($_POST['description']);
        
        $sql = "INSERT INTO books (title, author, price, category_id, description) 
                VALUES ('$title', '$author', $price, $category_id, '$description')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Book added successfully!";
        } else {
            $error = "Error adding book: " . $conn->error;
        }
    }
    
    // Add new category
    if (isset($_POST['add_category'])) {
        $category_name = $conn->real_escape_string($_POST['category_name']);
        
        $sql = "INSERT INTO categories (name) VALUES ('$category_name')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Category added successfully!";
        } else {
            $error = "Error adding category: " . $conn->error;
        }
    }
    
    // Delete book
    if (isset($_POST['delete_book'])) {
        $book_id = $conn->real_escape_string($_POST['book_id']);
        
        $sql = "DELETE FROM books WHERE id = $book_id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Book deleted successfully!";
        } else {
            $error = "Error deleting book: " . $conn->error;
        }
    }
    
    // Delete category
    if (isset($_POST['delete_category'])) {
        $category_id = $conn->real_escape_string($_POST['category_id']);
        
        // First, set books in this category to NULL category
        $update_books = "UPDATE books SET category_id = NULL WHERE category_id = $category_id";
        $conn->query($update_books);
        
        // Then delete the category
        $sql = "DELETE FROM categories WHERE id = $category_id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Category deleted successfully!";
        } else {
            $error = "Error deleting category: " . $conn->error;
        }
    }
}

// Get all books
$sql_books = "SELECT books.*, categories.name as category_name 
              FROM books 
              LEFT JOIN categories ON books.category_id = categories.id 
              ORDER BY books.created_at DESC";
$books = $conn->query($sql_books);

// Get all categories
$sql_categories = "SELECT * FROM categories ORDER BY name";
$categories = $conn->query($sql_categories);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Bookstore</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Online Bookstore</h1>
            <p>Browse our collection of books and manage your inventory</p>
        </header>
        
        <!-- Display messages -->
        <?php if (isset($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Book Listing Section -->
        <section class="section">
            <h2>Book Collection</h2>
            
            <div class="books-grid">
                <?php if ($books->num_rows > 0): ?>
                    <?php while($book = $books->fetch_assoc()): ?>
                        <div class="book-card">
                            <div class="book-info">
                                <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                                <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
                                <p class="book-price">$<?php echo number_format($book['price'], 2); ?></p>
                                <?php if ($book['category_name']): ?>
                                    <span class="book-category"><?php echo htmlspecialchars($book['category_name']); ?></span>
                                <?php endif; ?>
                                <p class="book-description"><?php echo substr(htmlspecialchars($book['description']), 0, 100) . '...'; ?></p>
                                
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" name="delete_book" class="btn btn-delete" 
                                            onclick="return confirm('Are you sure you want to delete this book?')">
                                        Delete Book
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No books found in the database.</p>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- Add Book Form -->
        <section class="section">
            <h2>Add New Book</h2>
            
            <form method="post">
                <div class="form-group">
                    <label for="title">Book Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="author">Author</label>
                    <input type="text" id="author" name="author" required>
                </div>
                
                <div class="form-group">
                    <label for="price">Price ($)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        <option value="">Select a category</option>
                        <?php if ($categories->num_rows > 0): ?>
                            <?php while($category = $categories->fetch_assoc()): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="form-group full-width">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="add_book" class="btn">Add Book</button>
                </div>
            </form>
        </section>
        
        <!-- Category Management -->
        <section class="section">
            <h2>Manage Categories</h2>
            
            <h3>Add New Category</h3>
            <form method="post" style="margin-bottom: 30px;">
                <div class="form-group">
                    <label for="category_name">Category Name</label>
                    <input type="text" id="category_name" name="category_name" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="add_category" class="btn">Add Category</button>
                </div>
            </form>
            
            <h3>Existing Categories</h3>
            <div class="categories-list">
                <?php 
                // Reset categories pointer
                $categories->data_seek(0); 
                ?>
                
                <?php if ($categories->num_rows > 0): ?>
                    <?php while($category = $categories->fetch_assoc()): ?>
                        <div class="category-item">
                            <span><?php echo htmlspecialchars($category['name']); ?></span>
                            <form method="post">
                                <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                <button type="submit" name="delete_category" class="btn btn-delete" 
                                        onclick="return confirm('Are you sure you want to delete this category?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No categories found.</p>
                <?php endif; ?>
            </div>
        </section>
        
        <footer>
            <p>Online Bookstore &copy; <?php echo date('Y'); ?></p>
        </footer>
    </div>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>