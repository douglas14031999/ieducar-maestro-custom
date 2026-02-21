CREATE VIEW public.schools AS
SELECT escola.cod_escola AS id,
       view_dados_escola.nome AS name,
       escola.latitude,
       escola.longitude,
       btrim(view_dados_escola.telefone) as phone,
       view_dados_escola.telefone_ddd AS area_code,
       view_dados_escola.email
FROM pmieducar.escola
         JOIN cadastro.pessoa ON escola.ref_idpes = pessoa.idpes
         JOIN relatorio.view_dados_escola ON escola.cod_escola = view_dados_escola.cod_escola
WHERE situacao_funcionamento = 1
  AND ativo = 1
