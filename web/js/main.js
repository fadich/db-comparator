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

function join(from, to, id) {
    var data = {
        from: from,
        to: to,
        params: window.location.search
    };
    var elem = document.getElementById(id);
    switchAttr('disabled', elem);
    $.ajax({
        url: "/main/join",
        type: "POST",
        data: data,
        statusCode: {
            200: function () {
                // location.reload();
            },
            400: function () {
                joinRequestErrors();
            }
        },
        complete: function () {
            switchAttr('disabled', elem);
        }
    });
}

function joinRequestErrors() {
    alert('Error!');
}

function switchAttr(attibute, element) {
    if (element.hasAttribute(attibute)) {
        element.removeAttribute(attibute);
    } else {
        element.setAttribute(attibute, attibute);
    }
}