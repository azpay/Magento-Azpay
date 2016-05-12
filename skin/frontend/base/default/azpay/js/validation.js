
Validation.creditCartTypes = $H({
    'elo': [new RegExp('^(40117(8|9)|431274|438935|636297|451416|45763(1|2)|504175|5067((17)|(18)|(22)|(25)|(26)|(27)|(28)|(29)|(30)|(33)|(39)|(40)|(41)|(42)|(44)|(45)|(46)|(47)|(48))|627780|636297|636368)[0-9]{10}$'), new RegExp('^([0-9]{3})?$'), false, new RegExp('^(40117(8|9)|431274|438935|636297|451416|45763(1|2)|504175|5067((17)|(18)|(22)|(25)|(26)|(27)|(28)|(29)|(30)|(33)|(39)|(40)|(41)|(42)|(44)|(45)|(46)|(47)|(48))|627780|636297|636368)')],
    'hipercard': [new RegExp('^(606282[0-9]{10}([0-9]{3})?)|(3841[0-9]{15})$'), new RegExp('^[0-9]{3}$'), false, new RegExp('^(606282|3841)')],
    'discover': [new RegExp('^6011[0-9]{12}$'), new RegExp('^[0-9]{3}$'), true, new RegExp('^6011')],
    'dinners': [new RegExp('^3(?:0[0-5]|[68][0-9])[0-9]{11}$'), new RegExp('^[0-9]{3}$'), true, new RegExp('^3(?:0[0-5]|[68][0-9])')],
    'jcb': [new RegExp('^35[0-9]{14}$'), new RegExp('^[0-9]{3,4}$'), true, new RegExp('^35')],
    'aura': [new RegExp('^50[0-9]{17}$'), new RegExp('^[0-9]{3}$'), false, new RegExp('^50')],
    'amex': [new RegExp('^3[47][0-9]{13}$'), new RegExp('^[0-9]{4}$'), true, new RegExp('^3[47]')],
    'visa': [new RegExp('^4[0-9]{12}([0-9]{3})?$'), new RegExp('^[0-9]{3}$'), true, new RegExp('^4')],
    'mastercard': [new RegExp('^5[1-5][0-9]{14}$'), new RegExp('^[0-9]{3}$'), true, new RegExp('^5[1-5]')]
});

function mascara(o, f) {
    v_obj = o
    v_fun = f
    setTimeout("execmascara()", 1)
}
function execmascara() {
    v_obj.value = v_fun(v_obj.value)
}

function sonumeros(v) {
    v = v.replace(/\D/g, "");
    return v;
}
