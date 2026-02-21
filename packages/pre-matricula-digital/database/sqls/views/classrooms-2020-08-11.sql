CREATE OR REPLACE VIEW public.classrooms AS
SELECT turma.cod_turma AS id,
       turma.turma_turno_id AS period_id,
       turma.ref_ref_cod_escola AS school_id,
       turma.ref_ref_cod_serie AS grade_id,
       school_years.id AS school_year_id,
       turma.nm_turma AS name,
       turma.max_aluno AS vacancy
FROM pmieducar.turma
         JOIN pmieducar.serie ON serie.cod_serie = turma.ref_ref_cod_serie
         JOIN public.school_years ON school_years.year = turma.ano
WHERE serie.importar_serie_pre_matricula
  AND turma.ativo = 1
