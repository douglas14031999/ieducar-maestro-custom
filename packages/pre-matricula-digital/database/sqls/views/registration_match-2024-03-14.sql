create or replace view public.registration_match as
select
    p.idpes as id,
    p.nome as name,
    p.slug as slug,
    f.data_nasc as date_of_birth,
    f.cpf as cpf,
    d.rg as rg,
    d.certidao_nascimento as birth_certificate,
    m.ano as year,
    m.ref_ref_cod_escola as school,
    m.ref_cod_curso as course,
    m.ref_ref_cod_serie as grade
from pmieducar.aluno a
inner join cadastro.pessoa p
    on p.idpes = a.ref_idpes
inner join cadastro.fisica f
    on f.idpes = p.idpes
left join cadastro.documento d
    on d.idpes = p.idpes
inner join pmieducar.matricula m
    on m.ref_cod_aluno = a.cod_aluno
        and m.ativo = 1
        and m.aprovado in (1, 2, 3, 12, 13, 14)
inner join pmieducar.curso c
    on c.cod_curso = m.ref_cod_curso
where f.ativo = 1
  and a.ativo = 1
  and c.modalidade_curso in (1, 3);
