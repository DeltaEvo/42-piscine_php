SELECT name FROM distrib
WHERE id_distrib IN (42, 62, 63, 64, 65, 66, 67, 68, 69, 71, 88, 89, 90)
OR    (LENGTH(name) - LENGTH(REPLACE(LOWER(name), 'y', '')) = 2)
LIMIT 5 OFFSET 2;
