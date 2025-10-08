-- Add Category column to MenuItem table
ALTER TABLE MenuItem ADD COLUMN IF NOT EXISTS Category varchar(50) DEFAULT 'Main Course';

-- Update existing items to have a category
UPDATE MenuItem SET Category = 'Main Course' WHERE Category IS NULL OR Category = '';
