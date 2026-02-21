import { ComputedRef, computed, ref } from 'vue';
import { getYear, isDateBetween } from '@/datetime';
import { Option } from '@/types';
import { PreRegistrationStageProcess } from '../modules/preregistration/types';
import { PreRegistrationStageProcessGrade } from '@/modules/preregistration/types';

export function useStudentProcessAndSuggestGrades(
  process: ComputedRef<PreRegistrationStageProcess>
) {
  const suggestedGrades = ref<PreRegistrationStageProcessGrade[]>([]);

  const grades = computed<Option[]>(() => {
    let grades = process.value.grades
      .map((g) => ({ key: g.id, label: g.name }))
      .sortBy('label');

    if (
      (suggestedGrades.value.length > 0 &&
        process.value.forceSuggestedGrade === true) ||
      process.value.blockIncompatibleAgeGroup === true
    ) {
      grades = grades.filter((grade) =>
        suggestedGrades.value.find(
          (suggestedGrade) => suggestedGrade.id === grade.key
        )
      );
    }

    if (
      process.value.blockIncompatibleAgeGroup &&
      suggestedGrades.value.length <= 0
    ) {
      grades = [];
    }

    return grades as Option[];
  });

  const suggestGrade = (date: string) => {
    let grades = process.value?.grades
      .filter((g) => g.startBirth && g.endBirth)
      .filter((g) => isDateBetween(date, g.startBirth, g.endBirth));

    /**
     * De acordo com a issue-2149, haverão casos em que o aluno nasce no mesmo ano em que a série é oferecida.
     *
     * Exemplo: Aluno nasce em 01/06/2019 e a série é oferecida para matrículas até 01/04/2019.
     *
     * Nesse caso, o aluno poderá ser matriculado na série, mesmo que a data de nascimento seja posterior ao início do ano letivo.
     *
     * Para isso, foi adicionado a verificação de que se o aluno nasceu posteriormente a data de fim do processo, porém, que nasceu no mesmo ano do processo, poderá se matricular na série daquele ano.
     */
    if (process.value.blockIncompatibleAgeGroup && grades.length == 0) {
      grades = process.value.grades
        .filter((g) => g.startBirth && g.endBirth)
        .filter(
          (g) =>
            (isDateBetween(date, g.startBirth, g.endBirth) ||
              getYear(date) === getYear(g.endBirth)) &&
            getYear(date) === process.value.schoolYear.year
        );
    }

    if (grades) {
      suggestedGrades.value = grades;
    }
  };

  const suggestedGradesMessage = computed(() => {
    let defaultText = 'Série(s) sugerida(s) conforme a idade: ';
    if (suggestedGrades.value && process.value.forceSuggestedGrade === true) {
      defaultText =
        'Série(s) identificada(s) como correspondente(s) com a faixa etária do(a) aluno(a): ';
    }
    return defaultText + suggestedGrades.value.map((g) => g.name).join(', ');
  });

  return {
    grades,
    suggestedGrades,
    suggestGrade,
    suggestedGradesMessage,
  };
}
