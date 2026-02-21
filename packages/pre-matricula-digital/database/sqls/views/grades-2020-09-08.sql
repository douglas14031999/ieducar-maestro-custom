CREATE OR REPLACE VIEW public.grades AS
SELECT serie.cod_serie     AS id,
       serie.nm_serie      AS name,
       serie.idade_inicial AS start_age,
       serie.idade_final   AS end_age,
       serie.ref_cod_curso AS course_id
FROM pmieducar.serie
WHERE importar_serie_pre_matricula
  AND ativo = 1
