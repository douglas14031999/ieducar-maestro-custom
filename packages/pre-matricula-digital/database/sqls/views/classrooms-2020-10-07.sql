CREATE OR REPLACE VIEW public.classrooms AS
SELECT turma.cod_turma AS id,
       turma.turma_turno_id AS period_id,
       turma.ref_ref_cod_escola AS school_id,
       turma.ref_ref_cod_serie AS grade_id,
       school_years.id AS school_year_id,
       turma.nm_turma AS name,
       turma.max_aluno AS vacancy,
       (SELECT turma.max_aluno - COUNT(*)
        FROM pmieducar.matricula_turma
                 JOIN pmieducar.matricula ON true
            AND matricula.cod_matricula = matricula_turma.ref_cod_matricula
        WHERE ref_cod_turma = turma.cod_turma
          AND matricula_turma.ativo = 1
          AND NOT matricula.dependencia) AS available_vacancies
FROM pmieducar.turma
JOIN pmieducar.serie ON serie.cod_serie = turma.ref_ref_cod_serie
JOIN school_years ON school_years.year = turma.ano
WHERE serie.importar_serie_pre_matricula
  AND turma.ativo = 1;
