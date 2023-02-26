const cpf = document.querySelector('#cpf')

cpf.addEventListener('keypress', () => {
    let cpfLength = cpf.value.length

    if (cpfLength == 3 || cpfLength == 7) {
        cpf.value += '.'
    } else if (cpfLength == 11) {
        cpf.value += '-'
    }
})