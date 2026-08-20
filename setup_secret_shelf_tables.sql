-- Create Secret_Shelf_Access table to track users who have access to the secret shelf
CREATE TABLE IF NOT EXISTS Secret_Shelf_Access (
    access_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    granted_date DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Create Secret_Manhwas table to store manhwas added to the secret shelf
CREATE TABLE IF NOT EXISTS Secret_Manhwas (
    secret_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    manhwa_id INT NOT NULL,
    added_date DATETIME NOT NULL,
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (manhwa_id) REFERENCES Manhwas(manhwa_id) ON DELETE CASCADE,
    UNIQUE KEY user_manhwa_unique (user_id, manhwa_id)
);