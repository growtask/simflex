/**
 * Счётчик символов многострочного поля: обновляется по вводу, значение «введено/предел»
 */
class Textarea {
    constructor(element) {
        this.field = element.querySelector('.textarea__field');
        this.counter = element.querySelector('.textarea__counter');

        if (!this.field || !this.counter) {
            return;
        }

        this.field.addEventListener('input', () => this.updateCounter());
    }

    updateCounter() {
        const max = parseInt(this.field.getAttribute('maxlength'), 10);
        const length = this.field.value.length;
        this.counter.textContent = max > 0 ? `${length}/${max}` : String(length);
    }
}

document.querySelectorAll('.textarea').forEach(element => {
    new Textarea(element);
});
