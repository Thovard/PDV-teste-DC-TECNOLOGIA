export default function applyMask(element, maskType) {
    let input = (typeof element === 'string') ? document.querySelector(element) : element;
    if (!input) return;

    function formatValue(value, maskType) {
        let val = value.replace(/\D/g, '');
        if (maskType === 'cpf') {
            if (val.length > 11) val = val.substring(0, 11);
            if (val.length > 9) {
                val = val.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
            } else if (val.length > 6) {
                val = val.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
            } else if (val.length > 3) {
                val = val.replace(/(\d{3})(\d{0,3})/, '$1.$2');
            }
        } else if (maskType === 'telefone') {
            if (val.length > 10) {
                val = val.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
            } else {
                val = val.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
            }
        } else if (maskType === 'dinheiro') {
            let numericVal = val === '' ? 0 : parseInt(val, 10);
            let valueNumber = numericVal / 100;
            val = valueNumber.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }
        return val;
    }
    if (input.value) {
        input.value = formatValue(input.value, maskType);
    }

    input.addEventListener('input', (e) => {
        e.target.value = formatValue(e.target.value, maskType);
    });
}
