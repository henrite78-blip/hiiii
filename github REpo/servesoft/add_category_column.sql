-- Add Category column to MenuItem table if it doesn't exist
ALTER TABLE MenuItem
ADD COLUMN Category varchar(50) DEFAULT 'Main Course' AFTER ItemDescription;

-- Update existing menu items to have a default category
UPDATE MenuItem
SET Category = 'Main Course'
WHERE Category IS NULL OR Category = '';
