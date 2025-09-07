-- Create the database
CREATE DATABASE IF NOT EXISTS bookstore_db;
USE bookstore_db;

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create books table
CREATE TABLE IF NOT EXISTS books (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category_id INT(6) UNSIGNED,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Insert sample categories
INSERT INTO categories (name) VALUES 
('Fiction'),
('Non-Fiction'),
('Science Fiction'),
('Biography'),
('Mystery'),
('Fantasy');

-- Insert sample books
INSERT INTO books (title, author, price, category_id, description) VALUES 
('The Great Gatsby', 'F. Scott Fitzgerald', 12.99, 1, 'A classic novel of the Jazz Age'),
('To Kill a Mockingbird', 'Harper Lee', 11.50, 1, 'A story of racial injustice and childhood innocence'),
('1984', 'George Orwell', 10.25, 3, 'A dystopian social science fiction novel'),
('The Hobbit', 'J.R.R. Tolkien', 14.95, 6, 'A fantasy novel and children''s book'),
('Sapiens', 'Yuval Noah Harari', 18.75, 2, 'A brief history of humankind');