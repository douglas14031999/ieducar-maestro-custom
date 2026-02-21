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
    m.ref_ref_cod_serie as grade,
    coalesce(mt.turno_id, t.turma_turno_id) as period
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
left join pmieducar.matricula_turma mt
    on mt.ref_cod_matricula = m.cod_matricula
      and mt.ativo = 1
left join pmieducar.turma t
    on t.cod_turma = mt.ref_cod_turma
inner join pmieducar.curso c
    on c.cod_curso = m.ref_cod_curso
where f.ativo = 1
  and a.ativo = 1
  and c.modalidade_curso in (1, 3);
