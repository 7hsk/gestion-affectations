-- Fix database relations quickly

-- 1. Delete test users and extra department
DELETE FROM users WHERE id IN (33, 34, 35, 36, 37, 38);
DELETE FROM departements WHERE id = 8;

-- 2. Fix user departments
UPDATE users SET departement_id = 2 WHERE id IN (21, 22, 23, 39, 40, 41, 42);
UPDATE users SET departement_id = 3 WHERE id IN (16, 32);

-- 3. Fix UE departments based on specialities
UPDATE unites_enseignement SET departement_id = 2 WHERE specialite LIKE '%Informatique%';
UPDATE unites_enseignement SET departement_id = 2 WHERE specialite LIKE '%Mathématiques%';
UPDATE unites_enseignement SET departement_id = 3 WHERE specialite LIKE '%Génie Civil%';
UPDATE unites_enseignement SET departement_id = 3 WHERE specialite LIKE '%Génie Mécanique%';

-- 4. Ensure we have proper chefs for each department
-- Department 2 (Info et Math): User 42 (mouad) is chef
-- Department 3 (Génie Civil): User 32 (karim l2ab ro7i) is chef

-- 5. Clear any existing affectations that might be wrong
DELETE FROM affectations WHERE annee_universitaire = '2026-2027';

-- 6. Clear notifications
DELETE FROM notifications;
