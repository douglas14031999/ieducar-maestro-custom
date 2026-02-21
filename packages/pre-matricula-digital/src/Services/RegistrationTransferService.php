<?php

namespace iEducar\Packages\PreMatricula\Services;

use App\Models\LegacyActiveLooking;
use App\Models\LegacyDisciplineScoreAverage;
use App\Models\LegacyInstitution;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolGradeDiscipline;
use App\Models\LegacySchoolHistory;
use App\Models\LegacySchoolHistoryDiscipline;
use App\Models\LegacyTransferRequest;
use App\Models\LegacyTransferType;
use App_Model_MatriculaSituacao;
use Carbon\Carbon;
use Exception;
use iEducar\Modules\School\Model\ActiveLooking;
use iEducar\Packages\PreMatricula\Events\PreRegistrationTransferEvent;
use iEducar\Packages\PreMatricula\Exceptions\TransferValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Serviço responsável por gerenciar a transferência de matrículas entre escolas
 */
class RegistrationTransferService
{
    private LegacyRegistration $registration;

    private int $schoolDestinyId;

    private LegacyInstitution $institution;

    private Carbon $date;

    private int $userId;

    /**
     * Inicializa os dados da instituição
     */
    private function initializeInstitution(): void
    {
        $this->institution = LegacyInstitution::query()->find(
            $this->registration->school->ref_cod_instituicao,
            [
                'cod_instituicao',
                'data_base_transferencia',
                'gerar_historico_transferencia',
            ]
        );
    }

    /**
     * Executa o processo completo de transferência
     *
     * @throws Exception
     */
    public function transfer(LegacyRegistration $registration, int $schoolDestinyId): void
    {
        $this->registration = $registration;
        $this->schoolDestinyId = $schoolDestinyId;
        $this->date = Carbon::today();
        $this->userId = Auth::id();
        $this->initializeInstitution();

        DB::beginTransaction();
        try {
            $this->createTransferSolicitation();
            $this->transferRegistration();
            $this->createHistoricTransfer();
            $this->updateRegistrationScoreSituation();
            $this->updateActiveLooking();
            DB::commit();
        } catch (TransferValidationException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cria ou atualiza a descrição do tipo de transferência
     */
    private function createTransferDescription(): LegacyTransferType
    {
        return LegacyTransferType::query()
            ->updateOrCreate(
                [
                    'nm_tipo' => config('prematricula.features.transfer_description', 'Transferência Pré-Matrícula Digital'),
                    'ref_cod_instituicao' => $this->institution->getKey(),
                ],
                [
                    'ref_usuario_cad' => $this->userId,
                    'ativo' => 1,
                    'data_exclusao' => null,
                    'ref_usuario_exc' => null,
                ]
            );
    }

    /**
     * Cria o histórico de transferência se configurado na instituição
     */
    private function createHistoricTransfer(): void
    {
        if (!$this->canCreateHistoricTransfer()) {
            return;
        }

        $schoolHistory = $this->createSchoolHistory();
        $this->createHistoricDisciplines($schoolHistory);
    }

    /**
     * Cria o histórico escolar
     */
    private function createSchoolHistory(): LegacySchoolHistory
    {
        $course = $this->registration->course->nm_curso;

        $sequential = LegacySchoolHistory::query()
            ->selectRaw('(COALESCE(MAX(sequencial),0) + 1) as sequential ')
            ->where('ref_cod_aluno', $this->registration->ref_cod_aluno)
            ->value('sequential');

        return LegacySchoolHistory::create([
            'sequencial' => $sequential,
            'ref_cod_aluno' => $this->registration->ref_cod_aluno,
            'ref_usuario_cad' => $this->userId,
            'nm_serie' => $this->registration->grade->nm_serie,
            'ano' => $this->registration->ano,
            'carga_horaria' => $this->registration->grade->carga_horaria,
            'escola' => $this->registration->school->data->nome,
            'escola_cidade' => $this->registration->school->data->municipio,
            'escola_uf' => $this->registration->school->data->uf_municipio,
            'aprovado' => App_Model_MatriculaSituacao::TRANSFERIDO,
            'ref_cod_instituicao' => $this->institution->getKey(),
            'ativo' => 1,
            'ref_cod_matricula' => $this->registration->getKey(),
            'nm_curso' => $course,
            'historico_grade_curso_id' => str_contains($course, '8') ? 1 : 2,
            'ref_cod_escola' => $this->registration->school->getKey(),
        ]);
    }

    /**
     * Cria as disciplinas do histórico
     */
    private function createHistoricDisciplines(LegacySchoolHistory $schoolHistory): void
    {
        $disciplines = $this->getDisciplines();

        foreach ($disciplines as $index => $disciplineName) {
            LegacySchoolHistoryDiscipline::create([
                'sequencial' => $index + 1,
                'ref_ref_cod_aluno' => $schoolHistory->ref_cod_aluno,
                'ref_sequencial' => $schoolHistory->sequencial,
                'nm_disciplina' => $disciplineName,
                'nota' => '',
            ]);
        }
    }

    /**
     * Obtém as disciplinas da série
     */
    private function getDisciplines()
    {
        return LegacySchoolGradeDiscipline::query()
            ->selectRaw("((translate(upper(cc.nome),'áéíóúýàèìòùãõâêîôûäëïöüÿçÁÉÍÓÚÝÀÈÌÒÙÃÕÂÊÎÔÛÄËÏÖÜÇ','AEIOUYAEIOUAOAEIOUAEIOUYCAEIOUYAEIOUAOAEIOUAEIOUC'))) as translate")
            ->join('modules.componente_curricular as cc', 'escola_serie_disciplina.ref_cod_disciplina', 'cc.id')
            ->whereGrade($this->registration->ref_ref_cod_serie)
            ->whereSchool($this->registration->ref_ref_cod_escola)
            ->pluck('translate');
    }

    /**
     * Verifica se deve criar histórico de transferência baseado na configuração da instituição
     */
    private function canCreateHistoricTransfer(): bool
    {
        return (bool) $this->institution->gerar_historico_transferencia;
    }

    /**
     * Atualiza o status da matrícula e seus vínculos para transferido
     */
    private function transferRegistration(): void
    {
        $this->updateRegistrationStatus();
        $this->updateEnrollmentsStatus();
    }

    /**
     * Atualiza o status da matrícula
     */
    private function updateRegistrationStatus(): void
    {
        $this->registration->data_cancel = $this->date;
        $this->registration->aprovado = App_Model_MatriculaSituacao::TRANSFERIDO;
        $this->registration->ref_usuario_exc = $this->userId;
        $this->registration->save();
    }

    /**
     * Atualiza o status dos vínculos
     */
    private function updateEnrollmentsStatus(): void
    {
        foreach ($this->registration->activeEnrollments as $enrollment) {
            $enrollment->transferido = true;
            $enrollment->remanejado = false;
            $enrollment->abandono = false;
            $enrollment->reclassificado = false;
            $enrollment->falecido = false;
            $enrollment->ref_usuario_exc = $this->userId;
            $enrollment->ativo = 0;
            $enrollment->data_exclusao = $this->date;
            $enrollment->save();
        }
    }

    /**
     * Atualiza o status da busca ativa para transferido
     */
    private function updateActiveLooking(): void
    {
        LegacyActiveLooking::query()
            ->where('ref_cod_matricula', $this->registration->getKey())
            ->where('resultado_busca_ativa', ActiveLooking::ACTIVE_LOOKING_IN_PROGRESS_RESULT)
            ->update([
                'resultado_busca_ativa' => ActiveLooking::ACTIVE_LOOKING_TRANSFER_RESULT,
                'data_fim' => $this->date,
            ]);
    }

    /**
     * Cria a solicitação de transferência
     *
     * @throws TransferValidationException Quando já existe uma solicitação de transferência ativa
     */
    private function createTransferSolicitation(): void
    {
        if ($this->hasActiveTransferRequest()) {
            throw TransferValidationException::activeTransferRequest($this->registration);
        }

        $transferRequest = $this->createTransferRequest();
        event(new PreRegistrationTransferEvent(transfer: $transferRequest));
    }

    /**
     * Verifica se existe solicitação de transferência ativa
     */
    private function hasActiveTransferRequest(): bool
    {
        return LegacyTransferRequest::query()
            ->where('ref_cod_matricula_saida', $this->registration->getKey())
            ->where('ativo', 1)
            ->exists();
    }

    /**
     * Cria a solicitação de transferência
     */
    private function createTransferRequest(): LegacyTransferRequest
    {
        $transferType = $this->createTransferDescription();

        return LegacyTransferRequest::create([
            'ref_cod_transferencia_tipo' => $transferType->getKey(),
            'ref_usuario_cad' => $this->userId,
            'ref_cod_matricula_saida' => $this->registration->getKey(),
            'ativo' => 1,
            'observacao' => '',
            'data_transferencia' => $this->date,
            'ref_cod_escola_destino' => $this->schoolDestinyId,
        ]);
    }

    /**
     * Atualiza a situação das notas da matrícula para transferido
     */
    private function updateRegistrationScoreSituation(): void
    {
        LegacyDisciplineScoreAverage::query()
            ->whereHas('registrationScore', fn ($q) => $q->where('matricula_id', $this->registration->getKey()))
            ->update([
                'situacao' => App_Model_MatriculaSituacao::TRANSFERIDO,
            ]);
    }
}
