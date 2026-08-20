UPDATE Manhwas SET cover_image = REPLACE(cover_image, 'images/covers/', 'uploads/covers/') WHERE cover_image LIKE 'images/covers/%';
