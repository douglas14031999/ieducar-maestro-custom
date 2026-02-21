<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Models\Process;
use Throwable;

class SaveProcessFields
{
    /**
     * @return Process
     *
     * @throws Throwable
     */
    private function findProcess(int $id)
    {
        /** @var Process $process */
        $process = Process::findOrFail($id);

        return $process;
    }

    /**
     * @param  mixed  $_
     * @return Process
     *
     * @throws Throwable
     */
    public function __invoke($_, array $args)
    {
        $process = $this->findProcess(
            $id = $args['id']
        );

        $fields = $args['fields'];

        foreach ($fields as $field) {
            $process->fields()->updateOrCreate([
                'process_id' => $id,
                'field_id' => $field['field_id'],
            ], $field);
        }

        $ids = array_column($fields, 'field_id');

        $process->fields()
            ->where('process_id', $id)
            ->whereNotIn('field_id', $ids)
            ->delete();

        return $process;
    }
}
