create view exporter_preregistration as
select
    p.protocol,
    s."name" as student_name,
    s.date_of_birth as student_date_of_birth,
    r."name" as responsible_name,
    r.phone as responsible_phone,
    r.email as responsible_email,
    concat(
        pa.address, ', ',
        pa."number", ', ',
        case when
                         pa.complement = '' or pa.complement is null
                 then ''
             else concat(pa.complement, ', ')
            end,
        pa.city,
        ', CEP ',
        pa.postal_code
        ) as responsible_address,
    p.process_id,
    p.school_id,
    sc."name" as school_name,
    p.grade_id,
    g."name" as grade_name,
    p.period_id,
    pe."name" as period_name,
    pp."position",
    p.created_at,
    p.preregistration_type_id,
    case p.preregistration_type_id
        when 1 then 'Rematrícula'
        when 2 then 'Matrícula'
        when 3 then 'Lista de espera'
        end as type_name,
    p.status,
    case p.status
        when 1 then 'Em espera'
        when 2 then 'Deferido'
        when 3 then 'Indeferido'
        when 4 then 'Responsável Convocado'
        when 5 then 'Em confirmação'
        end as status_name
from preregistrations p
         inner join preregistration_position pp
                    on pp.preregistration_id = p.id
         inner join people s
                    on s.id = p.student_id
         inner join people r
                    on r.id = p.responsible_id
         inner join person_addresses pa
                    on pa.person_id = r.id
         inner join schools sc
                    on sc.id = p.school_id
         inner join grades g
                    on g.id = p.grade_id
         inner join periods pe
                    on pe.id = p.period_id
order by
    sc."name",
    g."name",
    p.status,
    pp."position",
    s."name";
