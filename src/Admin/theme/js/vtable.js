const VTableEditor = {
    loaders: {},
    currentPage: {},

    // -------- PUBLIC --------
    init: function (id) {
        const val = this._loadValue(id);

        this.currentPage[id] = 0;
        this.loaders[id] = new Loader(`div[data-id="${id}"] table`);

        // setup columns
        for (const col of val.s) {
            if (col.n.startsWith('__')) continue;
            this._addVisualColumn(id, col.l);
        }

        this._addVisualColumn(id, 'Действия');

        // setup rows
        let i = 0;
        for (const row of val.v) {
            this._addVisualRow(id, Number(row.__id) ?? i++, row, val.s);
        }


        const pagination = this._getPaginationElement(id);
        if (pagination)
            pagination.addEventListener('click', (ev) => {
                if (ev.target.classList.contains('table-pagination')) {
                    this.currentPage[id] = parseInt(ev.target.innerHTML) - 1
                    this._loadData(id, this.currentPage[id]);
                } else if (ev.target.classList.contains('table__pagination-btns-mobile-prev')) {
                    this.currentPage[id] = parseInt(ev.target.dataset.page);
                    this._loadData(id, this.currentPage[id]);
                } else if (ev.target.classList.contains('table__pagination-btns-mobile-next')) {
                    this.currentPage[id] = parseInt(ev.target.dataset.page);
                    this._loadData(id, this.currentPage[id]);
                }
            });

    },

    onRowEdit: function (id, r) {
        // compose input crap
        const val = this._loadValue(id);
        let input = '';

        const cmbs = [];
        const imgs = [];

        for (const col of val.s) {
            if (col.v == '__NO_EDIT__') continue;

            const cv = r != null ? (this._findById(val.v, r)[col.n] ?? col.v) : col.v;
            if (col.t.startsWith('editor')) {
                input += `
                <label class="modal-point__data-label">
                    ${col.l}
                    <div class="form-control form-control--sm">
                    <textarea class="form-control__textarea" name="${col.n}" id="te-${col.n}" placeholder="${col.l}" rows="5">${cv}</textarea>
                    </div>
                </label>`;
            } else if (col.t.startsWith('image')) {
                input += `
<div class="modal-point__data-label">
                    ${col.l}
<label class="form-control">
                                    <div class="form-control__file" id="img-${col.n}">
                                        <img src="${cv}" onerror="this.src = '/vendor/glushkovds/simflex/src/Admin/theme/new/img/default-img.png'" alt="" class="form-control__file-img">
                                        <div class="form-control__file-area-wrapper drop-area">
                                            <div class="form-control__file-area">
                                                <input type="file" name="" accept="image/*"
                                                    class="form-control__file-input">
                                                    <input type="hidden" data-path="/content/" name="${col.n}" value="${cv}" />
                                                <div class="form-control__file-progressbar progressbar">
                                                    <div class="progressbar__bg">
                                                        <div class="progressbar__progress"></div>
                                                    </div>
                                                </div>
                                                <div class="form-control__file-head">
                                                    <div class="form-control__file-title">Перетащите или загрузите файл
                                                    </div>
                                                    <div class="form-control__file-size"></div>
                                                </div>
                                                <div class="form-control__file-btns">
                                                    <div
                                                        class="form-control__file-btn btn-file-upload BtnPrimarySm BtnIconLeft">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M21 15V16.2C21 17.8802 21 18.7202 20.673 19.362C20.3854 19.9265 19.9265 20.3854 19.362 20.673C18.7202 21 17.8802 21 16.2 21H7.8C6.11984 21 5.27976 21 4.63803 20.673C4.07354 20.3854 3.6146 19.9265 3.32698 19.362C3 18.7202 3 17.8802 3 16.2V15M17 8L12 3M12 3L7 8M12 3V15"
                                                                stroke="white" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                        Загрузить
                                                    </div>
                                                    <button type="button"
                                                        class="form-control__file-btn btn-file-reset BtnSecondarySm BtnIconLeft">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path style="fill: #ffffff !important" fill-rule="evenodd"
                                                                clip-rule="evenodd"
                                                                d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                                                                fill="#ffffff" />
                                                        </svg>
                                                        Удалить
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label></div>
                `;
                imgs.push(col.n);
            } else if (col.t.startsWith('file')) {
                input += `
<label class="modal-point__data-label">
    ${col.l}
    <p id="te-${col.n}-file">${cv}</p>
    <input type="file" data-val="${cv}" class="form-control" name="${col.n}" id="te-${col.n}" onchange="VTableEditor.onUpdateFile('${id}', '${col.n}', this)">
</label>
                `;
            } else if (col.t.startsWith('combo')) {
                const aj = col.e.startsWith('AJAX:');

                let extra = '';
                if (aj) {
                    extra = `data-action="searchProducts" data-ajax="true"`;
                }

                input += `<div class="data-point">
    ${col.l}
<div class="form-control form-control--sm">
                        <div id="cb-${col.n}" class="form-control__dropdown" ${extra}>
                            <div class="form-control__dropdown-top">
                                <input class="form-control__dropdown-input" onchange="${col.c ?? ''}" value="${cv}" id="te-${col.n}" type="hidden" name="${col.n}" >
                                <input placeholder="Начните вводить название..." class="form-control__dropdown-text" type="text">
                                <div class="form-control__dropdown-current">—</div>
                                <button class="form-control__dropdown-toggle" type="button">
                                    <svg viewBox="0 0 24 24">
                                        <use xlink:href="/vendor/glushkovds/simflex/src/Admin/theme/new/img/icons/svg-defs.svg#chevron-mini"></use>
                                    </svg>
                                </button>
                            </div>
                            <div class="form-control__dropdown-list">`;

                if (!aj) {
                    const params = col.e.split(',,');
                    for (const param of params) {
                        const par = param.split('=');
                        input += ` <div data-value="${par[0]}" class="form-control__dropdown-item">${par[1]}</div>`;
                    }
                }
                input += '</div></div></div>';
                input += '</div>';

                cmbs.push(col.n);
            } else {
                input += `
<label class="data-point">
    ${col.l}
    <div class="form-control form-control--sm">
        <input type="${this._typeToHtml(col.t)}" class="form-control__input" name="${col.n}" id="te-${col.n}" value="${cv}" placeholder="${col.l}">
    </div>
</label>
            `;
            }
        }

        // output
        this._openModal(`
   
    <form data-teid="${id}"   class="modal-point__data"  onsubmit="return false;">
        ${input}
    </form>
    <div class="modal-point__btns">
            <button type="button" class="BtnPrimarySm BtnIconLeft" onclick="VTableEditor.onRowSave('${id}', ${r});return false">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 12L10 17L19 8" stroke="#ffffff" stroke-width="2" stroke-linecap="round"
                          stroke-linejoin="round" />
                </svg>

                Сохранить
            </button>
            <button type="button" class="BtnOutlineMonoSm BtnIconLeftOutline" onclick="window.closePointModal()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                          fill="#0D0D0D" />
                </svg>

                Отмена
            </button>
        </div>
`);

        for (const c of cmbs) {
            window.initializeDropdown(`cb-${c}`);
        }

        for (const c of imgs) {
            window.initializeFileInput(`img-${c}`);
        }
    },
    onRowSave: function (id, r) {
        const val = this._loadValue(id);

        const form = document.querySelector(`form[data-teid="${id}"]`);
        const data = new FormData(form);

        let action = 'vtAdd';

        if (r) {
            data.append('id', r);
            action = 'vtEdit';
        }

        $.ajax({
            url: this._getPath(id) + `?action=${action}&${this._makePk(id)}&id=${data.get('product')}`,
            type: 'POST',
            processData: false,
            contentType: false,
            data: data,
            success: () => {
                window.closePointModal();
                this._loadData(id, this.currentPage[id]);
            }
        });
    },

    onRowDeleteRequest: function (id, r) {
        const mdl = document.querySelector('.modal-delete__btns');
        mdl.innerHTML = `
        <button class="BtnSecondarySm" onclick="VTableEditor.onRowDelete('${id}', ${r})">Удалить</button>
            <button class="BtnOutlineMonoSm" onclick="window.closeDeleteModal()">Отмена</button>
        `;

        window.openDeleteModal();
    },

    onRowDelete: function (id, r) {
        $.ajax({
            url: this._getPath(id) + `?action=vtDelete&id=${r}`,
            success: () => {
                const val = this._loadValue(id);
                const newV = [];
                let i = 0;
                for (let row of val.v) {
                    if ((row.__id ?? i++) == r) {
                        row.__deleted = '1';
                    }

                    newV.push(row);
                }
                val.v = newV;
                this._storeValue(id, val);
                document.querySelector(`tr[data-r="${r};${id}"]`).remove();

                // update ids
                this._getHeaderElement(id).innerHTML = '';
                this._getElementsElement(id).innerHTML = '';

                for (const col of val.s) {
                    if (col.n.startsWith('__')) continue;
                    this._addVisualColumn(id, col.l);
                }

                this._addVisualColumn(id, 'Действия');

                i = 0;
                for (const row of val.v) {
                    this._addVisualRow(id, row.__id ?? i++, row, val.s);
                }

                window.closeDeleteModal();
            }
        });
    },
    onUpdateImage: function (id, n, i) {
        if (!i.files[0]) {
            return;
        }

        document.querySelector(`#te-${n}-img`).innerHTML = '<b>Загружаю...</b>';

        let fd = new FormData();
        fd.append('file', i.files[0]);
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (r) {
                document.querySelector(`#te-${n}-img`).src = r;
                i.dataset.val = r;
            }
        });
    },
    onUpdateFile: function (id, n, i) {
        if (!i.files[0]) {
            return;
        }

        document.querySelector(`#te-${n}-file`).innerHTML = '<b>Загружаю...</b>';

        let fd = new FormData();
        fd.append('file', i.files[0]);
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (r) {
                document.querySelector(`#te-${n}-file`).innerHTML = r;
                i.dataset.val = r;
            }
        });
    },

    // -------- PRIVATE --------
    _openModal: function (content) {
        const mdl = document.querySelector('.modal-point__inner .modal-point__data');
        mdl.innerHTML = content;

        window.openPointModal();
    },

    _typeToHtml: function (type) {
        switch (type) {
            case 'int':
                return 'number';
        }

        return 'text';
    },

    _storeValue: function (id, val) {
        this._getInputElement(id).value = JSON.stringify(val);
    },
    _loadValue: function (id) {
        return JSON.parse(this._getInputElement(id).value);
    },
    _getPath: function (id) {
        return this._getInputElement(id).dataset.path;
    },
    _makePk: function (id) {
        const el = this._getInputElement(id);
        return `${el.dataset.pk}=${el.dataset.pkid}`;
    },
    _isValAnImage: function (id, col) {
        return col.t.startsWith('image');
    },
    _isValAFile: function (id, n) {
        const v = this._loadValue(id);
        for (const col of v.s) {
            if (col.n === n) {
                return col.t.startsWith('file');
            }
        }

        return false;
    },

    _findById: function (v, r) {
        for (const i of v) {
            if (i.__id == r) {
                return i;
            }
        }

        return null;
    },

    _addVisualColumn: function (id, name) {
        const el = document.createElement('th');
        el.classList.add('table__head-item');
        el.innerText = name;

        this._getHeaderElement(id).appendChild(el);
    },
    _addVisualRow: function (id, r, val, s) {
        let valdata = '';
        for (const k of s) {
            if (k.n.startsWith('__')) continue;
            if (!this._isValAnImage(id, k)) {
                valdata += `<td class="table__body-item"><div class="table__row-text">${val[k.n]}</div></td>`;
            } else {
                valdata += `<td class="table__body-item"><div class="table__body-item-img"><img src="${val[k.n]}" /></div></td>`;
            }
        }

        let disabled = ``;
        if (val.__deleted == '1') {
            disabled = `table__body-row--disabled`;
        }

        let btns = `<div class="table__body-item-btns">
                <div class="tooltip" onclick="VTableEditor.onRowEdit('${id}', ${r}); return false" data-tip="Редактировать">
                    <button type="button" class="BtnIconPrimaryXs table-btn-editor">
                        <svg width="24" height="24" viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11 3.99998H6.8C5.11984 3.99998 4.27976 3.99998 3.63803 4.32696C3.07354 4.61458 2.6146 5.07353 2.32698 5.63801C2 6.27975 2 7.11983 2 8.79998V17.2C2 18.8801 2 19.7202 2.32698 20.362C2.6146 20.9264 3.07354 21.3854 3.63803 21.673C4.27976 22 5.11984 22 6.8 22H15.2C16.8802 22 17.7202 22 18.362 21.673C18.9265 21.3854 19.3854 20.9264 19.673 20.362C20 19.7202 20 18.8801 20 17.2V13M7.99997 16H9.67452C10.1637 16 10.4083 16 10.6385 15.9447C10.8425 15.8957 11.0376 15.8149 11.2166 15.7053C11.4184 15.5816 11.5914 15.4086 11.9373 15.0627L21.5 5.49998C22.3284 4.67156 22.3284 3.32841 21.5 2.49998C20.6716 1.67156 19.3284 1.67155 18.5 2.49998L8.93723 12.0627C8.59133 12.4086 8.41838 12.5816 8.29469 12.7834C8.18504 12.9624 8.10423 13.1574 8.05523 13.3615C7.99997 13.5917 7.99997 13.8363 7.99997 14.3255V16Z"
                                stroke="#ffffff" stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
        
                    </button>
                </div>
                <div class="tooltip" onclick="VTableEditor.onRowDeleteRequest('${id}', ${r}); return false" data-tip="Удалить">
                    <button type="button" class="BtnIconSecondaryXs">
                        <svg width="17" height="16" viewBox="0 0 17 16"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M6.5 2H10.5M2.5 4H14.5M13.1667 4L12.6991 11.0129C12.629 12.065 12.5939 12.5911 12.3667 12.99C12.1666 13.3412 11.8648 13.6235 11.5011 13.7998C11.088 14 10.5607 14 9.50623 14H7.49377C6.43927 14 5.91202 14 5.49889 13.7998C5.13517 13.6235 4.83339 13.3412 4.63332 12.99C4.40607 12.5911 4.371 12.065 4.30086 11.0129L3.83333 4"
                                stroke="white" stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
        
                    </button>
                </div>
            </div>`;

        if (val.__deleted == '1') {
            btns = ``;
        }

        this._getElementsElement(id).innerHTML += `
        <tr class="table__body-row ${disabled}" data-r="${r};${id}">
        ${valdata}
        <td class="table__body-item table__body-item_content-btns">
            ${btns}
        </td>
</tr>
        `;
    },
    _editVisualRow: function (id, r, val) {
        let valdata = '';
        for (const k of Object.keys(val)) {
            if (k === '__i') continue;
            if (!this._isValAnImage(id, k)) {
                valdata += `<td class="table__body-item"><div class="table__row-text">${val[k]}</div></td>`;
            } else {
                valdata += `<td class="table__body-item"><div class="table__body-item-img"><img src="${val[k]}" /></div></td>`;
            }
        }

        let btns = `<div class="table__body-item-btns">
                <div class="tooltip" onclick="VTableEditor.onRowEdit('${id}', ${r}); return false" data-tip="Редактировать">
                    <button type="button" class="BtnIconPrimaryXs table-btn-editor">
                        <svg width="24" height="24" viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11 3.99998H6.8C5.11984 3.99998 4.27976 3.99998 3.63803 4.32696C3.07354 4.61458 2.6146 5.07353 2.32698 5.63801C2 6.27975 2 7.11983 2 8.79998V17.2C2 18.8801 2 19.7202 2.32698 20.362C2.6146 20.9264 3.07354 21.3854 3.63803 21.673C4.27976 22 5.11984 22 6.8 22H15.2C16.8802 22 17.7202 22 18.362 21.673C18.9265 21.3854 19.3854 20.9264 19.673 20.362C20 19.7202 20 18.8801 20 17.2V13M7.99997 16H9.67452C10.1637 16 10.4083 16 10.6385 15.9447C10.8425 15.8957 11.0376 15.8149 11.2166 15.7053C11.4184 15.5816 11.5914 15.4086 11.9373 15.0627L21.5 5.49998C22.3284 4.67156 22.3284 3.32841 21.5 2.49998C20.6716 1.67156 19.3284 1.67155 18.5 2.49998L8.93723 12.0627C8.59133 12.4086 8.41838 12.5816 8.29469 12.7834C8.18504 12.9624 8.10423 13.1574 8.05523 13.3615C7.99997 13.5917 7.99997 13.8363 7.99997 14.3255V16Z"
                                stroke="#ffffff" stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
        
                    </button>
                </div>
                <div class="tooltip" onclick="VTableEditor.onRowDeleteRequest('${id}', ${r}); return false" data-tip="Удалить">
                    <button type="button" class="BtnIconSecondaryXs">
                        <svg width="17" height="16" viewBox="0 0 17 16"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M6.5 2H10.5M2.5 4H14.5M13.1667 4L12.6991 11.0129C12.629 12.065 12.5939 12.5911 12.3667 12.99C12.1666 13.3412 11.8648 13.6235 11.5011 13.7998C11.088 14 10.5607 14 9.50623 14H7.49377C6.43927 14 5.91202 14 5.49889 13.7998C5.13517 13.6235 4.83339 13.3412 4.63332 12.99C4.40607 12.5911 4.371 12.065 4.30086 11.0129L3.83333 4"
                                stroke="white" stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
        
                    </button>
                </div>
            </div>`;

        if (val.__deleted == '1') {
            btns = ``;
        }

        valdata += `<td class="table__body-item">
            ${btns}
        </td>`;

        const rw = document.querySelector(`tr[data-r="${r};${id}"]`);
        rw.innerHTML = valdata;
        rw.classList.toggle('table__body-row--disabled', val.__deleted == '1');
    },

    _getInputElement: function (id) {
        return document.querySelector(`input[data-id="${id}"]`);
    },
    _getContainerElement: function (id) {
        return document.querySelector(`div[data-id="${id}"]`);
    },
    _getTableElement: function (id) {
        return this._getContainerElement(id).querySelector('table');
    },
    _getHeaderElement: function (id) {
        return this._getTableElement(id).querySelector('thead .table__head-row');
    },
    _getElementsElement: function (id) {
        return this._getTableElement(id).querySelector('tbody');
    },

    _getPaginationElement: function (id) {
        return document.querySelector(`div[data-id="${id}"] .table__pagination`);
    },
    _getPaginationHead: function (page, total) {
        // page -= 1;
        const end = page * 50 + 50 > total ? total : page * 50 + 50;
        const start = page * 50 + 1;

        return `
            <div class="table__pagination-text">Показаны записи: с ${start} по ${end} из ${total}</div>
            <div class="table__pagination-text-mobile">с ${start} по ${end} из ${total}</div>
        `
    },
    _getPaginationFooter: function (page, total) {
        const maxPages = Math.ceil(total / 50);

        return `
         <div class="table__pagination-btns-mobile">
                ${page != 0 ? `
                <button  data-page="${page - 1}" type="button" class="BtnIconOutlineMonoXs table__pagination-btns-mobile-prev">
                    <svg class="table__pagination-btns-mobile-prev"  data-page="${page - 1}" viewBox="0 0 24 24">
                        <use xlink:href="/vendor/glushkovds/simflex/src/Admin/theme/new/img/icons/svg-defs.svg#chevron-mini"></use>
                    </svg>
                </button>
                ` : ''}
                <span class="table__pagination-btns-mobile-text">${page + 1}</span>
                ${page + 1 != maxPages ? `
                    <button  data-page="${page + 1}" type="button" class="BtnIconOutlineMonoXs table__pagination-btns-mobile-next">
                        <svg class="table__pagination-btns-mobile-next" data-page="${page + 1}" viewBox="0 0 24 24">
                            <use xlink:href="/vendor/glushkovds/simflex/src/Admin/theme/new/img/icons/svg-defs.svg#chevron-mini"></use>
                        </svg>
                    </button>
                ` : ''}
                
            </div>
        `
    },

    _getPaginationLayoutStart: function (id, page, total) {
        const maxPages = Math.ceil(total / 50);
        let btns = '';

        if (page != 0) {
            btns += `
                <button type="button" data-page="${page - 1}" class="BtnIconOutlineMonoXs table__pagination-btns-mobile-prev">
                    <svg data-page="${page - 1}" viewBox="0 0 24 24" class="table__pagination-btns-mobile-next" data-page="${page - 1}">
                        <use xlink:href="/vendor/glushkovds/simflex/src/Admin/theme/new/img/icons/svg-defs.svg#chevron-mini"></use>
                    </svg>
                </button>
            `
        }

        for (let i = 0; i < Math.min(maxPages, 5); i++) {
            btns += `<button type="button" class="table-pagination ${page == i ? 'BtnSecondaryMonoXs' : 'BtnOutlineMonoXs'}">${i + 1}</button>`;
        }


        if (maxPages > 4) {
            btns += '<button type="button" class="table-pagination BtnIconOutlineNoneXs">...</button>';
        }

        if (maxPages > 5) {
            btns += `<button type="button" class="table-pagination BtnOutlineMonoXs">${maxPages}</button>`
        } else if (page + 1 != maxPages) {
            btns += `
                <button type="button" data-page="${page + 1}" class="BtnIconOutlineMonoXs table__pagination-btns-mobile-next">
                    <svg data-page="${page + 1}" viewBox="0 0 24 24" class="table__pagination-btns-mobile-next" data-page="${page + 1}">
                        <use xlink:href="/vendor/glushkovds/simflex/src/Admin/theme/new/img/icons/svg-defs.svg#chevron-mini"></use>
                    </svg>
                </button>`
        }

        const layout = `
            ${this._getPaginationHead(page, total)}
            <div class="table__pagination-btns">
                ${btns}
            </div>
            ${this._getPaginationFooter(page, total)}
        `;

        return layout;
    },
    _getPaginationLayoutMiddle: function (id, page, total) {
        const maxPages = Math.ceil(total / 50);
        page += 1;
        // page -= 1;
        const layout = `
            ${this._getPaginationHead(page, total)}
            <div class="table__pagination-btns">
                <button type="button" data-page="${page - 1}" class="BtnIconOutlineMonoXs table__pagination-btns-mobile-prev">
                    <svg viewBox="0 0 24 24" data-page="${page - 1}" class="table__pagination-btns-mobile-prev">
                        <use xlink:href="/vendor/glushkovds/simflex/src/Admin/theme/new/img/icons/svg-defs.svg#chevron-mini"></use>
                    </svg>
                </button>
                <button type="button" class="table-pagination BtnOutlineMonoXs">1</button>
                <button type="button" class="BtnIconOutlineNoneXs">
                            ...
                </button>
                <button type="button"  class="table-pagination BtnOutlineMonoXs">${page - 1}</button>
                <button type="button" class="table-pagination BtnSecondaryMonoXs">${page}</button>
                <button type="button"  class="table-pagination BtnOutlineMonoXs">${page + 1}</button>
                <button type="button" class="BtnIconOutlineNoneXs">
                            ...
                </button>
                <button type="button"  class="table-pagination BtnOutlineMonoXs">${maxPages}</button>
                <button type="button" data-page="${page + 1}" class="BtnIconOutlineMonoXs table__pagination-btns-mobile-next">
                <svg viewBox="0 0 24 24" data-page="${page + 1}" class="table__pagination-btns-mobile-next">
                    <use xlink:href="/vendor/glushkovds/simflex/src/Admin/theme/new/img/icons/svg-defs.svg#chevron-mini"></use>
                </svg>
                </button>
            </div>
            ${this._getPaginationFooter(page, total)}
        `;

        return layout;
    },
    _getPaginationLayoutEnd: function (id, page, total) {
        const maxPages = Math.ceil(total / 50);
        // page -= 1;
        const layout = `
           
                ${this._getPaginationHead(page, total)}
                <div class="table__pagination-btns">
                    <button type="button" data-page="${page - 1}" class="BtnIconOutlineMonoXs table__pagination-btns-mobile-prev">
                            <svg viewBox="0 0 24 24" data-page="${page - 1}" class="table__pagination-btns-mobile-prev">
                                <use xlink:href="/vendor/glushkovds/simflex/src/Admin/theme/new/img/icons/svg-defs.svg#chevron-mini"></use>
                            </svg>
                    </button>
                    <button type="button" class="table-pagination BtnOutlineMonoXs">1</button>
                    <button type="button" class="BtnIconOutlineNoneXs">...</button>
                    <button type="button" class="table-pagination ${page == maxPages - 5 ? 'BtnSecondaryMonoXs' : 'BtnOutlineMonoXs'}">${maxPages - 4}</button>
                    <button type="button" class="table-pagination ${page == maxPages - 4 ? 'BtnSecondaryMonoXs' : 'BtnOutlineMonoXs'}">${maxPages - 3}</button>
                    <button type="button" class="table-pagination ${page == maxPages - 3 ? 'BtnSecondaryMonoXs' : 'BtnOutlineMonoXs'}">${maxPages - 2}</button>
                    <button type="button" class="table-pagination ${page == maxPages - 2 ? 'BtnSecondaryMonoXs' : 'BtnOutlineMonoXs'}">${maxPages - 1}</button>
                    <button type="button" class="table-pagination ${page == maxPages - 1 ? 'BtnSecondaryMonoXs' : 'BtnOutlineMonoXs'}">${maxPages}</button>
                </div>
                ${this._getPaginationFooter(page, total)}
            `;

        return layout;
    },
    _loadData: function (id, page) {
        $.ajax({
            url: `?action=vtGet&page=${page}&${this._makePk(id)}`,
            method: 'GET',
            dataType: 'json',
            data: {},
            beforeSend: () => {
                this.loaders[id].mount();
            },
            success: (data) => {
                const {total, v} = data;
                this._storeValue(id, JSON.parse(v));

                this._getElementsElement(id).innerHTML = '';

                let i = 0;
                for (const row of JSON.parse(v).v) {
                    this._addVisualRow(id, row.__id ?? i++, row, JSON.parse(v).s);
                }

                if (total > 50)
                    this._updatePagination(id, page, total);
            },
            complete: () => {
                this.loaders[id].unmount();
            },
            error: function () {
                alert('Ошибка загрузки');
                this.loaders[id].unmount();
            }
        })
    },

    _updatePagination: function (id, page, total) {
        const maxPages = Math.ceil(total / 50);
        const pagination = this._getPaginationElement(id);

        if (!pagination) return;

        if (page < 4) {
            pagination.innerHTML = this._getPaginationLayoutStart(id, page, total);
        } else if (page >= maxPages - 3 && page <= maxPages) {
            pagination.innerHTML = this._getPaginationLayoutEnd(id, page, total);
        } else {
            pagination.innerHTML = this._getPaginationLayoutMiddle(id, page, total);
        }
    },
};