CREATE VIEW preregistration_position
AS SELECT
       id as preregistration_id,
       ROW_NUMBER() OVER (PARTITION BY process_id, school_id, grade_id, period_id, status ORDER BY priority DESC, created_at ASC) AS position
FROM preregistrations p
ORDER BY p.id
