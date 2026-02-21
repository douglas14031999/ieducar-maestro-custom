<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Queries;

use iEducar\Packages\PreMatricula\Models\Timeline;
use iEducar\Packages\PreMatricula\Services\TimelineService;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class GetTimelines
{
    private TimelineService $timelineService;

    public function __construct(TimelineService $timelineService)
    {
        $this->timelineService = $timelineService;
    }

    /**
     * Retorna as timelines de um modelo específico.
     *
     * @param  mixed  $rootValue
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context)
    {
        return Timeline::where('model_id', $args['model_id'])
            ->where('model_type', $args['model_type'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($timeline) {
                $data = $timeline->toArray();
                if (isset($data['payload'])) {
                    $data['payload'] = $this->timelineService->translatePayload($data['payload']);
                }

                return $data;
            });
    }
}
