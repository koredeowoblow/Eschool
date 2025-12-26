-- Clean up duplicate roles script
-- Run this in your database to remove duplicate roles

-- First, let's see what duplicates exist
SELECT name, school_id, COUNT(*) as count
FROM roles
GROUP BY name, school_id
HAVING COUNT(*) > 1;

-- Delete duplicates, keeping only the oldest one for each name/school combination
DELETE r1 FROM roles r1
INNER JOIN roles r2 
WHERE r1.id > r2.id 
AND r1.name = r2.name 
AND r1.school_id = r2.school_id;

-- Verify no more duplicates
SELECT name, school_id, COUNT(*) as count
FROM roles
GROUP BY name, school_id
HAVING COUNT(*) > 1;
