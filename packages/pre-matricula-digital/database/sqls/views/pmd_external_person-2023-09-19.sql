create view pmd_external_person as
select
  pre.id as preregistration_id,
  p.idpes as external_person_id,
  p.nome as name,
  f.sexo as gender,
  f.data_nasc as date_of_birth,
  trim(to_char(f.cpf, '000"."000"."000"-"00')) as cpf,
  d.rg,
  d.certidao_nascimento as birth_certificate,
  '(' || trim(to_char(fp.ddd, '999')) || ') ' || trim(to_char(fp.fone, '99999-9999')) as phone,
  '(' || trim(to_char(fpm.ddd, '999')) || ') ' || trim(to_char(fpm.fone, '99999-9999')) as mobile,
  a.address,
  a."number",
  a.complement,
  a.neighborhood,
  trim(to_char(a.postal_code::integer, '99999-999')) as postal_code
from preregistrations pre
inner join people pe
on pre.student_id = pe.id
inner join cadastro.pessoa p
on p.idpes = pe.external_person_id
inner join cadastro.fisica f
on f.idpes = p.idpes
left join cadastro.documento d
on d.idpes = p.idpes
left join cadastro.fone_pessoa fp
on fp.idpes = p.idpes
and fp.tipo = 1
left join cadastro.fone_pessoa fpm
on fpm.idpes = p.idpes
and fpm.tipo = 2
left join person_has_place php
on php.person_id = p.idpes
left join addresses a
on a.id = php.place_id
where true
and pe.external_person_id is not null;
