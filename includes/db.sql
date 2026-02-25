-- 1. Create the Database
CREATE DATABASE IF NOT EXISTS splar_machinery;
USE splar_machinery;

-- --------------------------------------------------------

-- 2. Table: admins
-- Used for logging into the Admin Panel.
-- Note: Passwords should be hashed (e.g., using PHP's password_hash) before inserting.
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Stores the hashed password
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert a default admin account
-- Username: admin
-- Password: password123 (This is a simplified hash for testing only. In production, use PHP password_hash())
INSERT INTO admins (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- --------------------------------------------------------

-- 3. Table: products
-- Stores machinery details including the 3D model file path.
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(50) NOT NULL, -- e.g., 'Solar', 'Laser', 'Automation'
    description TEXT,              -- Short summary for the card
    features LONGTEXT,             -- Detailed specs or HTML content
    image_url VARCHAR(255),        -- Main display image (e.g., 'uploads/products/laser.jpg')
    model_url VARCHAR(255),        -- 3D Model file (e.g., 'uploads/products/laser.glb')
    is_active BOOLEAN DEFAULT 1,   -- To hide products without deleting them
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

-- 4. Table: blogs
-- For news, articles, and updates.
CREATE TABLE blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE, -- For URL friendly links (e.g., new-solar-tech)
    content LONGTEXT NOT NULL,         -- The main article body
    image_url VARCHAR(255),            -- Featured image
    author VARCHAR(100) DEFAULT 'SPLAR Team',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

-- 5. Table: customers
-- Stores client logos and testimonials.
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(100) NOT NULL,
    logo_url VARCHAR(255),             -- Path to logo image
    testimonial TEXT,                  -- Optional: What they said about SPLAR
    website_link VARCHAR(255),         -- Optional: Link to client's site
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

-- 6. Table: enquiries
-- Stores messages submitted via the "Contact Us" form.
CREATE TABLE enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(150),
    message TEXT NOT NULL,
    status ENUM('New', 'Read', 'Replied') DEFAULT 'New', -- Tracks admin action
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

-- 7. Table: site_settings
-- Stores global contact info. This allows the Admin to change the phone number
-- or address on the website instantly without editing HTML files.
CREATE TABLE site_settings (
    id INT PRIMARY KEY, -- We will only have one row, ID always 1
    contact_phone VARCHAR(50),
    contact_email VARCHAR(100),
    office_address TEXT,
    map_embed_code TEXT,       -- Google Maps iframe code
    facebook_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default placeholder data for settings
INSERT INTO site_settings (id, contact_phone, contact_email, office_address, facebook_url) VALUES 
(1, '+91 99018 63914', 'splar@splar-machinery.com', 'Vazhakala Complex, Peenya 4th Phase, Bangalore 560 058', '#');