SELECT DISTINCT n.*
FROM `notes` AS n INNER JOIN `items` AS i ON n.`item_id` = i.`id`
  INNER JOIN `users` AS u ON i.user_id = u.id
  LEFT JOIN notes_tag_rel AS nt ON n.id = nt.note_id
WHERE i.id = 55 AND i.user_id = 1 AND ((nt.tag_id IS NULL) OR (nt.tag_id IN (1)))
ORDER BY n.c_time DESC
LIMIT 10 OFFSET 0;