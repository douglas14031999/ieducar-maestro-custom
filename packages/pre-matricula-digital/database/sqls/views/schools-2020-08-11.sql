CREATE OR REPLACE VIEW public.schools AS
SELECT escola.cod_escola AS id,
       pessoa.nome AS name,
       escola.latitude,
       escola.longitude
FROM pmieducar.escola
         JOIN cadastro.pessoa ON escola.ref_idpes = pessoa.idpes
WHERE situacao_funcionamento = 1
  AND ativo = 1
