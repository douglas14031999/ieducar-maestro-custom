CREATE OR REPLACE VIEW public.courses AS
SELECT
    curso.cod_curso AS id,
    curso.nm_curso AS name
FROM pmieducar.curso
WHERE importar_curso_pre_matricula
  AND ativo = 1
