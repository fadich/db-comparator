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

function join(from, to) {
    // console.log(from, to);

    var data = {
        from: from,
        to: to,
        params: window.location.search
    };
    $.ajax({
        url: "/main/join",
        type: "POST",
        data: data,
        success: function () {
            location.reload();
        },
        error: function() {
            joinRequestErrors();
        },
        complete: function () {
        }
    });
}

function joinRequestErrors() {

}