CREATE VIEW process_vacancy
AS SELECT p.id AS process_id,
          c.school_id,
          c.grade_id,
          c.period_id,
          sum(c.vacancy) AS total,
          sum(c.available_vacancies) as available_vacancies,
          sum(c.available_vacancies) - (
              select count(*) from preregistrations pr
              where pr.school_id = c.school_id
                and pr.grade_id = c.grade_id
                and pr.period_id = c.period_id
                and pr.process_id = p.id
                and pr.status = 1
                and pr.preregistration_type_id in (1, 2)
          ) AS available
FROM processes p
JOIN process_grade pg ON pg.process_id = p.id
JOIN process_period pp ON pp.process_id = p.id
JOIN classrooms c ON c.grade_id = pg.grade_id AND c.period_id = pp.period_id AND c.school_year_id = p.school_year_id
GROUP BY p.id, c.school_id, c.grade_id, c.period_id;
