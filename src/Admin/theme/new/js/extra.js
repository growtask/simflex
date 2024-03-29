function editRows() {
        const d = [];
        document.querySelectorAll('.table-data-select .form-control__checkbox:checked').forEach(function (e) {
                    d.push(e.value);
        });

        if (!d.length) {
            return;
        }

    window.location.href = `?action=form&ids=${d.join(',')}`;
}

function copyRows() {
    const d = [];
    document.querySelectorAll('.table-data-select .form-control__checkbox:checked').forEach(function (e) {
        d.push(e.value);
    });

    if (!d.length) {
        return;
    }

    window.location.href = `?action=copy&ids=${d.join(',')}`;
}

function deleteRowsForce() {
    const d = {'rows': []};

    document.querySelectorAll('.table-data-select .form-control__checkbox:checked').forEach(function (e) {
        d['rows'][d['rows'].length] = e.value;
    });

    // const url = new URLSearchParams(d);

    window.location.href = './?ajax=1&action=delete&' + http_build_query(d);
}


function showInfoModal(id, pk) {
    fetch('?action=showDetail&id=' + id).then((res) => {
        res.text().then((data) => {
            document.querySelector('.modal-info__talbe-wrapper').innerHTML = data;
            document.querySelector('.modal-info__btn-edit').href = `?action=form&${pk}=${id}`;
            window.openInfoModal();
            new SimpleBar(document.querySelector('.modal-info__talbe-wrapper'));
        });
    });
}

function http_build_query(formdata, numeric_prefix, arg_separator) {
    //  discuss at: http://phpjs.org/functions/http_build_query/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Legaev Andrey
    // improved by: Michael White (http://getsprink.com)
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Brett Zamir (http://brett-zamir.me)
    //  revised by: stag019
    //    input by: Dreamer
    // bugfixed by: Brett Zamir (http://brett-zamir.me)
    // bugfixed by: MIO_KODUKI (http://mio-koduki.blogspot.com/)
    //        note: If the value is null, key and value are skipped in the http_build_query of PHP while in phpjs they are not.
    //  depends on: urlencode
    //   example 1: http_build_query({foo: 'bar', php: 'hypertext processor', baz: 'boom', cow: 'milk'}, '', '&amp;');
    //   returns 1: 'foo=bar&amp;php=hypertext+processor&amp;baz=boom&amp;cow=milk'
    //   example 2: http_build_query({'php': 'hypertext processor', 0: 'foo', 1: 'bar', 2: 'baz', 3: 'boom', 'cow': 'milk'}, 'myvar_');
    //   returns 2: 'myvar_0=foo&myvar_1=bar&myvar_2=baz&myvar_3=boom&php=hypertext+processor&cow=milk'

    var value, key, tmp = [],
        that = this;

    var _http_build_query_helper = function (key, val, arg_separator) {
        var k, tmp = [];
        if (val === true) {
            val = '1';
        } else if (val === false) {
            val = '0';
        }
        if (val != null) {
            if (typeof val === 'object') {
                for (k in val) {
                    if (val[k] != null) {
                        tmp.push(_http_build_query_helper(key + '[' + k + ']', val[k], arg_separator));
                    }
                }
                return tmp.join(arg_separator);
            } else if (typeof val !== 'function') {
                return encodeURIComponent(key) + '=' + encodeURIComponent(val);
            } else {
                throw new Error('There was an error processing for http_build_query().');
            }
        } else {
            return '';
        }
    };

    if (!arg_separator) {
        arg_separator = '&';
    }
    for (key in formdata) {
        value = formdata[key];
        if (numeric_prefix && !isNaN(key)) {
            key = String(numeric_prefix) + key;
        }
        var query = _http_build_query_helper(key, value, arg_separator);
        if (query !== '') {
            tmp.push(query);
        }
    }

    return tmp.join(arg_separator);
}
