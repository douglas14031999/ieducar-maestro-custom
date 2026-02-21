CREATE OR REPLACE VIEW search_students_by_name AS
WITH mother_phone AS (
  SELECT DISTINCT ON (idpes) idpes, ddd, fone
  FROM cadastro.fone_pessoa
  WHERE fone IS NOT NULL
  ORDER BY idpes, tipo DESC
),
     father_phone AS (
       SELECT DISTINCT ON (idpes) idpes, ddd, fone
       FROM cadastro.fone_pessoa
       WHERE fone IS NOT NULL
       ORDER BY idpes, tipo DESC
     ),
     guardian_phone AS (
       SELECT DISTINCT ON (idpes) idpes, ddd, fone
       FROM cadastro.fone_pessoa
       WHERE fone IS NOT NULL
       ORDER BY idpes, tipo DESC
     )
SELECT
  cod_aluno AS id,
  pessoa.nome AS name,
  fisica.cpf,
  fisica.data_nasc AS date_of_birth,
  CASE
    WHEN aluno.tipo_responsavel = 'p' THEN pp.nome
    WHEN aluno.tipo_responsavel = 'r' THEN pr.nome
    ELSE pm.nome
    END AS responsible_name,
  CASE
    WHEN aluno.tipo_responsavel = 'p' THEN fp.cpf
    WHEN aluno.tipo_responsavel = 'r' THEN fr.cpf
    ELSE fm.cpf
    END AS responsible_cpf,
  CASE
    WHEN aluno.tipo_responsavel = 'p' THEN fp.data_nasc
    WHEN aluno.tipo_responsavel = 'r' THEN fr.data_nasc
    ELSE fm.data_nasc
    END AS responsible_date_of_birth,
  CASE
    WHEN aluno.tipo_responsavel = 'p' THEN tfp.ddd::varchar || tfp.fone::varchar
    WHEN aluno.tipo_responsavel = 'r' THEN tfr.ddd::varchar || tfr.fone::varchar
    ELSE tfm.ddd::varchar || tfm.fone::varchar
    END AS responsible_phone
FROM cadastro.pessoa
       JOIN cadastro.fisica ON pessoa.idpes = fisica.idpes AND fisica.ativo = 1
       JOIN pmieducar.aluno ON aluno.ref_idpes = pessoa.idpes AND aluno.ativo = 1
       LEFT JOIN cadastro.pessoa as pm ON pm.idpes = fisica.idpes_mae
       LEFT JOIN cadastro.fisica as fm ON fm.idpes = fisica.idpes_mae
       LEFT JOIN mother_phone tfm ON tfm.idpes = fisica.idpes_mae
       LEFT JOIN cadastro.pessoa as pp ON pp.idpes = fisica.idpes_pai
       LEFT JOIN cadastro.fisica as fp ON fp.idpes = fisica.idpes_pai
       LEFT JOIN father_phone tfp ON tfp.idpes = fisica.idpes_pai
       LEFT JOIN cadastro.pessoa as pr ON pr.idpes = fisica.idpes_responsavel
       LEFT JOIN cadastro.fisica as fr ON fr.idpes = fisica.idpes_responsavel
       LEFT JOIN guardian_phone tfr ON tfr.idpes = fisica.idpes_responsavel;
