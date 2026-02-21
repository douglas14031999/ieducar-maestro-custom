create view report_preregistration as
SELECT exporter_preregistration.protocol,
       exporter_preregistration.student_name,
       exporter_preregistration.school_name,
       exporter_preregistration.status_name,
       exporter_preregistration.period_name,
       exporter_preregistration.grade_name,
       fcn_upper(instituicao.nm_instituicao::text) AS institution_name,
       fcn_upper(instituicao.nm_responsavel::text) AS responsible_name,
       instituicao.bairro AS institution_neighborhood,
       instituicao.ddd_telefone AS institution_area_code,
       to_char(instituicao.telefone, '99999-9999')::text AS institution_phone_number,
       instituicao.ref_sigla_uf AS institution_federal_unit,
       instituicao.cidade AS institution_city,
       instituicao.logradouro AS institution_address,
       instituicao.numero::text::character varying AS institution_address_number,
       instituicao.cep::text::character varying
       institution_postal_code,
       processes.school_year_id,
       exporter_preregistration.preregistration_type_id,
       exporter_preregistration.status,
       exporter_preregistration.process_id,
       exporter_preregistration.period_id,
       exporter_preregistration.school_id,
       exporter_preregistration.grade_id,
       exporter_preregistration.created_at
FROM exporter_preregistration
         INNER JOIN processes ON processes.id = exporter_preregistration.process_id
         INNER JOIN pmieducar.escola ON escola.cod_escola = exporter_preregistration.school_id
         INNER JOIN relatorio.view_dados_escola ON escola.cod_escola = view_dados_escola.cod_escola
         INNER JOIN pmieducar.instituicao ON instituicao.cod_instituicao = escola.ref_cod_instituicao
         INNER JOIN pmieducar.configuracoes_gerais ON configuracoes_gerais.ref_cod_instituicao = instituicao.cod_instituicao
         LEFT JOIN person_has_place php ON php.person_id = escola.ref_idpes AND php.type = 1
         LEFT JOIN addresses a ON a.id = php.place_id
ORDER BY exporter_preregistration.created_at ASC;
