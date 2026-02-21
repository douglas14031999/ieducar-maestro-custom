<?php

namespace iEducar\Packages\PreMatricula\Listeners;

use App\Models\LegacySchool;
use App\Models\LegacyTransferRequest;
use App\Models\NotificationType;
use App\Process;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class PreRegistrationTransferNotificationListener
{
    /**
     * @var NotificationService
     */
    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        /** @var LegacyTransferRequest $transfer */
        $transfer = $event->transfer;
        $registration = $transfer->oldRegistration;
        $destinySchoolName = LegacySchool::find($transfer->ref_cod_escola_destino, ['id'])->data->name;

        $segments = [
            $registration->student->person->name,
            $registration->school->name,
            $registration->grade->name,
            $registration->lastEnrollment?->schoolClass->name,
            $registration->ano,
        ];

        $segments = array_filter($segments, fn ($value) => $value !== null && $value !== '');

        $info = implode(', ', $segments);

        $message = "O(a) aluno(a) {$info} foi transferido(a) para a unidade {$destinySchoolName} através do Pré-Matrícula Digital.";

        $link = '/intranet/educar_matricula_det.php?cod_matricula=' . $registration->getKey();

        $originSchoolUsers = collect($this->getUsers(Process::NOTIFY_TRANSFER, $registration->school->getKey()));
        $destinySchoolUsers = collect($this->getUsers(Process::NOTIFY_TRANSFER, $transfer->ref_cod_escola_destino));

        $allUsers = $originSchoolUsers->merge($destinySchoolUsers)
            ->pluck('cod_usuario')
            ->unique()
            ->values();

        foreach ($allUsers as $userId) {
            $this->service->createByUser($userId, $message, $link, NotificationType::TRANSFER);
        }
    }

    public function getUsers($process, $school)
    {
        return DB::select('
            SELECT cod_usuario
              FROM pmieducar.usuario u
              JOIN pmieducar.menu_tipo_usuario mtu ON mtu.ref_cod_tipo_usuario = u.ref_cod_tipo_usuario
              JOIN pmieducar.tipo_usuario tu ON tu.cod_tipo_usuario = u.ref_cod_tipo_usuario
              JOIN public.menus m ON m.id = mtu.menu_id
         LEFT JOIN pmieducar.escola_usuario eu ON eu.ref_cod_usuario = u.cod_usuario
             WHERE m.process = :process
               AND u.ativo = 1
               AND (eu.ref_cod_escola = :school OR tu.nivel <= 2) --INSTITUCIONAL
        ', [
            $process,
            $school,
        ]);
    }
}
