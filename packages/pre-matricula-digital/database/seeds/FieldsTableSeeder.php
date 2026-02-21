<?php

namespace iEducar\Packages\PreMatricula\Database\Seeds;

use iEducar\Packages\PreMatricula\Models\Field;
use Illuminate\Database\Seeder;

class FieldsTableSeeder extends Seeder
{
    private function responsibleFields()
    {
        Field::query()->updateOrCreate([
            'internal' => Field::RESPONSIBLE_NAME,
        ], [
            'field_type_id' => Field::TYPE_TEXT,
            'group_type_id' => Field::GROUP_RESPONSIBLE,
            'required' => true,
            'name' => 'Nome',
        ]);

        Field::query()->updateOrCreate([
            'internal' => Field::RESPONSIBLE_DATE_OF_BIRTH,
        ], [
            'field_type_id' => Field::TYPE_DATE,
            'group_type_id' => Field::GROUP_RESPONSIBLE,
            'required' => true,
            'name' => 'Data de nascimento',
        ]);

        Field::query()->updateOrCreate([
            'internal' => Field::RESPONSIBLE_CPF,
        ], [
            'field_type_id' => Field::TYPE_CPF,
            'group_type_id' => Field::GROUP_RESPONSIBLE,
            'required' => true,
            'name' => 'CPF',
        ]);

        Field::query()->updateOrCreate([
            'internal' => Field::RESPONSIBLE_RG,
        ], [
            'field_type_id' => Field::TYPE_TEXT,
            'group_type_id' => Field::GROUP_RESPONSIBLE,
            'name' => 'RG',
        ]);

        Field::query()->updateOrCreate([
            'internal' => Field::RESPONSIBLE_GENDER,
        ], [
            'field_type_id' => Field::TYPE_GENDER,
            'group_type_id' => Field::GROUP_RESPONSIBLE,
            'name' => 'Gênero',
        ]);

        Field::query()->updateOrCreate([
            'internal' => Field::RESPONSIBLE_MARITAL_STATUS,
        ], [
            'field_type_id' => Field::TYPE_MARITAL_STATUS,
            'group_type_id' => Field::GROUP_RESPONSIBLE,
            'name' => 'Estado civil',
        ]);

        Field::query()->updateOrCreate([
            'internal' => Field::RESPONSIBLE_PLACE_OF_BIRTH,
        ], [
            'field_type_id' => Field::TYPE_CITY,
            'group_type_id' => Field::GROUP_RESPONSIBLE,
            'name' => 'Naturalidade',
        ]);

        Field::query()->updateOrCreate([
            'internal' => Field::RESPONSIBLE_PHONE,
        ], [
            'field_type_id' => Field::TYPE_PHONE,
            'group_type_id' => Field::GROUP_RESPONSIBLE,
            'required' => true,
            'name' => 'Telefone',
        ]);

        Field::query()->updateOrCreate([
            'internal' => Field::RESPONSIBLE_EMAIL,
        ], [
            'field_type_id' => Field::TYPE_EMAIL,
            'group_type_id' => Field::GROUP_RESPONSIBLE,
            'required' => false,
            'name' => 'E-mail',
        ]);
    }

    private function studentFields()
    {
        Field::query()->updateOrCreate([
            'internal' => Field::STUDENT_NAME,
        ], [
            'field_type_id' => Field::TYPE_TEXT,
            'group_type_id' => Field::GROUP_STUDENT,
            'required' => true,
            'name' => 'Nome',
        ]);

        Field::query()->updateOrCreate([
            'internal' => Field::STUDENT_DATE_OF_BIRTH,
        ], [
            'field_type_id' => Field::TYPE_DATE,
            'group_type_id' => Field::GROUP_STUDENT,
            'required' => true,
            'name' => 'Data de nascimento',
        ]);

        Field::query()->updateOrCreate([
            'internal' => Field::STUDENT_CPF,
        ], [
            'field_type_id' => Field::TYPE_CPF,
            'group_type_id' => Field::GROUP_STUDENT,
            'name' => 'CPF',
        ]);

        Field::query()->updateOrCreate([
            'internal' => Field::STUDENT_RG,
        ], [
            'field_type_id' => Field::TYPE_TEXT,
            'group_type_id' => Field::GROUP_STUDENT,
            'name' => 'RG',
        ]);

        Field::query()->updateOrCreate([
            'internal' => Field::STUDENT_BIRTH_CERTIFICATE,
        ], [
            'field_type_id' => Field::TYPE_BIRTH_CERTIFICATE,
            'group_type_id' => Field::GROUP_STUDENT,
            'name' => 'Certidão de nascimento',
        ]);

        Field::query()->updateOrCreate([
            'internal' => Field::STUDENT_GENDER,
        ], [
            'field_type_id' => Field::TYPE_GENDER,
            'group_type_id' => Field::GROUP_STUDENT,
            'name' => 'Gênero',
        ]);

        Field::query()->updateOrCreate([
            'internal' => Field::STUDENT_MARITAL_STATUS,
        ], [
            'field_type_id' => Field::TYPE_MARITAL_STATUS,
            'group_type_id' => Field::GROUP_STUDENT,
            'name' => 'Estado civil',
        ]);

        Field::query()->updateOrCreate([
            'internal' => Field::STUDENT_PLACE_OF_BIRTH,
        ], [
            'field_type_id' => Field::TYPE_CITY,
            'group_type_id' => Field::GROUP_STUDENT,
            'name' => 'Naturalidade',
        ]);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->responsibleFields();
        $this->studentFields();
    }
}
