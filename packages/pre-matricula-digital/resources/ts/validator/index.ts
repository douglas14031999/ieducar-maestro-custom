import { configure, defineRule } from 'vee-validate';
import {
  isBefore,
  isDateAfter,
  isValidBornDate,
  isValidDate,
  isValidTime,
} from '@/datetime';
import { localize, setLocale } from '@vee-validate/i18n';
import { Indexable } from '@/types';
import cpf from '@/rules/cpf';
import rg from '@/rules/rg';
import rules from '@vee-validate/rules';

// função importante na montagem das regras do vee-validate, importada da mesma
function isNullOrUndefined(value: string[] | string): boolean {
  return value === null || value === undefined;
}

// função importante na montagem das regras do vee-validate, importada da mesma
function getSingleParam(params: Indexable, paramName: string) {
  return Array.isArray(params) ? params[0] : params[paramName];
}

const pt = {
  code: 'pt',
  messages: {
    alpha: 'O campo deve conter somente letras',
    alpha_dash: 'O campo deve conter letras, números e traços',
    alpha_num: 'O campo deve conter somente letras e números',
    alpha_spaces: 'O campo só pode conter caracteres alfabéticos e espaços',
    between: 'O campo deve estar entre 0:{min} e 1:{max}',
    confirmed: 'A confirmação do campo deve ser igual',
    digits: 'O campo deve ser numérico e ter exatamente 0:{length} dígitos',
    dimensions:
      'O campo deve ter 0:{width} pixels de largura por 1:{height} pixels de altura',
    email: 'O campo deve ser um email válido',
    excluded: 'O campo deve ser um valor válido',
    ext: 'O campo deve ser um arquivo válido',
    image: 'O campo deve ser uma imagem',
    integer: 'O campo deve ser um número inteiro',
    is: 'O valor inserido no campo não é válido',
    one_of: 'O campo deve ter um valor válido',
    length: 'O tamanho do campo deve ser 0:{length}',
    max: 'O campo não deve ter mais que 0:{length} caracteres',
    max_value: 'O campo precisa ser 0:{max} ou menor',
    mimes: 'O campo deve ser um tipo de arquivo válido',
    min: 'O campo deve conter pelo menos 0:{length} caracteres',
    min_value: 'O campo precisa ser 0:{min} ou maior',
    numeric: 'O campo deve conter apenas números',
    regex: 'O campo possui um formato inválido',
    required: 'O campo é obrigatório',
    required_if: 'O campo é obrigatório',
    size: 'O campo deve ser menor que 0:{size}KB',
  },
};

configure({
  generateMessage: localize({
    pt,
  }),
});

setLocale('pt');

Object.keys(rules).forEach((rule) => {
  // Não utiliza a regra de validação 'length', pois a mesma é customizada abaixo.
  if (rule !== 'length') {
    defineRule(rule, rules[rule]);
  }
});

// Função adaptada da própria biblioteca Vee-validate
defineRule('length', (value: string, params: Indexable) => {
  let val: string[] | string = value;
  // Normalize the length value
  const length = getSingleParam(params, 'length');
  // Esta condição é importante, pois se o campo não for obrigatório, o validador
  // deve deixar passar o campo vazio.
  if (isNullOrUndefined(val) || val.length === 0) {
    return true;
  }
  if (typeof val === 'number') {
    val = String(val);
  }
  if (!val.length) {
    val = Array.from(val);
  }

  return val.length === Number(length);
});

defineRule('date', (value: string) => {
  if (value && value.length > 0 && !isValidDate(value)) {
    return 'O campo deve ser uma data';
  }
  return true;
});

defineRule('born_date', (value: string) => {
  if (value && value.length > 0 && !isValidBornDate(value)) {
    return 'O campo deve ser uma data de nascimento válida';
  }
  return true;
});

defineRule('date_after', (value: string, target: string) => {
  if (value && value.length > 0 && !isDateAfter(value, target)) {
    return 'A data final deve ser posterior a data inicial';
  }
  return true;
});

defineRule('minimum_age', (value: string, target: number) => {
  if (value && value.length > 0 && !isBefore(value, target)) {
    return 'O aluno(a) não possui idade mínima';
  }
  return true;
});

defineRule('hour', (value: string) => {
  if (value && value.length > 0 && !isValidTime(value)) {
    return 'O campo deve ser uma hora';
  }
  return true;
});

defineRule('cpf', (value = '') => {
  if (
    value &&
    value.length > 0 &&
    !(/^\d{3}\.\d{3}\.\d{3}-\d{2}$/.test(value) && cpf(value))
  ) {
    return 'O campo deve ser um CPF';
  }
  return true;
});

defineRule('rg', (value = '') => {
  if (value && value.length > 0 && rg(value)) {
    return 'O campo deve ser um RG';
  }
  return true;
});

defineRule('postal_code', (value: string) => {
  if (value && value.length > 0 && !/^\d{5}-\d{3}$/.test(value)) {
    return 'O campo deve ser um CEP';
  }
  return true;
});

defineRule('phone', (value: string) => {
  if (value && value.length > 0 && !/^\(\d{2}\) \d{4,5}-\d{4}$/.test(value)) {
    return 'O campo deve ser um telefone';
  }
  return true;
});
