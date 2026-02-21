<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Models\Process;
use Throwable;

class SaveProcessStages
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

        $stages = [];

        foreach ($args['stages'] as $stage) {
            $stages[] = $process->stages()->updateOrCreate([
                'id' => $stage['id'] ?? 0,
                'process_id' => $id,
            ], $stage);
        }

        $ids = array_column($stages, 'id');

        $process->stages()
            ->where('process_id', $id)
            ->whereNotIn('id', $ids)
            ->delete();

        return $process;
    }
}
