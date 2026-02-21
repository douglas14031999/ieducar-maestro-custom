CREATE OR REPLACE VIEW public.user AS
SELECT usuario.cod_usuario          AS id,
       usuario.ref_cod_tipo_usuario AS user_type_id,
       pessoa.nome                  AS name,
       funcionario.email            AS email,
       funcionario.senha            AS password
FROM pmieducar.usuario
         JOIN cadastro.pessoa ON pessoa.idpes = usuario.cod_usuario
         JOIN portal.funcionario ON funcionario.ref_cod_pessoa_fj = usuario.cod_usuario
WHERE usuario.ativo = 1
  AND funcionario.ativo = 1
