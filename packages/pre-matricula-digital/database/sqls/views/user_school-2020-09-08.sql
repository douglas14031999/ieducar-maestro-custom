CREATE OR REPLACE VIEW public.user_school AS
SELECT ref_cod_usuario user_id,
       ref_cod_escola school_id
FROM pmieducar.escola_usuario
