function hide(i) {
    var elementDiv  = document.getElementById('div-'  + i);
    var elementSpan = document.getElementById('span-' + i);
    console.log('div-'  + i);
    if (elementDiv.hasAttribute('hidden')) {
        elementDiv.removeAttribute('hidden');
        elementSpan.innerHTML = '(hide)';
    } else {
        elementDiv.setAttribute('hidden', 'hidden');
        elementSpan.innerHTML = '(show)';
    }
}

function merge(from, to) {
    console.log(from, to);
}