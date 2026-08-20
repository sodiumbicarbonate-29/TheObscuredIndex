-- Add is_private column to Manhwas table if it doesn't exist
ALTER TABLE Manhwas ADD COLUMN IF NOT EXISTS is_private TINYINT(1) DEFAULT 0;