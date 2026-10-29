/* Состояние partial: input с data-indeterminate становится indeterminate, выбор пользователем снимает его */
document.querySelectorAll('.checkbox__input[data-indeterminate]').forEach(input => {
    input.indeterminate = true;
    input.addEventListener('change', () => {
        input.indeterminate = false;
    });
});
